<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class Order extends Model
// {
//     use HasFactory;

//     protected $fillable = [
//         'order_id',
//         'client_id',
//         'product',
//         'amount',
//         'status',
//         'date'
//     ];
// }

// app/Models/Order.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    // protected $fillable = [
    //     'order_id', 'user_id', 'customer_name', 'customer_email', 'customer_phone',
    //     'shipping_address', 'city', 'country', 'zip_code',
    //     'payment_method', 'payment_status', 'transaction_id',
    //     'subtotal', 'shipping', 'discount', 'tax', 'total',
    //     'coupon_code', 'coupon_discount', 'status', 'notes', 'paid_at'
    // ];
  protected $fillable = [
        'order_id',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'city',
        'country',
        'zip_code',
        'payment_method',
        'payment_status',
        'transaction_id',
        'subtotal',
        'shipping',
        'discount',
        'tax',
        'total',
        'coupon_code',
        'coupon_discount',
        'status',           // <-- MUST BE HERE
        'notes',
        'paid_at',
    ];
    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}