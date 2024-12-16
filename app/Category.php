<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Kyslik\ColumnSortable\Sortable;

class Category extends Model
{
    use Sortable;
    //
    public $sortable = ['id', 'name','status','created_at','updated_at'];
    
    public static function getCategoryList(){
        return Category::where('status', 1)
        ->orderBy('name', 'ASC')
        ->pluck('name', 'id');
    
    }
}
