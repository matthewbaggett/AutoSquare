<?php 

class foursquare_api{
	private $client_id = 'A0E0UFLSVUV45SMMCD0O0J4OIO0I3OFJJB5U3SZ0IFSVNYJC';
	private $client_secret = '2UZWRFA2WL2UK4N1LCF3SBLPLNF0IBMI4BGD04S1QWC0VNTA';
	private $redirect_uri = 'http://autosquare.turbocrms.com/Me/add-foursquare-callback';
	
	public function redirect_get_access_token(){
		$redirect_url = "https://foursquare.com/oauth2/authenticate?client_id={$this->client_id}&response_type=code&redirect_uri={$this->redirect_uri}";
		header("Location: $redirect_url");
		exit;
	}
}