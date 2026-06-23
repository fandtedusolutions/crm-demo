<?php

/**
 * Generate converted-lead / mentor / faculty / support views for AI-integrated courses 30–32
 * and inject navigation buttons across existing converted-leads pages.
 */

$base = dirname(__DIR__);
$viewsDir = $base . '/resources/views/admin/converted-leads';

$courses = [
    [
        'id' => 30,
        'slug' => 'ai-integrated-video-editing',
        'title' => 'AI-Integrated Video Editing',
        'icon' => 'ti-video',
        'tableId' => 'aiIntegratedVideoEditingConvertedLeadsTable',
        'controller' => 'AiIntegratedVideoEditing',
    ],
    [
        'id' => 31,
        'slug' => 'ai-integrated-videography',
        'title' => 'AI-Integrated Videography',
        'icon' => 'ti-video-plus',
        'tableId' => 'aiIntegratedVideographyConvertedLeadsTable',
        'controller' => 'AiIntegratedVideography',
    ],
    [
        'id' => 32,
        'slug' => 'ai-integrated-photography',
        'title' => 'AI-Integrated Photography',
        'icon' => 'ti-camera',
        'tableId' => 'aiIntegratedPhotographyConvertedLeadsTable',
        'controller' => 'AiIntegratedPhotography',
    ],
];

$templates = [
    'graphic-designing-index.blade.php' => '{slug}-index.blade.php',
    'graphic-designing-mentor-index.blade.php' => '{slug}-mentor-index.blade.php',
    'graphic-designing-faculty-index.blade.php' => '{slug}-faculty-index.blade.php',
    'support-graphic-designing-index.blade.php' => 'support-{slug}-index.blade.php',
];

function transformCourseContent(string $content, array $course): string
{
    $slug = $course['slug'];
    $title = $course['title'];
    $tableId = $course['tableId'];

    $pairs = [
        'Diploma in Graphic Designing' => $title,
        'graphic-designing-converted-leads' => $slug . '-converted-leads',
        'graphic-designing-mentor-converted-leads' => $slug . '-mentor-converted-leads',
        'graphic-designing-faculty-converted-leads' => $slug . '-faculty-converted-leads',
        'support-graphic-designing-converted-leads' => 'support-' . $slug . '-converted-leads',
        'graphic-designing-index' => $slug . '-index',
        'graphic-designing-mentor-index' => $slug . '-mentor-index',
        'graphic-designing-faculty-index' => $slug . '-faculty-index',
        'support-graphic-designing-index' => 'support-' . $slug . '-index',
        'graphicDesigningConvertedLeadsTable' => $tableId,
        'GraphicDesigningMentorController' => $course['controller'] . 'MentorController',
        'GraphicDesigningFacultyController' => $course['controller'] . 'FacultyController',
        "where('course_id', 15)" => "where('course_id', {$course['id']})",
        'course_id = 15' => 'course_id = ' . $course['id'],
        'in_array(15,' => 'in_array(' . $course['id'] . ',',
        'admin.graphic-designing-faculty-converted-leads.index' => 'admin.' . $slug . '-faculty-converted-leads.index',
        '/graphic-designing-mentor-converted-leads/' => '/' . $slug . '-mentor-converted-leads/',
        '/graphic-designing-faculty-converted-leads/' => '/' . $slug . '-faculty-converted-leads/',
        '/support-graphic-designing-converted-leads/' => '/support-' . $slug . '-converted-leads/',
        '/graphic-designing-converted-leads/' => '/' . $slug . '-converted-leads/',
        'ti-palette' => $course['icon'],
    ];

    return str_replace(array_keys($pairs), array_values($pairs), $content);
}

foreach ($courses as $course) {
    foreach ($templates as $source => $destPattern) {
        $sourcePath = $viewsDir . '/' . $source;
        $destPath = $viewsDir . '/' . str_replace('{slug}', $course['slug'], $destPattern);

        if (!file_exists($sourcePath)) {
            fwrite(STDERR, "Missing template: $sourcePath\n");
            continue;
        }

        $content = file_get_contents($sourcePath);
        $content = transformCourseContent($content, $course);
        file_put_contents($destPath, $content);
        echo "Created: $destPath\n";
    }
}

