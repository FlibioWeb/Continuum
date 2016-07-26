<?php

    require_once BASEDIR."scripts/routes.php";

    class Router {

        private $routes;

        public function __construct() {
            // Add all the routes
            $this->routes = array(
                new DefaultRoute(),
                new RegisterRoute(),
                new LoginRoute(),
                new LogoutRoute()
                );
        }

        public function routeToPage($basePath) {
            $uri = substr($_SERVER['REQUEST_URI'], strlen(BASEPATH));
            if (strstr($uri, '?')) $uri = substr($uri, 0, strpos($uri, '?'));
            $request = '/' . trim($uri, '/');
            $params = array_filter(explode("/", $request));

            $page = false;

            foreach ($this->routes as $route) {
                // Check if the request matches the route
                if($route->isValid($params)) {
                    // Route the user
                    $page = $route->routeUser($basePath, $params);
                } else {
                    continue;
                }
            }

            return $page;
        }

    }
