<?php

class CronController extends Zend_Controller_Action
{
	public function updateLatitudeFeedsAction(){
		$tblUsers = new Turbo_Model_DbTable_Users();
		$arr_users = $tblUsers->fetchAll();
		print_r($arr_users);
		exit;
	}
}