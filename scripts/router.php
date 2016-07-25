<?php
    require_once BASEDIR."scripts/usermanager.php";
    require_once BASEDIR."scripts/configmanager.php";

    // Get the page requested
    $uri = substr($_SERVER['REQUEST_URI'], strlen(BASEPATH));
    if (strstr($uri, '?')) $uri = substr($uri, 0, strpos($uri, '?'));
    $request = '/' . trim($uri, '/');

    $page = "";

    // Attempt to load a page
    $params = array_filter(split("/", $request));

    if(count($params) < 1) {
        // Load the home page
        // Check if the initial registration page needs to load
        if(!UserManager::userExists()) {
            $page = "register.php";
        } else {
            // Check if the site is private
            if(!ConfigManager::getConfiguration()["private"]) {
                // The site is public
                $page = "projects.php";
            } else {
                // The site is private
                if(UserManager::isLoggedIn() && in_array("view.super", UserManager::getUser()["permissions"])) {
                    $page = "projects.php";
                } else {
                    // The user is not authenticated
                    $page = "login.php";
                }
            }
        }
    } else if(count($params) == 1) {
        // Load the page requested
        $requested = urldecode($params[1]);

        switch ($requested) {
            case 'login':
                // Load login page
                $page = "login.php";
                break;

            case 'logout':
                // Logout the user
                if(UserManager::isLoggedIn()) {
                    UserManager::logout();
                }
                header("Location: ".BASEPATH);
                die("Redirecting...");
                break;
            
            default:
                // Page not found
                $page = "404.php";
                break;
        }
    } else if(count($params) == 2) {
        // Attempt to parse the page
        $base = $params[1];
        $target = $params[2];
        switch ($base) {
            case 'project':
                // Load the individual project page
                break;
            
            default:
                // Page not found
                $page = "404.php";
                break;
        }
    } else if(count($params) > 2) {
        // Too many parameters
        $page = "404.php";
    } else {
        // Nothing could be found
        $page = "404.php";
    }
