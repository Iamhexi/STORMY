<?php

require_once "../classes/ErrorLog.php";
require_once "../classes/EditingArticle.php";
require_once "../classes/Menu.php";
require_once "../classes/PageSettings.php";
require_once "../classes/CommentsStatistics.php";
require_once "../classes/SubpageEditor.php";
require_once "../classes/Processor.php";
$processor = new Processor;

if (isset($_POST['addingArticle'])){
    $action = 'addEntry';
    $processor->addArticle($_POST['title'], $_FILES['photo'], $_POST['content'], $_POST['url'], $_POST['category'], $_POST['additionalCategory'], $_POST['publicationDateOnly'], $_POST['publicationTimeOnly']);
}
 
else if (isset($_POST['eraseErrorLog'])){ // ERASING ERROR LOG
    $errorLog = new ErrorLog;
    $errorLog->eraseErrors();
    $action = 'errorLog';
}

else if (isset($_POST['savingArticle']) && isset($_GET['url'])){ // SAVING CHANGES IN ARTICLE
    @$editingArticle = new EditingArticle($_GET['url']);
    $editingArticle->saveChanges($_POST['title'], $_POST['content'], $_POST['category'], $_POST['additionalCategory'], $_POST['publicationDate']);
    $action = 'entryList';
}

else if (isset($_POST['saveMenuLayout'])){ /// ALTERING MENU: CHANGING, REMOVING OPTIONS etc
    $menu = new Menu(false);
    $menu->updateMenu($_POST['name'], $_POST['order'], $_POST['destination'], $_POST['id'], $_POST['remove']);
    $action = 'editOptions';
}

else if (isset($_POST['addNewMenuElement'])){ // ADDING A NEW OPTION TO MENU
    $menu = new Menu(false);
    $menu->addElement($_POST['menuElementName'], $_POST['menuElementDestination']);
    $action = 'editOptions';
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
    $settings->__set('commentsPolicy', $_POST['commentsPolicy']);
    
    $settings->saveSettings();
    $action = 'settings';
}


else if (isset($_POST['startingDatePicker'])){
    $commStats = new CommentsStatistics();
    $s = $commStats->countAll($_POST['startingDatePicker'], $_POST['endingDate']);
    
    header("location: panel.php?action=commentStats&score=$s");
    exit();
}


else if (isset($_POST['savingSubpage'])){ // saving changes on subpage
    $subpageEditor = new SubpageEditor();
    $subpageEditor->editSubpageWithId($_POST['id'], $_POST['title'], $_POST['content']);
    $action = 'listSubpages';
}

else if (isset($_POST['addingSubpage'])){ // adding a new subpage
    $subpageEditor = new SubpageEditor();
    $subpageEditor->createSubpage($_POST['url'], $_POST['title']);
    $action = 'listSubpages';
}


else if (isset($_POST['addingCategory'])){ // adding a new category
    $categories = new Categories();
    if (isset($_POST['categoryName']) && isset($_POST['categoryUrl']))
        $categories->add($_POST['categoryName'], $_POST['categoryUrl']);
    $action = "addCategory";
}

else if (isset($_POST['removingCategory'])){ // removing the category
    $categories = new Categories();
    if (isset($_POST['categoryTitle']))
        $categories->removeWithTitle($_POST['categoryTitle']);
    $action = "listCategories";
}

else if (isset($_POST['acceptComment'])){ // accepting the reviewed comment
    $comments = new Comments("../settings/default.json");
    $comments->acceptComment($_POST['commentId']);
    $action = 'commentsReviewPanel';
}

else if (isset($_POST['refuseComment'])){ // removing the reviewed comment
    $comments = new Comments("../settings/default.json");
    $comments->removeComment($_POST['commentId']);
    $action = 'commentsReviewPanel';
}


header('location: panel.php?action='.$action);