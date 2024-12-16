<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Kyslik\ColumnSortable\Sortable;

class DbaDeposit extends Model
{
    use Sortable;
    
    public $sortable = ['id', 'user_id', 'user_name', 'amount', 'crypto_currency', 'blockchain_url', 'status', 'created_at', 'updated_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
      public function User(){
        return $this->belongsTo('App\User', 'user_id');
    }
    protected $fillable = [
        'id', 'user_id', 'user_name', 'trans_id', 'amount', 'crypto_currency', 'blockchain_url','dba_amount', 'status', 'created_at', 'updated_at'
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
