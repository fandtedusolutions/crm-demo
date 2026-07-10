<?php

namespace App\Observers;

use App\Models\ConvertedLead;
use App\Services\NatXNotificationService;

class ConvertedLeadObserver
{
    /**
     * Handle the ConvertedLead "created" event.
     */
    public function created(ConvertedLead $convertedLead): void
    {
        if (!$convertedLead->admission_batch_id) {
            return;
        }

        NatXNotificationService::notifyMentorOnAdmissionBatchAssign(
            $convertedLead,
            $convertedLead->admission_batch_id
        );
    }

    /**
     * Handle the ConvertedLead "updated" event.
     */
    public function updated(ConvertedLead $convertedLead): void
    {
        if (!$convertedLead->wasChanged('admission_batch_id')) {
            return;
        }

        NatXNotificationService::notifyMentorOnAdmissionBatchAssign(
            $convertedLead,
            $convertedLead->admission_batch_id,
            $convertedLead->getOriginal('admission_batch_id')
        );
    }
}
