<?php

class ClassAutoLoader {

    private ?string $customPath;

    public function __construct(?string $customPath = null) {
        $this->customPath = $customPath;
        spl_autoload_register('self::autoLoad');
    }

    private function autoLoad(string ...$classes): void {
        foreach ($classes as $class)
            require $this->customPath.$class.'.php';
    }
}
