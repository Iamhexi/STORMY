<?php

require_once 'ClassAutoLoader.php';
$autoLoader = new ClassAutoLoader();

interface Navigation {
    static function renderAddingElementForm(string $destination): void;
    function deleteElement(int $id): bool;
}

class Menu implements Navigation{
    use DatabaseControl;
    
    private $isForAdmin;
    private $loadedElements;
    private $elements;
    
    public function __construct(bool $isForAdmin, $processorLocation = null){
        $this->isForAdmin = $isForAdmin;
        $this->renderMenuFromDB($processorLocation);
    }
    
    private function retrieveMenuOptionsFromDB(): Mysqli_result{
        $table = DatabaseControl::$menuTable;
        $query = "SELECT optionId, optionOrder, visibleName, destination FROM $table ORDER BY optionOrder ASC";

        if(@!$connection = new mysqli(DB_HOST, DB_LOGIN, DB_PASSWORD, DB_NAME)) 
            throw new Exception($connection->connect_error);

        if(@!mysqli_query($connection, "SET CHARSET utf8")) 
            throw new Exception($connection->connect_error);

        if(@!$result = $connection->query($query)) 
            throw new Exception("Couldn't render comments for article with url = $articleUrl");
        
        return $result;
    }
    
    private function renderAppropriateMenuOptions(Mysqli_result $result): void{
        while ($fetched = $result->fetch_array(MYSQLI_BOTH)){
            if ($this->isForAdmin)
                $this->renderElementForAdmin($fetched['visibleName'], $fetched['destination'], $fetched['optionOrder'], $fetched['optionId']);
            else 
                $this->renderElement($fetched['visibleName'], $fetched['destination']);
        }
    }
    
    private function renderMenuFromDB($processorLocation): bool{
        try {
            $result = $this->retrieveMenuOptionsFromDB();
            
            echo ($this->isForAdmin) ?
                '<article class="menuEditorWrapper"><header class="header">Edytor menu</header><form id="menuEditor" class="menuEditor" action="'.$processorLocation.'" method="POST"><div id="optionsWrapper">' :
                '<nav class="menu sticky">';
            
            $this->renderAppropriateMenuOptions($result);
            
            echo ($this->isForAdmin) ? 
                '</div><input type="submit" class="button" value="Zapisz zmiany" name="saveMenuLayout"></form></article><script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script><link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
<!-- Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
-->

<script>
        
var el = document.getElementById(\'optionsWrapper\');
var sortable = Sortable.create(el, {
    animation: 150,
    ghostClass: \'selectedSortableElement\',
    handle: ".handle"
});
        
document.addEventListener(\'dragend\', function() {
    const order = sortable.toArray();
    
    const size = order.length;
    for (let i=0;i<size;i++){
        let element = document.querySelectorAll(".menuEditorElementOrder");
        element[i].value = i+1;
    }
    
});
        
</script>' : 
                '</nav>';
            
            return true;

        } catch (Exception $e){
            $this->reportException($e);
            return false;
        }
    }
    
    private function renderElement(string $name, string $destination): void{
        echo '<a href="'.$destination.'">'.$name.'</a>';
    }
    
    private function renderElementForAdmin(string $name, string $destination, int $order, int $id): void{
        echo '<div><i class="fas fa-arrows-alt-v handle"></i><input type="number" style="display:none;" name="id[]" value="'.$id.'">';
        echo '<input type="number" name="order[]" class="menuEditorElementOrder" value="'.$order.'">';
        echo '<label class="checkboxLabel" title="Nazwa wyświetlana w menu."><span>Nazwa odnośnika</span><input type="text" name="name[]" class="menuEditorElementName" value="'.$name.'"></label>';
        echo '<label title="Zalecamy nie zmieniać wartości tego pola, o ile to nie jest konieczne. Wskazuje ono, dokąd zostanie przkierowany użytkownik po kliknięciu w odnośnik."><span>Dokąd prowadzi?</span><input type="text" name="destination[]" class="menuEditorElementDestination" value="'.$destination.'"></label>';
        echo '<label class="switch" title="Jeśli chcesz usunąć odnośnik z menu, zaznacz tę opcję."><span>Do usunięcia?</span><input type="checkbox" name="remove[]" class="menuEditorCheckbox"><span class="checkbox"></span></label></div>';
             
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
        
        echo '<div><label><span>Dokąd prowadzi</span><select name="menuElementDestination">';
    
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
             <form action="$destination" method="POST" class="addingMenuOptionForm">
                <header class="header">Dodawanie opcji do menu</header>
                <div><label><span>Nazwa opcji</span> <input type="text" name="menuElementName" class="addingInput"></label></div>
END;
        Menu::renderDestinationSelector();
        echo<<<END
                 <div><label><input type="submit" class="button" value="Dodaj opcję do menu" name="addNewMenuElement" class="addingMenuElementButton"></label></div>
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
    
    private function deleteElementFromDB(int $id): void{
        $table = DatabaseControl::$menuTable;
        $query = "DELETE FROM $table FROM optionId = '$id'";
        
        if (@!$this->performQuery($query))
            throw new Exception("Couldn't add a new option to menu!");
    }
    
    public function deleteElement(int $id): bool{
        try {
            $this->deleteElementFromDB($id);
            return true;
        } catch (Exception $e){
            $this->reportException($e);
            return false;
        } 
    }


    
}
