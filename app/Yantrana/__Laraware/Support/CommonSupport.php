<?php

namespace App\Yantrana\__Laraware\Support;

use Illuminate\Support\Str;

/**
 * CommonSupport - 1.1.1 - 22 JAN 2021
 *
 *
 *--------------------------------------------------------------------------- */

/**
 * This CommonSupport class.
 *---------------------------------------------------------------- */
class CommonSupport
{
    /*
        Store public routes
    */
    protected $publicRoutes = [];

    public function stateViaRoute($stateRouteInfo)
    {
        $stateRoute = json_decode(base64_decode($stateRouteInfo), true);

        $state_via_route = json_encode([
            'stateName' => $stateRoute['stateName'],
            'stateParams' => empty($stateRoute['stateParams']) ? null : $stateRoute['stateParams'],
        ]);

        if (Str::contains($stateRoute['routeId'], ['http:', 'https:'])) {
            $redirect_route = $stateRoute['routeId'];
        } else {
            $redirect_route = (empty($stateRoute['routeParams']) === false)
                ? route($stateRoute['routeId'], $stateRoute['routeParams'])
                : route($stateRoute['routeId']);
        }
        $apiDomain = config('__tech.api_domain', $_SERVER['SERVER_NAME']);

        return <<<EOL
<!DOCTYPE html>
<html>
<head>
    <title>Redirecting ...</title>
</head>
    <body>
        Redirecting... please wait
        <script>
            var stateViaRoute = ('$state_via_route');
            window.localStorage.setItem('state_via_route', stateViaRoute);

            var baseDomain = '.$apiDomain',
                expireAfter = new Date();
            
            //setting up  cookie expire date after a week
            expireAfter.setDate(expireAfter.getDate() + 0.1);
            
            //now setup cookie
            document.cookie="state_via_route="+stateViaRoute+"; domain=" + baseDomain + "; expires=" + expireAfter + "; path=/";


            window.location = "$redirect_route";
        </script>
    </body>
</html>
EOL;
    }
}
