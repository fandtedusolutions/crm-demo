<?php

namespace App\Http\Controllers\Public;

class LeadAiIntegratedPhotographyRegistrationController extends BaseGraphicDesigningStyleRegistrationController
{
    protected function courseId(): int
    {
        return 32;
    }

    protected function courseTitle(): string
    {
        return 'AI-Integrated Photography';
    }

    protected function registerRouteName(): string
    {
        return 'public.lead.ai-integrated-photography.register';
    }

    protected function storeRouteName(): string
    {
        return 'public.lead.ai-integrated-photography.register.store';
    }

    protected function storageKey(): string
    {
        return 'ai_integrated_photography_form_data';
    }
}