function navButtonsForCourse(array $course, string $type): string
{
    $slug = $course['slug'];
    $title = $course['title'];
    $icon = $course['icon'];

    return match ($type) {
        'converted' => <<<HTML

                    <a href="{{ route('admin.{$slug}-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti {$icon}"></i> {$title} Converted Leads
                    </a>
HTML,
        'mentor' => <<<HTML

                    <a href="{{ route('admin.{$slug}-mentor-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> {$title} Mentor List
                    </a>
HTML,
        'mentor_nav' => <<<HTML

                    <a href="{{ route('admin.{$slug}-mentor-converted-leads.index') }}" class="btn btn-outline-primary {{ \$activeMentorRoute === 'admin.{$slug}-mentor-converted-leads.index' ? 'active' : '' }}">
                        <i class="ti ti-user-star"></i> {$title} Mentor List
                    </a>
HTML,
        'faculty_nav' => <<<HTML

                    <a href="{{ route('admin.{$slug}-faculty-converted-leads.index') }}" class="btn btn-outline-primary {{ \$activeFacultyRoute === 'admin.{$slug}-faculty-converted-leads.index' ? 'active' : '' }}">
                        <i class="ti ti-user-star"></i> {$title} Faculty List
                    </a>
HTML,
        'support' => <<<HTML

                    <a href="{{ route('admin.support-{$slug}-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> {$title} Converted Support List
                    </a>
HTML,
        default => '',
    };
}

$marker = 'admin.ai-integrated-video-editing-converted-leads.index';
$bladeFiles = glob($viewsDir . '/*.blade.php');

foreach ($bladeFiles as $file) {
    $content = file_get_contents($file);
    if (str_contains($content, $marker)) {
        continue;
    }

    $original = $content;

    // Converted leads nav buttons
    if (str_contains($content, "admin.graphic-designing-converted-leads.index")) {
        $insert = '';
        foreach ($courses as $course) {
            $insert .= navButtonsForCourse($course, 'converted');
        }
        $content = preg_replace(
            '/(<a href="\{\{ route\(\'admin\.graphic-designing-converted-leads\.index\'\)[^>]*>.*?<\/a>)/s',
            '$1' . $insert,
            $content,
            1
        );
    }

    // Mentor nav (inline pages)
    if (str_contains($content, "admin.graphic-designing-mentor-converted-leads.index") && !str_contains($file, 'mentor-list-nav')) {
        $insert = '';
        foreach ($courses as $course) {
            $insert .= navButtonsForCourse($course, 'mentor');
        }
        $content = preg_replace(
            '/(<a href="\{\{ route\(\'admin\.graphic-designing-mentor-converted-leads\.index\'\)[^>]*>.*?<\/a>)/s',
            '$1' . $insert,
            $content,
            1
        );
    }

    // Support nav
    if (str_contains($content, "admin.support-graphic-designing-converted-leads.index")) {
        $insert = '';
        foreach ($courses as $course) {
            $insert .= navButtonsForCourse($course, 'support');
        }
        $content = preg_replace(
            '/(<a href="\{\{ route\(\'admin\.support-graphic-designing-converted-leads\.index\'\)[^>]*>.*?<\/a>)/s',
            '$1' . $insert,
            $content,
            1
        );
    }

    if ($content !== $original) {
        file_put_contents($file, $content);
        echo "Updated nav: $file\n";
    }
}

// Partials: mentor-list-nav and faculty-list-nav
foreach (['mentor-list-nav.blade.php' => 'mentor_nav', 'faculty-list-nav.blade.php' => 'faculty_nav'] as $partial => $type) {
    $path = $viewsDir . '/partials/' . $partial;
    $content = file_get_contents($path);
    if (str_contains($content, $marker)) {
        continue;
    }
    $insert = '';
    foreach ($courses as $course) {
        $insert .= navButtonsForCourse($course, $type);
    }
    $content = preg_replace(
        '/(<a href="\{\{ route\(\'admin\.graphic-designing-' . ($type === 'mentor_nav' ? 'mentor' : 'faculty') . '-converted-leads\.index\'\)[^>]*>.*?<\/a>)/s',
        '$1' . $insert,
        $content,
        1
    );
    file_put_contents($path, $content);
    echo "Updated partial: $path\n";
}

echo "Done.\n";
