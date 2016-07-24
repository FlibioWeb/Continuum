<?php

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    class FormUtils {

        public static function generateToken($formName) {
            $token = password_hash(microtime(), PASSWORD_BCRYPT, ['cost' => 11]);

            $_SESSION["token-".$formName] = $token;

            return $token;
        }

        public static function verifyToken($formName, $token) {
            if(isset($_SESSION["token-".$formName])) {
                if($_SESSION["token-".$formName] == $token) {
                    unset($_SESSION["token-".$formName]);
                    return true;
                }
                unset($_SESSION["token-".$formName]);
            }
            return false;
        }

        public static function getParameters($parameters, $post) {
            $verifiedParamaters = array();

            $fail = false;
            foreach ($parameters as $param => $value) {
                if(isset($post["param"])) {
                    $verifiedParamaters[$param] = htmlentities($value);
                } else {
                    $fail = true;
                    break;
                }
            }
            if($fail) {
                return false;
            } else {
                return $verifiedParamaters;
            }
        }

        public static function getParametersWithToken($parameters, $post, $formName) {

            if(!isset($post["token"]) || !self::verifyToken($formName, $post["token"])) {
                return false;
            }

            $verifiedParamaters = array();

            $fail = false;
            foreach ($parameters as $param) {
                if(isset($post[$param])) {
                    $verifiedParamaters[$param] = htmlentities($post[$param]);
                } else {
                    $fail = true;
                    break;
                }
            }
            if($fail) {
                return false;
            } else {
                return $verifiedParamaters;
            }
        }
    }
