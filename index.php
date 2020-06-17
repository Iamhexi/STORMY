<?php

if (!file_exists('settings/connection.php')){ // autorun installer if not installed yet
    header('location: install/index.php');
    exit();
}

require 'classes/ClassAutoLoader.php';
$autoLoader = new ClassAutoLoader();

    
    $pageSettings = new PageSettings();

    $articleGrid = new ThumbnailView();
    $page = new Page($pageSettings);


    $page->renderHead();
    $page->renderMenu();
    

    @$articleGrid->renderThumbnails($_GET['category']);


   
    $page->renderFooter();