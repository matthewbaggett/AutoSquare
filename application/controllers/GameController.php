<?php 
class GameController extends Turbo_Controller_LoggedInAction{
	public function CheckForAchievementsAction(){
		$game_instance = new Game_Core(Turbo_Model_User::getCurrentUser());
		$arr_new_achievements = $game_instance->check_for_achievements();
		
	}
}