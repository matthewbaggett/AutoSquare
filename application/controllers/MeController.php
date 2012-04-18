<?php

class MeController extends Zend_Controller_Action
{
	public function indexAction(){
		
	}
	
	public function addlatitudeAction(){
		$oauthOptions = array(
		    'requestScheme'        => Zend_Oauth::REQUEST_SCHEME_HEADER,
		    'version'              => '1.0',
		    'consumerKey'          => 'autosquare.turbocrms.com',
		    'consumerSecret'       => 'Eme_vSHbukhyu-LNNrnvFgnZ',
		    'signatureMethod'      => 'HMAC-SHA1',
		    'requestTokenUrl'      => 'https://www.google.com/accounts/OAuthGetRequestToken',
		    'userAuthorizationUrl' => 'https://www.google.com/latitude/apps/OAuthAuthorizeToken',
		    'accessTokenUrl'       => 'https://www.google.com/accounts/OAuthGetAccessToken',
		    'callbackUrl'          => 'http://autosquare.turbocrms.com:8080/Me/AddLatitude/?show=callback',
		);
		$consumer = new Zend_Oauth_Consumer($oauthOptions); 
		if (!isset($_SESSION['ACCESS_TOKEN_GOOGLE'])) { 
		    if (!empty($_GET)) { 
		        $token = $consumer->getAccessToken($_GET, unserialize($_SESSION['REQUEST_TOKEN_GOOGLE'])); 
		        $_SESSION['ACCESS_TOKEN_GOOGLE'] = serialize($token); 
		    } else { 
		        $token = $consumer->getRequestToken(array('scope'=>'https://www.googleapis.com/auth/latitude')); 
		        $_SESSION['REQUEST_TOKEN_GOOGLE'] = serialize($token); 
		        $customparams = array('domain' => 'autosquare.turbocrms.com', 'granularity' => 'best', 'location' => 'current');
		        $consumer->redirect($customparams ); 
		        exit; 
		    } 
		} else { 
		    $token = unserialize($_SESSION['ACCESS_TOKEN_GOOGLE']); 
		    //$_SESSION['ACCESS_TOKEN_GOOGLE'] = null; // do not use, we want to keep the access token
		} 
		$client = $token->getHttpClient($oauthOptions); 
		$client->setUri('https://www.googleapis.com/latitude/v1/currentLocation'); 
		$client->setMethod(Zend_Http_Client::GET); 
		
		
		$response = $client->request(); 
		$body = $response->getBody();
		header('Content-Type: ' . $response->getHeader('Content-Type')); 
		echo $response->getBody(); 
			
	}
   
}





