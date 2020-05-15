<?php

require_once "Theme.php";
require_once "PageSettings.php";
require_once "DatabaseControl.php";
require_once "Menu.php";
require_once "Statistics.php";

interface iPage {
    function attachTracking(): void;
    function renderHead(): void;
    function includeScripts(): void;
    function renderMenu($isForAdmin = false, string $proccessorLocation = null): void;
    function renderFooter(): void;
    function setTitle($newTitle): void;
}

class Page extends Theme implements iTheme{
    use DatabaseControl;

    private $settings;
    private $theme;
    

    public function __construct(PageSettings $settings, string $pathToMainDirectory = null, bool $exceptionReporting = false){
        $this->settings = $settings;
        $this->theme = new Theme($this->settings->theme, $pathToMainDirectory);
        $this->exceptionReporting = $exceptionReporting;
    }
    
    private function attachTracking(): void{
        $stats = new Statistics();
        if (strpos($_SERVER['REQUEST_URI'], 'admin') === false) $stats->addRecord(); // ignore admins' visits
    }
    
    public function renderHead(): void{
        $this->attachTracking();
        echo<<<END
        <!DOCTYPE html>
        <html lang="pl">
            <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">

            <title>{$this->settings->title}</title>
            <meta name="description" content="{$this->settings->description}">
            <meta name="keywords" content="{$this->settings->keywords}">
            <meta name="author" content="Igor Sosnowicz">
            <link rel="icon" href="img/favicon.ico" type="image/x-icon">
            <meta http-equiv="X-Ua-Compatible" content="IE=edge">
END;
        
        $this->theme->renderStyles();
        echo '</head><body>';
    }
    
    public function includeScripts(): void{
        $path = $this->theme->path.'/'.Theme::$themeDirectory.'/'.$this->theme->name.'/'.Theme::$scriptSubdirectory;
        @$filesToAppend = scandir($path);

        if (!empty($filesToAppend)){
            foreach($filesToAppend as $jsFile){
                if ($jsFile != '.' && $jsFile != '..') echo "<script src=\"$jsFile\"></script>";
            }
        }
    }
    
    
    public function renderMenu($isForAdmin = false, string $proccessorLocation = null): void{
        $menu = new Menu($isForAdmin, $proccessorLocation);
    }
    
    public function renderFooter(): void{
        $this->includeScripts();
        echo '<footer class="footer" title="STORMY to silnik CMS, dzięki któremu stworzysz stronę swoich marzeń!">Powered by STORMY | <a href="https://github.com/Iamhexi">Igor Sosnowicz</a> @ 2020</footer></body></html>';
    }
    
    public function setTitle($newTitle): void{
        if ($newTitle != null) $this->settings->title = $newTitle;
    }
    

}