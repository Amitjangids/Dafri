<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Ngnexchange extends Model
{
    
    use Sortable;
    
    public $sortable = ['id', 'amount', 'fees', 'currency', 'trans_type', 'trans_to', 'trans_for', 'refrence_id', 'status', 'created_at', 'updated_at'];
    
}
