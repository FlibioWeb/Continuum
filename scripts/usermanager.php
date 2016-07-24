<?php

    require_once "spyc.php";

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $baseDir = dirname(__DIR__)."/";
    
    class UserManager {

        public static function isLoggedIn() {
            return isset($_SESSION["user"]);
        }

        public static function getUser() {
            if(self::isLoggedIn()) {
                return $_SESSION["user"];
            } else {
                return array();
            }
        }

        public static function createNewUser($user, $password, $display, $permissions) {
            global $baseDir;
            // Generate the users file if it doesn't exist
            self::generateFile();

            // Load the users file
            $data = Spyc::YAMLLoad($baseDir."config/users.yaml");

            // Make sure the user doesn't exist
            if(!isset($data[$user])) {
                // Hash the password
                $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 11]);
                // Set the user data
                $data[$user] = array("display" => $display, "password" => $hash, "permissions" => $permissions);
                // Save the file
                self::saveFile($data);

                return true;
            }

            return false;
        }

        public static function login($user, $password) {
            global $baseDir;
            // Make sure the user is not logged in
            if(!self::isLoggedIn()) {
                // Generate the users file if it doesn't exist
                self::generateFile();

                // Load the users file
                $data = Spyc::YAMLLoad($baseDir."config/users.yaml");

                // Check if the username is in the file
                if(isset($data[$user])) {
                    $savedPassword = $data[$user]["password"];
                    $display = $data[$user]["display"];
                    $permissions = $data[$user]["permissions"];
                    // Verify the passwords
                    if(password_verify($password, $savedPassword)) {
                        // Set the session variables
                        $_SESSION["user"] = array("name" => $user, "display" => $display, "permissions" => $permissions);
                        return true;
                    }
                }
            }
            return false;
        }

        public static function userExists() {
            global $baseDir;
            self::generateFile();

            // Load the users file
            $data = Spyc::YAMLLoad($baseDir."config/users.yaml");

            return (count($data) > 0);
        }

        public static function generateFile() {
            global $baseDir;
            // Make the directory if it doesn't exist
            if(!file_exists($baseDir."config")) {
                mkdir($baseDir."config");
            }
            // Make the file if it doesn't exist
            if(!file_exists($baseDir."config/users.yaml")) {
                file_put_contents($baseDir."config/users.yaml", "");
            }
        }

        private static function saveFile($data) {
            global $baseDir;
            // Make sure the file exists
            self::generateFile();
            // Write to the file
            file_put_contents($baseDir."config/users.yaml", Spyc::YAMLDump($data, false, false, true));
        }

        public static function logout() {
            unset($_SESSION["user"]);
        }
    }
