<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class MerchantPaymentLink extends Model
{
    use Sortable;
    
    protected $fillable = [
        'user_id','slug','created_at','updated_at'];
}
