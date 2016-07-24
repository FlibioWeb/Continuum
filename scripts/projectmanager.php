<?php

    class ProjectManager {

        public static function getProjects() {
            // Create the projects directory if it doesn't exist
            if(!file_exists("projects")) {
                mkdir("projects");
            }
            // Create the data file if it doesn't exist
            if(!file_exists("projects/data.json")) {
                file_put_contents("projects/data.json", "{}");
            }
            // Load the data
            return json_decode(file_get_contents("projects/data.json"), true);
        }

        public static function projectExists($project) {
            return isset(self::getProjects()[$project]);
        }

        public static function getProject($project) {
            if(self::projectExists($project)) {
                return self::getProjects()[$project];
            } else {
                return null;
            }
        }

        public static function createProject($projectName, $github, $branch) {
            // Make sure the project doesn't already exist
            if(!self::projectExists($projectName)) {
                // Create the directory if it doesn't exist
                if(!file_exists("projects/".$projectName)) {
                    mkdir("projects/".$projectName);
                }
                // Add the project to the data file
                $projectData = self::getProjects();
                $projectData[$projectName]["display"] = $projectName;
                $projectData[$projectName]["github"] = $github;
                $projectData[$projectName]["branch"] = $branch;
                $projectData[$projectName]["build-number"] = 0;
                $projectData[$projectName]["builds"] = array();
                self::writeData($projectData);
            }
        }

        public static function addBuild($project, $commit) {
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
                mkdir("projects/".$project."/".$buildNumber);

                // Save the data
                $projectData[$project]["builds"] = $builds;
                $projectData[$project]["build-number"] = $buildNumber;
                self::writeData($projectData);
            }
        }

        public static function addArtifact($project, $build, $file) {
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
                    file_put_contents("projects/".$project."/".$build."/".$filename, file_get_contents($file["tmp_name"]));

                    return true;
                }
            }

            return false;
        }

        private static function writeData($data) {
            // Create the projects directory if it doesn't exist
            if(!file_exists("projects")) {
                mkdir("projects");
            }
            // Write the data
            file_put_contents("projects/data.json", json_encode($data));
        }
    }
