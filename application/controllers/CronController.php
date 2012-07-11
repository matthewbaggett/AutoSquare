<?php

class CronController extends Zend_Controller_Action
{
	public function updateLatitudeFeedsAction(){
		$tblUsers = new Turbo_Model_DbTable_Users();
		$arr_users = $tblUsers->fetchAll();
		foreach($arr_users as $user){
			echo "{$user->strUsername}\n";
			
		}
		exit;
	}
	
	public function checkInLatestFoursquareAction(){
		// Include 4sq lib
		require_once dirname(__FILE__) . "/../../library/simple-api-clients/foursquare.php";
		
		// Get some tables.
		$tblUsers = new Turbo_Model_DbTable_Users();
		$tblUserLocations = new Game_Model_DbTable_UserLocations();
		
		// Get the users..
		$arr_users = $tblUsers->fetchAll();
		foreach($arr_users as $user){
			
			$select = $tblUserLocations->select();
			$select->where("intUserID = ?", $user->intUserID);
			$select->limit(1);
			$select->order("dtmTimestamp","desc");
			$location = $tblUserLocations->fetchRow($select);
			$fsq_token = $user->settingGet('foursquare_access_token');
			
			echo "{$user->strUsername}\n";
			if($location){
				echo " > {$location->locLatitude}, {$location->locLongitude}\n";
			}else{
				echo " > No location.\n";
				continue;
			}
			if($user->settingGet("foursquare_feed_push")){
				echo " > Got Foursquare feed.\n";
			}else{
				$user->settingSet('foursquare_feed_push',false);
				echo " > No Foursquare feed.\n";
				continue;
			}
			
			// Get the list of venues this user has checked into before
			
			$fsq = new foursquare_api();
			$fsq->make_request('users/USER_ID/venuehistory')
			
			// Check in to any that match
			
		}
		
		exit;
	}
}