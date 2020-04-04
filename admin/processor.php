<?php

require_once "../classes/Processor.php";
$processor = new Processor;

if (isset($_POST['addingArticle']))
    $processor->addArticle($_POST['title'], $_FILES['photo'], $_POST['content'], $_POST['url'], $_POST['category'], $_POST['additionalCategory'], $_POST['publicationDateOnly'], $_POST['publicationTimeOnly']);
 
else if (isset($_POST['eraseErrorLog'])){ // ERASING ERROR LOG
    $errorLog = new ErrorLog;
    $errorLog->eraseErrors();
}

else if (isset($_POST['savingArticle']) && isset($_GET['url'])){ // SAVING CHANGES IN ARTICLE
    @$editingArticle = new EditingArticle($_GET['url']);
    $editingArticle->saveChanges($_POST['title'], $_POST['content'], $_POST['category'], $_POST['additionalCategory'], $_POST['publicationDate']);
}

else if (isset($_POST['saveMenuLayout'])){ /// ALTERING MENU: CHANGING, REMOVING OPTIONS etc
    $menu = new Menu(false);
    $menu->updateMenu($_POST['name'], $_POST['order'], $_POST['destination'], $_POST['id'], $_POST['remove']);
}

else if (isset($_POST['addNewMenuElement'])){ // ADDING A NEW OPTION TO MENU
    $menu = new Menu(false);
    $menu->addElement($_POST['menuElementName'], $_POST['menuElementDestination']);
}

else if (isset($_POST['pageSettingsSavingButton'])){ // SAVING PAGE SETTINGS
    $settings = new PageSettings("../settings/default.json");
    
    $settings->__set('adminEmail', $_POST['adminEmail']);
    $settings->__set('adminPassword', $_POST['adminPassword']);
    $settings->__set('newsletterEmail', $_POST['newsletterEmail']);
    $settings->__set('description', $_POST['description']);
    $settings->__set('title', $_POST['title']);
    $settings->__set('keywords', $_POST['keywords']);
    $settings->__set('headerText', $_POST['headerText']);
    $settings->__set('url', $_POST['url']); 
    $settings->__set('theme', $_POST['theme']);
    
    $settings->saveSettings();
}


else if (isset($_POST['startingDatePicker'])){
    $commStats = new CommentsStatistics();
    $s = $commStats->countAll($_POST['startingDatePicker'], $_POST['endingDate']);
    
    header("location: panel.php?score=$s#statisticsForm");
    exit();
}

header('location: panel.php');