<?php

    require_once "spyc.php";

    $baseDir = dirname(__DIR__)."/";

    class ConfigManager {

        function getConfiguration() {
            global $baseDir;
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
            foreach(DefaultConfig::defaultValues as $option => $value) {
                if(!isset($currentConfig[$option])) {
                    $currentConfig[$option] = $value;
                }
            }

            return $currentConfig;
        }
    }

    abstract class DefaultConfig {
        const defaultValues = array("username" => "your_username", "secure_token" => "your_secure_token", "max_project_artifacts" => -1, "max_artifact_size" => 100000000, "private" => false);
    }
