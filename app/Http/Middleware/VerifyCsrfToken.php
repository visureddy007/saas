<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Session\TokenMismatchException;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
        'stripe/*',
        'razorpay/*',
        'subscription/*',
        // 'whatsapp-webhook url set under handle method using route name',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     *
     * @throws \Illuminate\Session\TokenMismatchException
     */
    public function handle($request, Closure $next)
    {
        $this->except[] = route('vendor.whatsapp_webhook', [
            'vendorUid' => '*',
        ]);

        try {
            return parent::handle($request, $next);
        } catch (TokenMismatchException $e) {
            return __apiResponse([
                'message' => __tr('Token Expired, Please reload and try again.'),
                'auth_info' => getUserAuthInfo(5),
                'show_message' => true,
            ], 2);
        }
    }
}
