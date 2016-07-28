<?php
    
    require_once BASEDIR."scripts/projectmanager.php";

    $data = ProjectManager::getProject($project);

    $display = $data["display"];

    $changes = "";
    $count = 0;
    foreach (array_reverse($data["builds"], true) as $build => $buildData) {
        if($count > 50)
            break;
        $link = "<a href='".BASEPATH."project/$project/build/$build'>";
        $date = (new DateTime($buildData["date"]))->format("M j, Y g:i a");
        $change = nl2br($buildData["message"]);

        $changes.="<span class=subtitle>$link#$build at $date</a></span><p><code>$change</code></p>";
        $count++;
    }
?>
<div class="projectInfo">
    <span class="title"><?php echo $display; ?> Changelog</span>
    <p>
    <?php echo $changes; ?>
    </p>
</div>
