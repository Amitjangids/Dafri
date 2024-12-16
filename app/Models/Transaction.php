<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Transaction extends Model
{
    use Sortable;
    
    public $sortable = ['id', 'name', 'trans_type', 'payment_mode', 'refrence_id', 'created_at'];
    
    public function User(){
        return $this->belongsTo('App\User', 'user_id');
    }
    
    public function Receiver(){
        return $this->belongsTo('App\User', 'receiver_id');
    }
    
    protected $fillable = [
        'user_id', 'receiver_id', 'amount', 'trans_type', 'payment_mode', 'refrence_id', 'status', 'created_at', 'updated_at','sender_currency' ,'sender_real_value'
    ];
}
