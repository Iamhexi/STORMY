<?php
require '../classes/TwoFactorAuth.php';

@session_start();
$auth = new TwoFactorAuth();


if ($auth->isBrowserAuthenticated()){
    header('location: panel.php');
    exit();
}

$status = $auth->isVerificationCodeCorrect(@$_POST['authenticationCode']);

if ($status == 1){
    $auth->authenticateBrowser();
    header('location: panel.php');
    exit();
}

else if ($status == 0){
    $auth->sendEmail();
    $auth->renderTwoFactorAuthForm($_SERVER['PHP_SELF']);
}

else if ($status == -1){
    header('location: login.php');
    exit();
}
    