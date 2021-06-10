<?php

require_once 'ClassAutoLoader.php';
$autoLoader = new ClassAutoLoader();

@session_start();

interface TwoFactorAuthentication {
    function renderTwoFactorAuthForm(string $destination): void;
    function demandAuthenticationViaEmail(): bool;
    function isBrowserAuthenticated(): bool;
}

class TwoFactorAuth implements TwoFactorAuthentication {

    private ?string $adminEmail;
    private ?string $webpageUrl;

    public function __construct(string $settingsLocation = "../settings/default.json"){
        $settings = new PageSettings($settingsLocation);
        $this->adminEmail = $settings->__get('adminEmail');
        $this->webpageUrl = $settings->__get('url');
    }

    public function renderTwoFactorAuthForm(string $destination): void{

        $partiallyHiddenEmail = $this->replaceLettersWithAsterisks($this->adminEmail);
        echo<<<END
        <article class="authenticationWrapper">
            <header class="header">Chcemy potwierdzić Twoją tożsamość</header>
            <p class="authenticationDescription">Aby sprawdzić, czy jesteś osobą, za którą się podajesz, wysłaliśmy Ci wiadomość na adres e-mail: <b>$partiallyHiddenEmail</b>.<br> Sprawdź proszę swoją skrzynkę odbiorczą i wprowadź otrzymany kod potwierdzający. W razie problemów spróbuj zalogować się, używając jednej ze wcześniej zweryfikowanych przeglądarek.</p>
            <form action="$destination" method="POST" class="authenticationForm">
                <div><label><span>Kod potwierdzający</span><input type="text" name="authenticationCode"></label></div>
                <div><input type="submit" value="Zaloguj się" class="button"></div>
            </form>
        </article>
END;
    }

    private function replaceLettersWithAsterisks(string $email): string {
        $length = strlen($email);
        for ($i=0;$i<$length;$i++)
            if ($i >= 2 && $i <= 10){
                if ($email[$i] === '@')
                    break;
                $email[$i] = '*';
            }

        return $email;
    }

    private function checkStatusOfEmailCode(int $emailCode): int { // 0 - isn't set yet, -1 - incorrect, 1 - correct
        $correctCode = $_SESSION['emailVerificationCode'];
        if (!isset($correctCode) || empty($correctCode))
            return 0;

        return ($emailCode == $correctCode) ? 1 : -1;
    }

    private function isVerficationNumberValid(int $verificationNumber): bool{
        if ($verificationNumber >= 100000 && $verificationNumber <= 999999)
            return true;
        return false;
    }

    public function isBrowserAuthenticated(): bool{
        if (isset($_COOKIE['STORMY_2FA_TOKEN']) && !empty($_COOKIE['STORMY_2FA_TOKEN']))
            return $this->isVerficationNumberValid($_COOKIE['STORMY_2FA_TOKEN']);
        else
            return false;
    }

    public function demandAuthenticationViaEmail(): bool{
        $verificationCode = $this->generateEmailVerificationCode();
        if ($this->sendVerificationCodeViaEmail($verificationCode)){
            $_SESSION['emailVerificationCode'] = $verificationCode;
            return true;
        }

        return false;
    }

    private function sendAuthenticationEmail(): void{
        if ($this->demandAuthenticationViaEmail()){
            echo '<div class="prompt success">Wysłano e-mail z kodem potwierdzającym</div>';
            $_SESSION['alreadySent'] = true;
        }

        else
            echo '<div class="prompt fail">Nie udało się wysłać e-maila z kodem potwierdzającym - Spróbuj odświeżyć stronę!</div>';
    }

    public function sendEmail(): void{
        if (@$_SESSION['alreadySent'] == true)
            echo '<div class="prompt fail">Już wysłano e-mail z kodem potwierdzającym - odczekaj minumum 5 minut przed wysłaniem kolejnej prośby o kod.</div>';
        else
            $this->sendAuthenticationEmail();
    }

    public function isVerificationCodeCorrect($code = null): int{
        if (!isset($code) || empty($code) || $code == null || !is_numeric($code))
            return 0;

        return $this->checkStatusOfEmailCode($code);
    }

    public function authenticateBrowser(): void{
        $authenticationId = rand(100000, 999999);
        setcookie('STORMY_2FA_TOKEN', $authenticationId, time()+60*60*24*365*10);
    }

    private function generateEmailVerificationCode(): int{
        if (!isset($_SESSION['emailVerificationCode']) || empty($_SESSION['emailVerificationCode']))
            return rand(100000, 999999);
    }

    private function sendVerificationCodeViaEmail(int $verificationCode): bool{

        $to = $this->adminEmail;
        $subject = "[$this->webpageUrl] Kod potwierdzający";
        $message = 'Witaj!<br>Oto Twój kod potwierdzający: <b>';
        $message .= (string)$verificationCode;
        $message .= '</b>.<p style="text-align: justify; max-width: 500px;">Teraz mamy pewność, że faktycznie jesteś osobą, za którą się podajesz. Zapamiętamy Twoje logowanie w tej przeglądarce i odtąd nie będzie wymagane ponowne potwierdzanie  tożsamości. Miłego korzystania ze STORMY, drogi administratorze!</p><br>';

        $message .= '<p style="text-align: justify; max-width: 500px;">Uwaga! <b>Jeżeli to nie Ty logowałeś się z nowego urządzenia</b>, możesz być ofiarą próby włamania. Potencjalny włamywacz przejął Twoje hasło i usiłuje się zalogować do panelu administratora Twojej strony. Nie masz się jednak czego obawiać - STORMY zablokował próbę włamania. Jak najszybciej zmień hasło i przeskanuj swoje urządzenie w poszukiwaniu złośliwego oprogramowania.</p>';

        $message .= '<br>Ten e-mail został wygenerowany automatycznie przez: '.$this->webpageUrl;


        $headers = array(
        'From' => $this->adminEmail,
        'Reply-To' => $this->adminEmail,
        'X-Mailer' => 'PHP '.phpversion(),
        'Content-type' => 'text/html; charset=utf-8'
        );

        return @mail($to, $subject, $message, $headers);
    }

}
