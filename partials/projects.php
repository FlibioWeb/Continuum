<?php
    
    require_once BASEDIR."scripts/projectmanager.php";

    $projects = "";

    foreach (ProjectManager::getProjects() as $project => $data) {
        $buildNumber = $data["build-number"];
        if($buildNumber > 0) {
            $buildDate = (new DateTime($data["builds"][$buildNumber]["date"]))->format("M j, Y g:i a");
        } else {
            $buildDate = "N/A";
        }
        $link = "<a href='".BASEPATH."project/".$project."'>";

        $projects.="<tr><td>$link$project</a></td><td>$link$buildNumber</a></td><td>$link$buildDate</a></td></tr>";
    }
?>
<table class="projectTable">
    <tr>
        <th>Project Name</th><th>Build Number</th><th>Last Build</th>
    </tr>
    <?php echo $projects; ?>
</table>
