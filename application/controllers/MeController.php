<?php

class MeController extends Turbo_Controller_LoggedInAction
{
	
	public function indexAction(){
		
	}
	
	public function showSessionAction(){
		$google_latitude_access_token = Application_Model_User::getCurrentUser()->settingGet("google_latitude_access_token");
		$this->view->assign("google_latitude_access_token",$google_latitude_access_token);
	}
	
	public function myKeysAction(){
		$this->view->assign("google_latitude_access_token", Application_Model_User::getCurrentUser()->settingGet("google_latitude_access_token"));
		$this->view->assign("foursquare_access_token", Application_Model_User::getCurrentUser()->settingGet("foursquare_access_token"));
	}
	
	public function mapAction(){
	
		$tblUserLocations = new Game_Model_DbTable_UserLocations();
		
		$sel = $tblUserLocations->select();
		$sel->where('intUserID = ?', Application_Model_User::getCurrentUser()->intUserID);
		$sel->order('dtmTimestamp DESC');
		$sel->limit(10000);
		
		$this->view->assign("arr_locations",$tblUserLocations->fetchAll($sel));
		
		$this->view->assign("arr_locations_latlongs",$this->_get_latlongs($this->view->arr_locations));

		$this->view->headScript()->appendScript("var waypoints = " . json_encode($this->view->arr_locations_latlongs));
		$this->view->headScript()->appendFile("http://maps.googleapis.com/maps/api/js?key=AIzaSyAeDI_T5MhRJtykibKEqszGZAxxGB3iaTg&sensor=true");
		$this->view->headScript()->appendFile($this->view->baseUrl() . "/js/application-me-map.js");
		$this->view->headLink()->appendStylesheet($this->view->baseUrl() . "/css/application-me-map.css");
	}
	
	protected function _get_latlongs($arr_locations){
		$arr_locations_latlongs = array();
		foreach($arr_locations as $obj_user_location){
			$arr_locations_latlongs[] = array(
					"location" => "{$obj_user_location->locLatitude},{$obj_user_location->locLongitude}",
					"lat" => $obj_user_location->locLatitude,
					"lng" => $obj_user_location->locLongitude,
					"stopover" => "true",
					"title" =>  (isset($obj_user_location->numSpeed)?"{$obj_user_location->dtmTimestamp} @ {$obj_user_location->numSpeed} mph":$obj_user_location->dtmTimestamp),
					'id' => $obj_user_location->intUserLocationID,
					'date' => date("Y-M-D",strtotime($obj_user_location->dtmTimestamp)),
					'time' => date("H:i:s",strtotime($obj_user_location->dtmTimestamp)),
					"speed" => isset($obj_user_location->numSpeed)?$obj_user_location->numSpeed.' mph':'',
					"deltaLat" => $obj_user_location->locLatitude - $obj_user_location->locPrevLatitude,
					"deltaLon" => $obj_user_location->locLongitude - $obj_user_location->locPrevLongitude,
					);
		}
		return $arr_locations_latlongs;
	}
	public function dataAction(){
		$tblUserLocations = new Game_Model_DbTable_UserLocations();
		$sel = $tblUserLocations->select()->setIntegrityCheck(false);
		$sel->from('viewUserLocations');
		$sel->where('intUserID = ?', Application_Model_User::getCurrentUser()->intUserID);
		$sel->limit(100);
		$this->view->assign("arr_locations",$tblUserLocations->fetchAll($sel));
	}
	
	protected function _get_movement($id){
		$tblUserLocations = new Game_Model_DbTable_UserLocations();
		$sel = $tblUserLocations->select();
		$sel->where('intUserID = ?', Application_Model_User::getCurrentUser()->intUserID);
		$sel->where('intUserLocationID = ?',$id);
		$sel->order('dtmTimestamp DESC');
		return $tblUserLocations->fetchRow($sel);
	}
	public function viewMovementsAction(){
		$arr_locations['left'] = $this->_request->getParam('left');
		$arr_locations['right'] = $this->_request->getParam('right');
		
		foreach($arr_locations as &$location){
			$location = $this->_get_movement($location);
		}
		$timestamps[] 	= $arr_locations['left']->intTimestampMs;
		$timestamps[] 	= $arr_locations['right']->intTimestampMs;
		sort($timestamps);
				
		$tblUserLocations = new Game_Model_DbTable_UserLocations();
		$sel = $tblUserLocations->select()->setIntegrityCheck(false);
		$sel->from('viewUserLocations','viewUserLocations.intUserLocationID');
		$sel->join('tblUserLocations','viewUserLocations.intUserLocationID = tblUserLocations.intUserLocationID');
		$sel->where('tblUserLocations.intUserID = ?', Application_Model_User::getCurrentUser()->intUserID);
		$sel->where('intTimestampMs >= ?',$timestamps[0]);
		$sel->where('intTimestampMs <= ?',$timestamps[1]);
		$sel->order('dtmTimestamp DESC');
		
		$array_of_ids = $tblUserLocations->fetchAll($sel);
		
	
		
		$this->view->assign("arr_locations",$array_of_ids);
		
		$this->view->assign("arr_locations_latlongs",$this->_get_latlongs($this->view->arr_locations));
		
		$this->view->headScript()->appendScript("var waypoints = " . json_encode($this->view->arr_locations_latlongs));
		$this->view->headScript()->appendFile("http://maps.googleapis.com/maps/api/js?key=AIzaSyAeDI_T5MhRJtykibKEqszGZAxxGB3iaTg&sensor=true");
		$this->view->headScript()->appendFile($this->view->baseUrl() . "/js/application-me-map.js");
		$this->view->headLink()->appendStylesheet($this->view->baseUrl() . "/css/application-me-map.css");
		
	}
}





