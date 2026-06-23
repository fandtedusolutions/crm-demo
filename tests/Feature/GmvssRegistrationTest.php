<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Lead;
use App\Models\Subject;
use App\Models\Batch;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GmvssRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_gmvss_registration_form_loads()
    {
        // Create a test lead
        $lead = Lead::factory()->create([
            'course_id' => 16, // Grameen Mukt Vidhyalayi Shiksha Sansthan course ID
            'title' => 'Test Student',
            'email' => 'test@example.com',
            'phone' => '1234567890'
        ]);

        // Create test course, subjects, and batches
        $course = Course::create([
            'id' => 16,
            'title' => 'Grameen Mukt Vidhyalayi Shiksha Sansthan',
            'is_active' => true
        ]);

        $subject = Subject::create([
            'course_id' => 16,
            'title' => 'Test Subject',
            'is_active' => true
        ]);

        $batch = Batch::create([
            'course_id' => 16,
            'title' => 'Test Batch',
            'is_active' => true
        ]);

        $response = $this->get(route('public.lead.gmvss.register', $lead->id));

        $response->assertStatus(200);
        $response->assertSee('Grameen Mukt Vidhyalayi Shiksha Sansthan Registration');
        $response->assertSee('Test Student');
        $response->assertSee('Test Subject');
        $response->assertSee('Test Batch');
    }

    public function test_gmvss_registration_form_has_passed_year_field()
    {
        $response = $this->get(route('public.lead.gmvss.register'));

        $response->assertStatus(200);
        $response->assertSee('Passed Year');
        $response->assertSee('name="passed_year"');
    }

    public function test_gmvss_registration_form_has_correct_year_options()
    {
        $currentYear = date('Y');
        $response = $this->get(route('public.lead.gmvss.register'));

        $response->assertStatus(200);
        
        // Check that the form contains year options from current year back to 10 years
        for ($year = $currentYear; $year >= ($currentYear - 10); $year--) {
            $response->assertSee('<option value="' . $year . '">' . $year . '</option>');
        }
    }
}
