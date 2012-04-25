<?php
class Game_Core{
	protected $user;
	
	public function __construct(Turbo_Model_User $user){
		$this->user = $user;
	}
	
	public function check_for_achievements(){
		$tblUserLocations = new Game_Model_DbTable_UserLocations();
		foreach($tblUserLocations->get_unchecked_locations_for_user($this->user) as $user_location){
			$achievement_locations_search = new Game_Model_DbTable_AchievementLocationsSearch();
			$arr_achievements_to_award = $achievement_locations_search->do_lookup($user_location->locLatitude, $user_location->locLongitude);
			$this->user->award($arr_achievements_to_award);
		}
	}
}