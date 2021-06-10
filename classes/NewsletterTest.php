<?php
declare(strict_types=1);
require_once 'ClassAutoLoader.php';
$autoLoader = new ClassAutoLoader();

class NewsletterTest extends Tester {
    public function testAddEmailAddressAndGetMailingListAsString(): void {
        $tested = new Newsletter(null);
        $email = 'example@example.example';

        $this->assertTrue($tested->addEmailAddress($email));

        $contains = str_contains($tested->getMailingListAsString());

        $this->assertTrue($contains);
    }
}

$tester = new NewsletterTest;
$tester->runTests($tester);
