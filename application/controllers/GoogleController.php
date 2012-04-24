<?php

class GoogleController extends Turbo_Controller_LoggedInAction
{
	private $client_id = '120853944602-j79u0cinskab6ile6gvoin71pc3b9d5i.apps.googleusercontent.com';
	private $client_secret = 'Eme_vSHbukhyu-LNNrnvFgnZ';
	private $application_name = "AutoSquare";
	
	private function _include_google_api(){
		require_once dirname(__FILE__) . '/../../library/simple-api-clients/google.php';
		require_once dirname(__FILE__) . '/../../library/google-api-php-client/src/apiClient.php';
		require_once dirname(__FILE__) . '/../../library/google-api-php-client/src/contrib/apiLatitudeService.php';
	}
	
	private function _set_up_google_api(){
		$this->_include_google_api();
		
		$client = new apiClient();
		// Visit https://code.google.com/apis/console to generate your
		// oauth2_client_id, oauth2_client_secret, and to register your oauth2_redirect_uri.
		$client->setClientId($this->client_id);
		$client->setClientSecret($this->client_secret);
		$client->setRedirectUri('http://'.$_SERVER['SERVER_NAME'].'/Google/Add-Latitude');
		$client->setApplicationName($this->application_name);
		
		if(is_object(Turbo_Model_User::getCurrentUser()->settingGet("google_latitude_access_token"))){
			$token = Turbo_Model_User::getCurrentUser()->settingGet("google_latitude_access_token");
			$client->setAccessToken(json_encode($token));
		}
		$service = new apiLatitudeService($client);
		
		return array($service, $client);
	}
	
	private function _get_latitude_location(){
		list($service, $client) = $this->_set_up_google_api();
		$currentLocation = $service->currentLocation->get();
		return $currentLocation;
	}
	
	private function _get_latitude_locations($count = 100){
		list($service, $client) = $this->_set_up_google_api();
		$location = $service->location->listLocation(array('granularity' => 'best'));
		return $location['items'];
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
		}

		if(isset($authUrl)){
			//$this->_helper->redirector('add-latitude-complete', 'Google');
		}
		$this->view->assign('currentLocation', $currentLocation);
		$this->view->assign('location', $location);
		$this->view->assign('authUrl', $authUrl);
		
	}

	public function addLatitudeCompleteAction(){}
	
	public function updateLocationFeedAction(){
		$recent_locations = $this->_get_latitude_locations();
		$tblUserLocations = new Application_Model_DbTable_UserLocations();
		$user = Turbo_Model_User::getCurrentUser();
		$count_new = 0;
		
		foreach($recent_locations as $recent_location){
			//Test to see if we can find a matching location for this user
			if(!$tblUserLocations->user_location_already_reported($user, $recent_location['timestampMs'])){
				//Insert the location
				$count_new++;
				$tblUserLocations->insert_location($user, $recent_location);
			}
		}
		$this->view->assign('count_seen', count($recent_locations));
		$this->view->assign('count_new', $count_new);
	}
	
}





