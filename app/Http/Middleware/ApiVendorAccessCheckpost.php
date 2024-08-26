<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Yantrana\Components\Auth\Models\AuthModel;
use App\Yantrana\Components\Vendor\Models\VendorModel;

class ApiVendorAccessCheckpost
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // behave like ajax request
        $request->headers->set('x-requested-with', 'XMLHttpRequest');
        $request->headers->set('x-external-api-request', true);
        $accessToken = $request->bearerToken() ?: $request->get('token');
        $vendorUid = $request->vendorUid;
        $vendorAccessToken = getVendorSettings('vendor_api_access_token', null, null, $vendorUid);
        if($accessToken !== $vendorAccessToken) {
            return processExternalApiResponse([
                'result' => 'failed',
                'message' => __tr('Invalid Token'),
            ]);
        }
        $vendor = VendorModel::where([
            '_uid' => $vendorUid
        ])->first();
        if(__isEmpty($vendor)) {
            return processExternalApiResponse([
                'result' => 'failed',
                'message' => __tr('Invalid Vendor'),
            ]);
        }
        //  check vendor status
        if($vendor->status != 1) {
            return processExternalApiResponse([
                'result' => 'failed',
                'message' => __tr('Vendor account is not in active state'),
            ]);
        }
        $vendorAdmin = AuthModel::where([
            'vendors__id' => $vendor->_id
        ])->first();
        if(__isEmpty($vendorAdmin)) {
            return processExternalApiResponse([
                'result' => 'failed',
                'message' => __tr('Invalid Vendor admin'),
            ]);
        }
        // check vendor admin status
        if($vendorAdmin->status != 1) {
            return processExternalApiResponse([
                'result' => 'failed',
                'message' => __tr('Vendor admin account is not in active state'),
            ]);
        }
        Auth::loginUsingId($vendorAdmin->_id);
        return $next($request);
    }
}
