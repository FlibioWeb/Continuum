<?php

    require_once BASEDIR."scripts/formutils.php";

    $token = FormUtils::generateToken("register");
?>
<form class="userForm" method="POST" action="./scripts/formprocess.php">
    <span class="title">Continuum Initial Setup</span>
    <p>
    <input type="hidden" name="formname" value="register">
    <input type="hidden" name="token" value="<?php echo $token; ?>">
    <label>Username</label><p><input type="text" name="username">
    <p>
    <label>Password</label><p><input type="password" name="password">
    <p>
    <input type="submit" value="Register">
</form>
