<?php

require_once 'ClassAutoLoader.php';
$autoLoader = new ClassAutoLoader();

interface CommandsProcessor{
    function addArticle(string $title, array $photoFile, string $content, string $author, string $category, $additionalCategory = null, $publicationDateOnly = null, $publicationTimeOnly = null): void;
}

class Processor implements CommandsProcessor {

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
    
    public function addArticle(string $title, array $photoFile, string $content, string $author, string $category, $additionalCategory = null, $publicationDateOnly = null, $publicationTimeOnly = null): void{
        
        $publicationDate = $this->preparePublicationDate($publicationDateOnly, $publicationTimeOnly);
        $photoName = $this->handleUploadingPhoto($photoFile);
        
        $addingArticle = new addingArticle($title, $photoName, $content, $author, $category, $additionalCategory, $publicationDate);
    }
    
    
    
    
    
}