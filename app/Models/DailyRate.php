<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class DailyRate extends Model
{
    
    
    protected $fillable = [
        'daily_rate', 'created_at', 'updated_at'
    ];
}
