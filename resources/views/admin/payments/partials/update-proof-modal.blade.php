<div class="modal fade" id="updateProofModal" tabindex="-1" aria-labelledby="updateProofModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateProofModalLabel">Update Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateProofForm"
                  method="POST"
                  enctype="multipart/form-data"
                  data-action-template="{{ route('admin.payments.proofs.update', ['id' => '__PAYMENT_ID__']) }}">
                @csrf
                <input type="hidden" name="proof_id" id="updateProofId" value="">
                <div class="modal-body">
                    <div class="alert alert-warning d-none" id="updateProofRejectedNotice">
                        Saving this rejected payment sends it back to Pending Approval.
                    </div>
                    <p class="text-muted mb-3" id="updateProofCurrent">Update the transaction ID or replace the receipt.</p>
                    <div class="mb-3">
                        <label for="updateProofTransactionId" class="form-label">Transaction ID</label>
                        <input type="text"
                               class="form-control"
                               id="updateProofTransactionId"
                               name="transaction_id"
                               maxlength="255"
                               placeholder="Enter transaction ID">
                    </div>
                    <div class="mb-0">
                        <label for="updateProofFile" class="form-label">Receipt/Proof</label>
                        <input type="file"
                               class="form-control"
                               id="updateProofFile"
                               name="file"
                               accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png">
                        <small class="form-text text-muted">PDF, JPG, or PNG. Maximum size 2 MB. Leave empty to keep the current file.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('click', function (event) {
        const button = event.target.closest('.js-update-payment-proof');
        if (!button) {
            return;
        }

        const form = document.getElementById('updateProofForm');
        const fileName = button.dataset.fileName || '';
        const current = document.getElementById('updateProofCurrent');
        const rejectedNotice = document.getElementById('updateProofRejectedNotice');

        form.action = form.dataset.actionTemplate.replace('__PAYMENT_ID__', button.dataset.paymentId);
        document.getElementById('updateProofId').value = button.dataset.proofId || '';
        document.getElementById('updateProofFile').value = '';
        document.getElementById('updateProofTransactionId').value = button.dataset.transactionId || '';
        current.textContent = fileName
            ? 'Current file: ' + fileName + '. Choose a new file only if you want to replace it.'
            : 'No receipt is uploaded yet. You can add one below.';
        rejectedNotice.classList.toggle('d-none', button.dataset.rejected !== '1');

        bootstrap.Modal.getOrCreateInstance(document.getElementById('updateProofModal')).show();
    });
</script>
@endpush
