<?php

namespace App\Http\Controllers\API\Webhook;

use App\Helpers\PhoneNumberHelper;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MetaWhatsAppLeadWebhookController extends Controller
{
    /**
     * Receive Meta WhatsApp contact webhooks and create CRM leads.
     *
     * POST /api/v1/webhooks/meta-whatsapp
     * Headers: X-API-Key, X-API-Secret
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'event' => 'required|string|in:contact.created,contact.updated',
            'data' => 'required|array',
            'data.id' => 'required',
            'data.name' => 'required|string|max:255',
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
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $event = (string) $request->input('event');
        $contact = $request->input('data', []);
        $sentAt = (string) $request->input('sent_at', '');
        $isMetaWhatsapp = $request->has('is_meta_whatsapp')
            ? (int) filter_var($request->input('is_meta_whatsapp'), FILTER_VALIDATE_BOOLEAN)
            : 1;

        $phoneData = PhoneNumberHelper::get_phone_code((string) ($contact['phone'] ?? ''));
        $code = (string) ($phoneData['code'] ?? '');
        $phone = (string) ($phoneData['phone'] ?? '');

        if ($code === '' || $phone === '') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid phone number',
            ], 422);
        }

        $remark = $this->resolveRemark($request, $contact, $event, $sentAt);

        try {
            $lead = DB::transaction(function () use ($event, $contact, $code, $phone, $remark, $isMetaWhatsapp) {
                $existing = $this->findExistingMetaWhatsappLead($code, $phone, $contact['id'] ?? null);

                if ($existing) {
                    $existing->update([
                        'title' => $contact['name'],
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
                        'lead_status_id' => $existing->lead_status_id,
                        'activity_type' => 'webhook_updated',
                        'description' => 'Lead updated via Meta WhatsApp webhook ('.$event.')',
                        'remarks' => $remark,
                        'created_by' => 1,
                        'updated_by' => 1,
                    ]);

                    return $existing->fresh();
                }

                $telecallerId = $this->assignTelecallerRoundRobin();

                $lead = Lead::create([
                    'title' => $contact['name'],
                    'code' => $code,
                    'phone' => $phone,
                    'whatsapp_code' => $code,
                    'whatsapp' => $phone,
                    'telecaller_id' => $telecallerId,
                    'lead_status_id' => 1,
                    'lead_source_id' => 7,
                    'is_meta_whatsapp' => $isMetaWhatsapp,
                    'remarks' => $remark,
                    'created_by' => 1,
                    'updated_by' => 1,
                ]);

                LeadActivity::create([
                    'lead_id' => $lead->id,
                    'lead_status_id' => 1,
                    'activity_type' => 'webhook_created',
                    'description' => 'Lead created via Meta WhatsApp webhook ('.$event.')',
                    'remarks' => $remark,
                    'created_by' => 1,
                    'updated_by' => 1,
                ]);

                return $lead;
            });

            return response()->json([
                'success' => true,
                'message' => $event === 'contact.updated' ? 'Lead updated' : 'Lead created',
                'lead_id' => $lead->id,
                'lead_source_id' => 7,
                'is_meta_whatsapp' => (int) $lead->is_meta_whatsapp,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Meta WhatsApp webhook failed', [
                'error' => $e->getMessage(),
                'event' => $event,
                'contact_id' => $contact['id'] ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process webhook',
            ], 500);
        }
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

    protected function assignTelecallerRoundRobin(): ?int
    {
        $telecallers = User::where('role_id', 3)->get(['id']);
        if ($telecallers->isEmpty()) {
            return null;
        }

        $currentDate = now()->format('Y-m-d');
        $telecallerLeadCounts = [];

        foreach ($telecallers as $telecaller) {
            $telecallerLeadCounts[$telecaller->id] = Lead::where('telecaller_id', $telecaller->id)
                ->whereDate('created_at', $currentDate)
                ->count();
        }

        asort($telecallerLeadCounts);

        return array_key_first($telecallerLeadCounts);
    }
}
