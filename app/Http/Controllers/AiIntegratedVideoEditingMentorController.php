<?php

namespace App\Http\Controllers;

class AiIntegratedVideoEditingMentorController extends BaseGraphicDesigningStyleMentorController
{
    protected function courseId(): int { return 30; }
    protected function mentorViewName(): string { return 'admin.converted-leads.ai-integrated-video-editing-mentor-index'; }
    protected function updateMentorDetailsRouteName(): string { return 'admin.ai-integrated-video-editing-mentor-converted-leads.update-mentor-details'; }
}
