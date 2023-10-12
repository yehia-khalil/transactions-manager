<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionStatus extends Model
{
    use HasFactory;

    public static $PAID = 1;
    public static $OUTSTANDING = 2;
    public static $OVERDUE = 3;
}
