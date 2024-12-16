<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class DailyRate extends Model
{
    
    
    protected $fillable = [
        'daily_rate','USD_CHANGEPCT24HOUR','BTC','BTC_CHANGEPCT24HOUR','ETH','ETH_CHANGEPCT24HOUR','BNB','BNB_CHANGEPCT24HOUR','SOL','SOL_CHANGEPCT24HOUR', 'created_at', 'updated_at'
    ];
}
