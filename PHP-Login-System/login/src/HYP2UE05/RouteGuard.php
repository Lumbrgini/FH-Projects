<?php

namespace HYP2UE05;

use Fhooe\Router\Router;

/**
 * RouteGuard is a simple, session-based mechanism to protect certain routes. It can either check if a session token is
 * present and valid and if not, perform a redirect or check, if a session token is explicitly not present.
 * @package HYP2UE05
 * @author Wolfgang Hochleitner <wolfgang.hochleitner@fh-hagenberg.at>
 * @version 2024
 */
class RouteGuard
{
    /**
     * Requires that a session token is present and valid in order to access the route. If this is not the case,
     * a redirect is performed to the route provided in $exitRoute.
     * @param Router $router The Router object to perform the redirect.
     * @param string $exitRoute The route to redirect if the session token is not present or invalid.
     * @return void Returns nothing.
     */
    public static function requireLoggedIn(Router $router, string $exitRoute): void
    {
        if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["isLoggedIn"] !== Utilities::generateLoginHash()) {
            $router->redirectTo($exitRoute);
        }
    }

    /**
     * Requires that a session token is not present or invalid in order to access the route. If this is not the case,
     * a redirect is performed to the route provided in $exitRoute.
     * @param Router $router The Router object to perform the redirect.
     * @param string $exitRoute The route to redirect if the session token is present valid.
     * @return void Returns nothing.
     */
    public static function requireNotLoggedIn(Router $router, string $exitRoute): void
    {
        if (isset($_SESSION["isLoggedIn"]) && $_SESSION["isLoggedIn"] === Utilities::generateLoginHash()) {
            $router->redirectTo($exitRoute);
        }
    }
}
