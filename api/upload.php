<?php

    $baseDir = dirname(__DIR__)."/";

    require_once $baseDir."scripts/configmanager.php";
    require_once $baseDir."scripts/projectmanager.php";

    // Check if the variables are present
    if(isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'], $_FILES["file"], $_POST["project"], $_POST["build"])) {
        
        $config = ConfigManager::getConfiguration();

        $user = $_SERVER['PHP_AUTH_USER'];
        $pw = $_SERVER['PHP_AUTH_PW'];
        $project = $_POST["project"];
        $build = $_POST["build"];

        // Check if the user is authenticated
        if($user == $config["username"] && $pw == $config["secure_token"]) {
                  
            $filename = $_FILES["file"]["name"];
            $size = $_FILES["file"]["size"];
            
            // Check if the file is within the size requirements
            if($config["max_artifact_size"] == -1 || $size <= $config["max_artifact_size"]) {

                // Add the artifact
                $success = ProjectManager::addArtifact($project, $build, $_FILES["file"]);
                
                if($success) {
                    echo json_encode(array('status' => 'success'));
                } else {
                    echo json_encode(array('status' => 'error'));
                }
            } else {
                echo json_encode(array('status' => 'file too large'));
            }
        } else {
            echo json_encode(array('status' => 'auth fail'));
        }
    } else {
        echo json_encode(array('status' => 'invalid parameters'));
    }
