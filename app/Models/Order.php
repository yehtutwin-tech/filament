<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    public function orderdetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }


    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

}
