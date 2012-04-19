<?php

class MeController extends Zend_Controller_Action
{
	public function indexAction(){
		
	}
	
	public function addlatitudeAction(){
		$CONSUMER_KEY = '120853944602-j79u0cinskab6ile6gvoin71pc3b9d5i.apps.googleusercontent.com';
		$CONSUMER_SECRET = 'Eme_vSHbukhyu-LNNrnvFgnZ';
		
		// Multi-scoped token.
		$SCOPES = array(
		  'https://docs.google.com/feeds/',
		  'https://spreadsheets.google.com/feeds/'
		);
		
		$oauthOptions = array(
		  'requestScheme' => Zend_Oauth::REQUEST_SCHEME_HEADER,
		  'version' => '1.0',
		  'consumerKey' => $CONSUMER_KEY,
		  'consumerSecret' => $CONSUMER_SECRET,
		  'signatureMethod' => 'HMAC-SHA1',
		  'callbackUrl' => 'http://myapp.example.com/access_token.php',
		  'requestTokenUrl' => 'https://www.google.com/accounts/OAuthGetRequestToken',
		  'userAuthorizationUrl' => 'https://www.google.com/accounts/OAuthAuthorizeToken',
		  'accessTokenUrl' => 'https://www.google.com/accounts/OAuthGetAccessToken'
		);
		
		$consumer = new Zend_Oauth_Consumer($oauthOptions);
		
		// When using HMAC-SHA1, you need to persist the request token in some way.
		// This is because you'll need the request token's token secret when upgrading
		// to an access token later on. The example below saves the token object as a session variable.
		if (!isset($_SESSION['ACCESS_TOKEN'])) {
		  $_SESSION['REQUEST_TOKEN'] = serialize($consumer->getRequestToken(array('scope' => implode(' ', $SCOPES))));
		}
		// If on a Google Apps domain, use your domain for the hd param (e.g. 'example.com').
		$consumer->redirect(array('hd' => 'default'));
	}
   
}





