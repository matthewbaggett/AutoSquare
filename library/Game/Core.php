<?php
class Game_Core{
	protected $user;
	
	public function __construct(Turbo_Model_User $user){
		$this->user = $user;
		
	}
	
	protected function _check_for_achievements_for_userlocation($user_location){

		$tblAchievementLocations = new Game_Model_DbTable_AchievementLocations();
		$select = $tblAchievementLocations->select();
		
		$select->where('locLatitude - (intRadius/(111*1000)) < ?', 	$user_location->locLatitude);
		$select->where('locLatitude + (intRadius/(111*1000)) > ?', 	$user_location->locLatitude);
		$select->where('locLongitude - (intRadius/(85*1000)) < ?', 	$user_location->locLongitude);
		$select->where('locLongitude + (intRadius/(85*1000)) > ?', 	$user_location->locLongitude);
		$arr_rough_search = $tblAchievementLocations->fetchAll($select);
		echo "Rough searches:\n";
		var_dump($arr_rough_search);
		// loop over these rough results, and compute the distances:
		foreach($arr_rough_search as $potential_location){
			$distance = $this->distance_haversine($latitude, $longitude, $potential_location->locLatitude, $potential_location->locLongitude);
			if($distance <= $potential_location->intRadius){
				$arr_locations_in_radius[$potential_location->strName] = $potential_location;
			}
		}
		echo "Refined searches:\n";
		var_dump($arr_locations_in_radius);
		
		echo "Found " . count($arr_rough_search) . " rough searches.\n";
		echo " > Of which, " . count($arr_locations_in_radius) . " were refined.\n";
		exit;
			
		return $arr_locations_in_radius;
	}
	public function check_for_achievements(){
		$tblUserLocations = new Game_Model_DbTable_UserLocations();
		foreach($tblUserLocations->get_unchecked_locations_for_user($this->user) as $user_location){
			$arr_achievements_to_award = $this->_check_for_achievements_for_userlocation($user_location);
			$this->user->award($arr_achievements_to_award);
		}
	}
}