<?php

require_once "classes/CustomPageManager.php";

$pageSettings = new PageSettings();
$pageManager = new CustomPageManager($pageSettings);

if (isset($_GET['purl']) && !empty($_GET['purl'])){
    $pageManager->loadByUrl($_GET['purl']);
    $pageManager->renderLoadedPage();
} 

else header('location: index.php');