<?php

require_once "DatabaseControl.php";

interface Settings {
    function getAdminPassword(): string;
    function getSettingsFileLocation(): ?string;
    function __get(string $variable): ?string; // this needs to be removed soon
    function __set(string $varName, $value): void; //this needs to be removed soon
    function saveSettings(): bool;
    function renderEditor(string $destination): void;
}

class PageSettings implements Settings{
    use DatabaseControl;
    
    private $sourceFile;
    private $settingsObject;
    
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
    }
    
    public function getAdminPassword(): string {
        return $this->settingsObject->adminPassword;
    }
    
    public function getSettingsFileLocation(): string {
        return $this->sourceFile;
    }
    
    public function __get(string $variable): ?string{
        return $this->settingsObject->$variable;
    }
    
    public function __set(string $varName, $value): void{
        $this->settingsObject->$varName = $value;
    }
    
    private function saveSettingsToFileAsJSON(): void{
        if (@!($data = json_encode($this->settingsObject)))
            throw new Exception("Couldn't encode JSON object to format of JSON data!");

        if (@!($data = file_put_contents($this->sourceFile, $data)))
            throw new Exception("Couldn't save the file $sourceFile with settings!");
    }
    
    public function saveSettings(): bool{
        try {
            $this->saveSettingsToFileAsJSON();
            return true;
        } catch (Exception $e){
            $this->reportException($e);
            return false;
        }

    }
    

    public function renderEditor(string $destination): void{
        echo<<<END
            <form action="$destination" method="POST" class="settingsEditor">
                <header class="header">Ustawienia strony</header>
                <div><label>Tytuł strony <input type="text" name="title" value="{$this->settingsObject->title}" class="settingsInput" required></label></div>
                <div><label>Nagłówek strony <input type="text" name="headerText" value="{$this->settingsObject->headerText}" class="settingsInput" required></label></div>
                <div title="Na przykład: https://mojastrona.pl"><label>Adres URL strony<input type="url" name="url" value="{$this->settingsObject->url}" class="settingsInput" maxlength="54" required></label></div>
END;
            
        $this->renderThemeChoice();
        echo<<<END
            <div><label>Hasło administratora <input type="password" name="adminPassword" value="{$this->settingsObject->adminPassword}" class="settingsInput" required></label></div>
            <div><label>E-mail administratora <input type="email" name="adminEmail" value="{$this->settingsObject->adminEmail}" class="settingsInput" required></label></div>
            <div><label>E-mail do wysyłania newslettera <input type="email" name="newsletterEmail" value="{$this->settingsObject->newsletterEmail}" class="settingsInput"></label></div>
            <div title="Słowa kluczowe wpisywane po przecinku"><label>Słowa kluczowe <input type="text" name="keywords" value="{$this->settingsObject->keywords}" class="settingsInput" required></label></div>
            <div><label>Autor strony <input type="text" name="author" value="{$this->settingsObject->author}" class="settingsInput" required></label></div>
            <div><label>Opis strony <textarea name="description" rows="10" cols="50" maxlength="220" class="settingsInput">{$this->settingsObject->description}</textarea></label></div>
END;
        $this->renderCommentPolicySelector();
        echo<<<END
            <div><input type="submit" name="pageSettingsSavingButton" class="pageSettingsButton" value="Zapisz ustawienia"></div>
        </form>
        
END;
    }
    
    private function isItThemeDirectory(string $object): bool{
        if ($object != '.' && $object != '..' && $object != 'favicon.ico' && $object != $this->theme && $object != 'admin.css')
            return true;
        else
            return false;
    }
    
    private function renderThemeChoice(): void{
        echo '<div><label>Motyw graficzny <select name="theme" class="themeSelectInput">';
        
        $themes = scandir("../themes/");
        foreach ($themes as $theme){
            if ($this->isItThemeDirectory($theme))
                echo "<option value=\"$theme\">$theme</option>";
            else if ($theme === $this->settingsObject->theme)
                echo "<option value=\"$theme\" selected>$theme</option>";
        } 
            
        echo '</select></label></div>';
    }
    
    private function renderCommentPolicySelector(){
        if ($this->settingsObject->commentsPolicy === 'safetyPolicy') $s1 = 'selected';
        else $s2 = 'selected';
        echo<<<END
            <div>
                <label>Polityka komentarzy:
                    <select name="commentsPolicy" class="settingsInput">
                        <option value="safetyPolicy" $s1>Najpierw zaakceptuj, potem publikuj</option>
                        <option value="freedomPolicy" $s2>Od razu publikuj wszystkie</option>
                    </select>
                </label>
            </div>
            
        
END;
    }
    
}