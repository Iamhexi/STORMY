<?php

require_once 'ClassAutoLoader.php';
$autoLoader = new ClassAutoLoader();

interface iNewsletter {
    function renderEmails(): void;
    function getMailingListAsString(): ?string;
    function sendMail(string $subject, string $message);
    function addEmail(string $email): bool;
    function removeEmail(string $email): bool;
    function renderFrom(string $destinationFile): void;
}


$settingsForMail = new PageSettings();
define("NEWSLETTER_EMAIL", $settingsForMail->__get("newsletterEmail"));
define("TITLE", $settingsForMail->__get("title"));

class Newsletter implements iNewsletter{
    use DatabaseControl;
    
    private $emailsFile = 'mailing.txt';
    private $mailingList;
    
    public function __construct($emailsFile = null){
        if ($emailsFile != null) $this->emailsFile = $emailsFile;
        $this->loadEmails();
    }
    
    private function loadEmails(){
        try {
            $this->loadMailingList();
        } catch (Exception $e){
            $this->reportException($e);
        }
    }
    
    private function loadMailingList(): ?Exception{
        if (@!$lines = file($this->emailsFile))
            throw new Exception("Couldn't open mailing list from the file $this->emailsFile or the file is empty!");
        
        $this->mailingList = $lines;
        return null;
    }
    
    public function renderEmails(): void{
        if (!empty($this->mailingList)){
            echo '<ol class="emailList">';
            foreach ($this->mailingList as $email)
                echo "<li class=\"email\">$email</li>";
            echo '</ol>';
        }       
    }
    
    public function getMailingListAsString(): ?string{
        return implode(', ', $this->mailingList);
    }
    
    public function sendMail(string $subject, string $message){
        $title = TITLE;    
        $newsletterEmail = NEWSLETTER_EMAIL;    

        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=utf-8';
        $headers[] = "From: $title <$newsletterEmail>";
        $headers[] = "Bcc: $newsletterEmail";

        mail($this->getMailingListAsString(), $subject, $message, implode("\r\n", $headers));
    }
    
    private function verifyEmail(string $email): bool{
        if (empty(filter_var($email, FILTER_VALIDATE_EMAIL))) return false;
        else return true;
    }
    
    private function addEmailToFile(): void{
        if (@!($this->verifyEmail($email)))
            throw new Exception("E-mail given by user incorrect!");
        if (@!(file_put_contents($this->emailsFile, $email."\n", FILE_APPEND | LOCK_EX)))
            throw new Exception("Couldn't save a new e-mail from user in the mailing file!");
    }
    
    public function addEmail(string $email): bool{ // subscribe to newsletter
        try {
            $this->addEmailToFile();
            return true;
        } catch (Exception $e){
            $this->reportException($e);
            return false;
        }
    }
    
    private function saveMailingList(): bool{
        try {
            if (@!(file_put_contents($this->emailsFile, $mailingList, LOCK_EX)))
                throw new Exception("Couldn't save mailing list!");
            return true;
        } catch (Exception $e){
            $this->reportException($e);
            return false;
        }
    }
    
    private function removeEmailFromMailingList(string $wantedEmail): bool{
       $this->loadEmails();
        
       foreach ($this->mailingList as $email){
           $email = preg_replace('/\s+/', '', $email);
           
            if ($email == $wantedEmail){ 
               $email = null;
               $this->saveMailingList();
               return true;
           }
       } 
        
        return false;
    }
    
    public function removeEmail(string $email): bool{ // unsubscribe from newsletter
        try {
            if ($this->removeEmailFromMailingList($email) === false) 
                throw new Exception("Couldn't remove the email requested by user from the mailing list. Given e-mail doesn't exist or was removed before.");
            return true;
        } catch (Exception $e){
            $this->reportException($e);
            return false;
        }
    }
    
    public function renderFrom(string $destinationFile): void{
        echo<<<END
            <form class="newsletterForm" action="$destinationFile" method="POST">
                <div><label>Twój e-mail<input class="newsletterEmail" type="email" name="email" placeholder="john.smith@mail.com" required></label></div>
                <div><input class="newsletterButton" type="submit" value="Zapisz się!"></div>
            </form>
            
        
END;
    }
    
}