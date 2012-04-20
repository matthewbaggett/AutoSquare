<?php

class MeController extends Zend_Controller_Action
{
	public function indexAction(){
		
	}
	public function showsessionAction(){
		print_r($_SESSION);
	}
	
	public function addlatitudeAction(){
		
		
		require_once dirname(__FILE__) . '/../../library/google-api-php-client/src/apiClient.php';
		require_once dirname(__FILE__) . '/../../library/google-api-php-client/src/contrib/apiLatitudeService.php';
		
		$client = new apiClient();
		// Visit https://code.google.com/apis/console to generate your
		// oauth2_client_id, oauth2_client_secret, and to register your oauth2_redirect_uri.
		$client->setClientId('120853944602-j79u0cinskab6ile6gvoin71pc3b9d5i.apps.googleusercontent.com');
		$client->setClientSecret('Eme_vSHbukhyu-LNNrnvFgnZ');
		$client->setRedirectUri('http://'.$_SERVER['SERVER_NAME'].'/Me/AddLatitude');
		$client->setApplicationName("AutoSquare");
		$service = new apiLatitudeService($client);
		
		if (isset($_REQUEST['logout'])) {
			unset($_SESSION['access_token']);
		}
		
		if (isset($_GET['code'])) {
			$client->authenticate();
			$_SESSION['access_token'] = $client->getAccessToken();
			$redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
			//header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
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
			Zend_Auth::getInstance()->getIdentity()->settingSet("google_latitude_access_token",$client->getAccessToken());
		}

		$this->view->assign('currentLocation', $currentLocation);
		$this->view->assign('location', $location);
		$this->view->assign('authUrl', $authUrl);
		
			
	}
   
}





