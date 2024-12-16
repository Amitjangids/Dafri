<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Kyslik\ColumnSortable\Sortable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, Sortable;
    
public $sortable = ['id', 'first_name', 'last_name', 'director_name', 'business_name', 'email', 'country_code', 'phone', 'is_verify', 'created_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name','written_notes', 'certificate_of_incorporation','article','memorandum','tax_certificate','address_proof','identity','person_identity', 'director_name', 'business_name', 'user_type', 'account_number', 'account_category', 'country_code', 'phone', 'email', 'country', 'currency', 'password', 'addrs_line1', 'addrs_line2', 'referral', 'parent_id', 'is_verify', 'slug','verify_code','otp_verify','registration_number', 'business_type','gender'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'profile_image','password', 'referral', 'parent_id', 'remember_token', 'is_kyc_done', 'is_verify',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
