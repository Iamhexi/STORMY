<?php
session_start();

define("ADMIN_PASSWORD", "admin");

class AdminAuth {
    private $isLogged;
    private $loggingUrl;
    private $adminPanelUrl;
    private $prompt;
    
    public function __construct(bool $isLogged = false){
        $this->isLogged = $isLogged;
        $this->loggingUrl = "login.php";
        $this->adminPanelUrl = "panel.php";
        @$this->prompt = $_SESSION['prompt'];
    }
    
    private function isPasswordCorrect(string $givenPassword){
        if (ADMIN_PASSWORD == $givenPassword) return true;
        else return false;
    }
    
    private function redirectUser(){
        $url = ($this->isLogged) ? $this->adminPanelUrl : $this->loggingUrl;
        if (basename($_SERVER['PHP_SELF']) != $url){
            header("location: $url");
            die();
        }
    }
    
    public function controlAccess(string $givenPassword = null){
        if (!$this->isLogged) {
            
            if ($givenPassword !== null){
                if ($this->isPasswordCorrect($givenPassword)){
                    $this->isLogged = true;
                    $this->redirectUser();
                }
                
                else $this->prompt = "Incorrect password has been given!";
                
            }
            
            else if ($givenPassword == null) $this->redirectUser();
            
        }
        
    }
    
    public function renderLoggingForm(){
        echo<<<END
            <div class="loggingPanel">
                <header><h1 class="loggingHeader">Panel administratora - Logowanie</h1></header>
                <form class="loggingForm" action="$this->loggingUrl" method="POST">
                    <div><label title="Insert a password to log in to the admin panel.">Password: <input type="password" name="adminPassword" class="adminPasswordInput" autofocus required><label></div>
                    <div><input type="submit" value="Log in" class="adminLoggingButton"></div>
                </form>
            </div>
END;
    }
    
    public function handleloggingOut(){
        if (@$_POST['logout']){
            $_SESSION = array();
            session_destroy();
            header("location: $this->loggingUrl");
        }

    }
    
    public function renderLoggingOutForm(){
        echo '<form action="panel.php" method="POST">
              <input type="submit" class="logoutButton" name="logout" value="Wyloguj">
              </form>';
    }
    
    public function renderPrompt(){
        if (isset($this->prompt)) echo '<div class="prompt fail">'.$this->prompt.'</div>';
    }
    
    public function __destruct(){
        $_SESSION['isLogged'] = $this->isLogged;
        $_SESSION['prompt'] = $this->prompt;
    }
    
    public static function renderHomeButton():void{
        echo '<a class="returnButtonLink" href="panel.php"><button class="returnButton">Wróć</button></a>';
    }
    
    
    
}