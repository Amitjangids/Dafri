<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
        'charges',
        'merchat-withdrawal',
        'withdraw-payment',
        'api-transaction-detail',
        'ref',
        'updateStatus',
        'checkAccountLink'
    ];
}
