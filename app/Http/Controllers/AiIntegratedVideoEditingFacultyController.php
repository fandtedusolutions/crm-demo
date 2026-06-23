<?php

namespace App\Http\Controllers;

class AiIntegratedVideoEditingFacultyController extends BaseGraphicDesigningStyleFacultyController
{
    protected function courseId(): int { return 30; }
    protected function facultyViewName(): string { return 'admin.converted-leads.ai-integrated-video-editing-faculty-index'; }
    protected function activeFacultyRoute(): string { return 'admin.ai-integrated-video-editing-faculty-converted-leads.index'; }
}
