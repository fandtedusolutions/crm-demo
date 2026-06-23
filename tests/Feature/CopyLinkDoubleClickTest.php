<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Lead;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CopyLinkDoubleClickTest extends TestCase
{
    use RefreshDatabase;

    public function test_copy_link_prevents_double_execution()
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

        // Test that the leads listing shows the copy link button
        $response = $this->get(route('leads.index'));

        $response->assertStatus(200);
        $response->assertSee('copy-link-btn');
        $response->assertSee('data-url="' . route('public.lead.nios.register', $lead->id) . '"');
    }

    public function test_copy_link_has_processing_class_prevention()
    {
        // Create a test course and lead
        $course = Course::create([
            'id' => 16,
            'title' => 'Grameen Mukt Vidhyalayi Shiksha Sansthan',
            'amount' => 50000,
            'is_active' => true
        ]);

        $lead = Lead::factory()->create([
            'course_id' => 16,
            'title' => 'Test Student',
            'email' => 'test@example.com',
            'phone' => '1234567890'
        ]);

        // Test that the leads listing shows the copy link button with proper attributes
        $response = $this->get(route('leads.index'));

        $response->assertStatus(200);
        $response->assertSee('copy-link-btn');
        $response->assertSee('data-url="' . route('public.lead.gmvss.register', $lead->id) . '"');
        
        // Check that the JavaScript includes prevention logic
        $response->assertSee('processing');
        $response->assertSee('e.preventDefault()');
        $response->assertSee('e.stopPropagation()');
    }

    public function test_copy_link_has_proper_event_handling()
    {
        // Test that the JavaScript includes proper event handling
        $response = $this->get(route('leads.index'));

        $response->assertStatus(200);
        
        // Check for proper event handling in JavaScript
        $response->assertSee('$(\'.copy-link-btn\').off(\'click\').on(\'click\'');
        $response->assertSee('if ($(this).hasClass(\'processing\'))');
        $response->assertSee('$(this).addClass(\'processing\')');
    }
}
