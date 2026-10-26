<?php

namespace App\Http\Controllers\Public;

class LeadAiIntegratedVideoEditingRegistrationController extends BaseGraphicDesigningStyleRegistrationController
{
    protected function courseId(): int
    {
        return 30;
    }

    protected function courseTitle(): string
    {
        return \App\Helpers\CourseTitleHelper::title($this->courseId(), 'AI-Integrated Video Editing');
    }

    protected function registerRouteName(): string
    {
        return 'public.lead.ai-integrated-video-editing.register';
    }

    protected function storeRouteName(): string
    {
        return 'public.lead.ai-integrated-video-editing.register.store';
    }

    protected function storageKey(): string
    {
        return 'ai_integrated_video_editing_form_data';
    }
}
