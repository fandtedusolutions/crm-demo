<?php

/**
 * One-time generator: faculty converted-lead controllers and views from mentor equivalents.
 */

$base = dirname(__DIR__);

$controllerMap = [
    'MentorConvertedLeadController.php' => 'FacultyConvertedLeadController.php',
    'UGPGMentorConvertedLeadController.php' => 'UGPGFacultyConvertedLeadController.php',
    'EduMasterMentorConvertedLeadController.php' => 'EduMasterFacultyConvertedLeadController.php',
    'NiosMentorConvertedLeadController.php' => 'NiosFacultyConvertedLeadController.php',
    'ESchoolEduthanzeelMentorController.php' => 'ESchoolEduthanzeelFacultyController.php',
    'DataScienceMentorController.php' => 'DataScienceFacultyController.php',
    'MachineLearningMentorController.php' => 'MachineLearningFacultyController.php',
    'DigitalMarketingMentorController.php' => 'DigitalMarketingFacultyController.php',
    'GraphicDesigningMentorController.php' => 'GraphicDesigningFacultyController.php',
    'JuniorVloggerMentorController.php' => 'JuniorVloggerFacultyController.php',
    'AdditionalMentorCourseController.php' => 'AdditionalFacultyCourseController.php',
];

$viewMap = [
    'mentor-bosse-index.blade.php' => 'faculty-bosse-index.blade.php',
    'mentor-ugpg-index.blade.php' => 'faculty-ugpg-index.blade.php',
    'mentor-edumaster-index.blade.php' => 'faculty-edumaster-index.blade.php',
    'nios-mentor-index.blade.php' => 'nios-faculty-index.blade.php',
    'eschool-mentor-index.blade.php' => 'eschool-faculty-index.blade.php',
    'eduthanzeel-mentor-index.blade.php' => 'eduthanzeel-faculty-index.blade.php',
    'gmvss-mentor-index.blade.php' => 'gmvss-faculty-index.blade.php',
    'digital-marketing-mentor-index.blade.php' => 'digital-marketing-faculty-index.blade.php',
    'data-science-mentor-index.blade.php' => 'data-science-faculty-index.blade.php',
    'graphic-designing-mentor-index.blade.php' => 'graphic-designing-faculty-index.blade.php',
    'machine-learning-mentor-index.blade.php' => 'machine-learning-faculty-index.blade.php',
    'junior-vlogger-mentor-index.blade.php' => 'junior-vlogger-faculty-index.blade.php',
    'additional-mentor-course-index.blade.php' => 'additional-faculty-course-index.blade.php',
];

