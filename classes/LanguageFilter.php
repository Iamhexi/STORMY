<?php 

require_once 'ClassAutoLoader.php';
$autoLoader = new ClassAutoLoader();

class LanguageFilter {
	use DatabaseControl;

	private string $wordlistLocation = 'settings/bannedWords.txt';
	private array $bannedWords;

	public function __construct(){

	}

	public function isVulgar(string $toCheck): bool {
		try {

			$this->loadBannedWords();
			$toCheck = $this->prepareString($toCheck);
			return $this->containsBannedWord($toCheck);

		} catch (Exception $e){

			$this->reportException($e);
			return false;

		}
	}

	private function prepareString(string $toCheck): string {
		$toCheck = preg_replace('/[[:punct:]]/', '', $toCheck);
		return strtolower($toCheck);
	}

	private function containsBannedWord(string $toCheck): bool {
		foreach ($this->bannedWords as $vulgar){
			if (preg_match_all('/'.$vulgar.'/', $toCheck) >= 1)
				return true;
		}

		return false;
	}

	private function loadBannedWords(): void {
		if (!$this->bannedWords = file($this->wordlistLocation, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)
		   )
			throw new Exception("Couldn't load the wordlist containing banned words!");
	}
}