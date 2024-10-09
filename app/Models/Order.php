<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'price',
        'delivery_id',
        'delivery_date',
        'customer_id'
    ];
    protected $guarded = [
        'status',
    ];
    // foramt the date
    public function setDateAttribute($value)
    {
        $this->attributes['delivery_date'] = Carbon::create($value)->format('Y-m-d');
    }
    protected $attributes = [
        'status' => OrderStatus::PINDING->value,
    ];

    public function scopeMyOrder(Builder $query, $id)
    {
        return  $query->where('delivery_id', '=', $id);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }


    public function delivery()
    {
        return $this->belongsTo(User::class, 'delivery_id');
    }
    public function order_details()
    {
        return $this->hasMany(OrderDetails::class);
    }
}
