<?php

class Installer {
    private $dbUsername;
    private $dbPassword;
    private $dbName;
    private $dbServerAddress;

    
    public function __construct(string $databaseServerAddress, string $databaseUsername, string $databasePassword, string $databaseName){
        try {
            $this->dbServerAddress = $this->sanitizeInput($databaseServerAddress);
            $this->dbUsername = $this->sanitizeInput($databaseUsername);
            $this->dbPassword = $this->sanitizeInput($databasePassword);
            $this->dbName = $this->sanitizeInput($databaseName);
            
            $this->runInstallation();
            
            // successful installation
            
            header('location: ../admin/login.php?firstTime=1');
            
            
        } catch (Exception $e){
            echo 'Instalacja nie powiodła się: ';
            echo $e->getMessage().'<br>';
            echo '<a href="index.php">Spróbuj ponownie zainstalować STORMY.</a><br>';
            echo 'Jeśli już to robiłeś, napisz do autora: <a href="mailto:igor.sosnowicz@gmail.com">[kliknij tutaj]</a>';
        }
    }
    
    private function runInstallation(): ?Exception{
        if (!($this->canConnect()))
            throw new Exception("Cannot connect with given database credentials! Change access information to the database and try again.");
        //if (!($this->createDatabase())) // USER HAS TO CREATE DATABASE BEFOREHAND
            //throw new Exception("Couldn't create a new database! Admit higher privileges to the given account to solve this problem.");
        if (!($this->importData()))
            throw new Exception("Couldn't import SQL data to database. Check whether the file exits. ");
        if (!($this->createDatabaseConnectionFile()))
            throw new Exception("Coldn't create the database configuration file! Try again using another database name.");
        
        
        return null;
    }
    
    private function performQuery(string $query, bool $enterToDb = true, bool $needResponce = false, bool $needResult = false){
        try {
            if ($enterToDb){
                if(@!$connection = new mysqli($this->dbServerAddress, $this->dbUsername, $this->dbPassword, $this->dbName))
                    throw new Exception($connection->connect_error);
            }
                
            else 
                if(@!$connection = new mysqli($this->dbServerAddress, $this->dbUsername, $this->dbPassword))
                    throw new Exception($connection->connect_error);
            
            
            if(@!mysqli_query($connection, "SET CHARSET utf8")) 
                throw new Exception($connection->connect_error);

            if(@!$result = $connection->query($query))
                throw new Exception($connection->connect_error);

            if ($needResponce === true){
                $fetched = $result->fetch_array(MYSQLI_BOTH);
                return $fetched;
            } 

            else if ($needResult === true){
                return $result;
            } else return true;

        } catch (Exception $e){
            return false;
        }
    }
    
    private function canConnect(): bool{
        $query = "SELECT 1";
        
        if ($this->performQuery($query, false, false, true))
            return true;
        else
            return false;
    }
    
    private function createDatabase(): bool{
        $n = $this->dbName;
        $query = "CREATE DATABASE $n";
        
        if ($this->performQuery($query, false, false, true))
            return true;
        
        else
            return false;
    }
    
    private function importData(): bool {
        $file = "script.sql";
        $query = file_get_contents($file);
        try {
            if(@!$connection = new mysqli($this->dbServerAddress, $this->dbUsername, $this->dbPassword, $this->dbName))
                throw new Exception($connection->connect_error);
            
            if(@!mysqli_query($connection, "SET CHARSET utf8")) 
                throw new Exception($connection->connect_error);

            if(@!$result = $connection->multi_query($query))
                throw new Exception($connection->connect_error);

            return true;

        } catch (Exception $e){
            return false;
        }
    }
    
    public static function renderInstallationForm(string $destination): void{
        echo<<<END
        <form action="$destination" method="POST" class="installerForm">
            <div><label>Sewer bazy danych SQL: <input type="text" name="dbServer" class="installerInput" required></label></div>
            <div><label>Nazwa użytkownika bazy danych: <input type="text" name="dbUser" class="installerInput" required></label></div>
            <div><label>Hasło użytkownika bazy danych: <input type="password" name="dbPassword" class="installerInput"></label></div>
            <div><label>Nazwa bazy danych: <input type="name" name="dbName" class="installerInput" required></label></div>
            <div><input type="submit" value="Instaluj STORMY!"></div>
        </form>
END;
    }
    
    
    protected function sanitizeInput(string $input): ?string{
        return filter_var($input, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
    
    private function createDatabaseConnectionFile(): bool{
        $content = <<<END
        <?php
          define("DB_HOST", "{$this->dbServerAddress}");
          define("DB_LOGIN", "{$this->dbUsername}");
          define("DB_PASSWORD", "{$this->dbPassword}");
          define("DB_NAME", "{$this->dbName}");
END;

        $fileLocation = '../settings/connection.php';
        if (file_put_contents($fileLocation, $content))
            return true;
        else 
            return false;
    }
}

