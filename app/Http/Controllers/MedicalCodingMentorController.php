<?php

namespace App\Http\Controllers;

class MedicalCodingMentorController extends BaseGraphicDesigningStyleMentorController
{
    protected function courseId(): int
    {
        return 3;
    }

    protected function mentorViewName(): string
    {
        return 'admin.converted-leads.medical-coding-mentor-index';
    }

    protected function updateMentorDetailsRouteName(): string
    {
        return 'admin.medical-coding-mentor-converted-leads.update-mentor-details';
    }
}
