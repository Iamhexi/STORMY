<?php

@session_start();

require_once '../classes/ClassAutoLoader.php';
$autoLoader = new ClassAutoLoader();

@$adminAuth = new AdminAuth($_SESSION['isLogged']);
$adminAuth->handleloggingOut();

@$adminAuth->controlAccess();


$settings = new PageSettings("../settings/default.json");
$page = new Page($settings, "..");
$menu = new AdminMenu();
$menu->renderMenu();

$page->renderHead();


$adminAuth->renderLoggingOutForm();

if (isset($_GET['url'])){
    $EditingArticle = new EditingArticle($_GET['url']);
    $EditingArticle->renderEditor();
}


if (isset($_GET['purl'])){
    $subpageEditor = new SubpageEditor;
    $subpageEditor->renderEditor("processor.php", $_GET['purl']);

}
$page->renderFooter();