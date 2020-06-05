<?php
require '../classes/ClassAutoLoader.php';
$autoLoader = new ClassAutoLoader();


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
    $settings = new PageSettings('../settings/default.json');
    $page = new Page($settings, '..');
    $page->renderHead();
    
    $auth->sendEmail();
    
    $auth->renderTwoFactorAuthForm($_SERVER['PHP_SELF']);
    
    $page->renderFooter();
}

else if ($status == -1){
    header('location: login.php');
    exit();
}

