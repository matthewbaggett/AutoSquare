<?php

class Application_Model_DbTable_UserLocations extends Zend_Db_Table_Abstract
{

    protected $_name = 'tblUserLocations';
	//protected $_rowClass = 'Turbo_Model_User';

    /**
     * Test to see if the users location was already reported.
     * @param Turbo_Model_User $user
     * @param integer $timestamp
     * @return boolean
     */
    public function user_location_already_reported(Turbo_Model_User $user, $timestamp){
    	$select = $this->select(true);
    	$select->where('intUserID = ?',$user->intUserID);
    	$select->where('intTimestamp = ?', $timestamp);
    	echo "<pre>";
    	echo $select;
    	echo "</pre>";
    	if($this->fetchRow($sel_setting)){
    		return TRUE;
    	}
    	return FALSE;
    }
    
    /**
     * Insert location into the database
     * @param Turbo_Model_User $user
     * @param array $location Google Latitude response.
     * @return integer insertion id
     */
    public function insert_location(Turbo_Model_User $user, array $location){
    	return $this->insert(array(
    			'intUserID' => $user->intUserID,
    			'strKind' => $location['kind'],
    			'intTimestampMs' => $location['timestampMs'],
    			'dtmTimestamp' => date("Y-m-d H:i:s", ($location['timestampMs']/1000)),
    			'locLatitude' => $location['latitude'],
    			'locLongitude' => $location['longitude'],
    			'accuracy' => $location['accuracy'],
    			'dtmDiscovered' => date("Y-m-d H:i:s")
    	));
    	
    }
}

