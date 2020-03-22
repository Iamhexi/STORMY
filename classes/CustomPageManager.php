<?php

require_once "DatabaseControl.php";
require_once "CustomPage.php";

class CustomPageManager{
    use DatabaseControl;
    
    private $currentSubpage;
    
    public function __construct(PageSettings $settings){
        $this->currentSubpage = new CustomPage($settings);
    }
    
    public function loadByUrl(string $url){
        try {
            $table = DatabaseControl::$pagesTable;
            $url = $this->sanitizeInput($url);
            
            if (empty($url)) 
                throw new Exception("Incorrect subpage url, please enter correct url!");
            
            $query = "SELECT content, id FROM $table WHERE url = '$url'";

            if (@!($fetched = $this->performQuery($query, true)))
                throw new Exception("Couldn't retrieve a subpage with the url '$url' from the database!");
            
            $this->currentSubpage->content = $fetched['content'];
            $this->currentSubpage->id = $fetched['id'];
            
        } catch (Exception $e){
            $this->reportException($e);
        }  
    }
    
   public function renderLoadedPage(): void{
       $this->currentSubpage->renderPage();
   }
    
    
    
}