<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_category_id', 'transaction_sub_category_id', 'amount', 'payer',
        'due_date', 'vat', 'is_vat_inclusive'
    ];

    protected function gettransactionStatusAttribute()
    {
        return $this->determineTransactionStatus($this->attributes['due_date']);
    }

    public function scopeForUser($query)
    {
        if (!Auth::user()->hasRole('admin')) {
            $query->where('payer', Auth::id());
        }
    }

    public function payments()
    {
        return $this->hasMany(TransactionPayment::class);
    }

    public function transactionCategory()
    {
        return $this->belongsTo(TransactionCategory::class);
    }

    public function transactionSubCategory()
    {
        return $this->belongsTo(TransactionSubCategory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'payer', 'id');
    }

    private function determineTransactionStatus($dueDate)
    {
        if ($this->payments_sum_amount >= $this->amount) {
            return TransactionStatus::$PAID;
        }
        $today = now();
        if ($today > $dueDate) {
            return TransactionStatus::$OVERDUE;
        }
        // Additional logic for other statuses like PAID if needed
        return TransactionStatus::$OUTSTANDING;
    }
}
