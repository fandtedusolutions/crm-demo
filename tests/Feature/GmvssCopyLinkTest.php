<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Lead;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GmvssCopyLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_gmvss_copy_link_button_appears_in_leads_listing()
    {
        // Create Grameen Mukt Vidhyalayi Shiksha Sansthan course
        $course = Course::create([
            'id' => 16,
            'title' => 'Grameen Mukt Vidhyalayi Shiksha Sansthan',
            'amount' => 50000,
            'is_active' => true
        ]);

        // Create a lead with Grameen Mukt Vidhyalayi Shiksha Sansthan course
        $lead = Lead::factory()->create([
            'course_id' => 16,
            'title' => 'Test Student',
            'email' => 'test@example.com',
            'phone' => '1234567890'
        ]);

        // Test that the leads listing shows the copy link button for Grameen Mukt Vidhyalayi Shiksha Sansthan leads
        $response = $this->get(route('leads.index'));

        $response->assertStatus(200);
        $response->assertSee('copy-link-btn');
        $response->assertSee('data-url="' . route('public.lead.gmvss.register', $lead->id) . '"');
        $response->assertSee('Copy Grameen Mukt Vidhyalayi Shiksha Sansthan Registration Link');
    }

    public function test_gmvss_copy_link_has_correct_url()
    {
        // Create Grameen Mukt Vidhyalayi Shiksha Sansthan course
        $course = Course::create([
            'id' => 16,
            'title' => 'Grameen Mukt Vidhyalayi Shiksha Sansthan',
            'amount' => 50000,
            'is_active' => true
        ]);

        // Create a lead with Grameen Mukt Vidhyalayi Shiksha Sansthan course
        $lead = Lead::factory()->create([
            'course_id' => 16,
            'title' => 'Test Student',
            'email' => 'test@example.com',
            'phone' => '1234567890'
        ]);

        // Test that the copy link button has the correct URL
        $response = $this->get(route('leads.index'));

        $response->assertStatus(200);
        $response->assertSee('data-url="' . route('public.lead.gmvss.register', $lead->id) . '"');
    }

    public function test_non_gmvss_leads_dont_have_copy_link_button()
    {
        // Create non-Grameen Mukt Vidhyalayi Shiksha Sansthan course
        $course = Course::create([
            'id' => 1,
            'title' => 'National Institute of Open Schooling',
            'amount' => 30000,
            'is_active' => true
        ]);

        // Create a lead with non-Grameen Mukt Vidhyalayi Shiksha Sansthan course
        $lead = Lead::factory()->create([
            'course_id' => 1,
            'title' => 'Test Student',
            'email' => 'test@example.com',
            'phone' => '1234567890'
        ]);

        // Test that non-Grameen Mukt Vidhyalayi Shiksha Sansthan leads don't have the copy link button
        $response = $this->get(route('leads.index'));

        $response->assertStatus(200);
        $response->assertDontSee('copy-link-btn');
    }

    public function test_gmvss_registration_route_exists()
    {
        // Test that the Grameen Mukt Vidhyalayi Shiksha Sansthan registration route exists and is accessible
        $response = $this->get(route('public.lead.gmvss.register'));

        $response->assertStatus(200);
        $response->assertSee('Grameen Mukt Vidhyalayi Shiksha Sansthan Registration');
    }
}
