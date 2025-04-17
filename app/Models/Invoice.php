<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table="invoices";
    protected $fillable=[
        'id','user_id','category','quantity','price','payment_method','screenshot'
    ];
}
