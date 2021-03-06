<?php

require_once 'ClassAutoLoader.php';
$autoLoader = new ClassAutoLoader();

interface Settings {
    function getAdminPassword(): string;
    function getSettingsFileLocation(): ?string;
    function __get(string $variable); // this needs to be removed soon
    function __set(string $varName, $value): void; //this needs to be removed soon
    function saveSettings(): bool;
    function renderEditor(string $destination): void;
}

class PageSettings implements Settings{
    use DatabaseControl;
    
    private string $sourceFile;
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
    
    public function __get(string $variable){
        return $this->settingsObject->$variable;
    }
    
    public function __set(string $varName, $value): void{
        if ($varName === 'twoFactorAuth')
            $value = (bool)$value;
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

    private function render2FASelector(): void {
        if ($this->settingsObject->twoFactorAuth === false)
            $selected1 = 'selected';
        
        else
            $selected2 = 'selected';
        
            

         echo<<<END
            <div>
                <label><span>Autoryzacja dwustopniowa<span>
                    <select name="twoFactorAuth" class="settingsInput">
                        <option value="0" $selected1>Nie</option>
                        <option value="1" $selected2>Tak</option>
                    </select>
                </label>
            </div>
END;

    }

    

    public function renderEditor(string $destination): void{
        echo<<<END
            <form action="$destination" method="POST" class="settingsEditor" enctype="multipart/form-data">
                <header class="header">Ustawienia strony</header>
                <div><label><span>Tytuł strony</span><input type="text" name="title" value="{$this->settingsObject->title}" class="settingsInput" required></label></div>
                <div title="Na przykład: https://mojastrona.pl"><label><span>Adres URL strony</span><input type="url" name="url" value="{$this->settingsObject->url}" class="settingsInput" maxlength="54" required></label></div>
END;
            
        $this->renderThemeChoice();
        echo<<<END
            <div><label><span>Hasło administratora</span><input type="password" name="adminPassword" placeholder="Wpisz tylko, jeśli chcesz zmienić obecne hasło" class="settingsInput"></label></div>
            <div><label><span>E-mail administratora</span><input type="email" name="adminEmail" value="{$this->settingsObject->adminEmail}" class="settingsInput" required></label></div>
            <div><label><span>E-mail do wysyłania newslettera</span><input type="email" name="newsletterEmail" value="{$this->settingsObject->newsletterEmail}" class="settingsInput"></label></div>
            <div title="Słowa kluczowe wpisywane po przecinku"><label><span>Słowa kluczowe</span><input type="text" name="keywords" value="{$this->settingsObject->keywords}" class="settingsInput" required></label></div>
            <div><label><span>Autor strony</span><input type="text" name="author" value="{$this->settingsObject->author}" class="settingsInput" required></label></div>
            <div><label><span>Opis strony</span><textarea name="description" rows="10" cols="50" maxlength="220" class="settingsInput">{$this->settingsObject->description}</textarea></label></div>
END;
        $this->renderCommentPolicySelector();
        $this->render2FASelector();
        echo<<<END
            <div><input type="submit" name="pageSettingsSavingButton" class="button" value="Zapisz ustawienia"></div>
        </form>
        
END;
    }
    
    private function isItThemeDirectory(string $object): bool{
        if ($object != '.' && $object != '..' && $object != $this->theme && is_dir('../themes/'.$object))
            return true;
        else
            return false;
    }
    
    private function renderThemeChoice(): void{
        echo '<div><label><span>Motyw graficzny</span><select name="theme" class="themeSelectInput">';
        
        $themes = scandir('../themes/');
        foreach ($themes as $theme){
            if ($this->isItThemeDirectory($theme))
                echo "<option value=\"$theme\">$theme</option>";
            else if ($theme === $this->settingsObject->theme)
                echo "<option value=\"$theme\" selected>$theme</option>";
        } 
            
        echo '</select></label></div>';
    }
    
    private function renderCommentPolicySelector(){
        
        switch ($this->settingsObject->commentsPolicy){
            case 'safetyPolicy':
                $s1 = 'selected';
                break;

            case 'freedomPolicy':
                $s2 = 'selected';
                break;

            default:
            case 'smartFilter':
                $s3 = 'selected';
                break; 

        }

        echo<<<END
            <div>
                <label><span>Polityka komentarzy<span>
                    <select name="commentsPolicy" class="settingsInput">
                        <option value="smartFilter" $s3>Inteligentny filtr</option>
                        <option value="safetyPolicy" $s1>Najpierw zaakceptuj, potem publikuj</option>
                        <option value="freedomPolicy" $s2>Od razu publikuj wszystkie</option>
                    </select>
                </label>
            </div>
            
        
END;
    }
    
}