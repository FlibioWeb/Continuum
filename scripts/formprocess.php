<?php

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
                        header("Location: ../");
                    } else {
                        // Creation failed
                        header("Location: ../");
                    }
                } else {
                    // Invalid parameters
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
                        header("Location: ../");
                    } else {
                        // Creation failed
                        header("Location: ../");
                    }
                } else {
                    // Invalid parameters
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
