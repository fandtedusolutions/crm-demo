<?php

namespace App\Http\Controllers;

class AiIntegratedVideographyMentorController extends BaseGraphicDesigningStyleMentorController
{
    protected function courseId(): int { return 31; }
    protected function mentorViewName(): string { return 'admin.converted-leads.ai-integrated-videography-mentor-index'; }
    protected function updateMentorDetailsRouteName(): string { return 'admin.ai-integrated-videography-mentor-converted-leads.update-mentor-details'; }
}
