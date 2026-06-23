<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Lead;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AllCoursesCopyLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_courses_have_copy_link_buttons()
    {
        // Define all courses with their IDs and names
        $courses = [
            1 => 'National Institute of Open Schooling',
            2 => 'Board of Open Schooling and Skill Education', 
            3 => 'Certificate Course in Medical Coding',
            4 => 'Diploma in Hospital Administration',
            5 => 'E-School',
            6 => 'Eduthanzeel',
            7 => 'TTC',
            8 => 'Hotel Management',
            9 => 'UG/PG',
            10 => 'Python',
            11 => 'AI Integrated Digital Marketing',
            12 => 'AI Automation',
            13 => 'Web Development & Designing',
            14 => 'Vibe Coding',
            15 => 'Diploma in Graphic Designing',
            16 => 'Grameen Mukt Vidhyalayi Shiksha Sansthan'
        ];

        foreach ($courses as $courseId => $courseName) {
            // Create course
            $course = Course::create([
                'id' => $courseId,
                'title' => $courseName,
                'amount' => 50000,
                'is_active' => true
            ]);

            // Create a lead with this course
            $lead = Lead::factory()->create([
                'course_id' => $courseId,
                'title' => 'Test Student',
                'email' => 'test@example.com',
                'phone' => '1234567890'
            ]);

            // Test that the leads listing shows the copy link button
            $response = $this->get(route('leads.index'));

            $response->assertStatus(200);
            $response->assertSee('copy-link-btn');
            $response->assertSee('Copy ' . $courseName . ' Registration Link');
        }
    }

    public function test_copy_link_buttons_have_correct_urls()
    {
        // Test a few key courses to ensure URLs are correct
        $testCourses = [
            1 => 'public.lead.nios.register',
            2 => 'public.lead.bosse.register',
            16 => 'public.lead.gmvss.register'
        ];

        foreach ($testCourses as $courseId => $routeName) {
            // Create course
            $course = Course::create([
                'id' => $courseId,
                'title' => 'Test Course',
                'amount' => 50000,
                'is_active' => true
            ]);

            // Create a lead with this course
            $lead = Lead::factory()->create([
                'course_id' => $courseId,
                'title' => 'Test Student',
                'email' => 'test@example.com',
                'phone' => '1234567890'
            ]);

            // Test that the copy link button has the correct URL
            $response = $this->get(route('leads.index'));

            $response->assertStatus(200);
            $response->assertSee('data-url="' . route($routeName, $lead->id) . '"');
        }
    }

    public function test_copy_link_functionality_works_for_all_courses()
    {
        // Test that all course registration routes exist and are accessible
        $routes = [
            'public.lead.nios.register',
            'public.lead.bosse.register',
            'public.lead.medical-coding.register',
            'public.lead.hospital-admin.register',
            'public.lead.eschool.register',
            'public.lead.eduthanzeel.register',
            'public.lead.ttc.register',
            'public.lead.hotel-mgmt.register',
            'public.lead.ugpg.register',
            'public.lead.python.register',
            'public.lead.digital-marketing.register',
            'public.lead.diploma-in-data-science.register',
            'public.lead.web-dev.register',
            'public.lead.vibe-coding.register',
            'public.lead.graphic-designing.register',
            'public.lead.gmvss.register',
            'public.lead.rpa.register'
        ];

        foreach ($routes as $route) {
            $response = $this->get(route($route));
            $response->assertStatus(200);
        }
    }

    public function test_mobile_view_also_has_copy_link_buttons()
    {
        // Create a test course and lead
        $course = Course::create([
            'id' => 1,
            'title' => 'National Institute of Open Schooling',
            'amount' => 50000,
            'is_active' => true
        ]);

        $lead = Lead::factory()->create([
            'course_id' => 1,
            'title' => 'Test Student',
            'email' => 'test@example.com',
            'phone' => '1234567890'
        ]);

        // Test that mobile view also shows copy link buttons
        $response = $this->get(route('leads.index'));

        $response->assertStatus(200);
        // Check for both desktop and mobile copy link buttons
        $response->assertSee('copy-link-btn');
        $response->assertSee('Copy National Institute of Open Schooling Registration Link');
    }
}
