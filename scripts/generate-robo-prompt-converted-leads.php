<?php

/**
 * Generate converted-lead / mentor / faculty / support pages for Robo Vibe (33) and Prompt Engineering (34)
 * from CreateX AI (junior-vlogger) templates.
 */

$base = dirname(__DIR__);
$viewsDir = $base . '/resources/views/admin/converted-leads';
$controllersDir = $base . '/app/Http/Controllers';

$courses = [
    [
        'id' => 33,
        'slug' => 'robo-vibe',
        'title' => 'Robo Vibe',
        'pascal' => 'RoboVibe',
        'camel' => 'roboVibe',
        'studentDetails' => 'roboVibeStudentDetails',
        'tableId' => 'roboVibeTable',
        'icon' => 'ti-robot',
        'jvVar' => 'rv',
        'jvLeadVar' => 'rvLead',
    ],
    [
        'id' => 34,
        'slug' => 'prompt-engineering',
        'title' => 'Prompt Engineering',
        'pascal' => 'PromptEngineering',
        'camel' => 'promptEngineering',
        'studentDetails' => 'promptEngineeringStudentDetails',
        'tableId' => 'promptEngineeringTable',
        'icon' => 'ti-brain',
        'jvVar' => 'pe',
        'jvLeadVar' => 'peLead',
    ],
];

$viewTemplates = [
    'junior-vlogger-index.blade.php' => '{slug}-index.blade.php',
    'junior-vlogger-mentor-index.blade.php' => '{slug}-mentor-index.blade.php',
    'junior-vlogger-faculty-index.blade.php' => '{slug}-faculty-index.blade.php',
    'support-junior-vlogger-index.blade.php' => 'support-{slug}-index.blade.php',
];

$controllerTemplates = [
    'JuniorVloggerMentorController.php' => '{pascal}MentorController.php',
    'JuniorVloggerFacultyController.php' => '{pascal}FacultyController.php',
];

function transformJuniorVloggerContent(string $content, array $course): string
{
    $pairs = [
        'CreateX AI' => $course['title'],
        'junior-vlogger-converted-leads' => $course['slug'] . '-converted-leads',
        'junior-vlogger-mentor-converted-leads' => $course['slug'] . '-mentor-converted-leads',
        'junior-vlogger-faculty-converted-leads' => $course['slug'] . '-faculty-converted-leads',
        'support-junior-vlogger-converted-leads' => 'support-' . $course['slug'] . '-converted-leads',
        'junior-vlogger-index' => $course['slug'] . '-index',
        'junior-vlogger-mentor-index' => $course['slug'] . '-mentor-index',
        'junior-vlogger-faculty-index' => $course['slug'] . '-faculty-index',
        'support-junior-vlogger-index' => 'support-' . $course['slug'] . '-index',
        'juniorVloggerTable' => $course['tableId'],
        'juniorVloggerStudentDetails' => $course['studentDetails'],
        'JuniorVloggerMentorController' => $course['pascal'] . 'MentorController',
        'JuniorVloggerFacultyController' => $course['pascal'] . 'FacultyController',
        'JuniorVlogger' => $course['pascal'],
        'juniorVlogger' => $course['camel'],
        "where('course_id', 25)" => "where('course_id', {$course['id']})",
        'course_id = 25' => 'course_id = ' . $course['id'],
        'course_id, 25' => 'course_id, ' . $course['id'],
        'Course::find(25)' => 'Course::find(' . $course['id'] . ')',
        'COURSE_ID = 25' => 'COURSE_ID = ' . $course['id'],
        'self::COURSE_ID' => 'self::COURSE_ID',
        '/junior-vlogger-mentor-converted-leads/' => '/' . $course['slug'] . '-mentor-converted-leads/',
        '/junior-vlogger-faculty-converted-leads/' => '/' . $course['slug'] . '-faculty-converted-leads/',
        '/support-junior-vlogger-converted-leads/' => '/support-' . $course['slug'] . '-converted-leads/',
        '/junior-vlogger-converted-leads/' => '/' . $course['slug'] . '-converted-leads/',
        'admin.junior-vlogger-faculty-converted-leads.index' => 'admin.' . $course['slug'] . '-faculty-converted-leads.index',
        '$jvLead' => '$' . $course['jvLeadVar'],
        '$jv =' => '$' . $course['jvVar'] . ' =',
        '$leadDetailJv' => '$leadDetail' . ucfirst($course['camel']),
        'leadDetailJv' => 'leadDetail' . ucfirst($course['camel']),
        'ti-video' => $course['icon'],
    ];

    return str_replace(array_keys($pairs), array_values($pairs), $content);
}

