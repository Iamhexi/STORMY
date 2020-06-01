<?php 

require_once 'ClassAutoLoader.php';
$autoLoader = new ClassAutoLoader();

interface iErrorLog {
    function renderErrorLog(string $destination = "processor.php"): void;
    function eraseErrors(): void;
}

class ErrorLog implements iErrorLog{
    use DatabaseControl;
    
    private $errorLogFile;
    private $errors;
    
    public function __construct(string $errorLogFile = "errorLog.txt") {
        $this->errorLogFile = $errorLogFile;
        $this->loadErrorLogFile();
    }
    
    private function loadErrorLogFile(): void{
        try {
            if (@!($this->errors = file($this->errorLogFile, LOCK_EX)))
                throw new Exception("Couldn't load error log file or it's empty! ($this->errorLogFile)");
        } catch (Exception $e){
            $this->reportException($e);
        }
    }
    
    private function renderErasingButton(string $destination){
        echo<<<END
            <form class="errorLogForm" action="$destination" method="POST">
                <div><input class="errorLogButton" name="eraseErrorLog" type="submit" value="Wyczyść dziennik błędów"></div>
            </form>
END;
    }
    
    public function renderErrorLog(string $destination = "processor.php"): void{
        echo '<div class="errorLog"><header class="header">Dziennik błędów</header>';
        foreach ($this->errors as $error){
            echo "<div class=\"errorEntry\">$error</div>";
        }
        $this->renderErasingButton($destination);
        echo '</div>';
    }
    
    private function saveErrorLogFile(string $data = null){
        try {
            if (@!file_put_contents($this->errorLogFile, $data, LOCK_EX))
                throw new Exception("Couldn't save error log file! ($this->errorLogFile)");
        } catch (Exception $e){
            $this->reportException($e);
        }
    }
    
    public function eraseErrors(): void{
        $this->errors = [];
        $this->saveErrorLogFile(" ");
    }
}