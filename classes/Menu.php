<?php

require_once "DatabaseControl.php";
require_once "CustomPageManager.php";
require_once "Categories.php";

class Menu {
    use DatabaseControl;
    
    private $isForAdmin;
    private $loadedElements;
    private $elements;
    
    public function __construct(bool $isForAdmin, $processorLocation = null){
        $this->isForAdmin = $isForAdmin;
        $this->renderMenuFromDB($processorLocation);
    }
    
    private function renderMenuFromDB($processorLocation){
        try {
            $table = DatabaseControl::$menuTable;
            $query = "SELECT optionId, optionOrder, visibleName, destination FROM $table ORDER BY optionOrder ASC";

            if(@!$connection = new mysqli(DB_HOST, DB_LOGIN, DB_PASSWORD, DB_NAME)) 
                throw new Exception($connection->connect_error);

            if(@!mysqli_query($connection, "SET CHARSET utf8")) 
                throw new Exception($connection->connect_error);

            if(@!$result = $connection->query($query)) 
                throw new Exception("Couldn't render comments for article with url = $articleUrl");

            echo ($this->isForAdmin) ? '<form class="menuEditor" action="'.$processorLocation.'" method="POST">' : '<nav class="menu sticky">';
            while ($fetched = $result->fetch_array(MYSQLI_BOTH)){
                if ($this->isForAdmin)
                    $this->rederElementForAdmin($fetched['visibleName'], $fetched['destination'], $fetched['optionOrder'], $fetched['optionId']);
                else 
                    $this->rederElement($fetched['visibleName'], $fetched['destination']);
            }
            echo ($this->isForAdmin) ? '<input type="submit" class="menuEditorButton" value="Zapisz zmiany" name="saveMenuLayout"></form>' : '</nav>';

        } catch (Exception $e){
            $this->reportException($e);
        }
    }
    
    private function rederElement(string $name, string $destination): void{
        echo '<a href="'.$destination.'">'.$name.'</a>';
    }
    
    private function rederElementForAdmin(string $name, string $destination, int $order, int $id): void{
        echo '<div><input type="number" style="display:none;" name="id[]" value="'.$id.'">';
        echo '<label>Kolejność w menu <input type="number" name="order[]" class="menuEditorElementOrder" value="'.$order.'"></label>';
        echo '<label>Nazwa odnośnika <input type="text" name="name[]" class="menuEditorElementName" value="'.$name.'"></label>';
        echo '<label>Do usunięcia? <input type="checkbox" name="remove[]" class="menuEditorCheckbox"></label>';
        echo '<label>Dokąd prowadzi? <input type="text" name="destination[]" class="menuEditorElementDestination" value="'.$destination.'"></label></div>';
             
    }
    
    private function updateElements(array $name, array $order, array $destination, array $id, $toRemove): ?Exeption{
        $table = DatabaseControl::$menuTable;
        $i = 0;
        foreach ($name as $elementName){
            
            $elementOrder = $order[$i];
            $elementDestination = $destination[$i];
            $elementId = $id[$i];
            
        if (!isset($toRemove[$i])){
            $query = "UPDATE $table SET visibleName = '$elementName', optionOrder = '$elementOrder', destination = '$elementDestination' WHERE optionId = '$elementId'";
            if (!$this->performQuery($query)) throw new Exception("Couldn't update menu with data: optionId = $id, optionOrder = $order, destination = $destination, visibleName = $name!");
        }  
            
        else {
            $query = "DELETE FROM $table WHERE optionId = '$elementId'";
            if (!$this->performQuery($query)) throw new Exception("Couldn't delete menu option with data: optionId = $id, optionOrder = $order, destination = $destination, visibleName = $name!");
        }
            
            $i++;
        }
        
        return null;
    }
    
    public function updateMenu(array $elementName, array $order, array $destination, array $id, $toRemove): bool{
        try {
            $this->updateElements($elementName, $order, $destination, $id, $toRemove);
            return true;
        } catch(Exception $e){
            $this->reportException($e);
            return false;
        }
    }
    
    private static function renderDestinationSelector(): void{
        $categories = new Categories;
        $pageSettings = new PageSettings("../settings/default.json");
        $pageManager = new CustomPageManager($pageSettings);
        
        $arrayOfCategories = $categories->getCategoriesArray();
        $arrayOfSubpages = $pageManager->getArrayOfSubpages();
        
        echo '<div><label>Dokąd prowadzi <select name="menuElementDestination">';
    
        echo '<option selected disabled>Wybierz lokalizację</option>';
        echo '<option value="'.$pageSettings->__get("url").'">Strona główna</option>';
        foreach ($arrayOfCategories as $category)
            echo "<option value=\"index.php?category={$category['categoryUrl']}\">KATEGORIA: {$category['categoryTitle']}</option>";
        
        foreach ($arrayOfSubpages as $subpage)
            echo "<option value=\"page.php?purl={$subpage['url']}\">PODSTRONA: {$subpage['title']}</option>";
        
        echo '</select></label></div>';
    }
    
    public static function renderAddingElementForm(string $destination): void{ 
        echo<<<END
            <form action="$destination" method="POST">
                <div><label>Nazwa opcji <input type="text" name="menuElementName" class="addingInput"></label></div>
END;
        Menu::renderDestinationSelector();
        echo<<<END
                <div><label><input type="submit" value="Dodaj opcję do menu" name="addNewMenuElement" class="addingMenuElementButton"></label></div>
            </form>
END;
    }
    
    
    private function addElementToDB(string $name, string $destination): ?Exception{
        $table = DatabaseControl::$menuTable;
        $query = "INSERT INTO $table (optionOrder, visibleName, destination) VALUES ('1', '$name', '$destination')";
        
        if (@!$this->performQuery($query))
            throw new Exception("Couldn't add a new option to menu!");
        
        
        return null;
    }
    
    public function addElement(string $name, string $destination){
        try {
            $this->addElementToDB($name, $destination);
        } catch (Exception $e){
            $this->reportException($e);
        } 
    }
    
    private function deleteElementFromDB(int $id): ?Exception{
        $table = DatabaseControl::$menuTable;
        $query = "DELETE FROM $table FROM optionId = '$id'";
        
        if (@!$this->performQuery($query))
            throw new Exception("Couldn't add a new option to menu!");
        
        return null;
    }
    
    public function deleteElement(int $id){
        try {
            $this->deleteElementFromDB($id);
        } catch (Exception $e){
            $this->reportException($e);
        } 
    }


    
}
