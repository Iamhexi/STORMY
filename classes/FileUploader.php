<?php

require_once 'ClassAutoLoader.php';
$autoLoader = new ClassAutoLoader();

interface iFileUploader {
    public function uploadFile(array $fileArray): bool;
}

define("UPLOAD_DIRECTORY", '../'.AddingArticle::$photoDirectory);

class FileUploader implements iFileUploader{
    use DatabaseControl;
    
    public $uploadDirectory = "upload/storage/";
    
    public function __construct(string $uploadDirectory = UPLOAD_DIRECTORY){
        if (!is_null($this->uploadDirectory)) $this->uploadDirectory = $uploadDirectory;
    }
    
    private function checkForErrors($errorIndex): ?Exception{
        switch ($errorIndex){
            case UPLOAD_ERR_OK:
                return null;
            break;
                
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new Exception("Maximal file size has been exceeded! Change max file size in php.ini or the maximal size of data uploaded in php form.");
            break;
                
            case UPLOAD_ERR_PARTIAL:
                throw new Exception("Only a part of the uploaded file has been received!");
            break;
                
            case UPLOAD_ERR_NO_FILE:
                throw new Exception("No file has been uploaded!");
            break;
                
            case UPLOAD_ERR_NO_TMP_DIR:
                throw new Exception("Lacking access to temporary dir to store the uploaded file before moving it to destination location!");
            break;
                
            case UPLOAD_ERR_CANT_WRITE:
                throw new Exception("Couldn't save the uploaded file on the server! Check permissions to write.");
            break;
                
            case UPLOAD_ERR_EXTENSION:
                throw new Exception("Uploading a file has been inturrupted by PHP extension.");
            break;
                
                default:
                    throw new Exception("Unknown error during uploading a file!");
        }
    }
    
    private function handleUploadErrors($errorIndex): bool{
        try {
            $this->checkForErrors($errorIndex);
            return true;
        } catch (Exception $e){
            $this->reportException($e);
            return false;
        }
    }
    
    private function getNewFileLocation(string $uploadedFileName): string {
        return $this->uploadDirectory.$uploadedFileName;
    }
    
    public function uploadFile(array $fileArray): bool{ // $_FILES['inputName']
        if (!$this->handleUploadErrors($fileArray['error'])) return false;
        $tmpLocation = $fileArray['tmp_name'];
        $newLocation = $this->uploadDirectory.$fileArray['name'];
        
        if (!move_uploaded_file($tmpLocation, $newLocation)) return false;
        return true;
    }
    
    public function uploadFiles(){
        
    }
    
    public function renderUploadForm(){
        
    }
}