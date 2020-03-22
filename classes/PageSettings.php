<?php

require_once "DatabaseControl.php";

class PageSettings{
    use DatabaseControl;
    
    private $sourceFile;
    private $settingsObject;
    
    private $adminEmail;
    private $adminPassword;
    private $newsletterEmail;
    private $description;
    private $title;
    private $keywords;
    private $headerText;
    private $url;
    private $theme;
    
    private function loadSettings(string $sourceFile){
        try {
            if (@!($data = file_get_contents($sourceFile)))
                throw new Exception("Couldn't open the file $sourceFile to load settings!");
            
            if (@!($this->settingsObject = json_decode($data)))
                throw new Exception("Couldn't decode the file $sourceFile to load settings!");
            
            return true;
            
        } catch (Exception $exception){
            $this->reportException($exception);
            return false;
        }
    }
    
    public function __construct(string $sourceFile = "settings/default.json"){
        $this->loadSettings($sourceFile);
        
        $this->adminEmail = $this->settingsObject->adminEmail;
        $this->adminPassword = $this->settingsObject->adminPassword;
        $this->newsletterEmail = $this->settingsObject->newsletterEmail;
        $this->description = $this->settingsObject->description;
        $this->title = $this->settingsObject->title;
        $this->keywords = $this->settingsObject->keywords;
        $this->headerText = $this->settingsObject->headerText;
        $this->url = $this->settingsObject->url;
        $this->theme = $this->settingsObject->theme;
        
    }
    
    public function __get(string $variable): ?string{
        return $this->$variable;
    }
    
    public function __set(string $varName, $value): void{
        $this->$varName = $value;
    }
    
    public function saveSettings(): ?Exception{
        try {
            if (@!($data = json_encode($settingsObject)))
                throw new Exception("Couldn't encode JSON object to format of JSON data!");
            
            if (@!($data = file_put_contents($sourceFile, $data)))
                throw new Exception("Couldn't save the file $sourceFile with settings!");
            
            return null;
        } catch (Exception $e){
            $this->reportException($e);
        }

    }
}