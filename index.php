<?php

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    define('BASEDIR', __DIR__."/");
    define('BASEPATH', implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/');

    require_once BASEDIR."scripts/usermanager.php";

    $user = "<a href=\"".BASEPATH."login\">login</a>";

    if(UserManager::isLoggedIn()) {
        $user = "<a href=\"".BASEPATH."logout\">logout</a>";
        if(UserManager::hasPermission("admin.super")) {
            $user = "<a href=\"".BASEPATH."admin\">admin</a> | <a href=\"".BASEPATH."logout\">logout</a>";
        }
    }

    require_once BASEDIR."scripts/router.php";

    $page = (new Router)->routeToPage(BASEPATH);

    if($page == false) {
        $page = "404";
    }
?>
<head>
    <title><?php if(isset($titlePrefix)) echo $titlePrefix." | "; ?>Continuum</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500|Dosis" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo BASEPATH; ?>style/main.css?hello=<?php echo microtime(); ?>">
</head>
<body>
    <div class="header">
        <div class="title"><a href="<?php echo BASEPATH; ?>">Continuum</a></div>
        <div class="user"><?php echo $user; ?></div>
    </div>
    <div class="good message"><?php if(isset($_SESSION["message-good"])){echo $_SESSION["message-good"];unset($_SESSION["message-good"]);} ?></div>
    <div class="bad message"><?php if(isset($_SESSION["message-bad"])){echo $_SESSION["message-bad"];unset($_SESSION["message-bad"]);} ?></div>
    <div class="content"><?php global $page; require_once BASEDIR."partials/".$page.".php"; ?></div>
</body>
