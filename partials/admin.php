<?php

    require_once BASEDIR."scripts/formutils.php";
    require_once BASEDIR."scripts/updater.php";
    require_once BASEDIR."scripts/configmanager.php";

    $config = ConfigManager::getConfiguration();

    $configToken = FormUtils::generateToken("config");
    $updateToken = FormUtils::generateToken("update");
    $addProjectToken = FormUtils::generateToken("addproject");

    $version = "N/A";
    $currentVersion = Updater::getCurrentVersion();
    $updateAvailable = Updater::checkForUpdate();

    if($currentVersion !== false) {
        $version = $currentVersion;
    }
?>
<div class="admin">
    <!--Update Form-->
    <form class="adminForm" method="POST" action="./scripts/formprocess.php">
        <p><span class="title">Updates</span></p>
        <input type="hidden" name="formname" value="update">
        <input type="hidden" name="token" value="<?php echo $updateToken; ?>">
        <p class="margin">Current Version: <?php echo $version; ?></p>
        <?php if($updateAvailable) echo "<input type=\"submit\" value=\"Update Now\">"; ?>
    </form>
    <p>
    <hr>
    <!--Config Form-->
    <form class="adminForm" method="POST" action="./scripts/formprocess.php">
        <p><span class="title">Configuration</span></p>
        <input type="hidden" name="formname" value="config">
        <input type="hidden" name="token" value="<?php echo $configToken; ?>">
        <p class="margin">
            <label>API Token</label>
            <p><code><?php echo $config["api_token"]; ?></code>
        </p>
        <p class="margin">
            <label>Maximum Project Artifacts</label>
            <p><input type="number" name="max_artifacts" value="<?php echo $config["max_project_artifacts"]; ?>">
        </p>
        <p class="margin">
            <label>Maximum Artifact Size</label>
            <p><input type="number" name="max_size" value="<?php echo $config["max_artifact_size"]; ?>"></p>
        <p class="margin">
            <label>Private</label>
            <input type="hidden" name="private" value="0">
            <p><input type="checkbox" name="private" value="1" <?php if($config["private"]) echo "checked"; ?>></p>
        </p>
        <input type="submit" value="Save">
    </form>
    <p>
    <hr>
    <!--Add Project Form-->
    <form class="adminForm" method="POST" action="./scripts/formprocess.php">
        <p><span class="title">Add Project</span></p>
        <input type="hidden" name="formname" value="addproject">
        <input type="hidden" name="token" value="<?php echo $addProjectToken; ?>">
        <p class="margin">
            <label>Project Name</label>
            <p><input type="text" name="name">
        </p>
        <p class="margin">
            <label>GitHub User</label>
            <p><input type="text" name="user">
        </p>
        <p class="margin">
            <label>GitHub Repository</label>
            <p><input type="text" name="repo"></p>
        </p>
        <p class="margin">
            <label>Repository Branch</label>
            <p><input type="text" name="branch"></p>
        </p>
        <p class="margin">
            <label>Description</label>
            <p><input type="text" name="description"></p>
        </p>
        <input type="submit" value="Add Project">
    </form>
    <p>
    <hr>
</div>
