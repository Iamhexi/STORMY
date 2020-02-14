<?php
require_once "classes/Article.php";
require_once "classes/Comments.php";

$comments = new Comments();
echo $comments->countAllComments();
$comments->renderCommentsForArticle("lol-braum");

$article = new Article("lol-braum");
$article->renderArticle();