<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchasePayment extends Model
{
    protected $fillable = ['purchase_id', 'payment_method', 'amount', 'reference_no', 'paid_at', 'created_by'];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function purchase(): BelongsTo { return $this->belongsTo(Purchase::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
