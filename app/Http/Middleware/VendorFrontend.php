<?php

namespace App\Http\Middleware;

use Closure;

class VendorFrontend
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (vendorPlanDetails(null, null, getPublicVendorId())->hasActivePlan() === false) {
            if ($request->ajax()) {
                return __apiResponse([
                    'message' => __tr('No Active Plan'),
                    'auth_info' => getUserAuthInfo(5),
                ], 11);
            }

            return response()->view('errors.no-active-plan');
        }

        return $next($request);
    }
}
