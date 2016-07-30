<?php

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    define('BASEDIR', dirname(__DIR__)."/");

    require_once "formutils.php";
    require_once "usermanager.php";
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
                        $_SESSION["message-good"] = "Registered $username as an administrator!";
                        header("Location: ../");
                    } else {
                        // Creation failed
                        $_SESSION["message-bad"] = "Registration failed!";
                        header("Location: ../register");
                    }
                } else {
                    // Invalid parameters
                    $_SESSION["message-bad"] = "Invalid parameters!";
                    header("Location: ../register");
                }

                break;

            case 'login':
                $params = FormUtils::getParametersWithToken(array("username", "password"), $_POST, "login");

                if($params != false) {
                    $username = $params["username"];
                    $password = $params["password"];

                    if(UserManager::login($username, $password)) {
                        // Success
                        $_SESSION["message-good"] = "Logged in!";
                        header("Location: ../");
                    } else {
                        // Login failed
                        $_SESSION["message-bad"] = "Invalid login!";
                        header("Location: ../login");
                    }
                } else {
                    // Invalid parameters
                    $_SESSION["message-bad"] = "Invalid parameters!";
                    header("Location: ../login");
                }

                break;

            case 'update':
                $params = FormUtils::verifyPostToken($_POST, "update");

                if($params !== false) {
                    // Download the update
                    if(Updater::downloadUpdate()) {
                        // Success
                        $_SESSION["message-good"] = "Installed update!";
                        header("Location: ../admin");
                    } else {
                        // Update failed
                        $_SESSION["message-bad"] = "Failed to install update!";
                        header("Location: ../admin");
                    }
                } else {
                    // Invalid parameters
                    $_SESSION["message-bad"] = "Invalid parameters!";
                    header("Location: ../admin");
                }

                break;
            
            default:
                die("An error has occurred!");
                break;
        }
    } else {
        die("An error has occurred!");
    }
