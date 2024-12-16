<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Contact extends Model {

    use Sortable;

    public $sortable = ['id', 'user_id', 'first_name', 'last_name', 'email', 'created_at', 'updated_at'];
    protected $fillable = [
        'user_id', 'support_txt', 'first_name', 'last_name', 'email', 'created_at', 'updated_at',
    ];

}
