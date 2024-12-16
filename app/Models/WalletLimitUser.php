<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class WalletLimitUser extends Model
{
    use Sortable;
    
    // public $sortable = ['id', 'user_id', 'daily_limit', 'week_limit', 'month_limit', 'edited_by','created_at','updated_at'];
    
    public function User(){
        return $this->belongsTo('App\User', 'user_id');
    }
    
    protected $fillable = [
        'id', 'user_id', 'daily_limit', 'week_limit', 'month_limit', 'edited_by','created_at','updated_at'
    ];
}
