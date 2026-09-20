<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'company_name',
        'phone',
        'email',
        'address',
        'opening_due',
        'status',
    ];

    protected $casts = [
        'opening_due' => 'decimal:2',
        'status' => 'boolean',
    ];
}
