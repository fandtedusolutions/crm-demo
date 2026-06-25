<?php

/**
 * Replace duplicated converted-leads navigation blocks with shared partials.
 */

$base = dirname(__DIR__);
$viewsDir = $base . '/resources/views/admin/converted-leads';

$courseInclude = "@include('admin.converted-leads.partials.converted-leads-course-nav')\n";
$mentorInclude = "@include('admin.converted-leads.partials.mentor-list-nav', ['activeMentorRoute' => \$activeMentorRoute ?? null])\n";
$supportInclude = "@include('admin.converted-leads.partials.converted-leads-support-nav')\n";
$facultyInclude = "@include('admin.converted-leads.partials.faculty-list-nav', ['activeFacultyRoute' => \$activeFacultyRoute ?? null])\n";

$skipFiles = [
    'support-ajax-index.blade.php',
    'support-show.blade.php',
    'support-bosse-show.blade.php',
    'support-nios-show.blade.php',
    'send-course-mail-page.blade.php',
];

$files = glob($viewsDir . '/*-index.blade.php');

foreach ($files as $file) {
    $basename = basename($file);
    if (in_array($basename, $skipFiles, true)) {
        continue;
    }

    $content = file_get_contents($file);
    $original = $content;

    if (preg_match('/<!-- \[ Course Filter Buttons \] start -->/s', $content)) {
        $content = preg_replace(
            '/<!-- \[ Course Filter Buttons \] start -->.*?<!-- \[ Course Filter Buttons \] end -->/s',
            trim($courseInclude),
            $content,
            1
        );
    } elseif (str_contains($content, 'Filter by Course') && !str_contains($content, 'converted-leads-course-nav')) {
        $content = preg_replace(
            '/@if\(\\App\\Helpers\\RoleHelper::is_admin_or_super_admin\(\) \|\| \\App\\Helpers\\RoleHelper::is_admission_counsellor\(\) \|\| \\App\\Helpers\\RoleHelper::is_academic_assistant\(\) \|\| \\App\\Helpers\\RoleHelper::is_finance\(\)\)\s*<div class="row mb-3">.*?<h6 class="mb-3">Filter by Course<\/h6>.*?<\/div>\s*<\/div>\s*<\/div>\s*@endif/s',
            trim($courseInclude),
            $content,
            1
        );
    }

    if (preg_match('/<!-- \[ Mentor List \] start -->/s', $content) && !preg_match('/@include\([\'"]admin\.converted-leads\.partials\.mentor-list-nav/s', $content)) {
        $content = preg_replace(
            '/<!-- \[ Mentor List \] start -->.*?<!-- \[ Mentor List \] end -->/s',
            trim($mentorInclude),
            $content,
            1
        );
    } elseif (
        str_contains($content, '<h6 class="mb-3">Mentor List</h6>')
        && !str_contains($content, 'mentor-list-nav')
        && !str_contains($basename, 'additional-mentor')
    ) {
        $content = preg_replace(
            '/@if\(\\App\\Helpers\\RoleHelper::is_admin_or_super_admin\(\) \|\| \\App\\Helpers\\RoleHelper::is_admission_counsellor\(\) \|\| \\App\\Helpers\\RoleHelper::is_mentor\(\)[^\)]*\)\s*<div class="row mb-3">.*?<h6 class="mb-3">Mentor List<\/h6>.*?<\/div>\s*<\/div>\s*<\/div>\s*@endif/s',
            trim($mentorInclude),
            $content,
            1
        );
    }

    if (!str_contains($content, 'faculty-list-nav')) {
        if (preg_match('/<!-- \[ Faculty List \] start -->/s', $content)) {
            $content = preg_replace(
                '/<!-- \[ Faculty List \] start -->.*?<!-- \[ Faculty List \] end -->/s',
                trim($facultyInclude),
                $content,
                1
            );
        }
    }

    if (preg_match('/<!-- \[ Support List \] start -->/s', $content)) {
        $content = preg_replace(
            '/<!-- \[ Support List \] start -->.*?<!-- \[ Support List \] end -->/s',
            trim($supportInclude),
            $content,
            1
        );
    } elseif (str_contains($content, '<h6 class="mb-3">Support List</h6>') && !str_contains($content, 'converted-leads-support-nav')) {                                                           
        $content = preg_replace(
            '/@if\(\\App\\Helpers\\RoleHelper::is_admin_or_super_admin\(\) \|\| \\App\\Helpers\\RoleHelper::is_admission_counsellor\(\) \|\| \\App\\Helpers\\RoleHelper::is_support_team\(\)\)\s*<div class="row mb-3">.*?<h6 class="mb-3">Support List<\/h6>.*?<\/div>\s*<\/div>\s*<\/div>\s*@endif/s',
            trim($supportInclude),
            $content,
            1
        );
    }

    if ($content !== $original) {
        file_put_contents($file, $content);
        echo "Updated: $basename\n";
    }
}

$indexPath = $viewsDir . '/index.blade.php';
if (file_exists($indexPath)) {
    $content = file_get_contents($indexPath);
    $navBlock = implode("\n\n", [
        "@include('admin.converted-leads.partials.converted-leads-course-nav')",
        "@include('admin.converted-leads.partials.mentor-list-nav', ['activeMentorRoute' => \$activeMentorRoute ?? null])",
        "@include('admin.converted-leads.partials.faculty-list-nav', ['activeFacultyRoute' => \$activeFacultyRoute ?? null])",
        "@include('admin.converted-leads.partials.converted-leads-support-nav')",
    ]);
    $updated = preg_replace(
        '/<!-- \[ Course Filter Buttons \] start -->.*?<!-- \[ Support List \] end -->/s',
        $navBlock,
        $content,
        1
    );
    if ($updated !== null && $updated !== $content) {
        file_put_contents($indexPath, $updated);
        echo "Updated: index.blade.php\n";
    }
}

echo "Done.\n";
