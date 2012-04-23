<?php 

class google_api extends Turbo_Api_Base{
	private $token;
	private $id;
	private $secret;
	
	public function __construct($token, $id, $secret){
		$this->token = $token;
		$this->id = $id;
		$this->secret = $secret;
	}
	
	public function get_locations($count = 1){
		$url = "https://www.googleapis.com/latitude/v1/location?key={$this->token}";
		$response = $this->curl($url);
		$response = json_decode($response);
		print_r($response);
		exit;
		return $response;
	}
}