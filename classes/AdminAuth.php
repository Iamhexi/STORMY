<?php

require_once 'ClassAutoLoader.php';
$autoLoader = new ClassAutoLoader();

@session_start();

interface AdministratorAuthentication {
    function controlAccess(string $givenPassword = null);
    function renderLoggingForm(): void;
    function handleloggingOut(): void;
    function renderLoggingOutForm(): void;
    function renderPrompt(): void;
    function handleFirstTimeLogging(bool $isFirstTime): void;
}


class AdminAuth implements AdministratorAuthentication {

    private static string $hashedAdminPassword;
    private static bool $isTwoFactorAuthEnabled; 
    private static PageSettings $settings;

    private bool $isLogged;
    private string $loggingUrl;
    private string $adminPanelUrl;
    private ?string $prompt;
    
    public function __construct(?bool $isLogged = false){
        self::$settings = new PageSettings("../settings/default.json");
        self::$isTwoFactorAuthEnabled = self::$settings->__get('twoFactorAuth');
        self::$hashedAdminPassword = self::$settings->getAdminPassword();

        $this->isLogged = ($isLogged == null) ? false : $isLogged;

        $this->loggingUrl = 'login.php';
        $this->adminPanelUrl = 'panel.php';
        @$this->prompt = $_SESSION['prompt'];
    }
    
    private function isPasswordCorrect(?string $givenPassword){
        return password_verify($givenPassword, self::$hashedAdminPassword);
    }
    
    private function redirectUser(){
        $url = ($this->isLogged) ? $this->adminPanelUrl : $this->loggingUrl;
        if (basename($_SERVER['PHP_SELF']) != $url){
            header("location: $url");
            exit();
        }
    }
    
    public function controlAccess(?string $givenPassword = null){

        if ($this->isLogged)
            $this->redirectUser();
 
        if ($givenPassword === null){
            $this->redirectUser();
            return null;
        }

        if (!$this->isPasswordCorrect($givenPassword)){
            $this->prompt = 'The given password is incorrect!';
            return null;
        }
                    
        $this->isLogged = true;
        $auth = new TwoFactorAuth();

        if (self::$isTwoFactorAuthEnabled !== true)
            $this->redirectUser();
                                 
        if ($auth->isBrowserAuthenticated())
            $this->redirectUser();
                            
        header('location: 2FA.php');
        exit();
                        

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
        if (isset($this->prompt))
            echo '<div class="prompt fail">'.$this->prompt.'</div>';
    }
    
    public function __destruct(){
        $_SESSION['isLogged'] = $this->isLogged;
        $_SESSION['prompt'] = $this->prompt;
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