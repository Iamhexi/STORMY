<?php
class Page {

    private $title;
    private $keywords;
    private $description;
    private $stylesheets;
    private $exceptionReporting;

    function __construct(string $title, string $keywords, string $description, string $primaryStylesheet = "main.css", bool $exceptionReporting = false){
        $this->title = $title;
        $this->description = $description;
        $this->keywords = $keywords;
        
        $this->stylesheets = [];
        $this->stylesheets[] = $primaryStylesheet; 
        
        $this->exceptionReporting = $exceptionReporting;
    }

    private function reportException(Exception $e): void{
        if ($this->exceptionReporting === true) 
            echo 'Error: '.$e->getMessage().' on line '.$e->getLine().' in the file '.$e->getFile();
        else {
            echo 'Unfortunately, an error has occured! The administrator of the website has been already informed about this case. We would be thankful for your patience.';
            $message = 'Error: '.$e->getMessage().' on line '.$e->getLine().' in the file '.$e->getFile();
            file_put_contents('admin/log.txt', $message);
        }
    }

    function includeStylesheet(string $stylesheetFile): void{
         $this->stylesheets[] = $stylesheetFile;
    }
    
    function showHead(): void{
        echo<<<END
<!DOCTYPE html>
<html lang="pl">
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{$this->title}</title>
    <meta name="description" content="{$this->description}">
    <meta name="keywords" content="{$this->keywords}">
    <meta name="author" content="Igor Sosnowicz">
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
    <meta http-equiv="X-Ua-Compatible" content="IE=edge">
END;
        foreach ($this->stylesheets as $stylesheet)
            echo '<link rel="stylesheet" href="'.$stylesheet.'">';
        
        
        echo '</head><body>';
    }
    
    function showFooter(){
        echo '<footer class="footer">This webiste was coded by <a href="https://github.com/Iamhexi">Igor Sosnowicz</a> @ 2020</footer></body></html>';
    }
    

}