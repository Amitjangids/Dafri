<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class CryptoCurrency extends Model
{
    use Sortable;
    
    protected $fillable = [
        'name', 'address', 'type','created_at', 'updated_at'];
}
