<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionStatus extends Model
{
    use HasFactory;

    public static $PAID = 'Paid';

    public static $OUTSTANDING = 'OutStanding';

    public static $OVERDUE = 'Overdue';
}
