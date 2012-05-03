<?php

class CronController extends Zend_Controller_Action
{
	public function updateLatitudeFeedsAction(){
		$tblUsers = new Turbo_Model_DbTable_Users();
		$arr_users = $tblUsers->fetchAll();
		foreach($arr_users as $user){
			echo "{$user->strUsername}\n";
		}
		exit;
	}
}