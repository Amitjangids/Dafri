<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Kyslik\ColumnSortable\Sortable;

class Tempuser extends Model
{
    use Sortable;
    
    public $sortable = ['id', 'first_name', 'last_name', 'director_name', 'business_name', 'user_type', 'account_number', 'account_category', 'country_code', 'phone', 'email', 'country', 'currency', 'password', 'referral', 'parent_id', 'is_verify', 'verify_code', 'status', 'slug', 'created_at', 'updated_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'first_name', 'last_name', 'director_name', 'business_name', 'user_type', 'account_number', 'account_category', 'country_code', 'phone', 'email', 'country', 'currency', 'registration_number', 'business_type', 'registration_document', 'password', 'addrs_line1', 'addrs_line2', 'referral', 'parent_id', 'is_verify', 'verify_code', 'status', 'slug','gender', 'created_at', 'updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
      
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
      
    ];
}
