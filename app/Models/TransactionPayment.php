<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionPayment extends Model
{
    use HasFactory;

    protected $fillable = ['amount', 'transaction_id'];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
