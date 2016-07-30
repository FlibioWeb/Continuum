<?php

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    require_once BASEDIR."scripts/usermanager.php";
    require_once BASEDIR."scripts/configmanager.php";
    require_once BASEDIR."scripts/projectmanager.php";

    abstract class Route {

        function redirect($basePath, $location, $message = "") {
            // Parse the message
            if(!empty($message)) {
                $max = 1;
                if(strlen($message) >= 3 && substr($message, 0, 3) == "bad") {
                    if(isset($_SESSION["message-bad"])) {
                        $_SESSION["message-bad"].=" - ";
                    }
                    $_SESSION["message-bad"].= str_ireplace("bad/", "", $message, $max);
                }
                if(strlen($message) >= 4 && substr($message, 0, 4) == "good") {
                    if(isset($_SESSION["message-good"])) {
                        $_SESSION["message-good"].=" - ";
                    }
                    $_SESSION["message-good"].= str_ireplace("good/", "", $message, $max);
                }
            }
            // Redirect the user
            header("Location: ".$basePath.$location);
            die("Redirecting...");
        }

        function checkPrivate($basePath) {
            if(!UserManager::userExists()) {
                // There needs to be at least one user
                $this->redirect($basePath, "register", "bad/Please create an account!");
            } else {
                // Check if the site is private
                if(!ConfigManager::getConfiguration()["private"]) {
                    // The site is public
                    return true;
                } else {
                    // The site is private
                    if(UserManager::isLoggedIn() && in_array("view.super", UserManager::getUser()["permissions"])) {
                        return true;
                    } else {
                        // The user is not authenticated
                        $this->redirect($basePath, "login", "bad/You do not have permission to view that page!");
                    }
                }
            }
        }

        abstract function isValid($params);

        abstract function routeUser($basePath, $params);
    }

    class DefaultRoute extends Route {

        public function isValid($params) {
            if(count($params) == 0) {
                return true;
            }
            return false;
        }

        public function routeUser($basePath, $params) {
            $this->checkPrivate($basePath);
            return "projects";
        }

    }

    class LoginRoute extends Route {

        public function isValid($params) {
            if(count($params) == 1) {
                if($params[1] == "login") {
                    return true;
                }
            }
            return false;
        }

        public function routeUser($basePath, $params) {
            if(UserManager::isLoggedIn()) {
                // Route to default page
                $this->redirect($basePath, "", "bad/You are already logged in!");
            } else {
                // Load the login page
                return "login";
            }
        }

    }

    class RegisterRoute extends Route {

        public function isValid($params) {
            if(count($params) == 1) {
                if($params[1] == "register") {
                    return true;
                }
            }
            return false;
        }

        public function routeUser($basePath, $params) {
            // Prevent an infinite loop
            if(UserManager::isLoggedIn()) {
                UserManager::logout();
            }
            if(UserManager::userExists()) {
                // Route to default page
                $this->redirect($basePath, "", "bad/An account already exists!");
            } else {
                // Load the register page
                return "register";
            }
        }

    }

    class LogoutRoute extends Route {

        public function isValid($params) {
            if(count($params) == 1) {
                if($params[1] == "logout") {
                    return true;
                }
            }
            return false;
        }

        public function routeUser($basePath, $params) {
            if(UserManager::isLoggedIn()) {
                // Logout the user
                UserManager::logout();
                $this->redirect($basePath, "", "good/Logged out!");
            } else {
                // Load the register page
                $this->redirect($basePath, "", "bad/You are not logged in!");
            }
        }

    }

    class ProjectRoute extends Route {

        public function isValid($params) {
            if(count($params) == 2) {
                if($params[1] == "project") {
                    return ProjectManager::projectExists($params[2]);
                }
            }
            return false;
        }

        public function routeUser($basePath, $params) {
            $this->checkPrivate($basePath);
            $GLOBALS['project'] = $params[2];
            $GLOBALS['titlePrefix'] = $params[2];
            return "project";
        }

    }

    class ProjectBuildRoute extends Route {

        public function isValid($params) {
            if(count($params) == 4) {
                if($params[1] == "project" && $params[3] == "build") {
                    if(ProjectManager::projectExists($params[2])) {
                        $projectData = ProjectManager::getProject($params[2]);
                        $buildNumber = $params[4];
                        return ($buildNumber > 0 && $buildNumber <= $projectData["build-number"]);
                    }
                }
            }
            return false;
        }

        public function routeUser($basePath, $params) {
            $this->checkPrivate($basePath);
            $GLOBALS['project'] = $params[2];
            $GLOBALS['build'] = $params[4];
            $GLOBALS['titlePrefix'] = $params[2]." #".$params[4];
            return "projectbuild";
        }

    }

    class ChangesRoute extends Route {

        public function isValid($params) {
            if(count($params) == 3) {
                if($params[1] == "project" && $params[3] == "changes") {
                    if(ProjectManager::projectExists($params[2])) {
                        return true;
                    }
                }
            }
            return false;
        }

        public function routeUser($basePath, $params) {
            $this->checkPrivate($basePath);
            $GLOBALS['project'] = $params[2];
            $GLOBALS['titlePrefix'] = $params[2]." Changes";
            return "changes";
        }

    }

    class AdminRoute extends Route {

        public function isValid($params) {
            if(count($params) == 1) {
                return ($params[1] == "admin");
            }
            return false;
        }

        public function routeUser($basePath, $params) {
            if(UserManager::isLoggedIn()) {
                if(in_array("admin.super", UserManager::getUser()["permissions"])) {
                    $GLOBALS['titlePrefix'] = "Admin";
                    return "admin";
                }
            }
            $this->redirect($basePath, "", "bad/You do not have permission to view that page!");
        }

    }
