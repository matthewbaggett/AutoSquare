<?php
class Game_Core{
	protected $user;
	
	public function __construct(Turbo_Model_User $user){
		$this->user = $user;
		$this->db = Zend_Db::factory('default');
	}
	
	protected function _check_for_achievements_for_userlocation($user_location){

		$select = new Zend_Db_Select($this->db);
		$select->from('viewAchievementLocations');
		$select->where('locLatitudeMin < ?', 	$user_location->locLatitude);
		$select->where('locLatitudeMax > ?', 	$user_location->locLatitude);
		$select->where('locLongitudeMin < ?', 	$user_location->locLongitude);
		$select->where('locLongitudeMax > ?', 	$user_location->locLongitude);
		echo $select;
		exit;
		$arr_rough_search = $this->fetchAll($select);
			
			
		// loop over these rough results, and compute the distances:
		foreach($arr_rough_search as $potential_location){
			$distance = $this->distance_haversine($latitude, $longitude, $potential_location->locLatitude, $potential_location->locLongitude);
			if($distance <= $potential_location->intRadius){
				$arr_locations_in_radius[$potential_location->strName] = $potential_location;
			}
		}
			
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