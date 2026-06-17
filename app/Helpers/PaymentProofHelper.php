<?php

namespace App\Helpers;

use App\Models\Payment;
use App\Models\PaymentProof;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class PaymentProofHelper
{
    /**
     * Normalize payment proof rows from request (new array format or legacy single fields).
     *
     * @return array<int, array{transaction_id: ?string, file: ?UploadedFile}>
     */
    public static function normalizeFromRequest(Request $request): array
    {
        $proofs = [];
        $rawProofs = $request->input('payment_proofs');

        if (is_array($rawProofs) && count($rawProofs) > 0) {
            foreach ($rawProofs as $index => $proof) {
                if (!is_array($proof)) {
                    continue;
                }

                $transactionId = trim((string) ($proof['transaction_id'] ?? ''));
                $file = $request->file("payment_proofs.{$index}.file");

                if ($transactionId !== '' || $file) {
                    $proofs[] = [
                        'transaction_id' => $transactionId !== '' ? $transactionId : null,
                        'file' => $file,
                    ];
                }
            }
        }

        if (empty($proofs)) {
            $legacyTxn = trim((string) ($request->input('transaction_id') ?? ''));
            $legacyFile = $request->file('payment_file');

            if ($legacyTxn !== '' || $legacyFile) {
                $proofs[] = [
                    'transaction_id' => $legacyTxn !== '' ? $legacyTxn : null,
                    'file' => $legacyFile,
                ];
            }
        }

        return $proofs;
    }

    /**
     * @param  array<int, array{transaction_id: ?string, file: ?UploadedFile}>  $proofs
     * @return array<int, array{transaction_id: ?string, file_upload: ?string}>
     */
    public static function storeProofFiles(array $proofs): array
    {
        $stored = [];

        foreach ($proofs as $proof) {
            $filePath = null;
            $file = $proof['file'] ?? null;

            if ($file instanceof UploadedFile) {
                $fileName = Str::uuid() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('payments', $fileName, 'public');
            }

            $stored[] = [
                'transaction_id' => $proof['transaction_id'] ?? null,
                'file_upload' => $filePath,
            ];
        }

        return $stored;
    }

    /**
     * @param  array<int, array{transaction_id: ?string, file_upload: ?string}>  $storedProofs
     */
    public static function attachToPayment(int $paymentId, array $storedProofs): void
    {
        foreach ($storedProofs as $index => $proof) {
            PaymentProof::create([
                'payment_id' => $paymentId,
                'transaction_id' => $proof['transaction_id'] ?? null,
                'file_upload' => $proof['file_upload'] ?? null,
                'sort_order' => $index,
            ]);
        }
    }

    /**
     * Collect non-empty transaction IDs from proof rows.
     *
     * @param  array<int, array{transaction_id: ?string, file: ?UploadedFile}>  $proofs
     * @return array<int, string>
     */
    public static function collectTransactionIds(array $proofs): array
    {
        $ids = [];

        foreach ($proofs as $proof) {
            $transactionId = trim((string) ($proof['transaction_id'] ?? ''));
            if ($transactionId !== '') {
                $ids[] = $transactionId;
            }
        }

        return $ids;
    }

    /**
     * Find transaction IDs that already exist in payments or payment_proofs.
     *
     * @param  array<int, string>  $transactionIds
     * @return array<int, string>
     */
    public static function findExistingTransactionIds(array $transactionIds): array
    {
        $existing = [];

        foreach ($transactionIds as $transactionId) {
            $transactionId = trim($transactionId);
            if ($transactionId === '') {
                continue;
            }

            if (self::transactionIdExists($transactionId)) {
                $existing[] = $transactionId;
            }
        }

        return array_values(array_unique($existing));
    }

    /**
     * Find duplicate transaction IDs within the same submission.
     *
     * @param  array<int, string>  $transactionIds
     * @return array<int, string>
     */
    public static function findDuplicateWithinSubmission(array $transactionIds): array
    {
        $normalized = array_map(static fn ($id) => trim((string) $id), $transactionIds);
        $normalized = array_values(array_filter($normalized, static fn ($id) => $id !== ''));

        $counts = array_count_values($normalized);
        $duplicates = [];

        foreach ($counts as $transactionId => $count) {
            if ($count > 1) {
                $duplicates[] = $transactionId;
            }
        }

        return $duplicates;
    }

    public static function transactionIdExists(string $transactionId, ?int $excludePaymentId = null): bool
    {
        $transactionId = trim($transactionId);
        if ($transactionId === '') {
            return false;
        }

        $paymentQuery = Payment::query()
            ->where(function ($query) use ($transactionId) {
                $query->where('transaction_id', $transactionId)
                    ->orWhere('transaction_id', 'like', $transactionId . ' (%');
            });

        if ($excludePaymentId) {
            $paymentQuery->where('id', '!=', $excludePaymentId);
        }

        if ($paymentQuery->exists()) {
            return true;
        }

        $proofQuery = PaymentProof::query()
            ->where(function ($query) use ($transactionId) {
                $query->where('transaction_id', $transactionId)
                    ->orWhere('transaction_id', 'like', $transactionId . ' (%');
            });

        if ($excludePaymentId) {
            $proofQuery->where('payment_id', '!=', $excludePaymentId);
        }

        return $proofQuery->exists();
    }
}
