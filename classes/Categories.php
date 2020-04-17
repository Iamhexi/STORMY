<?php

require_once "DatabaseControl.php";


class Categories {
    use DatabaseControl;
    
    private $table;
    private $categories;
    
    public function __construct(){
        $this->categories = array();
        $this->table = DatabaseControl::$categoriesTable;
        $this->loadCategories();
    }
    
    private function loadCategoriesFromDb(): void{
        $query = "SELECT * FROM $this->table";

        if(@!$connection = new mysqli(DB_HOST, DB_LOGIN, DB_PASSWORD, DB_NAME)) 
            throw new Exception($connection->connect_error);

        if(@!mysqli_query($connection, "SET CHARSET utf8")) 
            throw new Exception($connection->connect_error);

        if(@!$result = $connection->query($query)) 
            throw new Exception("Couldn't load categories from database!");

        while ($fetched = $result->fetch_array(MYSQLI_BOTH)){
            $this->categories[] = array("categoryId" => $fetched['categoryId'], "categoryTitle" => $fetched['categoryTitle'], "categoryUrl" => $fetched['categoryUrl']);
        }
    }
    
    private function loadCategories(): bool{
        try {
            $this->loadCategoriesFromDb();
            return true;
        } catch (Exception $e){
            $this->reportException($e);
            return false;
        }
    }
    
    public function renderAll(): void{
        var_dump($this->categories);
    }
    
    public function getCategoriesArray(): array {
        return $this->categories;
    }
    
    private function saveCategory(int $id, string $title, string $url){
        $query = "UPDATE $this->table SET categoryTitle = '$title', categoryUrl = '$url' WHERE categoryId = '$id'";
        if (@!($this->performQuery($query)))
            throw new Exception("Couldn't update");
    }
    
    private function saveCategories(){
        foreach($this->categories as $category){
            $id = $category['categoryId'];
            $title = $category['categoryTitle'];
            $url = $category['categoryUrl'];
            
            $this->saveCategory($id, $title, $url);
        }
    }
    
    public function saveAll(){
        try {
            $this->saveCategories();
            return true;
        } catch (Exception $e){
            $this->reportException($e);
            return false;
        }
    }
    
    public function editCategory(int $givenId, string $givenTitle, $givenUrl = null){ 
        // all changes are automatically saved to database
        foreach($this->categories as $category){
            $id = $category['categoryId'];
            $title = $category['categoryTitle'];
            $url = $category['categoryUrl'];
            
            if ($givenId == $id){
                if ($givenUrl === null)
                    $givenUrl = $url;
                    
                $this->saveCategory($givenId, $givenTitle, $givenUrl);
            }
            
        }
    }
    
    private function addCategory(string $title, string $url){
        $query = "INSERT INTO $this->table (categoryTitle, categoryUrl) VALUES ('$title', '$url')";
        if (@!($this->performQuery($query)))
            throw new Exception("Couldn't update");
    }
    
    public function add(string $title, string $url){
        try {
            $this->addCategory($title, $url);
            return true;
        } catch (Exception $e){
            $this->reportException($e);
            return false;
        }
    }
    
    private function removeCategoryWithUrl(string $url){
        $query = "DELETE FROM $this->table WHERE categoryUrl = '$url'";
        
        if (@!($this->performQuery($query)))
            throw new Exception("Couldn't delete cateogry with url = $url from database!");
    }
    
    public function removeWithUrl(string $url){
        try {
            $this->removeCategoryWithUrl($url);
            return true;
        } catch (Exception $e){
            $this->reportException($e);
            return false;
        }
    }
    
    private function removeCategoryWithTitle(string $title){
        $query = "DELETE FROM $this->table WHERE categoryTitle = '$title'";
        
        if (@!($this->performQuery($query)))
            throw new Exception("Couldn't delete cateogry with title = $title from database!");
    }
    
    public function removeWithTitle(string $title){
        try {
            $this->removeCategoryWithTitle($title);
            return true;
        } catch (Exception $e){
            $this->reportException($e);
            return false;
        }
    }
    
    
    
    public static function renderRemovalForm(string $destination){
        echo<<<END
         <form action="$destination" method="POST" class="removingCategoryForm">
            <header class="header">Usuwanie kategorii</header>
END;
        DatabaseControl::renderCategorySelector(" ", "categoryTitle");
        
        echo<<<END
            <div><input type="submit" value="Usuń kategorię" name="removingCategory"></div>
        </form>
END;
    }
    
    
    public static function renderAddingForm(string $destination){
        echo<<<END
            <form action="$destination" method="POST" class="addingCategoryForm">
                <header class="header">Dodawanie kategorii</header>
                <div><label>Nazwa kategorii <input type="text" name="categoryName" required></label></div>
                <div title="Bez polskich znaków, spacji, tabulatorów"><label>URL kategorii <input type="text" name="categoryUrl" required></label></div>
                <div><input type="submit" name="addingCategory" value="Dodaj kategorię"></div>
            </form>
END;
    }
    
}