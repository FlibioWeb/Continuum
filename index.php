<?php
    
    define('BASEDIR', __DIR__."/");

    require_once BASEDIR."scripts/usermanager.php";
    require_once BASEDIR."scripts/configmanager.php";
?>
<head>
    <title>Continuum</title>
    <link rel="stylesheet" type="text/css" href="./style/main.css">
</head>
<body>
    <div class="header">
        <img src="./images/logo-black.png">
    </div>

    <div class="content">
        <?php
            // Check if the initial registration page needs to load
            if(!UserManager::userExists()) {
                require_once BASEDIR."partials/register.php";
            } else {
                // Check if the site is private
                if(ConfigManager::getConfiguration()["private"]) {
                    // The site is public
                } else {
                    // The site if private
                }
            }
        ?>
    </div>
</body>
