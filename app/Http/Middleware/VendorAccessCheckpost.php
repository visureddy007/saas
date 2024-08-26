<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class VendorAccessCheckpost
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // check vendor and user account status
        if((getVendorUid() and !session('loggedBySuperAdmin') and ((getUserAuthInfo('vendor_status') != 1) or (getUserAuthInfo('status') != 1)))) {
            Auth::logout();
            $request->session()->invalidate();
            if ($request->ajax()) {
                return __apiResponse([
                    'message' => __tr('Vendor/User account is not in active state'),
                    'auth_info' => getUserAuthInfo(5),
                ], 11);
            }
            return redirect()->route('home');
        }
        // check if user has permissions to access area
        if ((hasVendorAccess() === false) and (hasVendorUserAccess() === false)) {
            if ($request->ajax()) {
                return __apiResponse([
                    'message' => __tr('Restricted Area'),
                    'auth_info' => getUserAuthInfo(5),
                ], 11);
            }
            return redirect()->route('home');
        }
        return $next($request);
    }
}
