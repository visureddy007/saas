<?php

/**
 * Auth.php - Model file
 *
 * This file is part of the Auth component.
 *-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Auth\Models;

use App\Yantrana\Base\BaseModel;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\Access\Authorizable;
use App\Yantrana\Components\Vendor\Models\VendorModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Yantrana\Components\Vendor\Models\VendorUserModel;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class AuthModel extends BaseModel implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail;
    use HasFactory, Notifiable;

    /**
     * @var string - The database table used by the model.
     */
    protected $table = 'users';

    /**
     * @var array - The attributes that should be casted to native types.
     */
    protected $casts = [];

    /**
     * @var array - The attributes that are mass assignable.
     */
    protected $fillable = [];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    protected $appends = [
        'full_name',
    ];

    /**
     * Get user role
     */
    public function role()
    {
        return $this->belongsTo(AuthRoleModel::class, 'user_roles__id', '_id');
    }

    /**
     * Get user role
     */
    public function vendor()
    {
        return $this->belongsTo(VendorModel::class, 'vendors__id', '_id');
    }

    /**
     * Get the vendor user details
     *
     * @return HasOne
     */
    public function vendorUserDetails():HasOne
    {
        return $this->hasOne(VendorUserModel::class, 'users__id', '_id')->where('vendors__id', getVendorId());
    }

    /**
     * prepare and get user full name
     */
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => (($attributes['first_name'] ?? '') . ' ' . ($attributes['last_name'] ?? '')),
        );
    }

}
