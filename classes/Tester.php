<?php

class Tester {
    protected ?string $recentMethodName = null;

    public function runTests(Tester $testingClass): void {
        $testingMethods = $this->get_this_class_methods($testingClass);
        foreach ($testingMethods as $method){
            $testingClass->$method();
        }
    }

    public function assertEqual($expected, $received): void {
        $this->initRecentMethodName();
        if ($expected != $received)
            $this->getInfo($expected, $received);
    }

    public function assertFalse($received): void {
        $this->initRecentMethodName();
        if ($received != false)
            $this->getInfo(false, $received);
    }

    public function assertTrue($received): void {
        $this->initRecentMethodName();
        if ($received == false)
            $this->getInfo(true, $received);
    }

    public function assertDifferent($expected, $received): void {
        $this->initRecentMethodName();
        if ($expected == $received)
            $this->getInfo($expected, $received, "!=");
    }

    protected function replaceEmpty(): string {
        return '(empty)';
    }

    protected function getInfo($expected = null, $received = null, string $operator = '=='): void {
        echo 'TEST FAILED: '.$this->recentMethodName.'()'.PHP_EOL;
        if ($received == null || $received == '')
            $received = $this->replaceEmpty();
        if ($expected == null || $expected == '')
            $expected = $this->replaceEmpty();
        echo 'Expected: '.$expected.PHP_EOL;
        echo 'Received: '.$received.PHP_EOL;
        echo "Which evaluates to: $expected $operator $received";
    }

    protected function initRecentMethodName(): void {
        $name = debug_backtrace()[2]['function'];
        if (isset($name))
            $this->recentMethodName = $name;
        else
            $this->recentMethodName = null;
    }

    protected function get_this_class_methods($class): array {
        $array1 = get_class_methods($class);
        if($parent_class = get_parent_class($class)){
            $array2 = get_class_methods($parent_class);
            $array3 = array_diff($array1, $array2);
        }else{
            $array3 = $array1;
        }
        return($array3);
    }

}
