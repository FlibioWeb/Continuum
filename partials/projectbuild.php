<?php
    
    require_once BASEDIR."scripts/projectmanager.php";

    $data = ProjectManager::getProject($project);

    echo $project." - ".$build;
?>
