<?php
    
    define('BASEDIR', __DIR__."/");
    define('BASEPATH', implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/');

    require_once BASEDIR."scripts/router.php";
?>
<head>
    <title>Continuum</title>
    <link rel="stylesheet" type="text/css" href="<?php echo BASEPATH; ?>style/main.css">
    <link href="https://fonts.googleapis.com/css?family=Comfortaa" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
</head>
<body>
    <div class="header">
        <a href="<?php echo BASEPATH; ?>">Continuum</a>
    </div>
    <div class="message"></div>
    <div class="content"><?php global $page; require_once BASEDIR."partials/".$page; ?></div>
</body>
