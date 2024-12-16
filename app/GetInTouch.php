<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class GetInTouch extends Model {

    use Sortable;

    public $sortable = ['name', 'email', 'created_at', 'updated_at', 'id'];
    protected $fillable = [
        'name', 'message', 'subject', 'email', 'created_at', 'updated_at',
    ];

}
