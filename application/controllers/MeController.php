<?php

class MeController extends Turbo_Controller_LoggedInAction
{
	
	public function indexAction(){
		$tblUserSettings = new Turbo_Model_DbTable_UserSettings();
		
		// Get user settings
		$select = $tblUserSettings->select(true);
		$select->where("intUserID = ?", Application_Model_User::getCurrentUser()->intUserID);
		
		//For each setting, get its type
		$arrSettings = $tblUserSettings->fetchAll($select);
		
		$this->view->assign('arrSettings', $arrSettings);
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
	
		$one_week_in_sec = 604800;
		$start = $this->_request->getParam('start')!=NULL?$this->_request->getParam('start'):date("Y-m-d H:i:s",time() - $one_week_in_sec);
		$end = $this->_request->getParam('end')!=NULL?$this->_request->getParam('end'):date("Y-m-d H:i:s",time());
		
		//Strip underscores from timestamps
		//$start = "{$start} 00:00:00";
		//$end = "{$end} 23:59:59";
		
		//Turn into a timestamp
		$start = strtotime($start);
		$end = strtotime($end);
		
		$tblUserLocations = new Game_Model_DbTable_UserLocations();
		
		$sel = $tblUserLocations->select()->setIntegrityCheck(false);
		$sel->from('viewUserLocations');
		$sel->where('intUserID = ?', Application_Model_User::getCurrentUser()->intUserID);
		$sel->order('dtmTimestamp DESC');
		$sel->where('dtmTimestamp >= ?', date("Y-m-d H:i:s",$start));
		$sel->where('dtmTimestamp <= ?', date("Y-m-d H:i:s",$end));
		$sel->limit(10000);
		
		$this->view->assign('query_selection',$sel);
		
		$this->view->assign('timestamp_start',	$start);
		$this->view->assign('timestamp_end',	$end);
		
		$this->view->assign("arr_locations",$tblUserLocations->fetchAll($sel));
		
		$this->view->assign("arr_locations_latlongs",$this->_get_latlongs($this->view->arr_locations));


		// Add some CSS style sheets...
		$this->view->headLink()->appendStylesheet($this->view->baseUrl() . "/js/jquery-ui-1.8.20.custom/development-bundle/themes/base/jquery.ui.all.css");
		$this->view->headLink()->appendStylesheet($this->view->baseUrl() . "/css/application-me-map.css");
		$this->view->headLink()->appendStylesheet($this->view->baseUrl() . "/css/sqlsyntax.css");
		
		// Inject google maps API libraries.. 
		$this->view->headScript()->appendFile("//maps.googleapis.com/maps/api/js?key=AIzaSyAeDI_T5MhRJtykibKEqszGZAxxGB3iaTg&sensor=true");
		
		// Inject location json
		$this->view->headScript()->appendScript("var waypoints = " . json_encode($this->view->arr_locations_latlongs));
		
		// Add jQuery UI for the datepicker and such
		$this->view->headScript()->appendFile($this->view->baseUrl() . "/js/jquery-ui-1.8.20.custom/js/jquery-ui-1.8.20.custom.min.js");
		$this->view->headScript()->appendFile($this->view->baseUrl() . "/js/jquery-ui-1.8.20.custom/development-bundle/ui/jquery.ui.core.js");
		$this->view->headScript()->appendFile($this->view->baseUrl() . "/js/jquery-ui-1.8.20.custom/development-bundle/ui/jquery.ui.widget.js");
		$this->view->headScript()->appendFile($this->view->baseUrl() . "/js/jquery-ui-1.8.20.custom/development-bundle/ui/jquery.ui.datepicker.js");
		$this->view->headScript()->appendFile($this->view->baseUrl() . "/js/jquery-ui-1.8.20.custom/js/jquery-ui-timepicker-addon.js");
		$this->view->headScript()->appendFile($this->view->baseUrl() . "/js/jquery-ui-1.8.20.custom/js/jquery-ui-sliderAccess.js");
		
		// Add application JS
		$this->view->headScript()->appendFile($this->view->baseUrl() . "/js/application-me-map.js");
		
	}
	
	protected function _get_latlongs($arr_locations){
		$arr_locations_latlongs = array();
		foreach($arr_locations as $obj_user_location){
			$arr_locations_latlongs[] = array(
					"location" => "{$obj_user_location->locLatitude},{$obj_user_location->locLongitude}",
					"lat" => $obj_user_location->locLatitude,
					"lng" => $obj_user_location->locLongitude,
					"stopover" => "true",
					"title" =>  (isset($obj_user_location->numSpeed)?"{$obj_user_location->dtmTimestamp} - {$obj_user_location->miles} miles @ {$obj_user_location->numSpeed} mph":$obj_user_location->dtmTimestamp),
					'id' => $obj_user_location->intUserLocationID,
					'date' => date("Y-M-D",strtotime($obj_user_location->dtmTimestamp)),
					'time' => date("H:i:s",strtotime($obj_user_location->dtmTimestamp)),
					"speed" => isset($obj_user_location->numSpeed)?$obj_user_location->numSpeed.' mph':'',
					"bearing" => $obj_user_location->numBearing,
					'trusted' => $obj_user_location->trusted,
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
		$sel->where('tblUserLocations.intTimestampMs >= ?',$timestamps[0]);
		$sel->where('tblUserLocations.intTimestampMs <= ?',$timestamps[1]);
		$sel->order('tblUserLocations.dtmTimestamp DESC');
		
		$array_of_ids = $tblUserLocations->fetchAll($sel);	
		
		$this->view->assign("arr_locations",$array_of_ids);
		
		$this->view->assign("arr_locations_latlongs",$this->_get_latlongs($this->view->arr_locations));
		
		$this->view->headScript()->appendScript("var waypoints = " . json_encode($this->view->arr_locations_latlongs));
		$this->view->headScript()->appendFile("http://maps.googleapis.com/maps/api/js?key=AIzaSyAeDI_T5MhRJtykibKEqszGZAxxGB3iaTg&sensor=true");
		$this->view->headScript()->appendFile($this->view->baseUrl() . "/js/application-me-map.js");
		$this->view->headLink()->appendStylesheet($this->view->baseUrl() . "/css/application-me-map.css");
		
	}
	
	
}





