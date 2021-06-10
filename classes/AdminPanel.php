<?php

require_once 'ClassAutoLoader.php';
$autoLoader = new ClassAutoLoader();

interface iAdminPanel {
    function renderPanel(?string $action): void;
}

class AdminPanel {

    private string $processorLocation = 'processor.php';
    private PageSettings $settings;
    private Page $page;
    private AdminAuth $adminAuth;

    public function __construct(){

        @$this->adminAuth = new AdminAuth($_SESSION['isLogged']);
        $this->adminAuth->handleloggingOut();
        @$this->adminAuth->controlAccess();

        $this->settings = new PageSettings('../settings/default.json');
        $this->page = new Page($this->settings, '..');
    }

    private function renderNewEntryForm(): void {
        AddingArticle::renderForm();
    }

    private function renderErrorLog(): void {
        $errorLog = new ErrorLog();
        $errorLog->renderErrorLog();
    }

    private function renderListOfArticles(): void {
        $articleGrid = new ThumbnailView();
        $articleGrid->renderThumbnails(null, true);
    }

    private function renderListOfMenuOptions(): void {
        $this->page->renderMenu(true, $this->processorLocation);
    }

    private function renderNewMenuElementForm(): void {
        Menu::renderAddingElementForm($this->processorLocation);
    }

    private function renderSettingsEditor(): void {
        $this->settings->renderEditor($this->processorLocation);
    }

    private function renderVisitsStatistics(): void {
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

    private function renderAddingCategoryForm(): void{
        Categories::renderAddingForm($this->processorLocation);
    }

    private function renderRemovingCategoryfForm(): void{
        Categories::renderRemovalForm($this->processorLocation);
    }

    private function renderErrorPrompt(): void{
        echo '<div class="prompt fail">Nie udało się dokończyć wybranego działania.</div>
              <a class="returnButtonAfterExit" href="panel.php">Powrót</a>';
    }

    private function renderEntryAndSubpageEditor(): void {
        if (isset($_GET['url'])){
            $EditingArticle = new EditingArticle($_GET['url']);
            $EditingArticle->renderEditor($this->processorLocation);
        }

        else if (isset($_GET['purl'])){
            $subpageEditor = new SubpageEditor;
            $subpageEditor->renderEditor($this->processorLocation, $_GET['purl']);
        }
    }

    private function handleAction(?string $action){
        switch ($action){
            case 'addEntry':
                $this->renderNewEntryForm();
            break;

            case 'entryList':
                $this->renderListOfArticles();
            break;

            case 'entryAndSubpageEditor':
                $this->renderEntryAndSubpageEditor();
            break;

            case 'commentStats':
                $this->renderCommentsStatictics();
            break;

            case 'normalStats':
                $this->renderVisitsStatistics();
            break;

            case 'addSubpage':
                $this->renderAddingSupageForm();
            break;

            case 'listSubpages':
                $this->renderListOfSubpages();
            break;

            case 'addOption':
                $this->renderNewMenuElementForm();
            break;

            case 'addCategory':
                $this->renderAddingCategoryForm();
            break;

            case 'removeCategory':
                $this->renderRemovingCategoryfForm();
            break;

            case 'editOptions':
                $this->renderListOfMenuOptions();
            break;

            case 'settings':
                $this->renderSettingsEditor();
            break;

            case 'errorLog':
                $this->renderErrorLog();
            break;

            case '10lastComments':
                $this->renderCommentsPreview();
            break;

            case 'commentsReviewPanel':
                $this->renderCommentsReviewPanel();
            break;

            case 'error':
                $this->renderErrorPrompt();
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
