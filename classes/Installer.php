<?php

require_once 'ClassAutoLoader.php';
$autoLoader = new ClassAutoLoader();

interface iInstaller {
    static function renderInstallationForm(string $destination): void;
    static function removeInstallDirectory(): bool;
}

class Installer implements iInstaller{
    private string $dbUsername;
    private ?string $dbPassword;
    private string $dbName;
    private string $dbServerAddress;
    
    private string $adminEmail;

    protected function sanitizeInput(string $input): ?string{
        return filter_var($input, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
    
    public function __construct(
        string $databaseServerAddress,
        string $databaseUsername,
        string $databasePassword,
        string $databaseName,
        string $adminEmail
    ){
        try {
            $this->dbServerAddress = $this->sanitizeInput($databaseServerAddress);
            $this->dbUsername = $this->sanitizeInput($databaseUsername);
            $this->dbPassword = $this->sanitizeInput($databasePassword);
            $this->dbName = $this->sanitizeInput($databaseName);
            $this->adminEmail = $this->sanitizeInput($adminEmail);
            
            $this->runInstallation();
            
            header('location: ../admin/login.php?firstTime=1');
            exit();
            
        } catch (Exception $e){
            echo 'Instalacja nie powiodła się: ';
            echo $e->getMessage().'<br>';
            echo '<a href="index.php">Spróbuj ponownie zainstalować STORMY.</a><br>';
            echo 'Jeśli już to robiłeś, napisz do autora: <a href="mailto:igor.sosnowicz@gmail.com">[kliknij tutaj]</a>';
        }
    }
    
    private function runInstallation(): void {
        if (!$this->setAdminEmail())
            throw new Exception("Cannot set admin e-mail to configuration file. Insert correct e-mail or try later.");
        if (!($this->canConnect()))
            throw new Exception("Cannot connect with given database credentials! Change access information to the database and try again.");
        if (@!($this->createDatabaseIfNotExists()))
            throw new Exception("Couldn't create a new database! Admit higher privileges to the given account to solve this problem OR check whether the database name you have given is correct [only small letters without special chars, digits and underscores.");
        if (!($this->importData()))
            throw new Exception("Couldn't import SQL data to database. Check whether the file exits. ");
        if (!($this->createDatabaseConnectionFile()))
            throw new Exception("Coldn't create the database configuration file! Try again using another database name.");
    }
    
    private function setAdminEmail(): bool{
        $settings = new PageSettings('../settings/default.json');
        $settings->__set('adminEmail', $this->adminEmail);
        return $settings->saveSettings();
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
    
    
    private function createDatabaseIfNotExists(): bool{
        $n = $this->dbName;
        $query = "CREATE DATABASE IF NOT EXISTS $n";
        
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
            <div><label>Sewer bazy danych SQL: <input type="text" name="dbServer" title="Dozwolone są tylko litery bez polskich znaków, cyfry oraz znak podkreślenia. Ponadto, nazwa bazy danych nie może się rozpoczynać cyfrą." class="installerInput" pattern="[a-zA-z_]{1}[a-zA-Z0-9_@]{1,}" required></label></div>
            <div><label>Nazwa użytkownika bazy danych: <input type="text" name="dbUser" class="installerInput" required></label></div>
            <div><label>Hasło użytkownika bazy danych: <input type="password" name="dbPassword" class="installerInput"></label></div>
            <div><label>Nazwa bazy danych: <input type="name" name="dbName" class="installerInput" required></label></div>
            <div title="Koniecznie podaj poprawny e-mail, ponieważ inaczej nie będziesz w stanie się zalogować."><label>E-mail administratora: <input type="email" name="adminEmail" class="installerInput" required></label></div>
            <div><input type="submit" value="Instaluj STORMY"></div>
        </form>
END;
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
    
    private static function removeDirectory(string $dir): bool{
        if (!file_exists($dir))
            return true;
        
        if (!is_dir($dir))
            return unlink($dir);
        
        foreach (scandir($dir) as $item) {
            if ($item != '.' && $item != '..'){
                if (filetype($dir.'/'.$item) == 'dir') 
                    @rmdir($dir.'/'.$item);
                else 
                    @unlink($dir.'/'.$item);
            }
        }

        return rmdir($dir);
    }
    
    public static function removeInstallDirectory(): bool{
        $installDirectory = '../install';
        if (self::removeDirectory($installDirectory)) return true;
        else return false;
    }
}

