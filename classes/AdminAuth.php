<?php
session_start();

require_once "PageSettings.php";
require_once "Installer.php";
require_once 'TwoFactorAuth.php';

interface AdministratorAuthentication {
    function controlAccess(string $givenPassword = null);
    function renderLoggingForm(): void;
    function handleloggingOut(): void;
    function renderLoggingOutForm(): void;
    function renderPrompt(): void;
    static function renderHomeButton(): void;
    function handleFirstTimeLogging(bool $isFirstTime): void;
}


$settings = new PageSettings("../settings/default.json");
define('HASHED_ADMIN_PASSWORD', $settings->getAdminPassword());

class AdminAuth implements AdministratorAuthentication {
    private bool $isLogged;
    private string $loggingUrl;
    private string $adminPanelUrl;
    private $prompt;
    
    public function __construct(bool $isLogged = false){
        $this->isLogged = $isLogged;
        $this->loggingUrl = 'login.php';
        $this->adminPanelUrl = 'panel.php';
        @$this->prompt = $_SESSION['prompt'];
    }
    
    private function isPasswordCorrect(string $givenPassword){
        return (password_verify($givenPassword, HASHED_ADMIN_PASSWORD)) ? true : false;
    }
    
    private function redirectUser(){
        $url = ($this->isLogged) ? $this->adminPanelUrl : $this->loggingUrl;
        if (basename($_SERVER['PHP_SELF']) != $url){
            header("location: $url");
            die();
        }
    }
    
    public function controlAccess(string $givenPassword = null){
        if (!$this->isLogged){
            
            if ($givenPassword !== null){
                if ($this->isPasswordCorrect($givenPassword)){
                    
                    $this->isLogged = true;
                    $auth = new TwoFactorAuth();
                    
                    if ($auth->isBrowserAuthenticated()){
                        $this->redirectUser();
                    } 
                    
                    else {               // 2FA
                        header('location: 2FA.php');
                        exit();
                    }
     
 
                }
                
                else $this->prompt = 'The given password is incorrect!';
                
            }
            
            else $this->redirectUser();
            
        }
        
    }
    
    public function renderLoggingForm(): void{
        echo<<<END
            <div class="loggingPanel">
                <header><h1 class="loggingHeader header">Panel administratora - Logowanie</h1></header>
                <form class="loggingForm" action="$this->loggingUrl" method="POST">
                <div class="passwordText">Hasło:</div>
                    <div><input type="password" name="adminPassword" class="adminPasswordInput" autofocus required><label></div>
                    <div><input type="submit" value="Zaloguj się" class="adminLoggingButton"></div>
                </form>
            </div>
END;
    }
    
    public function handleloggingOut(): void{
        if (@$_POST['logout']){
            $_SESSION = array();
            session_destroy();
            header("location: $this->loggingUrl");
        }

    }
    
    public function renderLoggingOutForm(): void{
        echo '<form action="panel.php" method="POST" class="logout">
              <input type="submit" class="logoutButton" name="logout" value="Wyloguj">
              <div style="clear: both;"></div>
              </form>';
    }
    
    public function renderPrompt(): void{
        if (isset($this->prompt)) echo '<div class="prompt fail">'.$this->prompt.'</div>';
    }
    
    public function __destruct(){
        $_SESSION['isLogged'] = $this->isLogged;
        $_SESSION['prompt'] = $this->prompt;
    }
    
    public static function renderHomeButton(): void{
        echo '<a class="returnButtonLink" href="panel.php"><button class="returnButton">Wróć</button></a>';
    }
    
    public function handleFirstTimeLogging(bool $isFirstTime): void{
        if ($isFirstTime){
        echo '<div class="prompt success">Instalacja powiodła się. Aby ją dokończyć...</div>
        <div class="prompt info">1. Zaloguj się domyślnym hasłem: admin. 
        Zmień to hasło od razu po zalogowaniu!</div>';
        if (Installer::removeInstallDirectory() === false)
            echo '<div class="prompt fail">2. Koniecznie usuń cały folder \'install\'. To ważne, ponieważ inaczej dowolna osoba będzie mogła włamać się na Twoją stronę!</div>';
        }
    }
    
}