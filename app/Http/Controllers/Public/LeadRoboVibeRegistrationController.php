<?php

namespace App\Http\Controllers\Public;

class LeadRoboVibeRegistrationController extends BaseCandidateRegistrationController
{
    protected function courseId(): int
    {
        return 33;
    }

    protected function courseTitle(): string
    {
        return 'Robo Vibe';
    }

    protected function registerRouteName(): string
    {
        return 'public.lead.robo-vibe.register';
    }

    protected function storeRouteName(): string
    {
        return 'public.lead.robo-vibe.store';
    }

    protected function successRouteName(): string
    {
        return 'public.lead.robo-vibe.register.success';
    }
}
