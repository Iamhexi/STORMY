<?php

require_once "../classes/AddingArticle.php";
require_once "../classes/EditingArticle.php";
require_once "../classes/FileUploader.php";
require_once "../classes/ErrorLog.php";
require_once "../classes/Menu.php";
require_once "../classes/PageSettings.php";
require_once "../classes/CommentsStatistics.php";

interface CommandsProcessor(){
    function addArticle(string $title, array $photoFile, string $content, string $url, string $author, string $category, $additionalCategory = null, $publicationDateOnly = null, $publicationTimeOnly = null): void;
}

class Processor implements {

    private function preparePublicationDate($publicationDateOnly = null, $publicationTimeOnly = null): ?string{
        if ($publicationDateOnly === null){
            $publicationDate = null;
        } else {
            if ($publicationTimeOnly === null) $publicationDate = $publicationDateOnly;
            else $publicationDate = $publicationDateOnly.' '.$publicationTimeOnly;
        }
        return $publicationDate;
    }
    
    private function handleUploadingPhoto(array $photoFile): ?string{
        $fileUploader = new FileUploader();
        $fileUploader->uploadFile($photoFile);
        return $photoFile['name'];
    }
    
    public function addArticle(string $title, array $photoFile, string $content, string $url, string $author, string $category, $additionalCategory = null, $publicationDateOnly = null, $publicationTimeOnly = null): void{
        
        $publicationDate = $this->preparePublicationDate($publicationDateOnly, $publicationTimeOnly);
        $photoName = $this->handleUploadingPhoto($photoFile);
        
        $addingArticle = new addingArticle($title, $photoName, $content, $url, $author, $category, $additionalCategory, $publicationDate);
    }
    
    
    
    
    
}