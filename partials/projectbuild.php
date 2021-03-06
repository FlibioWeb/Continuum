<?php
    
    require_once BASEDIR."scripts/projectmanager.php";

    $data = ProjectManager::getProject($project);
    $buildData = $data["builds"][$build];

    $message = "N/A";
    if(isset($buildData["message"])) {
        $message = $buildData["message"];
    }

    $log = false;
    if(isset($buildData["job"], $data["github"])) {
        $log = "<i class=\"fa fa-list-alt\"></i><a href=\"https://travis-ci.org/".$data["github"]."/builds/".$buildData["job"]."\">Build Log</a>";
    }

    $diff = false;
    if(isset($buildData["commit"], $data["github"])) {
        $diff = "<i class=\"fa fa-file-text\"></i><a href=\"https://github.com/".$data["github"]."/commit/".$buildData["commit"]."\">File Changes</a>";
    }

    $artifacts = "";

    if($build > 0) {
        foreach ($buildData["artifacts"] as $artifact) {
            $artifacts.="<p><a href='".BASEPATH."projects/$project/$build/$artifact' download target='_blank'>$artifact</a></p>";
        }
    }
?>
<div class="sidebar">
    <i class="fa fa-arrow-left"></i><a href="<?php echo BASEPATH."project/".$project; ?>">Back to <?php echo $data["display"]; ?></a>
    <p>
    <?php if($log !== false) echo $log; ?>
    <p>
    <?php if($diff !== false) echo $diff; ?>
</div>
<div class="projectInfo">
    <span class="title"><?php echo $data["display"]." Build #".$build; ?></span>
    <p>
    <code><?php echo nl2br($message); ?></code>
    </p>
    <b class="semibold">Commit SHA: </b><code><?php echo $buildData["commit"]; ?></code>
    <p>
    <span class="subtitle">Build Artifacts</span>
    <p>
    <?php echo $artifacts; ?>
    </p>
</div>
