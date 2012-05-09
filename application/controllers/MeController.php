<?php

class MeController extends Turbo_Controller_LoggedInAction
{
	
	public function indexAction(){
		
	}
	
	public function showSessionAction(){
		$google_latitude_access_token = Turbo_Model_User::getCurrentUser()->settingGet("google_latitude_access_token");
		$this->view->assign("google_latitude_access_token",$google_latitude_access_token);
	}
	
	public function myKeysAction(){
		$this->view->assign("google_latitude_access_token", Turbo_Model_User::getCurrentUser()->settingGet("google_latitude_access_token"));
		$this->view->assign("foursquare_access_token", Turbo_Model_User::getCurrentUser()->settingGet("foursquare_access_token"));
	}
	
	public function mapAction(){
		$this->view->headScript()->append("http://maps.googleapis.com/maps/api/js?key=AIzaSyAeDI_T5MhRJtykibKEqszGZAxxGB3iaTg&sensor=true");
		$this->view->headScript()->append($this->baseUrl() . "/application-me-map.js");
		
	}
}





