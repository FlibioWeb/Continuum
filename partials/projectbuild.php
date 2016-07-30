<?php
    
    require_once BASEDIR."scripts/projectmanager.php";

    $data = ProjectManager::getProject($project);
    $buildData = $data["builds"][$build];

    $message = "N/A";
    if(isset($buildData["message"])) {
        $message = $buildData["message"];
    }

    $log = "N/A";
    if(isset($buildData["job"], $data["github"])) {
        $log = "<a href=\"https://travis-ci.org/".$data["github"]."/builds/".$buildData["job"]."\">View on Travis CI</a>";
    }

    $artifacts = "";

    if($build > 0) {
        foreach ($buildData["artifacts"] as $artifact) {
            $artifacts.="<p><a href='".BASEPATH."projects/$project/$build/$artifact' download target='_blank'>$artifact</a></p>";
        }
    }
?>
<div class="projectInfo clear">
    <span class="title"><?php echo $data["display"]." Build #".$build; ?></span>
    <p>
    <a href="<?php echo BASEPATH."project/$project"; ?>">Back to Project</a>
    <p>
    <code><?php echo nl2br($message); ?></code>
    </p>
    <span class="subtitle">Build Artifacts</span>
    <p>
    <?php echo $artifacts; ?>
    </p>
    <span class="subtitle">Build Log</span>
    <p>
    <?php echo $log; ?>
    </p>
</div>
