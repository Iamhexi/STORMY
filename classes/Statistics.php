<?php

require_once 'ClassAutoLoader.php';
$autoLoader = new ClassAutoLoader();

interface iStatistics {
    function addRecord(): bool;
    function renderTable(): void;
}

class Statistics implements iStatistics {
    use DatabaseControl;
    private string $table;
    
    public function __construct(){
        $this->table = DatabaseControl::$statisticsTable;
    }
    
    private function prepareAddingQuery(): string{
        $ip = $_SERVER['REMOTE_ADDR'];    
        $visitedUrl = $_SERVER['REQUEST_URI'];
        
        return "INSERT INTO $this->table (ip, visitDatetime, visitedUrl) VALUES ('$ip', NOW(), '$visitedUrl')";
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
    
    
    private function retrieveUniqueVisitsMonthly(): ?array{
        $visitsMonthly = array();
        
        $query = "SELECT COUNT(DISTINCT ip) AS visits, MONTH(visitDatetime) AS month FROM statistics WHERE YEAR(visitDatetime) = YEAR(NOW()) GROUP BY MONTH(visitDateTime);";
        
        if(@!$connection = new mysqli(DB_HOST, DB_LOGIN, DB_PASSWORD, DB_NAME)) 
            throw new Exception($connection->connect_error);

        if(@!mysqli_query($connection, "SET CHARSET utf8")) 
            throw new Exception($connection->connect_error);

        if(@!$result = $connection->query($query)) 
            throw new Exception("Couldn't retireve statistical a number of unique visitis per month in this year from the database!");
                  
        while ($fetched = $result->fetch_array(MYSQLI_BOTH))
            $visitsMonthly[$fetched['month']] = $fetched['visits'];
  
        return $visitsMonthly;
    }
    
    
    private function prepareArrayWithMonthlyVisits(): array { 
        $dataToRender = $this->retrieveUniqueVisitsMonthly();
        
        for ($i = 1; $i <= 12; $i++)
            if ($dataToRender[$i] === null)
                $dataToRender[$i] = 0;
        
        $values=array(
            "Styczeń" => (int) $dataToRender[1],
            "Luty" => (int) $dataToRender[2],
            "Marzec" => (int) $dataToRender[3],
            "Kwiecień" => (int) $dataToRender[4],
            "Maj" => (int) $dataToRender[5],
            "Czerwiec" => (int) $dataToRender[6],
            "Lipiec" => (int) $dataToRender[7],
            "Sierpień" => (int) $dataToRender[8],
            "Wrzesień" => (int) $dataToRender[9],
            "Październik" => (int) $dataToRender[10],
            "Listopad" => (int) $dataToRender[11],
            "Grudzień" => (int) $dataToRender[12]
        );
        
        return $values;
    }
    
    public function renderTable(): void{
        $d = $this->prepareArrayWithMonthlyVisits();
        
        echo '<header class="header">Statystyki odwiedzin - rok '.date("Y").'</header>';
        echo '<table class="statisticsTable">';
        
        echo '<tr><td class="leftColumn"><b>Miesiąc</b></td><td class="rightColumn"><b>Unikalnych odwiedzin</b></td></tr>';
        foreach ($d as $month => $visits)
            echo "<tr><td class=\"leftColumn\">$month</td><td class=\"rightColumn\">$visits</td></tr>";
        echo '</table>';
    }
        
    
}