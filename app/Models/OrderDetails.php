<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'count',
        'price',
        'book_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }


    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