function transformFacultyContent(string $content, bool $isPhp): string
{
    $pairs = [
        'MentorConvertedLeadController' => 'FacultyConvertedLeadController',
        'UGPGMentorConvertedLeadController' => 'UGPGFacultyConvertedLeadController',
        'EduMasterMentorConvertedLeadController' => 'EduMasterFacultyConvertedLeadController',
        'NiosMentorConvertedLeadController' => 'NiosFacultyConvertedLeadController',
        'ESchoolEduthanzeelMentorController' => 'ESchoolEduthanzeelFacultyController',
        'DataScienceMentorController' => 'DataScienceFacultyController',
        'MachineLearningMentorController' => 'MachineLearningFacultyController',
        'DigitalMarketingMentorController' => 'DigitalMarketingFacultyController',
        'GraphicDesigningMentorController' => 'GraphicDesigningFacultyController',
        'JuniorVloggerMentorController' => 'JuniorVloggerFacultyController',
        'AdditionalMentorCourseController' => 'AdditionalFacultyCourseController',
        'gmvss-mentor-converted-leads' => 'gmvss-faculty-converted-leads',
        'junior-vlogger-mentor-converted-leads' => 'junior-vlogger-faculty-converted-leads',
        'machine-learning-mentor-converted-leads' => 'machine-learning-faculty-converted-leads',
        'graphic-designing-mentor-converted-leads' => 'graphic-designing-faculty-converted-leads',
        'digital-marketing-mentor-converted-leads' => 'digital-marketing-faculty-converted-leads',
        'data-science-mentor-converted-leads' => 'data-science-faculty-converted-leads',
        'medical-coding-mentor-converted-leads' => 'medical-coding-faculty-converted-leads',
        'python-mentor-converted-leads' => 'python-faculty-converted-leads',
        'flutter-mentor-converted-leads' => 'flutter-faculty-converted-leads',
        'rpa-mentor-converted-leads' => 'rpa-faculty-converted-leads',
        'mentor-eduthanzeel-converted-leads' => 'faculty-eduthanzeel-converted-leads',
        'mentor-eschool-converted-leads' => 'faculty-eschool-converted-leads',
        'mentor-edumaster-converted-leads' => 'faculty-edumaster-converted-leads',
        'mentor-ugpg-converted-leads' => 'faculty-ugpg-converted-leads',
        'mentor-nios-converted-leads' => 'faculty-nios-converted-leads',
        'mentor-bosse-converted-leads' => 'faculty-bosse-converted-leads',
        'additional-mentor-course-index' => 'additional-faculty-course-index',
        'junior-vlogger-mentor-index' => 'junior-vlogger-faculty-index',
        'machine-learning-mentor-index' => 'machine-learning-faculty-index',
        'graphic-designing-mentor-index' => 'graphic-designing-faculty-index',
        'digital-marketing-mentor-index' => 'digital-marketing-faculty-index',
        'data-science-mentor-index' => 'data-science-faculty-index',
        'gmvss-mentor-index' => 'gmvss-faculty-index',
        'eduthanzeel-mentor-index' => 'eduthanzeel-faculty-index',
        'eschool-mentor-index' => 'eschool-faculty-index',
        'nios-mentor-index' => 'nios-faculty-index',
        'mentor-edumaster-index' => 'faculty-edumaster-index',
        'mentor-ugpg-index' => 'faculty-ugpg-index',
        'mentor-bosse-index' => 'faculty-bosse-index',
        'renderMentorList' => 'renderFacultyList',
        'getMentorIndex' => 'getFacultyIndex',
        'eschoolMentorIndex' => 'eschoolFacultyIndex',
        'eduthanzeelMentorIndex' => 'eduthanzeelFacultyIndex',
        'medicalCodingMentorIndex' => 'medicalCodingFacultyIndex',
        'pythonMentorIndex' => 'pythonFacultyIndex',
        'flutterMentorIndex' => 'flutterFacultyIndex',
        'rpaMentorIndex' => 'rpaFacultyIndex',
    ];

    $content = str_replace(array_keys($pairs), array_values($pairs), $content);

    // Mentor -> Faculty in UI strings (blade titles, labels)
    if (!$isPhp) {
        $content = preg_replace('/\bMentor List\b/', 'Faculty List', $content);
        $content = preg_replace('/\bMentor\b/', 'Faculty', $content);
        $content = preg_replace('/\bmentor\b/', 'faculty', $content);

        $facultyInclude = "@include('admin.converted-leads.partials.faculty-list-nav', ['activeFacultyRoute' => \$activeFacultyRoute ?? null])";
        $content = preg_replace(
            '/<!-- \[ Mentor List \] start -->.*?<!-- \[ Mentor List \] end -->/s',
            $facultyInclude,
            $content
        );
    }

    // is_mentor() -> is_faculty() but not is_mentor_head()
    $content = preg_replace('/RoleHelper::is_mentor\(\)/', 'RoleHelper::is_faculty()', $content);

    if ($isPhp) {
        $content = preg_replace('/for mentoring/', 'for faculty', $content);
    }

    return $content;
}

foreach ($controllerMap as $source => $target) {
    $srcPath = $base . '/app/Http/Controllers/' . $source;
    $dstPath = $base . '/app/Http/Controllers/' . $target;
    if (!file_exists($srcPath)) {
        echo "Skip missing controller: $source\n";
        continue;
    }
    $content = file_get_contents($srcPath);
    file_put_contents($dstPath, transformFacultyContent($content, true));
    echo "Generated controller: $target\n";
}

$viewsDir = $base . '/resources/views/admin/converted-leads';
foreach ($viewMap as $source => $target) {
    $srcPath = $viewsDir . '/' . $source;
    $dstPath = $viewsDir . '/' . $target;
    if (!file_exists($srcPath)) {
        echo "Skip missing view: $source\n";
        continue;
    }
    $content = file_get_contents($srcPath);
    file_put_contents($dstPath, transformFacultyContent($content, false));
    echo "Generated view: $target\n";
}

// Add faculty nav include to all converted-leads index blades that have mentor list end
$facultyInclude = "@include('admin.converted-leads.partials.faculty-list-nav', ['activeFacultyRoute' => \$activeFacultyRoute ?? null])";
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsDir));
foreach ($iterator as $file) {
    if (!$file->isFile() || !str_ends_with($file->getFilename(), '.blade.php')) {
        continue;
    }
    if (str_contains($file->getPathname(), DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR)) {
        continue;
    }
    $path = $file->getPathname();
    $content = file_get_contents($path);
    if (str_contains($content, 'partials.faculty-list-nav')) {
        continue;
    }
    if (!str_contains($content, '<!-- [ Mentor List ] end -->')) {
        continue;
    }
    $content = str_replace(
        '<!-- [ Mentor List ] end -->',
        "<!-- [ Mentor List ] end -->\n\n" . $facultyInclude,
        $content
    );
    file_put_contents($path, $content);
    echo "Added faculty nav to: " . basename($path) . "\n";
}

echo "Done.\n";
