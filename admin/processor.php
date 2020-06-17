<?php

require_once "../classes/ErrorLog.php";
require_once "../classes/EditingArticle.php";
require_once "../classes/Menu.php";
require_once "../classes/PageSettings.php";
require_once "../classes/CommentsStatistics.php";
require_once "../classes/SubpageEditor.php";
require_once "../classes/Processor.php";
$processor = new Processor;

$action = 'error';

if (isset($_POST['addingArticle'], $_POST['title'], $_FILES['photo'], $_POST['content'], $_POST['author'], $_POST['category']) && !DatabaseControl::mempty($_POST['title'], $_FILES['photo']['name'], $_POST['content'], $_POST['author'], $_POST['category'])){
    $action = 'addEntry';
    
    $processor->addArticle($_POST['title'], $_FILES['photo'], $_POST['content'], $_POST['author'], $_POST['category'], $_POST['additionalCategory'], $_POST['publicationDateOnly'], $_POST['publicationTimeOnly']);
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


else if (isset($_POST['addNewMenuElement'], $_POST['menuElementDestination'])){ // ADDING A NEW OPTION TO MENU
    $menu = new Menu(false);
    $menu->addElement($_POST['menuElementName'], $_POST['menuElementDestination'], $_POST['menuElementName']);
    $action = 'editOptions';
}

else if (isset($_POST['pageSettingsSavingButton'])){ // SAVING PAGE SETTINGS
    $settings = new PageSettings("../settings/default.json");
    
    
    foreach($_POST as $name => $value)
        if ($name !== 'adminPassword' && $name !== 'pageSettingsSavingButton')
            $settings->__set($name, $value);
    
    if (isset($_POST['adminPassword']) && !empty($_POST['adminPassword'])){
        $options = ['cost' => 12];
        $password = password_hash($_POST['adminPassword'], PASSWORD_BCRYPT, $options);
        $settings->__set('adminPassword', $password);
    }
    
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
    $subpageEditor->createSubpage($_POST['title']);
    $action = 'listSubpages';
}

else if (isset($_POST['removingSubpage'])){
    $subpageEditor = new SubpageEditor();
    if ($subpageEditor->removeSubpageWithId($_POST['id']))
        $action = 'listSubpages';
}

else if (isset($_POST['addingCategory'])){ // adding a new category
    $categories = new Categories();
    if (isset($_POST['categoryName']))
        $categories->add($_POST['categoryName']);
    $action = "addCategory";
}

else if (isset($_POST['removingCategory'])){ // removing the category
    $categories = new Categories();
    if (isset($_POST['categoryUrl']))
        $categories->removeWithUrl($_POST['categoryUrl']);
    $action = "removeCategory";
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