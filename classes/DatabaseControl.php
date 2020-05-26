<?php

@include_once "../settings/connection.php";
@include_once "settings/connection.php";

trait DatabaseControl {

    private $exceptionReporting = true; /// TURN OFF BEFORE REALSING APP
    public static $contentTable = "news";
    public static $commentsTable = "comments";
    public static $pagesTable = "pages";
    public static $menuTable = "menu";
    public static $categoriesTable = "categories";
    public static $statisticsTable = "statistics";
    
protected function reportException(Exception $e): void{
    if ($this->exceptionReporting === true) 
        echo '<div class="prompt fail">Error: '.$e->getMessage().' on line '.$e->getLine().' in the file '.$e->getFile().'</div>';
    else {
        echo '<div class="prompt fail">Unfortunately, an error has occured! The administrator of the website has been already informed about this case. We would be thankful for your patience.</div>';
        $message = 'Error: '.$e->getMessage().' on line '.$e->getLine().' in the file '.$e->getFile();
        file_put_contents('errorLog.txt', $message,  FILE_APPEND | LOCK_EX);
    }
}
    
protected function performQuery(string $query, bool $needResponce = false, bool $needResult = false){
    try {
        if(@!$connection = new mysqli(DB_HOST, DB_LOGIN, DB_PASSWORD, DB_NAME)) throw new Exception($connection->connect_error);
        
        if(@!$connection->query("SET CHARSET utf8")) throw new Exception($connection->connect_error);
        if(@!$result = $connection->query($query)) throw new Exception($connection->connect_error);
        
        if ($needResponce === true){
            $fetched = $result->fetch_array(MYSQLI_BOTH);
            return $fetched;
        } 
        
        else if ($needResult === true){
            return $result;
        } else return true;
        
    } catch (Exception $e){
        $this->reportException($e);
        return false;
    }
}
    
protected function sanitizeInput(string $input): string{
    return filter_var($input, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
}
    
public static function renderCategorySelector(string $defaultValue, string $htmlAttributeName): void{
        $categories = new Categories();
        $categoryList = $categories->getCategoriesArray();
        
        echo '<div><label><span>Kategoria</span><select name="'.$htmlAttributeName.'" class="articleEditorInput" required>';
        
        foreach($categoryList as $c){
            if ($c['categoryTitle'] == $defaultValue) echo "<option selected value=\"{$c['categoryUrl']}\">{$c['categoryTitle']}</option>";
            else echo "<option value=\"{$c['categoryUrl']}\">{$c['categoryTitle']}</option>";
        }
        
        echo '</select></label></div>';
    } 
    
    
}

