<?php

trait UrlGenerator {
	
	private function generateUrlFromTitle(string $title): string {
        $title = $this->sanitizeInput($title);
        $title = preg_replace('/\s+/', '', $title);
        $title = strtolower($title);

        if ($title == null)
            throw new Exception('The illegal URL has been generated due to fact that title is incorrect. It has to contain numeric values, dashes, underscores and letters only!');
            

        return $this->transliterateString($title);
    }

    private function transliterateString(string $title) {
        $transliterationTable = array (
            'ą' => 'a',
            'ć' => 'c',
            'ę' => 'e',
            'ł' => 'l',
            'ó' => 'o',
            'ś' => 's',
            'ź' => 'z',
            'ż' => 'z'
        );

        return str_replace(array_keys($transliterationTable), array_values($transliterationTable), $title);
    }
    
}