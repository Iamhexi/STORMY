<?php
require_once "../classes/PageSettings.php";
require_once "../classes/AdminAuth.php";
require_once "../classes/EditingArticle.php";
require_once "../classes/Menu.php";

@$adminAuth = new AdminAuth($_SESSION['isLogged']);
$adminAuth->handleloggingOut();
@$adminAuth->controlAccess();

$settings = new PageSettings("../settings/default.json");
$page = new Page($settings, "..");

$page->renderHead();

$adminAuth->renderLoggingOutForm();
AdminAuth::renderHomeButton();

if (isset($_GET['url'])){
    $EditingArticle = new EditingArticle($_GET['url']);
    $EditingArticle->renderEditor();
}
$page->renderFooter();