<?php

require_once "DatabaseControl.php";

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
                    $this->rederMenuElemetForAdmin($fetched['visibleName'], $fetched['destination'], $fetched['optionOrder'], $fetched['optionId']);
                else 
                    $this->rederMenuElemet($fetched['visibleName'], $fetched['destination']);
            }
            echo ($this->isForAdmin) ? '<input type="submit" class="menuEditorButton" name="saveMenuLayout"></form>' : '</nav>';

        } catch (Exception $e){
            $this->reportException($e);
        }
    }
    
    private function rederMenuElemet(string $name, string $destination): void{
        echo '<a href="'.$destination.'">'.$name.'</a>';
    }
    
    private function rederMenuElemetForAdmin(string $name, string $destination, int $order, int $id): void{
        echo '<div><input type="number" style="display:none;" name="id[]" value="'.$id.'">';
        echo '<label>Kolejność w menu <input type="number" name="order[]" class="menuEditorElementOrder" value="'.$order.'"></label>';
         echo '<label>Nazwa odnośnika <input type="text" name="name[]" class="menuEditorElementName" value="'.$name.'"></label>';
         echo '<label>Dokąd prowadzi? <input type="text" name="destination[]" class="menuEditorElementDestination" value="'.$destination.'"></label></div>';
             
    }
    
    private function updateMenuElements(array $name, array $order, array $destination, array $id): ?Exeption{
        $table = DatabaseControl::$menuTable;
        $i = 0;
        foreach ($name as $elementName){
            
            $elementOrder = $order[$i];
            $elementDestination = $destination[$i];
            $elementId = $id[$i];
            
            $query = "UPDATE $table SET visibleName = '$elementName', optionOrder = '$elementOrder', destination = '$elementDestination' WHERE optionId = '$elementId'";
            if (!$this->performQuery($query)) throw new Exception("Couldn't update menu with data: optionId = $id, optionOrder = $order, destination = $destination, visibleName = $name!");
            
            $i++;
        }
        
        return null;
    }
    
    public function updateMenu(array $elementName, array $order, array $destination, array $id): bool{
        try {
            $this->updateMenuElements($elementName, $order, $destination, $id);
            return true;
        } catch(Exception $e){
            $this->reportException($e);
            return false;
        }
    }


}
