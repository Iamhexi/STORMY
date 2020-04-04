<?php

if (!isset($_GET['url'])) header('location: index.php');


require_once "classes/Article.php";
require_once "classes/Comments.php";
require_once "classes/Menu.php";

$pageSettings = new PageSettings();
$page = new Page($pageSettings);

$comments = new Comments();
if (isset($_POST['commented'])) $comments->addComment($_GET['url'], $_POST['name'], $_POST['content']);

$page->renderHead();
$page->renderMenu();

@$article = new Article($_GET['url']);
$article->renderArticle();

$comments->renderCommentForm($_GET['url']);
$comments->renderComments($_GET['url']);

$page->renderFooter();