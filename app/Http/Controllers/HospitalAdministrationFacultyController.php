<?php

namespace App\Http\Controllers;

class HospitalAdministrationFacultyController extends BaseGraphicDesigningStyleFacultyController
{
    protected function courseId(): int
    {
        return 4;
    }

    protected function facultyViewName(): string
    {
        return 'admin.converted-leads.hospital-administration-faculty-index';
    }

    protected function activeFacultyRoute(): string
    {
        return 'admin.hospital-administration-faculty-converted-leads.index';
    }
}
