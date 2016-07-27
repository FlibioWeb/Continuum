<?php
    
    require_once BASEDIR."scripts/projectmanager.php";

    $data = ProjectManager::getProject($project);

    $buildList = "";
    $count = 0;
    foreach (array_reverse($data["builds"], true) as $build => $buildData) {
        if($count > 15)
            break;
        $link = "<a href='".BASEPATH."project/$project/build/$build'>";
        $date = (new DateTime($buildData["date"]))->format("M j, Y g:i a");

        $buildList.="<tr><td>$link#$build</a></td><td>$link$date</a></td></tr>";
        $count++;
    }

    $latestBuild = $data["build-number"];

    $artifacts = "";
    foreach ($data["builds"][$data["build-number"]]["artifacts"] as $artifact) {
        $artifacts.="<p><a href='".BASEPATH."projects/$project/$latestBuild/$artifact' download target='_blank'>$artifact</a></p>";
    }
?>
<table class="buildTable">
    <tr><th>Recent Builds</th><th></th></tr>
    <?php echo $buildList; ?>
</table>
<div class="projectInfo">
    <span class="title"><?php echo $data["display"]; ?></span>
    <p>
    <a href="https://github.com/<?php echo $data["github"]."/tree/".$data["branch"]; ?>">View on GitHub</a>
    <p>
    <?php echo $data["description"]; ?>
    </p>
    <span class="subtitle">Latest Artifacts</span>
    <p>
    <?php echo $artifacts; ?>
    </p>
</div>
