<?php
    
    define('BASEDIR', __DIR__."/");

    require_once BASEDIR."scripts/usermanager.php";
    require_once BASEDIR."scripts/configmanager.php";
?>
<head>
    <title>Continuum</title>
    <link rel="stylesheet" type="text/css" href="./style/main.css">
    <link href="https://fonts.googleapis.com/css?family=Comfortaa" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
</head>
<body>
    <div class="header">
        Continuum
    </div>
    <div class="message"></div>
    <div class="content">
        <?php
            // Check if the initial registration page needs to load
            if(!UserManager::userExists()) {
                require_once BASEDIR."partials/register.php";
            } else {
                // Check if the site is private
                if(!ConfigManager::getConfiguration()["private"]) {
                    // The site is public
                    require_once BASEDIR."partials/projects.php";
                } else {
                    // The site is private
                    if(UserManager::isLoggedIn() && in_array("view.super", UserManager::getUser()["permissions"])) {
                        require_once BASEDIR."partials/projects.php";
                    } else {
                        // The user is not authenticated
                        echo "Please login!";
                    }
                }
            }
        ?>
    </div>
</body>
