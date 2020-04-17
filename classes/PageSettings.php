<?php

require_once "DatabaseControl.php";

class PageSettings{
    use DatabaseControl;
    
    private $sourceFile;
    private $settingsObject;
    
    private $adminEmail;
    private $adminPassword;
    private $newsletterEmail;
    private $description;
    private $title;
    private $keywords;
    private $headerText;
    private $url;
    private $theme;
    
    private function loadSettings(string $sourceFile){
        try {
            $this->sourceFile = $sourceFile;
            
            if (@!($data = file_get_contents($sourceFile)))
                throw new Exception("Couldn't open the file $sourceFile to load settings!");
            
            if (@!($this->settingsObject = json_decode($data)))
                throw new Exception("Couldn't decode the file $sourceFile to load settings!");
            
            return true;
            
        } catch (Exception $exception){
            $this->reportException($exception);
            return false;
        }
    }
    
    public function __construct(string $sourceFile = "settings/default.json"){
        $this->loadSettings($sourceFile);
        
        $this->adminEmail = $this->settingsObject->adminEmail;
        $this->adminPassword = $this->settingsObject->adminPassword;
        $this->newsletterEmail = $this->settingsObject->newsletterEmail;
        $this->description = $this->settingsObject->description;
        $this->title = $this->settingsObject->title;
        $this->keywords = $this->settingsObject->keywords;
        $this->headerText = $this->settingsObject->headerText;
        $this->url = $this->settingsObject->url;
        $this->theme = $this->settingsObject->theme;
        
    }
    
    public function __get(string $variable): ?string{
        return $this->$variable;
    }
    
    public function __set(string $varName, $value): void{
        $this->$varName = $value;
        $this->settingsObject->$varName = $value;
    }
    
    public function saveSettings(): ?Exception{
        try {
            if (@!($data = json_encode($this->settingsObject)))
                throw new Exception("Couldn't encode JSON object to format of JSON data!");
            
            if (@!($data = file_put_contents($this->sourceFile, $data)))
                throw new Exception("Couldn't save the file $sourceFile with settings!");
            
            return null;
        } catch (Exception $e){
            $this->reportException($e);
        }

    }
    

    public function renderEditor(string $destination): void{
        echo<<<END
            <form action="$destination" method="POST" class="settingsEditor">
                <header class="header">Ustawienia strony</header>
                <div><label>Tytuł strony <input type="text" name="title" value="{$this->title}" class="settingsInput" required></label></div>
                <div><label>Nagłówek strony <input type="text" name="headerText" value="{$this->headerText}" class="settingsInput" required></label></div>
                <div title="Na przykład: https://mojastrona.pl"><label>Adres URL strony<input type="url" name="url" value="{$this->url}" class="settingsInput" maxlength="54" required></label></div>
END;
            
        $this->renderThemeChoice();
        echo<<<END
             <div><label>Hasło administratora <input type="password" name="adminPassword" value="{$this->adminPassword}" class="settingsInput" required></label></div>
            <div><label>E-mail administratora <input type="email" name="adminEmail" value="{$this->adminEmail}" class="settingsInput" required></label></div>
            <div><label>E-mail do wysyłania newslettera <input type="email" name="newsletterEmail" value="{$this->newsletterEmail}" class="settingsInput"></label></div>
            <div title="Słowa kluczowe wpisywane po przecinku"><label>Słowa kluczowe <input type="text" name="keywords" value="{$this->keywords}" class="settingsInput" required></label></div>
            <div><label>Opis strony <textarea name="description" rows="10" cols="50" maxlength="220" class="settingsInput">{$this->description}</textarea></label></div>
            <div><input type="submit" name="pageSettingsSavingButton" value="Zapisz ustawienia"></div>
        </form>
        
END;
    }
    
    private function renderThemeChoice(): void{
        echo '<div><label>Motyw graficzny <select name="theme" class="themeSelectInput">';
        
        $themes = scandir("../themes/");
        foreach ($themes as $theme){
            if ($theme != "." && $theme != ".." && $theme != $this->theme) echo "<option value=\"$theme\">$theme</option>";
            else if ($theme === $this->theme) echo "<option value=\"$theme\" selected>$theme</option>";
        } 
            
        echo '</select></label></div>';
    }
    
}