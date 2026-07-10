<?php

namespace App\Services;

use App\Helpers\AuthHelper;
use App\Models\AdmissionBatch;
use App\Models\ConvertedLead;
use App\Models\NatXNotification;
use Illuminate\Support\Facades\Log;

class NatXNotificationService
{
    /**
     * Notify mentor when a student is assigned to their admission batch.
     */
    public static function notifyMentorOnAdmissionBatchAssign(
        ConvertedLead $convertedLead,
        $newAdmissionBatchId,
        $oldAdmissionBatchId = null
    ): ?NatXNotification {
        if (!$newAdmissionBatchId) {
            return null;
        }

        if ((string) $oldAdmissionBatchId === (string) $newAdmissionBatchId) {
            return null;
        }

        $admissionBatch = AdmissionBatch::with('batch:id,title')->find($newAdmissionBatchId);
        if (!$admissionBatch || !$admissionBatch->mentor_id) {
            return null;
        }

        $studentName = $convertedLead->name ?: 'A student';
        $batchName = $admissionBatch->title
            ?: ($admissionBatch->batch?->title ?: 'admission batch');

        $today = now()->toDateString();

        $notification = NatXNotification::create([
            'title' => 'New Student Assigned',
            'type' => 'high',
            'description' => "{$studentName} has been assigned to {$batchName}.",
            'date' => $today,
            'upto_date' => now()->addDays(7)->toDateString(),
            'is_active' => true,
            'user_id' => $admissionBatch->mentor_id,
            'created_by' => AuthHelper::getCurrentUserId(),
        ]);

        try {
            FcmPushService::sendNatXNotification($notification);
        } catch (\Throwable $e) {
            Log::error(
                'NatX push notification failed: ' . $e->getMessage(),
                ['notification_id' => $notification->id]
            );
        }

        return $notification;
    }
}
