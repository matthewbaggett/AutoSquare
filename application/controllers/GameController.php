<?php 
class GameController extends Turbo_Controller_LoggedInAction{
	public function checkForAchievementsAction($user = null){
		if($user === null){
			$user = Application_Model_User::getCurrentUser();
		}
		$game_instance = new Game_Core($user);
		$arr_new_achievements = $game_instance->check_for_achievements();
		
	}
}