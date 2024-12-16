<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class GiftAirtimeSetting extends Model
{
    use Sortable;

   

    protected $fillable = [
        'name', 'limits'
    ];
}
