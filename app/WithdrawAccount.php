<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Kyslik\ColumnSortable\Sortable;

class WithdrawAccount extends Model
{
    use Sortable;
    
    public $sortable = ['id', 'user_id', 'user_name', 'type_transfer','account_number', 'account_name', 'bank_name', 'branch_code', 'account_currency', 'account_type', 'routing_number','bnkAdd','reasonPay','iBan','sorCode','bic','wisaEmail','cotb','swc', 'status', 'created_at', 'updated_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'user_id', 'user_name', 'account_number', 'type_transfer','account_name', 'bank_name', 'branch_code', 'account_currency', 'account_type', 'routing_number','bnkAdd','reasonPay','iBan','sorCode','bic','wisaEmail','cotb','swc', 'status','currncy', 'created_at', 'updated_at','withdraw_type'
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
