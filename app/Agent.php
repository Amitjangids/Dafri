<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Kyslik\ColumnSortable\Sortable;

class Agent extends Model
{
    use Sortable;
    
    public $sortable = ['id', 'user_id', 'first_name', 'last_name', 'country', 'commission', 'min_amount', 'address', 'phone', 'email', 'payment_methods', 'description', 'is_approved', 'created_at', 'updated_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
       public function User(){
        return $this->belongsTo('App\User', 'user_id');
    }
    protected $fillable = [
        'id', 'user_id', 'first_name', 'last_name', 'country', 'commission', 'min_amount', 'address', 'phone', 'email', 'payment_methods', 'description', 'profile_image', 'is_approved', 'created_at', 'updated_at'
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
