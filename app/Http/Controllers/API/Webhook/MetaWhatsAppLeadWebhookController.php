<?php

namespace App\Http\Controllers\API\Webhook;

use App\Helpers\PhoneNumberHelper;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadStatus;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Psr\Log\LoggerInterface;

class MetaWhatsAppLeadWebhookController extends Controller
{
    protected function webhookLog(): LoggerInterface
    {
        return Log::channel('meta_whatsapp_webhook');
    }

    /**
     * Receive Meta WhatsApp contact webhooks and create CRM leads.
     *
     * POST /api/v1/webhooks/meta-whatsapp
     * Public endpoint — authenticates via Natdemy delivery only (no API key).
     * Reads X-Webhook-Event header and/or body event + data payload.
     */
    public function store(Request $request): JsonResponse
    {
        $log = $this->webhookLog();

        $eventFromHeader = (string) $request->header('X-Webhook-Event', '');
        $eventFromBody = (string) $request->input('event', '');
        $event = $eventFromHeader !== '' ? $eventFromHeader : $eventFromBody;

        $requestContext = [
            'ip' => $request->ip(),
            'content_type' => $request->header('Content-Type'),
            'x_webhook_event' => $eventFromHeader !== '' ? $eventFromHeader : null,
            'payload' => $request->all(),
        ];
        $log->info('Webhook request received', $requestContext);
        Log::info('[meta-whatsapp-webhook] request received', $requestContext);

        try {
            $validator = Validator::make(array_merge($request->all(), ['event' => $event]), [
                'event' => 'required|string|in:contact.created,contact.updated',
                'data' => 'required|array',
                'data.id' => 'required',
                'data.name' => 'nullable|string|max:255',
                'data.phone' => 'required|string|max:30',
                'data.created_at' => 'nullable|string|max:100',
                'data.remark' => 'nullable|string',
                'data.remarks' => 'nullable|string',
                'remark' => 'nullable|string',
                'remarks' => 'nullable|string',
                'is_meta_whatsapp' => 'nullable|boolean',
                'sent_at' => 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                $context = [
                    'errors' => $validator->errors()->toArray(),
                    'payload' => $request->all(),
                    'event' => $event,
                ];
                $log->warning('Webhook validation failed', $context);
                Log::warning('[meta-whatsapp-webhook] validation failed', $context);

                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $contact = $request->input('data', []);
            $sentAt = (string) $request->input('sent_at', '');
            $isMetaWhatsapp = $request->has('is_meta_whatsapp')
                ? (int) filter_var($request->input('is_meta_whatsapp'), FILTER_VALIDATE_BOOLEAN)
                : 1;

            $rawPhone = (string) ($contact['phone'] ?? '');
            $phoneData = PhoneNumberHelper::get_phone_code($rawPhone);
            $code = (string) ($phoneData['code'] ?? '');
            $phone = (string) ($phoneData['phone'] ?? '');

            $log->info('Parsed phone', [
                'raw_phone' => $rawPhone,
                'code' => $code,
                'phone' => $phone,
            ]);

            if ($code === '' || $phone === '') {
                $log->error('Invalid phone number after parse', [
                    'raw_phone' => $rawPhone,
                    'phone_data' => $phoneData,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid phone number',
                ], 422);
            }

            $title = trim((string) ($contact['name'] ?? ''));
            if ($title === '') {
                $title = $code.$phone;
            }

            $remark = $this->resolveRemark($request, $contact, $event, $sentAt);

            $lead = DB::transaction(function () use ($event, $contact, $title, $code, $phone, $remark, $isMetaWhatsapp, $log) {
                $existing = $this->findExistingMetaWhatsappLead($code, $phone, $contact['id'] ?? null);

                if ($existing) {
                    $log->info('Updating existing Meta WhatsApp lead', [
                        'lead_id' => $existing->id,
                        'event' => $event,
                    ]);

                    $existing->update([
                        'title' => $title,
                        'code' => $code,
                        'phone' => $phone,
                        'whatsapp_code' => $code,
                        'whatsapp' => $phone,
                        'remarks' => $remark,
                        'is_meta_whatsapp' => $isMetaWhatsapp,
                        'lead_source_id' => 7,
                        'updated_by' => 1,
                    ]);

                    LeadActivity::create([
                        'lead_id' => $existing->id,
                        'lead_status_id' => $existing->lead_status_id ?: 1,
                        'activity_type' => 'webhook_updated',
                        'description' => 'Lead updated via Meta WhatsApp webhook ('.$event.')',
                        'remarks' => $remark,
                        'created_by' => 1,
                        'updated_by' => 1,
                    ]);

                    return $existing->fresh();
                }

                $leadData = $this->buildLeadAttributes($title, $code, $phone, $remark, $isMetaWhatsapp);

                $log->info('Creating new Meta WhatsApp lead', [
                    'event' => $event,
                    'contact_id' => $contact['id'] ?? null,
                    'telecaller_id' => $leadData['telecaller_id'] ?? null,
                    'team_id' => $leadData['team_id'] ?? null,
                ]);

                $lead = Lead::create($leadData);

                LeadActivity::create([
                    'lead_id' => $lead->id,
                    'lead_status_id' => $leadData['lead_status_id'],
                    'activity_type' => 'webhook_created',
                    'description' => 'Lead created via Meta WhatsApp webhook ('.$event.')',
                    'remarks' => $remark,
                    'created_by' => 1,
                    'updated_by' => 1,
                ]);

                return $lead;
            });

            $successContext = [
                'lead_id' => $lead->id,
                'event' => $event,
                'is_meta_whatsapp' => (int) $lead->is_meta_whatsapp,
            ];
            $log->info('Webhook processed successfully', $successContext);
            Log::info('[meta-whatsapp-webhook] processed successfully', $successContext);

            return response()->json([
                'success' => true,
                'message' => $event === 'contact.updated' ? 'Lead updated' : 'Lead created',
                'lead_id' => $lead->id,
                'lead_source_id' => 7,
                'is_meta_whatsapp' => (int) $lead->is_meta_whatsapp,
            ], 200);
        } catch (\Throwable $e) {
            $errorContext = [
                'error' => $e->getMessage(),
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all(),
            ];
            $log->error('Meta WhatsApp webhook failed', $errorContext);
            Log::error('[meta-whatsapp-webhook] failed: '.$e->getMessage(), $errorContext);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process webhook',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function buildLeadAttributes(string $title, string $code, string $phone, string $remark, int $isMetaWhatsapp): array
    {
        $telecallerId = 183;
        $teamId = 11;
        $telecaller = User::find($telecallerId);
        $leadStatus = LeadStatus::find(1);
        $countryId = Country::query()
            ->where('phone_code', $code)
            ->value('id');

        return [
            'title' => $title,
            'code' => $code,
            'phone' => $phone,
            'whatsapp_code' => $code,
            'whatsapp' => $phone,
            'telecaller_id' => $telecallerId,
            'team_id' => $teamId,
            'lead_status_id' => 1,
            'lead_source_id' => 7,
            'interest_status' => $leadStatus?->interest_status,
            'country_id' => $countryId,
            'is_meta_whatsapp' => $isMetaWhatsapp,
            'is_converted' => false,
            'is_b2b' => $telecaller && $telecaller->is_b2b ? 1 : 0,
            'is_pullbacked' => false,
            'remarks' => $remark,
            'first_created_at' => now(),
            'created_by' => 1,
            'updated_by' => 1,
        ];
    }

    protected function resolveRemark(Request $request, array $contact, string $event, string $sentAt): string
    {
        $remark = $request->input('remark')
            ?? $request->input('remarks')
            ?? ($contact['remark'] ?? null)
            ?? ($contact['remarks'] ?? null);

        if (is_string($remark) && trim($remark) !== '') {
            return trim($remark);
        }

        $parts = [
            'Meta WhatsApp contact',
            'event: '.$event,
            'contact_id: '.($contact['id'] ?? ''),
        ];

        if (! empty($contact['created_at'])) {
            $parts[] = 'created_at: '.$contact['created_at'];
        }

        if ($sentAt !== '') {
            $parts[] = 'sent_at: '.$sentAt;
        }

        return implode(' | ', $parts);
    }

    protected function findExistingMetaWhatsappLead(string $code, string $phone, mixed $contactId = null): ?Lead
    {
        $query = Lead::query()
            ->where('is_meta_whatsapp', 1)
            ->where(function ($builder) use ($code, $phone) {
                $builder->where(function ($exact) use ($code, $phone) {
                    $exact->where('code', $code)->where('phone', $phone);
                })->orWhere(function ($full) use ($code, $phone) {
                    $full->where('code', $code)->where('phone', $code.$phone);
                });
            });

        $existing = $query->first();
        if ($existing) {
            return $existing;
        }

        if ($contactId === null || $contactId === '') {
            return null;
        }

        return Lead::query()
            ->where('is_meta_whatsapp', 1)
            ->where('remarks', 'like', '%contact_id: '.$contactId.'%')
            ->first();
    }
}
