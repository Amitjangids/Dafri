<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Transaction extends Model
{
    
    use Sortable;
    
    public $sortable = ['id', 'amount', 'fees', 'currency', 'trans_type', 'trans_to', 'trans_for', 'refrence_id', 'status', 'created_at', 'updated_at','sender_fees','receiver_fees'];
    
    public function User(){
        return $this->belongsTo('App\User', 'user_id');
    }
    
    public function Receiver(){
        return $this->belongsTo('App\User', 'receiver_id');
    }
    
    protected $fillable = [
        'user_id', 'receiver_id', 'amount', 'fees', 'currency', 'receiver_fees','receiver_currency', 'sender_fees', 'trans_type', 'trans_to', 'trans_for', 'refrence_id', 'billing_description', 'user_close_bal', 'receiver_close_bal', 'real_value', 'status','reference_note', 'created_at', 'updated_at','edited_by','sender_currency','sender_real_value','pay_by_agent','stripe_sender_email','stripe_payment_id','airtime_zar_price','airtime_data'
    ];
}
