<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class GiftCard extends Model
{
    use Sortable;

    public function User(){
        return $this->belongsTo('App\User', 'user_id');
    }

    protected $fillable = [
        'user_id', 'r_trans_id', 'd_trans_id', 'amount', 'discount', 'currencyCode', 'fee', 'recipientEmail', 'customIdentifier','productId' ,'productName','countryCode', 'quantity', 'unitPrice', 'totalPrice', 'productCurrencyCode', 'brandId', 'brandName', 'smsFee', 'recipientPhone','product_image_link' ,'transactionCreatedTime','created_at','amount_user_currency','user_currency','cardNumber','pinCode'
    ];
}
