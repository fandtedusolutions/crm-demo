<?php

namespace App\Http\Controllers;

class AiIntegratedPhotographyMentorController extends BaseGraphicDesigningStyleMentorController
{
    protected function courseId(): int { return 32; }
    protected function mentorViewName(): string { return 'admin.converted-leads.ai-integrated-photography-mentor-index'; }
    protected function updateMentorDetailsRouteName(): string { return 'admin.ai-integrated-photography-mentor-converted-leads.update-mentor-details'; }
}
