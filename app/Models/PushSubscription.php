<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;

class PushSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id','endpoint','p256dh','auth'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }     

}
