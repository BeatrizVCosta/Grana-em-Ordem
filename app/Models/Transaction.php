<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'amount',
        'description',
        'transaction_date',
        'type',
        'user_id', 
    ];
    protected $casts = [
        'transaction_date' => 'date', 
        'amount' => 'decimal:2', 
    ];
}