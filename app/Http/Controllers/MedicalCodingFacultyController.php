<?php

namespace App\Http\Controllers;

class MedicalCodingFacultyController extends BaseGraphicDesigningStyleFacultyController
{
    protected function courseId(): int
    {
        return 3;
    }

    protected function facultyViewName(): string
    {
        return 'admin.converted-leads.medical-coding-faculty-index';
    }

    protected function activeFacultyRoute(): string
    {
        return 'admin.medical-coding-faculty-converted-leads.index';
    }
}
