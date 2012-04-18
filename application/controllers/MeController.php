<?php

class MeController extends Zend_Controller_Action
{
	public function indexAction(){
		
	}
	
	public function addlatitudeAction(){
		$my_latitude = 'https://www.googleapis.com/auth/latitude.all.best';
		
		if (!isset($_SESSION['cal_token'])) {
			if (isset($_GET['token'])) {
				// You can convert the single-use token to a session token.
				$session_token =
				Zend_Gdata_AuthSub::getAuthSubSessionToken($_GET['token']);
				// Store the session token in our session.
				$_SESSION['cal_token'] = $session_token;
			} else {
				// Display link to generate single-use token
				$googleUri = Zend_Gdata_AuthSub::getAuthSubTokenUri(
						'http://'. $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'],
						$my_latitude, 0, 1);
				echo "Click <a href='$googleUri'>here</a> " .
				"to authorize this application.";
				exit();
			}
		}
		
		// Create an authenticated HTTP Client to talk to Google.
		$client = Zend_Gdata_AuthSub::getHttpClient($_SESSION['cal_token']);
		
		// Create a Gdata object using the authenticated Http Client
		$cal = new Zend_Gdata_Calendar($client);
	}
   
}





