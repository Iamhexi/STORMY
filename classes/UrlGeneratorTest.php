<?php
require_once 'ClassAutoLoader.php';
$autoLoader = new ClassAutoLoader();

class UrlGen {
    use UrlGenerator;
}

class UrlGeneratorTest extends Tester {
    public function testGenerateUrlFromTitle(): void {
        $urlGen = new urlGen;
        $title = 'Bżęg Óżćń.';
        $url = 'bzeg_ozcn_';

        $this->assertEqual($url, $urlGen->generateUrlFromTitle($title));
    }
}

$tester = new UrlGeneratorTest;
$tester->runTests($tester);
