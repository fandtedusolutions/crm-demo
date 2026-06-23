<?php

namespace App\Http\Controllers\Public;

class LeadAiIntegratedVideographyRegistrationController extends BaseGraphicDesigningStyleRegistrationController
{
    protected function courseId(): int
    {
        return 31;
    }

    protected function courseTitle(): string
    {
        return 'AI-Integrated Videography';
    }

    protected function registerRouteName(): string
    {
        return 'public.lead.ai-integrated-videography.register';
    }

    protected function storeRouteName(): string
    {
        return 'public.lead.ai-integrated-videography.register.store';
    }

    protected function storageKey(): string
    {
        return 'ai_integrated_videography_form_data';
    }
}
