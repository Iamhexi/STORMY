<?php
require_once "../classes/AdminAuth.php";
require_once "../classes/Page.php";
require_once "../classes/PageSettings.php";

$adminAuth = new AdminAuth();

@$adminAuth->controlAccess($_POST['adminPassword']);

$settings = new PageSettings("../settings/default.json");
$page = new Page($settings, "..");

$page->renderHead();

$adminAuth->renderPrompt();
$adminAuth->renderLoggingForm();

$page->renderFooter();