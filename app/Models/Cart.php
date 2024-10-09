<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'customer_id',
        'count',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }


    public function scopeMyCart(Builder $query, $id)
    {
        return  $query->where('customer_id', '=', $id);
    }
    public function scopeCart(Builder $query, $user_id, $book_id)
    {
        return  $query->where('customer_id', '=', $user_id)->where('book_id', '=', $book_id);
    }
}