<?php 

require_once '../classes/ClassAutoLoader.php';
$autoLoader = new ClassAutoLoader();

@session_start();

$adminAuth = new AdminAuth();

@$adminAuth->controlAccess($_POST['adminPassword']);

$settings = new PageSettings("../settings/default.json");
$page = new Page($settings, "..");

$page->addCSS('body { background-color: #244999 !important; }');
$page->renderHead();

if (isset($_GET['firstTime']))
   $adminAuth->handleFirstTimeLogging($_GET['firstTime']);

$adminAuth->renderPrompt();
$adminAuth->renderLoggingForm();

$page->renderFooter();