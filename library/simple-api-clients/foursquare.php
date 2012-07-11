<?php 

class foursquare_api extends Turbo_Api_Base{
	private $client_id = 'A0E0UFLSVUV45SMMCD0O0J4OIO0I3OFJJB5U3SZ0IFSVNYJC';
	private $client_secret = '2UZWRFA2WL2UK4N1LCF3SBLPLNF0IBMI4BGD04S1QWC0VNTA';
	private $redirect_uri = 'https://gamitu.de/Foursquare/add-foursquare-callback';
	
	public function redirect_get_access_token(){
		$redirect_url = "https://foursquare.com/oauth2/authenticate?client_id={$this->client_id}&response_type=code&redirect_uri={$this->redirect_uri}";
		header("Location: $redirect_url");
		exit;
	}
	
	public function get_access_token($code){
		$request_url = "https://foursquare.com/oauth2/access_token?client_id={$this->client_id}&client_secret={$this->client_secret}&grant_type=authorization_code&redirect_uri={$this->redirect_uri}&code={$code}";
		$response = $this->curl($request_url);
		
		return json_decode($response)->access_token;
	}
	
	public function make_request($api_path){
		users/USER_ID/venuehistory
	}
}