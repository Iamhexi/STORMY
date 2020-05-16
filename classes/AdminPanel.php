<?php

require_once "../classes/AdminMenu.php";
require_once "../classes/AdminAuth.php";
require_once "../classes/AddingArticle.php";
require_once "../classes/ErrorLog.php";
require_once "../classes/ThumbnailView.php";
require_once "../classes/Menu.php";
require_once "../classes/PageSettings.php";
require_once "../classes/CommentsStatistics.php";
require_once "../classes/Categories.php";
require_once "../classes/SubpageEditor.php";

interface iAdminPanel {
    function renderPanel(?string $action): void;
}

class AdminPanel {
    
    private $processorLocation = "processor.php";
    private $settings;
    private $page;
    private $adminAuth;
    
    public function __construct(){

        @$this->adminAuth = new AdminAuth($_SESSION['isLogged']);
        $this->adminAuth->handleloggingOut();
        @$this->adminAuth->controlAccess();

        $this->settings = new PageSettings("../settings/default.json");
        $this->page = new Page($this->settings, "..");
    }
    
    private function renderNewEntryForm(){
        AddingArticle::renderForm();
    }
    
    private function renderErrorLog(){
        $errorLog = new ErrorLog();
        $errorLog->renderErrorLog(); 
    }
    
    private function renderListOfArticles(){
        $articleGrid = new ThumbnailView();
        $articleGrid->renderThumbnails(null, true);
    }
    
    private function renderListOfMenuOptions(){
        $this->page->renderMenu(true, $this->processorLocation);
    }
    
    private function renderNewMenuElementForm(){
        Menu::renderAddingElementForm($this->processorLocation);
    }
    
    private function renderSettingsEditor(){
        $this->settings->renderEditor($this->processorLocation);
    }
    
    private function renderVisitsStatistics(): void{
        $stats = new Statistics();
        $stats->renderTable();
    }
    
    private function renderCommentsStatictics(){
        $commStats = new CommentsStatistics($this->settings->getSettingsFileLocation());
        @$commStats->renderPanel($this->processorLocation, $_GET['from'], null, $_GET['score']);
    }
    
    private function renderCommentsPreview(){
        $commStats = new CommentsStatistics($this->settings->getSettingsFileLocation());
        $commStats->renderCommentsPreview();
    }
    
    private function renderCommentsReviewPanel(){
        $comments = new Comments($this->settings->getSettingsFileLocation());
        $comments->renderCommentsReviewPanel($this->processorLocation);
    }
    
    private function renderAddingSupageForm(): void{
        SubpageEditor::renderSubpageCreator($this->processorLocation);
    }
    
    private function renderListOfSubpages(){
        $subpageEditor = new SubpageEditor;
        $subpageEditor->renderListOfSubpages();
    }
    
    private function renderAddingCategoryForm(){
        Categories::renderAddingForm($this->processorLocation);
    }
    
    private function renderRemovingCategoryfForm(){
        Categories::renderRemovalForm($this->processorLocation);
    }
    
    
    
    private function handleAction(?string $action){
        switch ($action){
            case "addEntry":
                $this->renderNewEntryForm();
            break;
                
            case "entryList":
                $this->renderListOfArticles();
            break; 
            
            case "commentStats":
                $this->renderCommentsStatictics();
            break;
            
            case "normalStats":
                $this->renderVisitsStatistics();
            break;
            
            case "addSubpage":
                $this->renderAddingSupageForm();
            break;
            
            case "listSubpages":
                $this->renderListOfSubpages();
            break;
            
            case "addOption":
                $this->renderNewMenuElementForm();
            break; 
                
            case "addCategory":
                $this->renderAddingCategoryForm();
            break;
                
            case "removeCategory":
                $this->renderRemovingCategoryfForm();
            break;
            
            case "editOptions":
                $this->renderListOfMenuOptions();
            break; 
            
            case "settings":
                $this->renderSettingsEditor();
            break;
            
            case "errorLog":
                $this->renderErrorLog();
            break;
                
            case "10lastComments":
                $this->renderCommentsPreview();
            break;
                
            case "commentsReviewPanel":
                $this->renderCommentsReviewPanel();
            break;
                
            default:
                $this->renderNewEntryForm();
            break;
        }
    }
    
    public function renderPanel($action): void{
        $this->page->renderHead();
        
        $adminMenu = new AdminMenu();
        $adminMenu->renderMenu();
        
        
        $this->handleAction($action);
        
        $this->page->renderFooter();
    }
}