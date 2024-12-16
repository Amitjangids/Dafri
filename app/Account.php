<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Kyslik\ColumnSortable\Sortable;

class Account extends Model
{
    use Sortable;
    
    public $sortable = ['id', 'user_id', 'bank_id', 'bank_code', 'bank_name', 'branch_code', 'account_number', 'routing_number', 'swift_code', 'first_name', 'last_name', 'mobile', 'email', 'address', 'street_number', 'street_name', 'city', 'postal_code', 'country', 'created_at', 'updated_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'user_id', 'bank_id', 'bank_code', 'bank_name', 'branch_code', 'account_number', 'routing_number', 'swift_code', 'first_name', 'last_name', 'mobile', 'email', 'address', 'street_number', 'street_name', 'city', 'postal_code', 'country', 'created_at', 'updated_at'
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
