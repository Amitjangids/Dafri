<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Beneficiary extends Model
{
    
    use Sortable;
    
    public $sortable = ['id', 'user_id','receiver_id','receiver_name', 'created_at', 'updated_at'];
    
    public function User(){
        return $this->belongsTo('App\User', 'user_id');
    }
    
    public function Receiver(){
        return $this->belongsTo('App\User', 'receiver_id');
    }
    
    protected $fillable = [
        'user_id', 'receiver_id','receiver_name','created_at', 'updated_at'
    ];
}
