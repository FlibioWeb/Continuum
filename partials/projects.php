<?php
    
    require_once BASEDIR."scripts/projectmanager.php";

    $projects = "";

    foreach (ProjectManager::getProjects() as $project => $data) {
        $projects.="<li>$project</li>";
    }
?>
<div class="sidebar">
    <span class="title">Projects</span>
    <ul>
        <?php echo $projects; ?>
    </ul>
</div>
