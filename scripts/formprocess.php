<?php

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    define('BASEDIR', dirname(__DIR__)."/");

    require_once "formutils.php";
    require_once "usermanager.php";
    require_once "configmanager.php";
    require_once "projectmanager.php";
    require_once "updater.php";

    if(isset($_POST["formname"])) {
        $formName = $_POST["formname"];

        switch ($formName) {
            case 'register':
                $params = FormUtils::getParametersWithToken(array("username", "password"), $_POST, "register");

                if($params != false) {
                    $username = $params["username"];
                    $password = $params["password"];

                    $perms = array("admin.super", "view.super");

                    if(UserManager::createNewUser(strtolower($username), $password, $username, $perms)) {
                        // Success
                        redirect("", "good/Registered $username as an administrator!");
                    } else {
                        // Creation failed
                        redirect("register", "bad/Registration failed!");
                    }
                } else {
                    // Invalid parameters
                    redirect("register", "bad/Invalid parameters!");
                }

                break;

            case 'login':
                $params = FormUtils::getParametersWithToken(array("username", "password"), $_POST, "login");

                if($params != false) {
                    $username = $params["username"];
                    $password = $params["password"];

                    if(UserManager::login($username, $password)) {
                        // Success
                        redirect("", "good/Logged in!");
                    } else {
                        // Login failed
                        redirect("login", "bad/Invalid login!");
                    }
                } else {
                    // Invalid parameters
                    redirect("login", "bad/Invalid parameters!");
                }

                break;

            case 'update':
                if(!UserManager::hasPermission("admin.super")) {
                    // User can not do this
                    redirect("", "bad/You do not have permission to do that!");
                }
                $params = FormUtils::verifyPostToken($_POST, "update");

                if($params !== false) {
                    // Download the update
                    if(Updater::downloadUpdate()) {
                        // Success
                        redirect("admin", "good/Installed update!");
                    } else {
                        // Update failed
                        redirect("admin", "bad/Failed to install update!");
                    }
                } else {
                    // Invalid parameters
                    redirect("admin", "bad/Invalid parameters!");
                }

                break;

            case 'config':
                if(!UserManager::hasPermission("admin.super")) {
                    // User can not do this
                    redirect("", "bad/You do not have permission to do that!");
                }
                $params = FormUtils::getParametersWithToken(array("max_artifacts", "max_size", "private"), $_POST, "config");

                if($params != false) {
                    $maxArtifacts = $params["max_artifacts"];
                    $maxSize = $params["max_size"];
                    $private = $params["private"];

                    ConfigManager::setConfigValue("max_project_artifacts", intval($maxArtifacts));
                    ConfigManager::setConfigValue("max_artifact_size", intval($maxSize));
                    ConfigManager::setConfigValue("private", boolval($private));

                    // Success
                    redirect("admin", "good/Saved configuration!");
                } else {
                    // Invalid parameters
                    redirect("admin", "bad/Invalid parameters!");
                }

                break;

            case 'addproject':
                if(!UserManager::hasPermission("admin.super")) {
                    // User can not do this
                    redirect("", "bad/You do not have permission to do that!");
                }
                $params = FormUtils::getParametersWithToken(array("name", "user", "repo", "branch", "description"), $_POST, "addproject");

                if($params != false) {
                    $name = strtolower(" ", "-", str_replace($params["name"]));
                    $display = $params["name"];
                    $user = $params["user"];
                    $repo = $params["repo"];
                    $branch = $params["branch"];
                    $description = $params["description"];

                    if(ProjectManager::projectExists($name)) {
                        // The project exists
                        redirect("admin", "bad/That project already exists!");
                    } else {
                        if(ProjectManager::createProject($repo, $display, $user."/".$repo, $branch, $description)) {
                            redirect("admin", "good/Created project!");
                        } else {
                            redirect("admin", "bad/Failed to create project!");
                        }
                    }
                } else {
                    // Invalid parameters
                    redirect("admin", "bad/Invalid parameters!");
                }

                break;
            
            default:
                die("An error has occurred!");
                break;
        }
    } else {
        die("An error has occurred!");
    }

    function redirect($location, $message = "") {
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
        header("Location: ../".$location);
        die("Redirecting...");
    }
