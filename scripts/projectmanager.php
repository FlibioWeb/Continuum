<?php

    $baseDir = dirname(__DIR__)."/";

    class ProjectManager {

        public static function getProjects() {
            global $baseDir;
            // Create the projects directory if it doesn't exist
            if(!file_exists($baseDir."projects")) {
                mkdir($baseDir."projects");
            }
            // Create the data file if it doesn't exist
            if(!file_exists($baseDir."projects/data.json")) {
                file_put_contents($baseDir."projects/data.json", "{}");
            }
            // Load the data
            return json_decode(file_get_contents($baseDir."projects/data.json"), true);
        }

        public static function projectExists($project) {
            global $baseDir;
            return isset(self::getProjects()[$project]);
        }

        public static function getProject($project) {
            global $baseDir;
            if(self::projectExists($project)) {
                return self::getProjects()[$project];
            } else {
                return null;
            }
        }

        public static function createProject($projectName, $github, $branch) {
            global $baseDir;
            // Make sure the project doesn't already exist
            if(!self::projectExists($projectName)) {
                // Create the directory if it doesn't exist
                if(!file_exists($baseDir."projects/".$projectName)) {
                    mkdir($baseDir."projects/".$projectName);
                }
                // Add the project to the data file
                $projectData = self::getProjects();
                $projectData[$projectName]["display"] = $projectName;
                $projectData[$projectName]["github"] = $github;
                $projectData[$projectName]["branch"] = $branch;
                $projectData[$projectName]["description"] = "";
                $projectData[$projectName]["build-number"] = 0;
                $projectData[$projectName]["builds"] = array();
                self::writeData($projectData);
            }
        }

        public static function addBuild($project, $commit) {
            global $baseDir;
            if(self::projectExists($project)) {
                // Get the project data
                $projectData = self::getProjects();

                // Load the build number
                $buildNumber = $projectData[$project]["build-number"] + 1;

                // Get all project builds
                $builds = $projectData[$project]["builds"];

                // Set build data
                $builds[$buildNumber]["date"] = (new DateTime)->format("Y-m-d H:i:s");
                $builds[$buildNumber]["commit"] = $commit;
                $builds[$buildNumber]["artifacts"] = array();

                // Create the build directory
                mkdir($baseDir."projects/".$project."/".$buildNumber);

                // Save the data
                $projectData[$project]["builds"] = $builds;
                $projectData[$project]["build-number"] = $buildNumber;
                self::writeData($projectData);

                return $buildNumber;
            }
            return false;
        }

        public static function addArtifact($project, $build, $file) {
            global $baseDir;
            if(self::projectExists($project)) {
                $projectData = self::getProjects();

                // Get all project builds
                $builds = $projectData[$project]["builds"];

                // Check if the build exists
                if(isset($builds[$build])) {
                    // Get the artifacts
                    $artifacts = $builds[$build]["artifacts"];

                    // Make sure the filename is unique
                    $filename = $file["name"];
                    $current = 1;

                    while(in_array($filename, $artifacts)) {
                        $filename = $current.$file["name"];

                        $current++;
                    }

                    // Add the artifact
                    array_push($artifacts, $filename);

                    // Save the build data
                    $projectData[$project]["builds"][$build]["artifacts"] = $artifacts;
                    self::writeData($projectData);

                    // Save the artifact
                    file_put_contents($baseDir."projects/".$project."/".$build."/".$filename, file_get_contents($file["tmp_name"]));

                    return true;
                }
            }

            return false;
        }

        private static function writeData($data) {
            global $baseDir;
            // Create the projects directory if it doesn't exist
            if(!file_exists($baseDir."projects")) {
                mkdir($baseDir."projects");
            }
            // Write the data
            file_put_contents($baseDir."projects/data.json", json_encode($data));
        }

        private static function init() {
            $baseDir = dirname(__DIR__)."/";
        }
    }
