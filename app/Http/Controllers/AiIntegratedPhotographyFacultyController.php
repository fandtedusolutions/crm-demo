<?php

namespace App\Http\Controllers;

class AiIntegratedPhotographyFacultyController extends BaseGraphicDesigningStyleFacultyController
{
    protected function courseId(): int { return 32; }
    protected function facultyViewName(): string { return 'admin.converted-leads.ai-integrated-photography-faculty-index'; }
    protected function activeFacultyRoute(): string { return 'admin.ai-integrated-photography-faculty-converted-leads.index'; }
}
