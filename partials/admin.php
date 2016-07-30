<?php

    require_once BASEDIR."scripts/formutils.php";
    require_once BASEDIR."scripts/updater.php";
    require_once BASEDIR."scripts/configmanager.php";

    $config = ConfigManager::getConfiguration();

    $configToken = FormUtils::generateToken("config");
    $updateToken = FormUtils::generateToken("update");

    $version = "N/A";
    $currentVersion = Updater::getCurrentVersion();
    $updateAvailable = Updater::checkForUpdate();

    if($currentVersion !== false) {
        $version = $currentVersion;
    }
?>
<div class="admin">
    <form class="adminForm" method="POST" action="./scripts/formprocess.php">
        <p><span class="title">Updates</span></p>
        <input type="hidden" name="formname" value="update">
        <input type="hidden" name="token" value="<?php echo $updateToken; ?>">
        <p class="margin last-plain">Current Version: <?php echo $version; ?></p>
        <?php if($updateAvailable) echo "<input type=\"submit\" value=\"Update Now\">"; ?>
    </form>
    <p>
    <hr>
    <form class="adminForm" method="POST" action="./scripts/formprocess.php">
        <p><span class="title">Configuration</span></p>
        <input type="hidden" name="formname" value="config">
        <input type="hidden" name="token" value="<?php echo $configToken; ?>">
        <p class="margin">
            <label>API Token</label>
            <p><input type="text" name="apiToken" value="<?php echo $config["api_token"]; ?>" readonly size=<?php echo strlen($config["api_token"]); ?>>
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
            <p><input type="checkbox" name="private" <?php if($config["private"]) echo "checked"; ?>></p>
        </p>
        <input type="submit" value="Save">
    </form>
    <p>
    <hr>
</div>
