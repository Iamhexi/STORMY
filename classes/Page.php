<?php

require_once 'ClassAutoLoader.php';
$autoLoader = new ClassAutoLoader();

interface iPage {
    function attachTracking(): void;
    function renderHead(): void;
    function includeScripts(): void;
    function renderMenu($isForAdmin = false, string $proccessorLocation = null): void;
    function renderFooter(): void;
    function setTitle($newTitle): void;
    function addCSS(string $css): void;
}

class Page extends Theme implements iTheme{
    use DatabaseControl;

    private PageSettings $settings;
    private Theme $theme;
    
    private ?string $authorForMetaTag = null;
    private ?string $addedCSS = null;
    

    public function __construct(PageSettings $settings, string $pathToMainDirectory = null){
        $this->settings = $settings;
        $this->theme = new Theme($this->settings->theme, $pathToMainDirectory);
    }
    
    public function addCSS(string $css){
        $this->addedCSS = $this->sanitizeInput($css);
    }
    
    private function attachTracking(): void{
        $stats = new Statistics();
        if (strpos($_SERVER['REQUEST_URI'], 'admin') === false) $stats->addRecord(); // ignore admins' visits
    }
    
    private function getAuthor(): ?string{
        $a = $this->authorForMetaTag;
        return ($a != null) ? $a : $this->settings->author;
    }
    
    public function renderHead(): void{
        $this->attachTracking();
        
        $author = $this->getAuthor();
        echo<<<END
        <!DOCTYPE html>
        <html lang="pl">
            <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">

            <title>{$this->settings->title}</title>
            <meta name="description" content="{$this->settings->description}">
            <meta name="keywords" content="{$this->settings->keywords}">
            <meta name="author" content="$author">
            <link rel="icon" href="themes/favicon.ico" type="image/x-icon">
            <meta http-equiv="X-Ua-Compatible" content="IE=edge">
            <style>{$this->addedCSS}</style>
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
    
    public function setTitle(?string $newTitle): void{
        if ($newTitle != null) $this->settings->title = $newTitle;
    }
    
    public function setAuthor(?string $newAuthor): void{
        if ($newAuthor != null) $this->authorForMetaTag = $newAuthor;
    }
    

}