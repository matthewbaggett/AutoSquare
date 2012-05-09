<?php

class Game_Model_DbTable_UserLocations extends Zend_Db_Table_Abstract
{

    protected $_name = 'tblUserLocations';
	//protected $_rowClass = 'Application_Model_User';

    /**
     * Test to see if the users location was already reported.
     * @param Application_Model_User $user
     * @param integer $timestamp
     * @return boolean
     */
    public function user_location_already_reported(Application_Model_User $user, $timestamp){
    	$select = $this->select(true);
    	$select->where('intUserID = ?',$user->intUserID);
    	$select->where('intTimestampMs = ?', $timestamp);
    	if($this->fetchRow($select)){
    		return TRUE;
    	}
    	return FALSE;
    }
    
    /**
     * Insert location into the database
     * @param Application_Model_User $user
     * @param array $location Google Latitude response.
     * @return integer insertion id
     */
    public function insert_location(Application_Model_User $user, array $location){
    	return $this->insert(array(
    			'intUserID' => $user->intUserID,
    			'strKind' => $location['kind'],
    			'intTimestampMs' => $location['timestampMs'],
    			'dtmTimestamp' => date("Y-m-d H:i:s", ($location['timestampMs']/1000)),
    			'locLatitude' => $location['latitude'],
    			'locLongitude' => $location['longitude'],
    			'accuracy' => isset($location['accuracy'])?$location['accuracy']:0,
    			'dtmDiscovered' => date("Y-m-d H:i:s")
    	));
    }
    
    /**
     * Grab the locations tied to this user that are not yet checked.
     * @param Application_Model_User $user
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function get_unchecked_locations_for_user(Application_Model_User $user){
    	$select = $this->select(true);
    	$select->where('intUserID = ?',$user->intUserID);
    	$select->where('bolChecked = ?', 0);
    	return $this->fetchAll($select);
    }
}

