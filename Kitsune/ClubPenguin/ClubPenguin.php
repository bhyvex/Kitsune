<?php

namespace Kitsune\ClubPenguin;
use Kitsune;

abstract class ClubPenguin extends Kitsune\Kitsune {

	// Maybe make these protected ?
	private static $xml_handlers = array(
		"policy" => "handlePolicyRequest",
		"verChk" => "handleVersionCheck",
		"rndK" => "handleRandomKey", // A *request* for a random-key
		"login" => "handleLogin"
	);
	
	private static $world_handlers = array(
		// Currently empty
	);
	
	private function handlePolicyRequest($socket, $packet) {
		$this->penguins[$socket]->send("<cross-domain-policy><allow-access-from domain='*' to-ports='*' /></cross-domain-policy>");
	}
	
	private function handleVersionCheck($socket, $packet) {
		$this->penguins[$socket]->send("<msg t='sys'><body action='apiOK' r='0'></body></msg>");
	}
	
	private function handleRandomKey($socket, $packet) {
		$penguin = $this->penguins[$socket];
		$penguin->random_key = Hashing::generateRandomKey();
		$penguin->send("<msg t='sys'><body action='rndK' r='-1'><k>" . $penguin->random_key . "</k></body></msg>");
	}
	
	abstract protected function handleLogin($socket, $packet);
	
	protected function handleXmlPacket($socket, $packet) {
		if(array_key_exists($packet::$handler, self::$xml_handlers)) {
			$invokee = self::$xml_handlers[$packet::$handler];
			call_user_func(array($this, $invokee), $socket, $packet);
		} else {
			echo "Method for {$packet::$handler} not found!\n";
		}
	}
	
	protected function handleWorldPacket($socket, $packet) {
		echo "{$packet::$handler}\n", print_r($packet);
	}
	
}

?>