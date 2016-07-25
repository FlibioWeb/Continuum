<?php

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    define('BASEDIR', dirname(__DIR__)."/");

    require_once "formutils.php";
    require_once "usermanager.php";

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
                        header("Location: ../");
                    }
                } else {
                    // Invalid parameters
                    $_SESSION["message-bad"] = "Invalid parameters!";
                    header("Location: ../");
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
                        header("Location: ../");
                    }
                } else {
                    // Invalid parameters
                    $_SESSION["message-bad"] = "Invalid parameters!";
                    header("Location: ../");
                }

                break;
            
            default:
                die("An error has occurred!");
                break;
        }
    } else {
        die("An error has occurred!");
    }
