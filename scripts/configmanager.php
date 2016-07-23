<?php

    require_once "spyc.php";

    class ConfigManager {

        public static function getConfiguration() {
            // Check if the config directory exists
            if(!file_exists("config")) {
                mkdir("config");
            }
            $currentConfig = array();
            // Check if the config file exists
            if(file_exists("config/config.yaml")) {
                $currentConfig = Spyc::YAMLLoad("config/config.yaml");
            } else {
                // Generate a new config
                file_put_contents("config/config.yaml", "");
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
        const defaultValues = array("secure_token" => "your_token_here", "max_project_artifacts" => -1, "max_artifact_size" => 100000000, "private" => false);
    }
