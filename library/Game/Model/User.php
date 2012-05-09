<?php 
class Game_Model_User extends Turbo_Model_User{
	public function award_locations($awards){
		
		foreach($awards as $award){
			$this->award_location($award);
		}
		return TRUE;
	}
	
	public function award_location($award){
		
		echo "Awarding {$this->strUsername} {$award->strName}\n";
		return TRUE;
	}
	
}