<?php

$url = $_GET['url'];
if (!isset($url) || empty($url)){
	header('location: index.php');
	exit();
}

require_once 'classes/ClassAutoLoader.php';
$autoLoader = new ClassAutoLoader();

$pageSettings = new PageSettings();
$page = new Page($pageSettings);
$articleManager = new ArticleManager();
if ($articleManager->loadArticleByUrl($url)){

	$page->setTitle($articleManager->getTitle());
	$page->setAuthor($articleManager->getAuthor());

	$page->renderHead();

	$comments = new Comments();
	if (isset($_POST['commented']))
		$comments->addComment($url, $_POST['name'], $_POST['content']);

	$page->renderMenu();

	$articleManager->renderLoadedArticle();

	$comments->renderCommentForm($url);
	$comments->renderComments($url);

	$page->renderFooter();
}