foreach ($courses as $course) {
    foreach ($viewTemplates as $source => $destPattern) {
        $sourcePath = $viewsDir . '/' . $source;
        $destPath = $viewsDir . '/' . str_replace('{slug}', $course['slug'], $destPattern);
        $content = transformJuniorVloggerContent(file_get_contents($sourcePath), $course);
        file_put_contents($destPath, $content);
        echo "Created view: $destPath\n";
    }

    foreach ($controllerTemplates as $source => $destPattern) {
        $sourcePath = $controllersDir . '/' . $source;
        $destName = str_replace('{pascal}', $course['pascal'], $destPattern);
        $destPath = $controllersDir . '/' . $destName;
        $content = transformJuniorVloggerContent(file_get_contents($sourcePath), $course);
        file_put_contents($destPath, $content);
        echo "Created controller: $destPath\n";
    }
}

function navButtonBlock(array $course, string $type): string
{
    $slug = $course['slug'];
    $title = $course['title'];
    $icon = $course['icon'];

    return match ($type) {
        'converted' => <<<HTML

                    <a href="{{ route('admin.{$slug}-converted-leads.index') }}" class="{{ \$convertedNavBtn('admin.{$slug}-converted-leads.index') }}">
                        <i class="ti {$icon}"></i> {$title} Converted Leads
                    </a>
HTML,
        'mentor' => <<<HTML

                    <a href="{{ route('admin.{$slug}-mentor-converted-leads.index') }}" class="{{ \$mentorNavBtn('admin.{$slug}-mentor-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> {$title} Converted Mentor List
                    </a>
HTML,
        'faculty' => <<<HTML

                    <a href="{{ route('admin.{$slug}-faculty-converted-leads.index') }}" class="{{ \$facultyNavBtn('admin.{$slug}-faculty-converted-leads.index') }}">
                        <i class="ti ti-user-star"></i> {$title} Converted Faculty List
                    </a>
HTML,
        'support' => <<<HTML

                    <a href="{{ route('admin.support-{$slug}-converted-leads.index') }}" class="{{ \$supportNavBtn('admin.support-{$slug}-converted-leads.index') }}">
                        <i class="ti ti-headphones"></i> {$title} - Course Support List
                    </a>
HTML,
        default => '',
    };
}

$marker = 'admin.robo-vibe-converted-leads.index';
$partials = [
    'converted-leads-course-nav.blade.php' => ['converted', "admin.junior-vlogger-converted-leads.index"],
    'mentor-list-nav.blade.php' => ['mentor', "admin.junior-vlogger-mentor-converted-leads.index"],
    'faculty-list-nav.blade.php' => ['faculty', "admin.junior-vlogger-faculty-converted-leads.index"],
    'converted-leads-support-nav.blade.php' => ['support', "admin.support-junior-vlogger-converted-leads.index"],
];

foreach ($partials as $partial => [$type, $anchorRoute]) {
    $path = $viewsDir . '/partials/' . $partial;
    $content = file_get_contents($path);
    if (str_contains($content, $marker)) {
        echo "Nav already present in $partial\n";
        continue;
    }
    $insert = '';
    foreach ($courses as $course) {
        $insert .= navButtonBlock($course, $type);
    }
    $escaped = preg_quote($anchorRoute, '/');
    $content = preg_replace(
        "/(<a href=\"\\{\\{ route\\('$escaped'\\)\\s*\\}\\}\"[^>]*>.*?<\\/a>)/s",
        '$1' . $insert,
        $content,
        1
    );
    file_put_contents($path, $content);
    echo "Updated partial: $path\n";
}

echo "Done.\n";
