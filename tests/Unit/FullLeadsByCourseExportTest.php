<?php

namespace Tests\Unit;

use App\Http\Controllers\API\Public\FullLeadsByCourseController;
use App\Models\ConvertedLead;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentProof;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use ReflectionMethod;
use Tests\TestCase;

class FullLeadsByCourseExportTest extends TestCase
{
    public function test_invoice_payload_includes_payments_proofs_and_receipts_and_skips_deleted_rows(): void
    {
        $activePayment = new Payment([
            'invoice_id' => 12,
            'amount_paid' => 4000,
            'previous_balance' => 5500,
            'payment_type' => 'Online',
            'transaction_id' => 'TXN-40',
            'status' => 'Approved',
            'file_upload' => 'payments/proof.pdf',
        ]);
        $activePayment->id = 40;
        $activePayment->payment_date = '2026-03-02';
        $activePayment->approved_date = '2026-03-02 11:00:00';
        $proof = new PaymentProof([
            'transaction_id' => 'TXN-40',
            'file_upload' => 'payments/proof.pdf',
            'sort_order' => 0,
        ]);
        $proof->id = 7;
        $activePayment->setRelation('proofs', new Collection([$proof]));

        $deletedPayment = new Payment([
            'invoice_id' => 12,
            'amount_paid' => 100,
            'payment_type' => 'Cash',
            'status' => 'Approved',
        ]);
        $deletedPayment->id = 41;
        $deletedPayment->deleted_at = Carbon::parse('2026-04-01');
        $deletedPayment->setRelation('proofs', new Collection());

        $invoice = new Invoice([
            'invoice_number' => 'INV2026030001',
            'invoice_type' => 'course',
            'student_id' => 5,
            'total_amount' => 10000,
            'discount_amount' => 500,
            'paid_amount' => 4000,
            'previous_balance' => 5500,
            'status' => 'Partially Paid',
            'invoice_date' => '2026-03-01',
        ]);
        $invoice->id = 12;
        $invoice->setRelation('payments', new Collection([$deletedPayment, $activePayment]));
        $invoice->setRelation('paymentLinks', new Collection());
        $invoice->setRelation('course', null);
        $invoice->setRelation('batch', null);

        $deletedInvoice = new Invoice([
            'invoice_number' => 'INV-DELETED',
            'invoice_type' => 'course',
            'total_amount' => 100,
            'paid_amount' => 0,
            'status' => 'Not Paid',
        ]);
        $deletedInvoice->id = 99;
        $deletedInvoice->deleted_at = Carbon::parse('2026-04-01');
        $deletedInvoice->setRelation('payments', new Collection());
        $deletedInvoice->setRelation('paymentLinks', new Collection());

        $student = new ConvertedLead();
        $student->setRelation('invoices', new Collection([$invoice, $deletedInvoice]));

        $method = new ReflectionMethod(FullLeadsByCourseController::class, 'formatInvoices');
        $invoices = $method->invoke(new FullLeadsByCourseController(), $student);

        $this->assertCount(1, $invoices);
        $this->assertSame('INV2026030001', $invoices[0]['invoice_number']);
        $this->assertNull($invoices[0]['deleted_at']);
        $this->assertCount(1, $invoices[0]['payments']);
        $this->assertSame(40, $invoices[0]['payments'][0]['id']);
        $this->assertTrue($invoices[0]['payments'][0]['receipt']['can_generate_receipt']);
        $this->assertSame('RCPT-40', $invoices[0]['payments'][0]['receipt']['receipt_number']);
        $this->assertSame('TXN-40', $invoices[0]['payments'][0]['proofs'][0]['transaction_id']);
        $this->assertNotNull($invoices[0]['payments'][0]['proofs'][0]['file_url']);
        $this->assertSame(40, $invoices[0]['first_approved_payment_id']);
        $this->assertTrue($invoices[0]['can_generate_tax_invoice']);

        $summary = (new ReflectionMethod(FullLeadsByCourseController::class, 'summarizeInvoices'))
            ->invoke(new FullLeadsByCourseController(), $invoices);

        $this->assertSame(1, $summary['invoice_count']);
        $this->assertSame(4000.0, $summary['total_paid']);
    }
}
