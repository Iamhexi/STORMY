<?php

trait UrlGenerator {

	protected function sanitizeInput(string $input): string {
	    return filter_var($input, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	}

	public function generateUrlFromTitle(string $title): string {
        $title = $this->sanitizeInput($title);
        $title = preg_replace('/\s+/', '_', $title);
        $title = strtolower($title);
		$title = trim($title);

        if ($title == null)
            throw new Exception
			('The illegal URL has been generated due to fact that title is incorrect.
				It has to contain numeric values, dashes, underscores and letters only!');


        return $this->transliterateString($title);
    }

    private function transliterateString(string $title) {
        $transliterationTable = array (
            'ą' => 'a',
			'Ą' => 'a',
            'ć' => 'c',
			'Ć' => 'c',
            'ę' => 'e',
			'Ę' => 'e',
            'ł' => 'l',
			'Ł' => 'l',
			'ń' => 'n',
			'Ń' => 'n',
            'ś' => 's',
			'Ś' => 's',
			'ó' => 'o',
			'&oacute;' => 'o',
			'&Oacute;' => 'o',
			'Ó' => 'o',
            'ź' => 'z',
			'Ź' => 'z',
            'ż' => 'z',
			'Ż' => 'z',
            '?' => '_',
            '=' => '_',
            '.' => '_'
        );

        return str_replace(array_keys($transliterationTable), array_values($transliterationTable), $title);
    }

}
