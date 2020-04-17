<?php
require_once "../classes/AdminAuth.php";
require_once "../classes/AddingArticle.php";
require_once "../classes/ErrorLog.php";
require_once "../classes/ThumbnailView.php";
require_once "../classes/Menu.php";
require_once "../classes/PageSettings.php";
require_once "../classes/CommentsStatistics.php";
require_once "../classes/Categories.php";
require_once "../classes/SubpageEditor.php";

$processorLocation = "processor.php";

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

$page->renderMenu(true, $processorLocation);
Menu::renderAddingElementForm($processorLocation);
$settings->renderEditor($processorLocation);

$commStats = new CommentsStatistics;
@$commStats->renderPanel($processorLocation, $_GET['from'], null, $_GET['score']);

$subpageEditor = new SubpageEditor;
$subpageEditor->renderListOfSubpages();

Categories::renderAddingForm($processorLocation);
Categories::renderRemovalForm($processorLocation);

$page->renderFooter();

?>