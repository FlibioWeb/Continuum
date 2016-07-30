<?php

    require_once "spyc.php";
    require_once "configmanager.php";

    $options = array('http' => array('user_agent'=> $_SERVER['HTTP_USER_AGENT']));
    $context = stream_context_create($options);
    $baseDir = dirname(__DIR__)."/";

    class ProjectManager {

        public static function getProjects() {
            global $baseDir;
            // Create the projects directory if it doesn't exist
            if(!is_dir($baseDir."projects")) {
                mkdir($baseDir."projects");
            }
            // Make a blank projects array
            $projects = array();
            // Loop through the project folders
            $potential = scandir($baseDir."projects");
            foreach ($potential as $project) {
                if($project != "." && $project != "..") {
                    // Check if the file is a directory
                    if(is_dir($baseDir."projects/$project")) {
                        // Check if the project data file exists
                        if(self::projectDataIntact($project)) {
                            array_push($projects, $project);
                        }
                    }
                }
            }

            return $projects;
        }

        public static function projectExists($project) {
            global $baseDir;
            return in_array($project, self::getProjects());
        }

        public static function getProject($project) {
            global $baseDir;
            if(self::projectExists($project)) {
                // Load the project data
                return Spyc::YAMLLoad($baseDir."projects/$project/data.yaml");
            } else {
                return false;
            }
        }

        public static function createProject($projectName, $github, $branch, $description) {
            global $baseDir;
            // Make sure the project doesn't already exist
            if(!self::projectExists($projectName)) {
                // Create the directory if it doesn't exist
                if(!file_exists($baseDir."projects/".$projectName)) {
                    mkdir($baseDir."projects/".$projectName);
                }
                // Add the project to the data file
                $projectData = array();
                $projectData["name"] = $projectName;
                $projectData["display"] = $projectName;
                $projectData["github"] = $github;
                $projectData["branch"] = $branch;
                $projectData["description"] = $description;
                $projectData["build-number"] = 0;
                $projectData["builds"] = array();
                self::writeData($projectName, $projectData);

                return true;
            }
            return false;
        }

        public static function addBuild($project, $commit, $jobId) {
            global $baseDir, $context;
            if(self::projectExists($project)) {
                // Get the project data
                $projectData = self::getProject($project);

                // Load the build number
                $buildNumber = $projectData["build-number"] + 1;

                // Get all project builds
                $builds = $projectData["builds"];

                // Load the commit
                $commitData = file_get_contents("https://api.github.com/repos/".$projectData["github"]."/commits/$commit", false, $context);
                $commitData = json_decode($commitData, true);

                if(isset($commitData["commit"]["message"])) {
                    // Set build data
                    $builds[$buildNumber]["date"] = (new DateTime)->format("Y-m-d H:i:s");
                    $builds[$buildNumber]["commit"] = $commit;
                    $builds[$buildNumber]["job"] = $jobId;
                    $builds[$buildNumber]["message"] = $commitData["commit"]["message"];
                    $builds[$buildNumber]["artifacts"] = array();

                    // Create the build directory
                    mkdir($baseDir."projects/".$project."/".$buildNumber);

                    // Save the data
                    $projectData["builds"] = $builds;
                    $projectData["build-number"] = $buildNumber;
                    self::writeData($project, $projectData);

                    return $buildNumber;
                }
            }
            return false;
        }

        public static function addArtifact($project, $build, $file) {
            global $baseDir;
            // Load the config
            $config = ConfigManager::getConfiguration();
            // Verify the file size
            if($config["max_artifact_size"] != -1) {
                if($file["size"] > $config["max_artifact_size"]) {
                    return false;
                }
            }
            // Check if the project exists
            if(self::projectExists($project)) {
                $projectData = self::getProject($project);

                // Get all project builds
                $builds = $projectData["builds"];

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
                    $projectData["builds"][$build]["artifacts"] = $artifacts;
                    self::writeData($project, $projectData);

                    // Save the artifact
                    file_put_contents($baseDir."projects/".$project."/".$build."/".$filename, file_get_contents($file["tmp_name"]));

                    // Check the project artifact count
                    $max = $config["max_project_artifacts"];
                    if($max > -1) {
                        $count = 0;
                        foreach (array_reverse($projectData["builds"], true) as $build => $buildData) {
                            if($count >= $max) {
                                // Delete the build artifacts
                                foreach ($buildData["artifacts"] as $artifact) {
                                    unlink($baseDir."projects/$project/$build/$artifact");
                                }
                                // Remove the artifacts from the data file
                                $projectData["builds"][$build]["artifacts"] = array();
                                self::writeData($project, $projectData);
                            }
                            $count++;
                        }
                    }

                    return true;
                }
            }

            return false;
        }

        private static function projectDataIntact($project) {
            global $baseDir;
            if(file_exists($baseDir."projects/$project/data.yaml")) {
                $data = Spyc::YAMLLoad($baseDir."projects/$project/data.yaml");
                return isset($data["name"],$data["display"],$data["github"],$data["branch"],$data["description"],$data["build-number"],$data["builds"]);
            }
            return false;
        }

        private static function writeData($project, $data) {
            global $baseDir;
            // Create the projects directory if it doesn't exist
            if(!file_exists($baseDir."projects")) {
                mkdir($baseDir."projects");
            }
            // Create the project directory if it doesn't exist
            if(!file_exists($baseDir."projects/".$project)) {
                mkdir($baseDir."projects/".$project);
            }
            // Write the data
            file_put_contents($baseDir."projects/$project/data.yaml", Spyc::YAMLDump($data, false, false, true));
        }

    }
