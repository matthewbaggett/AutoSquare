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
	
	private function _set_up_google_api($user = null){
		if($user === NULL){
			$user = Application_Model_User::getCurrentUser();
		}
		$this->_include_google_api();
		
		$client = new apiClient();
		// Visit https://code.google.com/apis/console to generate your
		// oauth2_client_id, oauth2_client_secret, and to register your oauth2_redirect_uri.
		$client->setClientId($this->client_id);
		$client->setClientSecret($this->client_secret);
		if (PHP_SAPI != 'cli'){
			$client->setRedirectUri('http://'.$_SERVER['SERVER_NAME'].'/Google/Add-Latitude');
		}
		$client->setApplicationName($this->application_name);
		
		if(is_object($user->settingGet("google_latitude_access_token"))){
			$token = $user->settingGet("google_latitude_access_token");
			$client->setAccessToken(json_encode($token));
		}
		$service = new apiLatitudeService($client);
		
		return array($service, $client);
	}
	
	private function _get_latitude_location(){
		list($service, $client) = $this->_set_up_google_api(Application_Model_User::getCurrentUser());
		$currentLocation = $service->currentLocation->get();
		return $currentLocation;
	}
	
	private function _get_latitude_locations(Application_Model_User $user, $count = 100){
		list($service, $client) = $this->_set_up_google_api($user);
		$location = $service->location->listLocation(array('granularity' => 'best','max-results' => 1000));
		return isset($location['items'])?$location['items']:false;
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
			Application_Model_User::getCurrentUser()->settingSet("google_latitude_access_token", json_decode($client->getAccessToken()));
		}

		if(isset($authUrl)){
			//$this->_helper->redirector('add-latitude-complete', 'Google');
		}
		$this->view->assign('currentLocation', $currentLocation);
		$this->view->assign('location', $location);
		$this->view->assign('authUrl', $authUrl);
		
	}

	public function addLatitudeCompleteAction(){}
	
	public function updateLocationFeedAction($user = null){
		if(!$user){
			$user = Application_Model_User::getCurrentUser();
		}
		$recent_locations = $this->_get_latitude_locations($user);
		$tblUserLocations = new Game_Model_DbTable_UserLocations();
		
		$count_new = 0;
		
		if(is_array($recent_locations)){
			foreach($recent_locations as $recent_location){
				//Test to see if we can find a matching location for this user
				if(!$tblUserLocations->user_location_already_reported($user, $recent_location['timestampMs'])){
					//Insert the location
					$count_new++;
					$tblUserLocations->insert_location($user, $recent_location);
				}
			}
		}
		$this->view->assign('count_seen', count($recent_locations));
		$this->view->assign('count_new', $count_new);
	}
	
	public function checkForAchievementsAction($user = null){
		if(!$user){
			$user = Application_Model_User::getCurrentUser();
		}
		$game_instance = new Game_Core($user);
		echo "instance got\n";
		$arr_new_achievements = $game_instance->check_for_achievements();
		echo "Found ".count($arr_new_achievements)." new achievements!\n";
		
		$game_instance->award($user, $arr_new_achievements);
		
		//$this->view->assign('achievements', $arr_new_achievements);
		return $arr_new_achievements;
	}
	
	public function cronUpdateLocationFeedsAction(){
		$tblUsers = new Turbo_Model_DbTable_Users();
		$arr_users = $tblUsers->fetchAll();
		echo "Processing " . count($arr_users) . " users.\n";
		foreach($arr_users as $user){
			var_dump($user->settingGet("google_latitude_access_token"));
			echo " > {$user->strUsername}";
			$this->updateLocationFeedAction($user);
			echo " - Got {$this->view->count_seen} locations, {$this->view->count_new} new.\n";
		}
		exit;
	}
	
	public function cronCheckForAchievementsAction(){
		$tblUsers = new Turbo_Model_DbTable_Users();
		$arr_users = $tblUsers->fetchAll();
		echo "Processing " . count($arr_users) . " users.\n";
		foreach($arr_users as $user){
			echo " > {$user->strUsername}..\n";
			$arr_new_achievements = $this->checkForAchievementsAction($user);
			$arr_new_achievements = array_filter($arr_new_achievements);
			if($arr_new_achievements !== null){
				foreach($arr_new_achievements as $achievement){
					echo "{$achievement->strAchievementName}!\n";
				}
			}else{
				echo " No achievements.\n";
			}
			echo "\n";
		}
		exit;
	}
	
	public function cronSpeedCalcAction(){
		$tblUsers = new Turbo_Model_DbTable_Users();
		$arr_users = $tblUsers->fetchAll();
		echo "Processing " . count($arr_users) . " users.\n";
		foreach($arr_users as $user){
			$tblUserLocations = new Game_Model_DbTable_UserLocations();
			$sel = $tblUserLocations->select();
			$sel->where("intUserID = ?", $user->intUserID);
			$sel->where('numSpeed IS NULL');
			$sel->order("dtmTimestamp ASC");
			$arr_uncalculated_speeds = $tblUserLocations->fetchAll($sel);
			$previous = null;
			foreach($arr_uncalculated_speeds as $location_without_speed){
				if($previous){
					passthru("clear");
					echo " > Processing distance between locations {$previous->intUserLocationID} & {$location_without_speed->intUserLocationID}\n";
					
					//Record what we're comparing with..
					$location_without_speed->intPrevUserLocationID = $previous->intUserLocationID;
					$location_without_speed->locPrevLatitude = $previous->locLatitude;
					$location_without_speed->locPrevLongitude = $previous->locLongitude;
					
					if(date("Y-m-d",($previous->intTimestampMs/1000)) != date("Y-m-d",($location_without_speed->intTimestampMs/1000))){
						echo "  > Skip, non-contiguous days.\n";
						unset($previous);
						continue;
					}
					
					$distance = Game_Core::distance_haversine(
							$location_without_speed->locLatitude,
							$location_without_speed->locLongitude,
							$previous->locLatitude,
							$previous->locLongitude
					);
					
					// Distances...
					echo "   > {$location_without_speed->locLatitude}\t{$location_without_speed->locLongitude}\t{$location_without_speed->dtmTimestamp}\n";
					echo "   > {$previous->locLatitude}\t{$previous->locLongitude}\t{$previous->dtmTimestamp}\n";
					echo "     > {$distance} miles\n";
					
					// Times...
					$location_without_speed->intTimeSinceLastLocationMs = $location_without_speed->intTimestampMs - $previous->intTimestampMs;
					echo "   > ms elapsed\t: {$location_without_speed->intTimeSinceLastLocationMs}\n";
					$sec_elapsed = $location_without_speed->intTimeSinceLastLocationMs/1000;
					$min_elapsed = $sec_elapsed / 60;
					echo "   > min elapsed\t: {$min_elapsed}\n";
					$location_without_speed->numDistance = $distance;
					
					// Bearings
					$location_without_speed->numBearing = (rad2deg(atan2(sin(deg2rad($previous->locLongitude) - deg2rad($location_without_speed->locLongitude)) * cos(deg2rad($previous->locLatitude)), cos(deg2rad($location_without_speed->locLatitude)) * sin(deg2rad($previous->locLatitude)) - sin(deg2rad($location_without_speed->locLatitude)) * cos(deg2rad($previous->locLatitude)) * cos(deg2rad($previous->locLongitude) - deg2rad($location_without_speed->locLongitude)))) + 360) % 360;
					$location_without_speed->numPrevBearing = $previous->numBearing;
					
					// Speed
					$location_without_speed->numSpeed = $distance / ($min_elapsed/60);
					$location_without_speed->numPrevSpeed = $previous->numSpeed;
					
					//Spit out results									
					echo "   > RESULTS\n";
					echo "     > it took {$sec_elapsed} sec / {$min_elapsed} min to cover \n";
					echo "     > {$location_without_speed->numDistance} miles \n";
					echo "     > @ {$location_without_speed->numSpeed} mph\n";
					echo "     > Bearing {$location_without_speed->numBearing}\n";
					
					$location_without_speed->save();

					echo "\n";
					//sleep(2);
					
					//exit;
				}
				$previous = $location_without_speed;
			}
		}
		exit;
	}
	
	
}





