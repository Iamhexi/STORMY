<?php

require_once "DatabaseControl.php";

class Statistics {
    use DatabaseControl;
    private $table;
    
    public function __construct(){
        $this->table = DatabaseControl::$statisticsTable;
    }
    
    private function prepareAddingQuery(): string{
        $ip = $_SERVER['REMOTE_ADDR'];
        $browserObject = get_browser();
        $browser = $browserObject->browser;
        $system = $browserObject->platform;
        $visitedUrl = $_SERVER['PHP_SELF'];
        
        return "INSERT INTO $this->table (ip, browser, system, visitDatetime, visitedUrl) VALUES ('$ip', '$browser', '$system', NOW(), '$visitedUrl')";
    }
    
    public function addRecord(): bool{
        try {
            $query = $this->prepareAddingQuery();
            if (@!($this->performQuery($query)))
                throw new Exception("Couldn't add a new record to statistics!");
            return true;
            
        } catch (Exception $e){
            $this->reportException($e);
            return false;
        }
    }
    
    private function convertToDatetime(int $timestamp){
        return date("Y-m-d G:i:s", $timestamp);
    }
    
    private function prepareSelectingQuery(int $startingDate, int $endingDate){
        $startingDate = $this->convertToDatetime($startingDate);
        $endingDate = $this->convertToDatetime($endingDate);
        
        return "SELECT COUNT(DISTINCT ip) visits FROM $this->table WHERE visitDatetime BETWEEN '$startingDate' AND '$endingDate'";
    }
    
    private function retrieveVisitsNumber(int $startingDate, int $endingDate){
        $query = $this->prepareSelectingQuery($startingDate, $endingDate);
        
        if (@!($retrieved = $this->performQuery($query, true)))
            throw new Exception("Couldn't retrieve statistical data from database!");
        
        return $retrieved['visits'];
    }
    
    public function countVisits(int $startingDate = 0, int $endingDate = 2147483647): int{ // if error occurs, returns -1
        try {
            return $this->retrieveVisitsNumber($startingDate, $endingDate);
        } catch (Exception $e){
            $this->reportException($e);
            return -1;
        }
        
    }
    
}