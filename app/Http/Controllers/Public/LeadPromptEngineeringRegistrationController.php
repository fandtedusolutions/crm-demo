<?php

namespace App\Http\Controllers\Public;

class LeadPromptEngineeringRegistrationController extends BaseCandidateRegistrationController
{
    protected function courseId(): int
    {
        return 34;
    }

    protected function courseTitle(): string
    {
        return 'Prompt Engineering';
    }

    protected function registerRouteName(): string
    {
        return 'public.lead.prompt-engineering.register';
    }

    protected function storeRouteName(): string
    {
        return 'public.lead.prompt-engineering.store';
    }

    protected function successRouteName(): string
    {
        return 'public.lead.prompt-engineering.register.success';
    }
}
