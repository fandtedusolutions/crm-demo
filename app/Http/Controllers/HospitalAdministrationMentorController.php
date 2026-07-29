<?php

namespace App\Http\Controllers;

class HospitalAdministrationMentorController extends BaseGraphicDesigningStyleMentorController
{
    protected function courseId(): int
    {
        return 4;
    }

    protected function mentorViewName(): string
    {
        return 'admin.converted-leads.hospital-administration-mentor-index';
    }

    protected function updateMentorDetailsRouteName(): string
    {
        return 'admin.hospital-administration-mentor-converted-leads.update-mentor-details';
    }
}
