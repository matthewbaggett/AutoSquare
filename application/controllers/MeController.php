<?php

class MeController extends Zend_Controller_Action
{
	private function _include_google_api(){
		require_once dirname(__FILE__) . '/../../library/google-api-php-client/src/apiClient.php';
		require_once dirname(__FILE__) . '/../../library/google-api-php-client/src/contrib/apiLatitudeService.php';
	}
	
	private function _include_foursquare_api(){
		require_once dirname(__FILE__) . "/../../library/foursquare-api-client/foursquare.php";
	}
	
	private function _set_up_google_api(){
		$this->_include_google_api();
		
		$client = new apiClient();
		// Visit https://code.google.com/apis/console to generate your
		// oauth2_client_id, oauth2_client_secret, and to register your oauth2_redirect_uri.
		$client->setClientId('120853944602-j79u0cinskab6ile6gvoin71pc3b9d5i.apps.googleusercontent.com');
		$client->setClientSecret('Eme_vSHbukhyu-LNNrnvFgnZ');
		$client->setRedirectUri('http://'.$_SERVER['SERVER_NAME'].'/Me/Add-Latitude');
		$client->setApplicationName("AutoSquare");
		
		if(is_object(Turbo_Model_User::getCurrentUser()->settingGet("google_latitude_access_token"))){
			$token = Turbo_Model_User::getCurrentUser()->settingGet("google_latitude_access_token");
			$client->setAccessToken(json_encode($token));
		}
		$service = new apiLatitudeService($client);
		
		return array($service, $client);
	}
	
	private function _set_up_foursquare_api(){
		$this->_include_foursquare_api();
		
	}
	
	private function _get_latitude_location(){
		list($service, $client) = $this->_set_up_google_api();
		$currentLocation = $service->currentLocation->get();
		return $currentLocation;
	}
	
	public function indexAction(){
		
	}
	
	public function showSessionAction(){
		$google_latitude_access_token = Turbo_Model_User::getCurrentUser()->settingGet("google_latitude_access_token");
		$this->view->assign("google_latitude_access_token",$google_latitude_access_token);
	}
	
	public function latitudeGetLocationAction(){
		$this->view->assign('currentLocation', $this->_get_latitude_location());
	}
	
	public function addLatitudeAction(){
		list($service, $client) = $this->_set_up_google_api();
		
		if (isset($_GET['code'])) {
			$client->authenticate();
			$_SESSION['access_token'] = $client->getAccessToken();
			$redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
		}
		
		if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
			$client->setAccessToken($_SESSION['access_token']);
		} else {
			$authUrl = $client->createAuthUrl();
		}
		
		if ($client->getAccessToken()) {
			// Start to make API requests.
			//$location = $service->location->listLocation();
			$currentLocation = $service->currentLocation->get();
			$_SESSION['access_token'] = $client->getAccessToken();
			Turbo_Model_User::getCurrentUser()->settingSet("google_latitude_access_token", json_decode($client->getAccessToken()));
			//$this->_helper->redirector('add-latitude-complete', 'Me');
		}

		$this->view->assign('currentLocation', $currentLocation);
		$this->view->assign('location', $location);
		$this->view->assign('authUrl', $authUrl);
		
	}
	
	public function addFoursquareAction(){
		$this->_include_foursquare_api();
		$fsq = new foursquare_api();
		$fsq->redirect_get_access_token();
	}
	
	public function addFoursquareCallbackAction(){
		$this->_include_foursquare_api();
		$fsq = new foursquare_api();
		$access_token = $fsq->get_access_token($_GET['code']);
		Turbo_Model_User::getCurrentUser()->settingSet('foursquare_access_token',$access_token);
		$this->_helper->redirector('add-foursquare-complete', 'Me');
	}

	public function addFoursquareCompleteAction(){}
	public function addLatitudeCompleteAction(){}
	
}





