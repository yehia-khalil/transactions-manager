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
        'due_date', 'vat', 'is_vat_inclusive', 'transaction_status_id'
    ];

    public function getStatusMapper()
    {
        return [
            TransactionStatus::$PAID => 'Paid',
            TransactionStatus::$OUTSTANDING => 'OutStanding',
            TransactionStatus::$OVERDUE => 'OverDue'
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            $transaction->transaction_status_id = self::determineTransactionStatus($transaction->due_date);
        });
    }

    protected function gettransactionStatusAttribute()
    {
        return $this->getStatusMapper()[$this->attributes['transaction_status_id']];
    }

    public function scopeForUser($query)
    {
        if (!Auth::user()->hasRole('admin')) {
            $query->where('payer', Auth::id());
        }
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

    private static function determineTransactionStatus($dueDate)
    {
        $today = now();
        if ($today > $dueDate) {
            return TransactionStatus::$OVERDUE;
        }
        // Additional logic for other statuses like PAID if needed
        return TransactionStatus::$OUTSTANDING;
    }
}
