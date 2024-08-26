<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\URL;
use Session;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('auth.login');
        } else {
            return __apiResponse([
                'message' => __tr('Please login to your account'),
                'auth_info' => getUserAuthInfo(11),
            ], 11);
        }
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);

        $user = Auth::user();
        // check if user is exists
        if (__isEmpty($user) or $user->status != 1) {
            if ($request->ajax()) {
                return __apiResponse([
                    'message' => __tr('Your account does not seems to be active'),
                    'auth_info' => getUserAuthInfo(11),
                    'redirect_to' => route('auth.login'),
                ], 21);
            }

            // Check if user is logged in then logout that user
            if (Auth::check()) {
                Auth::logout();
            }

            Session::put('intendedUrl', URL::current());

            return redirect()->route('auth.login')
                ->with([
                    'error' => true,
                    'message' => __tr('Your account does not seems to be active'),
                ]);
        }
        // check if demo mode is on
        if (
            $request->isMethod('post')
            and isDemo()
            and (in_array($request->route()->getName(), [
                'auth.password.confirm.process',
                'auth.password.update.process',
                'user.profile.update',
            ]))
            and ((getUserID() != 1) and (hasCentralAccess() or isDemoVendorAccount()))
        ) {
            return __apiResponse([
                'message' => __tr('Saving functionality is disabled in this demo.'),
                'show_message' => true,
            ], 22);
        }

        return $next($request);
    }

    /**
     * Handle an unauthenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function unauthenticated($request, array $guards)
    {
        /*  throw new AuthenticationException(
            'Unauthenticated.', $guards, $this->redirectTo($request)
        ); */

        if ($request->ajax()) {
            return __apiResponse([
                'message' => __tr('Restricted Area'),
                'auth_info' => getUserAuthInfo(5),
                'redirect_to' => route('auth.login'),
                'show_message' => true,
            ], 21);
        }
    }
}
