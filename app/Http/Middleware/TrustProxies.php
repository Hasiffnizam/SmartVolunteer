<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;

class TrustProxies extends Middleware
{
    /**
     * Trust all proxies (required for Railway / reverse proxies)
     */
    protected $proxies = '*';

    /**
     * Let Laravel auto-detect forwarded headers
     * (Laravel 11/12 compatible)
     */
    protected $headers = null;
}

