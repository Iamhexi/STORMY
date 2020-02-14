<?php

require_once "settings/connection.php";

abstract class DatabaseControl {

    private $exceptionReporting = true; /// TURN OFF BEFORE REALSING APP
    protected static $contentTable = "news";
    protected static $commentsTable = "comments";
    
protected function reportException(Exception $e): void{
    if ($this->exceptionReporting === true) 
        echo 'Error: '.$e->getMessage().' on line '.$e->getLine().' in the file '.$e->getFile();
    else {
        echo 'Unfortunately, an error has occured! The administrator of the website has been already informed about this case. We would be thankful for your patience.';
        $message = 'Error: '.$e->getMessage().' on line '.$e->getLine().' in the file '.$e->getFile();
        file_put_contents('log.txt', $message);
    }
}
    
protected function performQuery(string $query, bool $needResponce = false, bool $needResult = false){
    try {
        if(@!$connection = new mysqli(DB_HOST, DB_LOGIN, DB_PASSWORD, DB_NAME)) throw new Exception($connection->connect_error);
        
        if(@!mysqli_query($connection, "SET CHARSET utf8")) throw new Exception($connection->connect_error);
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
        echo $e->getMessage()."<br>";
        return false;
    }
}
    
    
}

