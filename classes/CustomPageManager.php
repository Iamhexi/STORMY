<?php

require_once "DatabaseControl.php";
require_once "CustomPage.php";

class CustomPageManager{
    use DatabaseControl;
    
    private $subpages;
    private $currentSubpage;
    private $table;
    
    public function __construct(PageSettings $settings){
        $this->table = DatabaseControl::$pagesTable;
        $this->currentSubpage = new CustomPage($settings);
    }
    
    public function loadByUrl(string $url){
        try {
            $url = $this->sanitizeInput($url);
            
            if (empty($url)) 
                throw new Exception("Incorrect subpage url, please enter correct url!");
            
            $query = "SELECT id, title, content FROM $this->table WHERE url = '$url'";

            if (@!($fetched = $this->performQuery($query, true)))
                throw new Exception("Couldn't retrieve a subpage with the url '$url' from the database!");
            
            $this->currentSubpage->content = $fetched['content'];
            $this->currentSubpage->title = $fetched['title'];
            $this->currentSubpage->id = $fetched['id'];
            
        } catch (Exception $e){
            $this->reportException($e);
        }  
    }
    
   public function renderLoadedPage(): void{
       $this->currentSubpage->renderPage();
   }
    
    
    private function loadListOfSubpagesFromDb(): void{
        $query = "SELECT * FROM $this->table";

        if(@!$connection = new mysqli(DB_HOST, DB_LOGIN, DB_PASSWORD, DB_NAME)) 
            throw new Exception($connection->connect_error);

        if(@!mysqli_query($connection, "SET CHARSET utf8")) 
            throw new Exception($connection->connect_error);

        if(@!$result = $connection->query($query)) 
            throw new Exception("Couldn't load categories from database!");

        while ($fetched = $result->fetch_array(MYSQLI_BOTH))
            $this->subpages[] = array("title" => $fetched['title'], "url" => $fetched['url']);
        
    }
    
    private function loadListOfSubpages(): bool{
        try {
            $this->loadListOfSubpagesFromDb();
            return true;
        } catch (Exception $e){
            $this->reportException($e);
            return false;
        }
    }
    
    public function getArrayOfSubpages(): array{
        if ($this->loadListOfSubpages())
            return $this->subpages;
    }
    
    
    
}