<?php

    $options = array('http' => array('user_agent'=> $_SERVER['HTTP_USER_AGENT']));
    $context = stream_context_create($options);
    $baseDir = dirname(__DIR__)."/";

    class Updater {

        public static function checkForUpdate() {
            global $baseDir;
            if(self::hasVersionData()) {
                $data = json_decode(file_get_contents($baseDir."updater.json"), true);
                // Load the installed version information
                $installedDate = $data["version"]["date"];
                // Load the latest release information
                $latest = self::getLatestRelease();
                $date = $latest["published_at"];
                // Check if the current release is newer than the installed
                return ((new DateTime($date)) > (new DateTime($installedDate)));
            } else {
                return true;
            }
        }

        public static function downloadUpdate() {
            global $baseDir;
            // Make sure there is an update available
            if(self::checkForUpdate()) {
                $data = json_decode(file_get_contents($baseDir."updater.json"), true);
                // Get the latest release
                $latest = self::getLatestRelease();
                $data["version"]["date"] = $latest["published_at"];
                $data["version"]["id"] = $latest["id"];
                // Install the update
                if(self::installUpdate($latest)) {
                    file_put_contents($baseDir."updater.json", $data);
                    return true;
                }
            }
            return false;
        }

        private static function installUpdate($latest) {
            global $baseDir;
            file_put_contents($baseDir."continuuminstall.zip", $latest["zipball_url"]);
            $zip = new ZipArchive;
            $res = $zip->open($baseDir."continuuminstall.zip");
            if ($res === TRUE) {
                $zip->extractTo($baseDir);
                $zip->close();
                unlink($baseDir."continuuminstall.zip");

                $destination = $baseDir;
                $from = $baseDir."Continuum-".$latest["tag_name"];
                
                $toMove = scandir($from);

                foreach ($toMove as $file) {
                    if($file != "." && $file != "..") {
                        rename($from.$file, $destination.$file);
                    }
                }
                
                rmdir($from);

                rmdir($baseDir."Continuum-".$latest["tag_name"]);
                return true;
            }
            return false;
        }

        private static function getLatestRelease() {
            global $baseDir, $context;
            if(self::hasCacheData()) {
                $data = json_decode(file_get_contents($baseDir."updater.json"), true);
                $cache = $data["cache"];
                // Check if the cache needs to be reloaded
                if((new DateTime($cache["date"]))->diff(new DateTime(date()))->$s >= 1800) {
                    // Reload the cache
                    $latest = self::getLatestRelease();
                    $data["cache"]["content"] = $latest;
                    $data["cache"]["date"] = (new DateTime)->format("Y-m-d H:i:s");
                    // Save the cache
                    file_put_contents($baseDir."updater.json", json_encode($data));

                    return json_decode($latest, true);
                } else {
                    // Return the release from the cache
                    return json_decode($cache["content"], true);
                }
            } else {
                $data = json_decode(file_get_contents($baseDir."updater.json"), true);
                // Load the latest data
                $latest = file_get_contents("https://api.github.com/repos/FlibioWeb/Continuum/releases/latest", false, $context);   
                $data["cache"]["content"] = $latest;
                $data["cache"]["date"] = (new DateTime)->format("Y-m-d H:i:s");
                // Save the cache
                file_put_contents($baseDir."updater.json", json_encode($data));

                return json_decode($latest, true);
            }
        }

        private static function hasCacheData() {
            global $baseDir;
            // Check if the file exists
            if(file_exists($baseDir."updater.json")) {
                // Check if the file contains cache data
                $data = json_decode(file_get_contents($baseDir."updater.json"), true);
                if(isset($data["cache"])) {
                    return isset($data["cache"]["content"], $data["cache"]["date"]);
                } else {
                    return false;
                }
            } else {
                // Create a new file
                file_put_contents($baseDir."updater.json", "{}");
                return false;
            }
        }

        private static function hasVersionData() {
            global $baseDir;
            // Check if the file exists
            if(file_exists($baseDir."updater.json")) {
                // Check if the file contains version data
                $data = json_decode(file_get_contents($baseDir."updater.json"), true);
                if(isset($data["version"])) {
                    return isset($data["version"]["id"], $data["version"]["date"]);
                } else {
                    return false;
                }
            } else {
                // Create a new file
                file_put_contents($baseDir."updater.json", "{}");
                return false;
            }
        }
    }
