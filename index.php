<?php

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    define('BASEDIR', __DIR__."/");
    define('BASEPATH', implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/');

    require_once BASEDIR."scripts/router.php";
?>
<head>
    <title>Continuum</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto|Dosis" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo BASEPATH; ?>style/main.css">
</head>
<body>
    <div class="header">
        <a href="<?php echo BASEPATH; ?>">Continuum</a>
    </div>
    <div class="good message"><?php if(isset($_SESSION["message-good"])){echo $_SESSION["message-good"];unset($_SESSION["message-good"]);} ?></div>
    <div class="bad message"><?php if(isset($_SESSION["message-bad"])){echo $_SESSION["message-bad"];unset($_SESSION["message-bad"]);} ?></div>
    <div class="content"><?php global $page; require_once BASEDIR."partials/".$page; ?></div>
</body>
