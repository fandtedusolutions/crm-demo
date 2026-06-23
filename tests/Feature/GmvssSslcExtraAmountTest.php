<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Lead;
use App\Models\LeadDetail;
use App\Models\Course;
use App\Models\ConvertedLead;
use App\Models\Invoice;
use App\Http\Controllers\InvoiceController;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GmvssSslcExtraAmountTest extends TestCase
{
    use RefreshDatabase;

    public function test_gmvss_sslc_extra_amount_in_convert_modal()
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

        // Create student details with SSLC class
        $studentDetail = LeadDetail::create([
            'lead_id' => $lead->id,
            'course_id' => 16,
            'student_name' => 'Test Student',
            'class' => 'sslc',
            'status' => 'pending'
        ]);

        // Test the convert modal shows extra amount
        $response = $this->get(route('leads.convert', $lead->id));

        $response->assertStatus(200);
        $response->assertSee('Grameen Mukt Vidhyalayi Shiksha Sansthan SSLC Extra: +₹10,000.00');
        $response->assertSee('Total: ₹60,000.00');
    }

    public function test_gmvss_sslc_extra_amount_in_invoice_generation()
    {
        // Create Grameen Mukt Vidhyalayi Shiksha Sansthan course
        $course = Course::create([
            'id' => 16,
            'title' => 'Grameen Mukt Vidhyalayi Shiksha Sansthan',
            'amount' => 50000,
            'is_active' => true
        ]);

        // Create a converted lead with Grameen Mukt Vidhyalayi Shiksha Sansthan course
        $convertedLead = ConvertedLead::create([
            'lead_id' => 1,
            'name' => 'Test Student',
            'course_id' => 16,
            'candidate_status_id' => 1
        ]);

        // Create student details with SSLC class
        $studentDetail = LeadDetail::create([
            'lead_id' => 1,
            'course_id' => 16,
            'student_name' => 'Test Student',
            'class' => 'sslc',
            'status' => 'pending'
        ]);

        // Test invoice auto-generation includes extra amount
        $invoiceController = new InvoiceController();
        $invoice = $invoiceController->autoGenerate($convertedLead->id, 16);

        $this->assertNotNull($invoice);
        $this->assertEquals(60000, $invoice->total_amount); // 50000 + 10000
    }

    public function test_gmvss_plustwo_no_extra_amount()
    {
        // Create Grameen Mukt Vidhyalayi Shiksha Sansthan course
        $course = Course::create([
            'id' => 16,
            'title' => 'Grameen Mukt Vidhyalayi Shiksha Sansthan',
            'amount' => 50000,
            'is_active' => true
        ]);

        // Create a converted lead with Grameen Mukt Vidhyalayi Shiksha Sansthan course
        $convertedLead = ConvertedLead::create([
            'lead_id' => 1,
            'name' => 'Test Student',
            'course_id' => 16,
            'candidate_status_id' => 1
        ]);

        // Create student details with Plus Two class (no extra amount)
        $studentDetail = LeadDetail::create([
            'lead_id' => 1,
            'course_id' => 16,
            'student_name' => 'Test Student',
            'class' => 'plustwo',
            'status' => 'pending'
        ]);

        // Test invoice auto-generation does not include extra amount
        $invoiceController = new InvoiceController();
        $invoice = $invoiceController->autoGenerate($convertedLead->id, 16);

        $this->assertNotNull($invoice);
        $this->assertEquals(50000, $invoice->total_amount); // Only base amount
    }

    public function test_non_gmvss_course_no_extra_amount()
    {
        // Create non-Grameen Mukt Vidhyalayi Shiksha Sansthan course
        $course = Course::create([
            'id' => 1,
            'title' => 'National Institute of Open Schooling',
            'amount' => 30000,
            'is_active' => true
        ]);

        // Create a converted lead with non-Grameen Mukt Vidhyalayi Shiksha Sansthan course
        $convertedLead = ConvertedLead::create([
            'lead_id' => 1,
            'name' => 'Test Student',
            'course_id' => 1,
            'candidate_status_id' => 1
        ]);

        // Create student details with SSLC class
        $studentDetail = LeadDetail::create([
            'lead_id' => 1,
            'course_id' => 1,
            'student_name' => 'Test Student',
            'class' => 'sslc',
            'status' => 'pending'
        ]);

        // Test invoice auto-generation does not include extra amount
        $invoiceController = new InvoiceController();
        $invoice = $invoiceController->autoGenerate($convertedLead->id, 1);

        $this->assertNotNull($invoice);
        $this->assertEquals(30000, $invoice->total_amount); // Only base amount
    }
}
