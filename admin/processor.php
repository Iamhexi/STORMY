<?php

require_once "../classes/AddingArticle.php";
require_once "../classes/EditingArticle.php";
require_once "../classes/FileUploader.php";
require_once "../classes/ErrorLog.php";
require_once "../classes/Menu.php";

if (isset($_POST['addingArticle'])){ // ADDING NEW ARTICLE
    
    $fileUploader = new FileUploader();
    
    $publicationDate = $_POST['publicationDateOnly'].' '.$_POST['publicationTimeOnly'];
    
    
    $photo = $_FILES['photo']['name'];
    $fileUploader->uploadFile($_FILES['photo']);
    
    if (empty($_POST['publicationDateOnly']))
        $publicationDate = null;
    
    $addingArticle = new addingArticle($_POST['title'], $photo, $_POST['content'], $_POST['url'], $_POST['category'], $_POST['additionalCategory'], $publicationDate);
}

else if (isset($_POST['eraseErrorLog'])){ // ERASING ERROR LOG
    $errorLog = new ErrorLog;
    $errorLog->eraseErrors();
}

else if (isset($_POST['savingArticle']) && isset($_GET['url'])){ // SAVING CHANGES IN ARTICLE
    
    @$editingArticle = new EditingArticle($_GET['url']);
    $editingArticle->saveChanges($_POST['title'], $_POST['content'], $_POST['category'], $_POST['additionalCategory'], $_POST['publicationDate']);
}

else if (isset($_POST['saveMenuLayout'])){ /// ALTERING MENU
    $menu = new Menu(false);
    if ($menu->updateMenu($_POST['name'], $_POST['order'], $_POST['destination'], $_POST['id'])) echo "SUCCEEDED!";
    else echo "FAILED!";
}

header('location: panel.php');