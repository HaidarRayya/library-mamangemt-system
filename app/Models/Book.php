<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'author',
        'published_at',
        'image',
        'category_id',
        'count',
        'price'
    ];
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */

    protected $casts = [
        'price' => 'double',
        'count' => 'int',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
    // foramt the date
    public function setDateAttribute($value)
    {
        $this->attributes['published_at'] = Carbon::create($value)->format('Y-m-d');
    }
    /**
     * search books by  is_active
     * @param  Builder $query  
     * @param  bool $is_active  
     * @return Builder query  
     */
    public function scopeByTitle(Builder $query, $title)
    {
        if ($title != null)
            return $query->where('title', 'like', "%$title%");
        else
            return $query;
    }
    /**
     * search books by  is_active
     * @param  Builder $query  
     * @param  bool $is_active  
     * @return Builder query  
     */
    public function scopeByAuthor(Builder $query, $author)
    {
        if ($author != null)
            return $query->where('author', 'like', "%$author%");
        else
            return $query;
    }
    /**
     * search books by  published_at
     * @param  Builder $query  
     * @param  bool $published_at  
     * @return Builder query  
     */
    public function scopeByPublishedAt(Builder $query, $published_at)
    {
        if ($published_at != null)
            return $query->where('published_at', 'like', "%$published_at%");
        else
            return $query;
    }

    /**
     * search books by  is_active
     * @param  Builder $query  
     * @param  bool $is_active  
     * @return Builder query  
     */
    public function scopeByIsActive(Builder $query, $is_active)
    {
        if ($is_active != null)
            return $query->where('count', '>', 0);
        else
            return $query;
    }
}
