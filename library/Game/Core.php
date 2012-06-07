<?php
class Game_Core{
	protected $user;
	
	static public $earth_radius = 3960.00; # in miles
	
	static public function distance_haversine($lat_1, $lon_1, $lat_2, $lon_2) {
		$delta_lat = $lat_2 - $lat_1 ;
		$delta_lon = $lon_2 - $lon_1 ;
		//echo "     > Lat: {$lat_1} & {$lat_2}\n";
		//echo "     > Lon: {$lon_1} & {$lon_2}\n";
		//echo "     > Deltas: Lat: {$delta_lat} Lon: {$delta_lon}\n";
		$alpha    = $delta_lat/2;
		$beta     = $delta_lon/2;
		$a        = sin(deg2rad($alpha)) * sin(deg2rad($alpha)) + cos(deg2rad($lat_1)) * cos(deg2rad($lat_2)) * sin(deg2rad($beta)) * sin(deg2rad($beta)) ;
		$c        = asin(min(1, sqrt($a)));
		$distance = 2 * self::$earth_radius * $c;
		//$distance = round($distance, 4);
	
		return $distance;
	}
	
	public function __construct(Application_Model_User $user){
		$this->user = $user;
	}
	
	protected function _refine_distance_search($arr_rough_search, $latitude, $longitude){
		$arr_locations_in_radius = array();
		foreach($arr_rough_search as $potential_location){
			$distance = $this->distance_haversine($latitude, $longitude, $potential_location->locLatitude, $potential_location->locLongitude);
			echo "  > Calcdist...\n";
			echo "    > Latitude 1  : {$latitude}\n";
			echo "    > Longitude 1 : {$longitude}\n";
			echo "    > Latitude 2  : {$potential_location->locLatitude}\n";
			echo "    > Longitude 2 : {$potential_location->locLongitude}\n";
			echo "    > Distance: $distance\n";
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
		
		echo "\n";
		echo "  > Rough search: ".count($arr_rough_search)." found\n";
		
		if(count($arr_rough_search) == 0){
			return array();
		}
		
		// loop over these rough results, and compute the distances:
		$arr_locations_in_radius = $this->_refine_distance_search($arr_rough_search, $user_location->locLatitude, $user_location->locLongitude);
		
		echo "  > Fine search: ".count($arr_locations_in_radius)." found\n";
		
		return $arr_locations_in_radius;
	}
	
	public function check_for_achievements(){
		$tblUserLocations = new Game_Model_DbTable_UserLocations();
		$arr_achievements_to_award = array();
		foreach($tblUserLocations->get_unchecked_locations_for_user($this->user) as $user_location){
			echo "\rLocation: {$user_location->locLatitude}, {$user_location->locLongitude}";
			$arr_new_achievements = $this->_check_for_achievements_for_userlocation($user_location);
			$arr_achievements_to_award = array_merge($arr_achievements_to_award, (array) $arr_new_achievements);
			$arr_checked_ids[] = $user_location->intUserLocationID;
		}
		$tblUserLocations->mark_checked($arr_checked_ids);
		echo "\n";
		
		return $arr_achievements_to_award;
	}
	
	public function award($user, $arr_award_locations){
		foreach($arr_award_locations as $award_location){
			Game_Model_DbTable_UserAchievements::add_location_award($user, $award_location);
		}
		
	}
}