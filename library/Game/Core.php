<?php
class Game_Core{
	protected $user;
	
	public function __construct(Turbo_Model_User $user){
		$this->user = $user;
	}
	
	public function check_for_achievements(){
		
	}
}