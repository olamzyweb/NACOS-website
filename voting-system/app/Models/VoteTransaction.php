<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class VoteTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'reference',
        'voter_name',
        'voter_email',
        'votes',
        'amount',
        'payment_method',
        'payment_status',
        'verification_status',
        'receipt_image',
        'payment_provider_reference',
        'processed_at',
    ];

    protected $casts = [
        'votes' => 'integer',
        'amount' => 'integer',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the candidate this transaction is for.
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    /**
     * Get formatted amount in Naira.
     */
    public function getFormattedAmountAttribute(): string
    {
        return '₦' . number_format($this->amount / 100, 2);
    }

    /**
     * Scope for successful payments.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('payment_status', 'success');
    }

    /**
     * Scope for pending payments.
     */
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    /**
     * Scope for bank transfers.
     */
    public function scopeBankTransfers($query)
    {
        return $query->where('payment_method', 'bank_transfer');
    }

    /**
     * Scope for paystack payments.
     */
    public function scopePaystack($query)
    {
        return $query->where('payment_method', 'paystack');
    }

    /**
     * Scope for korapay payments.
     */
    public function scopeKorapay($query)
    {
        return $query->where('payment_method', 'korapay');
    }

    /**
     * Scope for unverified transactions.
     */
    public function scopeUnverified($query)
    {
        return $query->where('verification_status', 'unverified');
    }

    /**
     * Check if this transaction has already been processed.
     */
    public function isProcessed(): bool
    {
        return $this->processed_at !== null;
    }

    /**
     * Mark transaction as processed (idempotency check).
     */
    public function markAsProcessed(): void
    {
        $this->update([
            'processed_at' => now(),
            'payment_status' => 'success',
            'verification_status' => 'verified',
        ]);
    }

    /**
     * Get receipt image URL.
     */
    public function getReceiptUrlAttribute(): ?string
    {
        if ($this->receipt_image) {
            return asset('storage/' . $this->receipt_image);
        }
        return null;
    }

    /**
     * Get a friendly payment method label.
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'korapay' => 'Korapay',
            'bank_transfer' => 'Bank Transfer',
            default => ucfirst(str_replace('_', ' ', $this->payment_method)),
        };
    }
}
