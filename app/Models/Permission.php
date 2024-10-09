<?php

namespace App\Models;

use App\Enums\UserPermission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use HasFactory, SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description'
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    public function scopeNotAdminPermission(Builder $query)
    {
        return $query->where('name', '!=', UserPermission::ADMIN_PERMISSIONS->value);
    }

    /**
     *  get a specific permission
     * @param  Builder $query  
     * @param  string $name  
     * @return Builder query  
     */
    public function scopeUserPermission(Builder $query, $name)
    {
        return $query->where('name', '=', $name);
    }

    /**
     *   search a permission by name
     * @param  Builder $query  
     * @param  string $name  
     * @return Builder query  
     */
    public function scopeByName(Builder $query, $name)
    {
        if ($name != null)
            return $query->where('name', 'like', "%$name%");
        else
            return $query;
    }
}
