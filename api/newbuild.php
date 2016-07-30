<?php

    $baseDir = dirname(__DIR__)."/";

    require_once $baseDir."scripts/configmanager.php";
    require_once $baseDir."scripts/projectmanager.php";

    // Check if the variables are present
    if(isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'], $_POST["project"], $_POST["commit"], $_POST["job"])) {
        
        $config = ConfigManager::getConfiguration();

        $user = $_SERVER['PHP_AUTH_USER'];
        $pw = $_SERVER['PHP_AUTH_PW'];
        $project = $_POST["project"];
        $commit = $_POST["commit"];
        $job = $_POST["job"];

        // Check if the user is authenticated
        if($user == "continuum" && $pw == $config["api_token"]) {

            // Create a new build
            $result = ProjectManager::addBuild($project, $commit, $job);
            
            if($result != false) {
                echo json_encode(array('status' => 'success', 'build' => $result));
            } else {
                echo json_encode(array('status' => 'error', 'build' => '0'));
            }

        } else {
            echo json_encode(array('status' => 'auth fail', 'build' => '0'));
        }
    } else {
        echo json_encode(array('status' => 'invalid parameters', 'build' => '0'));
    }
