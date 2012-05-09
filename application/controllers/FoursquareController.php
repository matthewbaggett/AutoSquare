<?php

class FoursquareController extends Turbo_Controller_LoggedInAction
{

	private function _include_foursquare_api(){
		require_once dirname(__FILE__) . "/../../library/simple-api-clients/foursquare.php";
	}
	
	private function _set_up_foursquare_api(){
		$this->_include_foursquare_api();
		
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
		Application_Model_User::getCurrentUser()->settingSet('foursquare_access_token',$access_token);
		$this->_helper->redirector('add-foursquare-complete');
	}

	public function addFoursquareCompleteAction(){}
	
	
}





