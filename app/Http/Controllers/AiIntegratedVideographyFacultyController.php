<?php

namespace App\Http\Controllers;

class AiIntegratedVideographyFacultyController extends BaseGraphicDesigningStyleFacultyController
{
    protected function courseId(): int { return 31; }
    protected function facultyViewName(): string { return 'admin.converted-leads.ai-integrated-videography-faculty-index'; }
    protected function activeFacultyRoute(): string { return 'admin.ai-integrated-videography-faculty-converted-leads.index'; }
}
