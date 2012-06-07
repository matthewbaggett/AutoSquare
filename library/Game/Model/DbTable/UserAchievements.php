<?php

class Game_Model_DbTable_UserAchievements extends Zend_Db_Table_Abstract
{

    protected $_name = 'tblUserAchievements';
    protected $_rowClass = 'Game_Model_UserAchievement';
    
    
    static public function has_award($user,$achievement){
    	$tblUserAchievements = new Game_Model_DbTable_UserAchievements();
    	$found_achievements = $tblUserAchievements->fetchAll(array("intUserID = {$user->intUserID}", "intAchievementID = {$achievement->intAchievementID}"));
    	var_dump($found_achievements);
    	echo "Thems the achievements\n\n";
    	exit;
    }
    static public function add_award($user,$achievement){
    	echo " > Adding award: {$achievement->strAchievementLabel}\n";
    	if(self::has_award($user,$achievement)){
    		echo "  > ERR: Already has award!\n";
    		return FALSE;
    	}
    	$tblUserAchievements = new Game_Model_DbTable_UserAchievements();
    	$int_insertion_id = $tblUserAchievements->insert(
    			array(
    					'intUserID' => $user->intUserID,
    					'intAchievementID' => $achievement->intAchievementID,
    			)
    	);
    	return $tblUserAchievements->fetchRow('intAchievementID = '.intAchievementID);
    }
    
    static public function add_location_award($user,$award_location){
    	$tblAchievementLocations = new Game_Model_DbTable_AchievementLocations();
    	$tblAchievements = new Game_Model_DbTable_Achievements();
    	echo " > Adding location award: '{$award_location->strName}'\n";
    	$achievement = $tblAchievements->fetchRow("strAchievementName = '{$award_location->strName}'");
    	return self::add_award($user, $achievement);
    }
}

