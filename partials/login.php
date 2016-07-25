<?php

    require_once BASEDIR."scripts/formutils.php";

    $token = FormUtils::generateToken("login");
?>
<form class="loginForm" method="POST" action="./scripts/formprocess.php">
    <input type="hidden" name="formname" value="login">
    <input type="hidden" name="token" value="<?php echo $token; ?>">
    <label>Username</label><input type="text" name="username">
    <p>
    <label>Password</label><input type="password" name="password">
    <p>
    <input type="submit" value="Login">
</form>
