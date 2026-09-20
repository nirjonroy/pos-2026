<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'customer_group_id',
        'name',
        'phone',
        'email',
        'address',
        'credit_limit',
        'opening_due',
        'status',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'opening_due' => 'decimal:2',
        'status' => 'boolean',
    ];

    public function group()
    {
        return $this->belongsTo(CustomerGroup::class, 'customer_group_id');
    }
}
