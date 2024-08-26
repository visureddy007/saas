<?php

namespace App\Http\Middleware;

use Closure;

class CentralAccessCheckpost
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // check if user has permissions to access area
        if (hasCentralAccess() === false) {
            if ($request->ajax()) {
                return __apiResponse([
                    'message' => __tr('Restricted Area'),
                    'auth_info' => getUserAuthInfo(5),
                ], 11);
            }

            return redirect()->route('home');
        }

        // check if demo mode is on
        if (
            $request->isMethod('post')
            and isDemo()
            and (getUserID() != 1)
        ) {
            return __apiResponse([
                'message' => __tr('Functionality is disabled in this demo.'),
                'show_message' => true,
            ], 22);
        }

        return $next($request);
    }
}
