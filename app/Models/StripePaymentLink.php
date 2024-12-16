<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class StripePaymentLink extends Model
{
    use Sortable;
    
    protected $fillable = [
        'user_id', 'stripe_account', 'amount','slug', 'payment_link', 'created_at'];
}
