<?php
require_once "../classes/Page.php";
require_once "../classes/AdminAuth.php";
require_once "../classes/AddingArticle.php";
require_once "../classes/ErrorLog.php";
require_once "../classes/ThumbnailView.php";

@$adminAuth = new AdminAuth($_SESSION['isLogged']);
$adminAuth->handleloggingOut();
@$adminAuth->controlAccess();

$settings = new PageSettings("../settings/default.json");



$page = new Page($settings, "..");
$page->renderHead();

$adminAuth->renderLoggingOutForm();

AddingArticle::renderForm();

$errorLog = new ErrorLog();
$errorLog->renderErrorLog(); 

$articleGrid = new ThumbnailView();
$articleGrid->renderThumbnails(null, true);

$page->renderMenu(true, "processor.php");
$page->renderFooter();

?>