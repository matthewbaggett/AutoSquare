<?php
class Game_Core{
	protected $user;
	
	static public $earth_radius = 3960.00; # in miles
	
	static public function distance_haversine($lat1, $lon1, $lat2, $lon2) {
		$delta_lat = $lat_2 - $lat_1 ;
		$delta_lon = $lon_2 - $lon_1 ;
		$alpha    = $delta_lat/2;
		$beta     = $delta_lon/2;
		$a        = sin(deg2rad($alpha)) * sin(deg2rad($alpha)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin(deg2rad($beta)) * sin(deg2rad($beta)) ;
		$c        = asin(min(1, sqrt($a)));
		$distance = 2 * self::$earth_radius * $c;
		$distance = round($distance, 4);
	
		return $distance;
	}
	
	public function __construct(Application_Model_User $user){
		$this->user = $user;
	}
	
	protected function _refine_distance_search($arr_rough_search){
		$arr_locations_in_radius = array();
		foreach($arr_rough_search as $potential_location){
			$distance = $this->distance_haversine($latitude, $longitude, $potential_location->locLatitude, $potential_location->locLongitude);
			if($distance <= $potential_location->intRadius){
				$arr_locations_in_radius[$potential_location->strName] = $potential_location;
			}
		}
		return $arr_locations_in_radius;
	}
	
	protected function _check_for_achievements_for_userlocation($user_location){

		$tblAchievementLocations = new Game_Model_DbTable_AchievementLocations();
		$select = $tblAchievementLocations->select();
		
		$select->where('locLatitude - (intRadius/(111*1000)) < ?', 	$user_location->locLatitude);
		$select->where('locLatitude + (intRadius/(111*1000)) > ?', 	$user_location->locLatitude);
		$select->where('locLongitude - (intRadius/(85*1000)) < ?', 	$user_location->locLongitude);
		$select->where('locLongitude + (intRadius/(85*1000)) > ?', 	$user_location->locLongitude);
		$arr_rough_search = $tblAchievementLocations->fetchAll($select);
		
		// loop over these rough results, and compute the distances:
		$arr_locations_in_radius = $this->_refine_distance_search($arr_rough_search);
			
		return $arr_locations_in_radius;
	}
	
	public function check_for_achievements(){
		$tblUserLocations = new Game_Model_DbTable_UserLocations();
		foreach($tblUserLocations->get_unchecked_locations_for_user($this->user) as $user_location){
			$arr_achievements_to_award = $this->_check_for_achievements_for_userlocation($user_location);
			
			$this->user->award_locations($arr_achievements_to_award);
		}
	}
}