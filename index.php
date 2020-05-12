<?php
require_once "classes/Menu.php";
require_once "classes/Article.php";
require_once "classes/Comments.php";
require_once "classes/ThumbnailView.php";
require_once "classes/PageSettings.php";
require_once "classes/Newsletter.php";

    
    $pageSettings = new PageSettings();

    $articleGrid = new ThumbnailView();
    $page = new Page($pageSettings);


    $page->renderHead();
    $page->renderMenu();




    @$articleGrid->renderThumbnails($_GET['category']);


   
    $page->renderFooter();