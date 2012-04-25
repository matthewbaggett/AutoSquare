<?php
class Game_Core{
	protected $user;
	
	public function __construct(Turbo_Model_User $user){
		$this->user = $user;
	}
	
	public function check_for_achievements(){
		foreach($this->user->getUncheckedLocations() as $user_location){
			$achievement_locations_search = new Game_Model_DbTable_AchievementLocationsSearch();
			$arr_achievements_to_award = $achievement_locations_search->do_lookup($user_location->posLatitude, $user_location->posLongitude);
			$this->user->award($arr_achievements_to_award);
		}
	}
}