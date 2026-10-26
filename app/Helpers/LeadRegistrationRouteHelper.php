<?php

namespace App\Helpers;

use App\Models\LeadDetail;
use App\Models\PlusTwoFollowUpQuestionnaire;

class LeadRegistrationRouteHelper
{
    /**
     * Course ID => public registration route name.
     */
    public static function courseRegistrationRouteNames(): array
    {
        return [
            1 => 'public.lead.nios.register',
            2 => 'public.lead.bosse.register',
            3 => 'public.lead.medical-coding.register',
            4 => 'public.lead.hospital-admin.register',
            5 => 'public.lead.eschool.register',
            6 => 'public.lead.eduthanzeel.register',
            7 => 'public.lead.ttc.register',
            8 => 'public.lead.hotel-mgmt.register',
            9 => 'public.lead.ugpg.register',
            10 => 'public.lead.python.register',
            11 => 'public.lead.digital-marketing.register',
            12 => 'public.lead.diploma-in-data-science.register',
            37 => 'public.lead.data-analytics-with-gen-ai.register',
            13 => 'public.lead.web-dev.register',
            14 => 'public.lead.vibe-coding.register',
            15 => 'public.lead.graphic-designing.register',
            16 => 'public.lead.gmvss.register',
            20 => 'public.lead.machine-learning.register',
            21 => 'public.lead.flutter.register',
            23 => 'public.lead.edumaster.register',
            25 => 'public.lead.junior-vlogger.register',
            27 => 'public.lead.rpa.register',
            29 => 'public.lead.ai-sales-marketing.register',
            30 => 'public.lead.ai-integrated-video-editing.register',
            31 => 'public.lead.ai-integrated-videography.register',
            32 => 'public.lead.ai-integrated-photography.register',
            33 => 'public.lead.robo-vibe.register',
            34 => 'public.lead.prompt-engineering.register',
        ];
    }

    /**
     * Course ID => display title for admin/mobile labels.
     */
    public static function courseRegistrationTitles(): array
    {
        $titles = [];
        foreach (static::courseRegistrationRouteNames() as $courseId => $routeName) {
            $titles[$courseId] = CourseTitleHelper::title((int) $courseId, 'Registration');
        }

        return $titles;
    }

    public static function hasRegistrationForm(?int $courseId): bool
    {
        return $courseId !== null && isset(static::courseRegistrationRouteNames()[$courseId]);
    }

    public static function registrationRouteName(?int $courseId): ?string
    {
        if ($courseId === null) {
            return null;
        }

        return static::courseRegistrationRouteNames()[$courseId] ?? null;
    }

    public static function registrationTitle(?int $courseId): ?string
    {
        if ($courseId === null) {
            return null;
        }

        return static::courseRegistrationTitles()[$courseId] ?? null;
    }

    public static function registrationUrlForLead(object $lead): string
    {
        $routeName = static::registrationRouteName($lead->course_id ?? null);

        if (!$routeName) {
            return '';
        }

        try {
            return route($routeName, $lead->id);
        } catch (\Exception $e) {
            return '';
        }
    }

    public static function hasPlusTwoFollowUpSubmission(object $lead): bool
    {
        if ((int) ($lead->lead_source_id ?? 0) !== PlusTwoFollowUpQuestionnaire::LEAD_SOURCE_ID) {
            return false;
        }

        if ($lead->relationLoaded('plusTwoFollowUpQuestionnaire')) {
            return (bool) $lead->plusTwoFollowUpQuestionnaire;
        }

        return !empty($lead->plusTwoFollowUpQuestionnaire);
    }

    public static function isRegistrationSubmittedForLead(object $lead): bool
    {
        $courseId = $lead->course_id ?? null;

        if (!$courseId) {
            return false;
        }

        if ($lead->relationLoaded('studentDetails') && $lead->studentDetails) {
            return (int) $lead->studentDetails->course_id === (int) $courseId;
        }

        return LeadDetail::where('lead_id', $lead->id)
            ->where('course_id', $courseId)
            ->exists();
    }

    /**
     * Registration link fields for mobile/API consumers.
     */
    public static function apiRegistrationFields(object $lead): array
    {
        $isPlusTwoLead = (int) ($lead->lead_source_id ?? 0) === PlusTwoFollowUpQuestionnaire::LEAD_SOURCE_ID;
        $isPlusTwoSubmitted = static::hasPlusTwoFollowUpSubmission($lead);
        $showPlusTwoFollowUpFormLink = $isPlusTwoLead && !$isPlusTwoSubmitted ? 1 : 0;
        $plusTwoFollowUpFormLink = '';

        if ($showPlusTwoFollowUpFormLink) {
            try {
                $plusTwoFollowUpFormLink = route('public.lead.plus-two-follow-up.register', $lead->id);
            } catch (\Exception $e) {
                $plusTwoFollowUpFormLink = '';
            }
        }

        $showLeadRegFormLink = static::hasRegistrationForm($lead->course_id ?? null) ? 1 : 0;
        $regFormLink = $showLeadRegFormLink ? static::registrationUrlForLead($lead) : '';

        return [
            'is_lead_reg_form_submitted' => static::isRegistrationSubmittedForLead($lead) ? 1 : 0,
            'show_lead_reg_form_link' => $showLeadRegFormLink,
            'reg_form_link' => $regFormLink,
            'registration_link' => $regFormLink,
            'registration_form_title' => static::registrationTitle($lead->course_id ?? null) ?? '',
            'show_plus_two_follow_up_form_link' => $showPlusTwoFollowUpFormLink,
            'plus_two_follow_up_form_link' => $plusTwoFollowUpFormLink,
            'is_plus_two_follow_up_form_submitted' => $isPlusTwoLead && $isPlusTwoSubmitted ? 1 : 0,
        ];
    }
}
