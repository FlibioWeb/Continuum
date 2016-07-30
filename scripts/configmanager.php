<?php

    require_once "spyc.php";

    $baseDir = dirname(__DIR__)."/";

    class ConfigManager {

        function getConfiguration() {
            global $baseDir;
            // Create default options
            $defaultValues = array("api_token" => password_hash(md5(microtime()), PASSWORD_BCRYPT, ['cost' => 11]), "max_project_artifacts" => -1, "max_artifact_size" => 100000000, "private" => false);
            // Check if the config directory exists
            if(!file_exists($baseDir."config")) {
                mkdir($baseDir."config");
            }
            $currentConfig = array();
            // Check if the config file exists
            if(file_exists($baseDir."config/config.yaml")) {
                $currentConfig = Spyc::YAMLLoad($baseDir."config/config.yaml");
            } else {
                // Generate a new config
                file_put_contents($baseDir."config/config.yaml", "");
            }
            // Set configuration values if they don't exist
            foreach($defaultValues as $option => $value) {
                if(!isset($currentConfig[$option])) {
                    $currentConfig[$option] = $value;
                }
            }

            file_put_contents($baseDir."config/config.yaml", Spyc::YAMLDump($currentConfig, false, false, true));

            return $currentConfig;
        }

        function setConfigValue($key, $value) {
            global $baseDir;
            // Load the current config
            $config = self::getConfiguration();
            // Set the new value
            $config[$key] = $value;
            // Save the file
            file_put_contents($baseDir."config/config.yaml", Spyc::YAMLDump($config, false, false, true));
        }
    }
