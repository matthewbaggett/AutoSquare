<?php 
class Game_Model_User extends Turbo_Model_User{
	public function award($award){
		if(is_array($award)){
			foreach($award as $iaward){
				$this->award($iaward);
			}
			return TRUE;
		}
		
		echo "Awarding {$this->strUsername} {$award->strName}\n";
	}
	
}