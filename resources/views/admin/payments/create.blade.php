@extends('layouts.mantis')

@section('title', 'Add Payment - ' . $invoice->invoice_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Add Payment for Invoice {{ $invoice->invoice_number }}</h4>
                        <a href="{{ route('admin.payments.index', $invoice->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Payments
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Invoice Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Invoice Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Invoice Number:</strong></td>
                                    <td>{{ $invoice->invoice_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Amount:</strong></td>
                                    <td>₹{{ number_format(round($invoice->net_amount)) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Paid Amount:</strong></td>
                                    <td>₹{{ number_format(round($invoice->paid_amount)) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Pending Amount:</strong></td>
                                    <td>₹{{ number_format(round($invoice->pending_amount)) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Student Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $invoice->student->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $invoice->student->code }} {{ $invoice->student->phone }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Type:</strong></td>
                                    <td>
                                        @if($invoice->invoice_type === 'course')
                                            Course: {{ $invoice->course->title ?? 'N/A' }}
                                        @elseif($invoice->invoice_type === 'e-service')
                                            E-Service: {{ $invoice->service_name ?? 'N/A' }}
                                        @elseif($invoice->invoice_type === 'batch_change')
                                            Batch Change: {{ $invoice->batch->title ?? 'N/A' }} ({{ $invoice->batch->course->title ?? 'N/A' }})
                                        @elseif($invoice->invoice_type === 'batch_postpond')
                                            Batch Postponed: {{ $invoice->batch->title ?? 'N/A' }} ({{ $invoice->batch->course->title ?? 'N/A' }})
                                        @elseif($invoice->invoice_type === 'fine')
                                            Fine: {{ $invoice->service_name ?? 'N/A' }}
                                        @elseif($invoice->invoice_type === 'supply')
                                            Supply
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <form action="{{ route('admin.payments.store', $invoice->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @php
                            $isCourse23 = (int) ($invoice->course_id ?? 0) === 23;
                            $hasNeedMobile = $isCourse23 && $invoice->hasNeedMobileAddon();
                            $feeBreakdown = [
                                'PG' => (float) ($invoice->fee_pg_amount ?? 0),
                                'UG' => (float) ($invoice->fee_ug_amount ?? 0),
                                'PLUS_TWO' => (float) ($invoice->fee_plustwo_amount ?? 0),
                                'SSLC' => (float) ($invoice->fee_sslc_amount ?? 0),
                                'MOBILE' => $hasNeedMobile ? (float) $invoice->mobileNetAmount() : 0.0,
                            ];
                            $feeHeadLabels = [
                                'PG' => 'PG',
                                'UG' => 'UG',
                                'PLUS_TWO' => 'Plus Two',
                                'SSLC' => 'SSLC',
                                'MOBILE' => 'Needed Mobile',
                            ];
                            $approvedPayments = $invoice->payments ?? collect();
                            $paidByHead = [
                                'PG' => (float) $approvedPayments->where('fee_head', 'PG')->sum('amount_paid'),
                                'UG' => (float) $approvedPayments->where('fee_head', 'UG')->sum('amount_paid'),
                                'PLUS_TWO' => (float) $approvedPayments->where('fee_head', 'PLUS_TWO')->sum('amount_paid'),
                                'SSLC' => (float) $approvedPayments->where('fee_head', 'SSLC')->sum('amount_paid'),
                                'MOBILE' => (float) $approvedPayments->where('fee_head', 'MOBILE')->sum('amount_paid'),
                            ];
                            $remainingByHead = [
                                'PG' => max($feeBreakdown['PG'] - $paidByHead['PG'], 0),
                                'UG' => max($feeBreakdown['UG'] - $paidByHead['UG'], 0),
                                'PLUS_TWO' => max($feeBreakdown['PLUS_TWO'] - $paidByHead['PLUS_TWO'], 0),
                                'SSLC' => max($feeBreakdown['SSLC'] - $paidByHead['SSLC'], 0),
                                'MOBILE' => $hasNeedMobile ? max($feeBreakdown['MOBILE'] - $paidByHead['MOBILE'], 0) : 0.0,
                            ];
                            $edumasterHeads = ['PG', 'UG', 'PLUS_TWO', 'SSLC'];
                            if ($hasNeedMobile) {
                                $edumasterHeads[] = 'MOBILE';
                            }
                        @endphp
                        <div class="row g-3">
                            @if($isCourse23)
                                <div class="col-12">
                                    <div class="alert alert-info mb-0">
                                        Enter the paid amount for each category and upload the corresponding payment proof.
                                    </div>
                                </div>

                                <div class="col-12">
                                    <h6 class="mb-2">Fee Breakdown (EduMaster)</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Fee Head</th>
                                                    <th class="text-end">Total</th>
                                                    <th class="text-end">Paid</th>
                                                    <th class="text-end">Remaining</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($edumasterHeads as $head)
                                                    <tr>
                                                        <td>{{ $feeHeadLabels[$head] }}</td>
                                                        <td class="text-end">₹{{ number_format($feeBreakdown[$head], 2) }}</td>
                                                        <td class="text-end">₹{{ number_format($paidByHead[$head], 2) }}</td>
                                                        <td class="text-end">₹{{ number_format($remainingByHead[$head], 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="payment_pg_amount" class="form-label">PG Paid Amount</label>
                                        <input type="number" class="form-control @error('payment_pg_amount') is-invalid @enderror"
                                               name="payment_pg_amount" id="payment_pg_amount" step="0.01" min="0"
                                               max="{{ $remainingByHead['PG'] }}" value="{{ old('payment_pg_amount') }}">
                                        <div class="form-text">Max: ₹{{ number_format($remainingByHead['PG'], 2) }}</div>
                                        @error('payment_pg_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="payment_pg_file" class="form-label">PG Payment Proof</label>
                                        <input type="file" class="form-control @error('payment_pg_file') is-invalid @enderror"
                                               name="payment_pg_file" id="payment_pg_file" accept=".pdf,.jpg,.jpeg,.png">
                                        <div class="form-text">Accepted formats: PDF, JPG, JPEG, PNG (Max: 2MB)</div>
                                        @error('payment_pg_file')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="payment_ug_amount" class="form-label">UG Paid Amount</label>
                                        <input type="number" class="form-control @error('payment_ug_amount') is-invalid @enderror"
                                               name="payment_ug_amount" id="payment_ug_amount" step="0.01" min="0"
                                               max="{{ $remainingByHead['UG'] }}" value="{{ old('payment_ug_amount') }}">
                                        <div class="form-text">Max: ₹{{ number_format($remainingByHead['UG'], 2) }}</div>
                                        @error('payment_ug_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="payment_ug_file" class="form-label">UG Payment Proof</label>
                                        <input type="file" class="form-control @error('payment_ug_file') is-invalid @enderror"
                                               name="payment_ug_file" id="payment_ug_file" accept=".pdf,.jpg,.jpeg,.png">
                                        <div class="form-text">Accepted formats: PDF, JPG, JPEG, PNG (Max: 2MB)</div>
                                        @error('payment_ug_file')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="payment_plustwo_amount" class="form-label">Plus Two Paid Amount</label>
                                        <input type="number" class="form-control @error('payment_plustwo_amount') is-invalid @enderror"
                                               name="payment_plustwo_amount" id="payment_plustwo_amount" step="0.01" min="0"
                                               max="{{ $remainingByHead['PLUS_TWO'] }}" value="{{ old('payment_plustwo_amount') }}">
                                        <div class="form-text">Max: ₹{{ number_format($remainingByHead['PLUS_TWO'], 2) }}</div>
                                        @error('payment_plustwo_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="payment_plustwo_file" class="form-label">Plus Two Payment Proof</label>
                                        <input type="file" class="form-control @error('payment_plustwo_file') is-invalid @enderror"
                                               name="payment_plustwo_file" id="payment_plustwo_file" accept=".pdf,.jpg,.jpeg,.png">
                                        <div class="form-text">Accepted formats: PDF, JPG, JPEG, PNG (Max: 2MB)</div>
                                        @error('payment_plustwo_file')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="payment_sslc_amount" class="form-label">SSLC Paid Amount</label>
                                        <input type="number" class="form-control @error('payment_sslc_amount') is-invalid @enderror"
                                               name="payment_sslc_amount" id="payment_sslc_amount" step="0.01" min="0"
                                               max="{{ $remainingByHead['SSLC'] }}" value="{{ old('payment_sslc_amount') }}">
                                        <div class="form-text">Max: ₹{{ number_format($remainingByHead['SSLC'], 2) }}</div>
                                        @error('payment_sslc_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="payment_sslc_file" class="form-label">SSLC Payment Proof</label>
                                        <input type="file" class="form-control @error('payment_sslc_file') is-invalid @enderror"
                                               name="payment_sslc_file" id="payment_sslc_file" accept=".pdf,.jpg,.jpeg,.png">
                                        <div class="form-text">Accepted formats: PDF, JPG, JPEG, PNG (Max: 2MB)</div>
                                        @error('payment_sslc_file')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                @if($hasNeedMobile)
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="payment_mobile_amount" class="form-label">Needed Mobile — Paid Amount</label>
                                        <input type="number" class="form-control @error('payment_mobile_amount') is-invalid @enderror"
                                               name="payment_mobile_amount" id="payment_mobile_amount" step="0.01" min="0"
                                               max="{{ $remainingByHead['MOBILE'] }}" value="{{ old('payment_mobile_amount') }}">
                                        <div class="form-text">Listed add-on ₹{{ number_format(\App\Models\Invoice::NEED_MOBILE_ADDON_GROSS, 2) }} · Net on invoice ₹{{ number_format($feeBreakdown['MOBILE'], 2) }} · Max payable now: ₹{{ number_format($remainingByHead['MOBILE'], 2) }}</div>
                                        @error('payment_mobile_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="payment_mobile_file" class="form-label">Needed Mobile — Payment Proof</label>
                                        <input type="file" class="form-control @error('payment_mobile_file') is-invalid @enderror"
                                               name="payment_mobile_file" id="payment_mobile_file" accept=".pdf,.jpg,.jpeg,.png">
                                        <div class="form-text">Accepted formats: PDF, JPG, JPEG, PNG (Max: 2MB)</div>
                                        @error('payment_mobile_file')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                @endif
                            @else
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="amount_paid" class="form-label">Payment Amount <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('amount_paid') is-invalid @enderror" 
                                               name="amount_paid" id="amount_paid" step="0.01" min="0.01" 
                                               max="{{ $invoice->pending_amount }}" value="{{ old('amount_paid') }}" required>
                                        <input type="hidden" id="pending_amount_value" value="{{ $invoice->pending_amount }}">
                                        <div class="form-text">Maximum amount: ₹{{ number_format(round($invoice->pending_amount)) }}</div>
                                        @error('amount_paid')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @endif

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_type" class="form-label">Payment Type <span class="text-danger">*</span></label>
                                    <select class="form-control @error('payment_type') is-invalid @enderror" name="payment_type" id="payment_type" required>
                                        <option value="">Select Payment Type</option>
                                        <option value="Cash" {{ old('payment_type') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="Online" {{ old('payment_type') == 'Online' ? 'selected' : '' }}>Online</option>
                                        <option value="Bank" {{ old('payment_type') == 'Bank' ? 'selected' : '' }}>Bank</option>
                                        <option value="Cheque" {{ old('payment_type') == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                        <option value="Card" {{ old('payment_type') == 'Card' ? 'selected' : '' }}>Card</option>
                                        <option value="Other" {{ old('payment_type') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('payment_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="transaction_id" class="form-label">Transaction ID</label>
                                    <input type="text" class="form-control @error('transaction_id') is-invalid @enderror" 
                                           name="transaction_id" id="transaction_id" 
                                           value="{{ old('transaction_id') }}" placeholder="Enter transaction ID">
                                    @error('transaction_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_date" class="form-label">Payment Date</label>
                                    <input type="date" class="form-control @error('payment_date') is-invalid @enderror"
                                           name="payment_date" id="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}">
                                    @error('payment_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            @if(!$isCourse23)
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="file_upload" class="form-label">Upload Receipt/Proof</label>
                                    <input type="file" class="form-control @error('file_upload') is-invalid @enderror" 
                                           name="file_upload" id="file_upload" accept=".pdf,.jpg,.jpeg,.png">
                                    <div class="form-text">Accepted formats: PDF, JPG, JPEG, PNG (Max: 2MB)</div>
                                    @error('file_upload')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            @endif

                            <div class="col-12">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Add Payment
                                </button>
                                <a href="{{ route('admin.payments.index', $invoice->id) }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('amount_paid');
    if (amountInput) {
        const maxAmount = parseFloat(document.getElementById('pending_amount_value')?.value || '0');
        amountInput.addEventListener('input', function() {
            if (parseFloat(this.value) > maxAmount) {
                this.value = maxAmount;
                alert('Payment amount cannot exceed the pending amount of ₹' + maxAmount.toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            }
        });
    }
});
</script>
@endsection
