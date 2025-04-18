<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $table="clients";
    protected $fillable = [
        'id',
        'user_id',
        'name',
        'type',
        'contact',
        'address',
        'pan',
        'gst_certificate'
    ];

    public function user()
    {
        return $this->belongsTo(User::class); // or Seller::class if you're using a Seller model
    }
}
