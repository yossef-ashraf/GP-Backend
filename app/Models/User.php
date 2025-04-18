<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{

    protected $table = 'users';

    protected $fillable = [
        'role_id',
        'name',
        'email',
        'password',
        'phone',
        'gender',
        'date_of_birth'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
    public function area()
    {
        return $this->hasMany(Area::class);
    }
    public function getFullNameAttribute()
    {
        return $this->name;
    }

}
