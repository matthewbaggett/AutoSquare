<?php

class Game_Model_DbTable_AchievementLocationsSearch extends Zend_Db_Table_Abstract
{

	protected $_name = 'viewAchievementLocations';
	protected $_rowClass = 'Game_Model_AchievementLocation';
	

	private function distance_haversine($lat1, $lon1, $lat2, $lon2) {
		return Game_Core::distance_haversine($lat1, $lon1, $lat2, $lon2);
	}


	public function do_lookup($latitude,$longitude){
		$arr_locations_in_radius = array();

		// Make a rough, box-shaped selection from the database
		$select = $this->select(true);
		
		$select->where('locLatitudeMin < ?',$latitude);
		$select->where('locLatitudeMax > ?',$latitude);
		$select->where('locLongitudeMin < ?',$longitude);
		$select->where('locLongitudeMax > ?',$longitude);
		$arr_rough_search = $this->fetchAll($select);

		// loop over these rough results, and compute the distances:
		foreach($arr_rough_search as $potential_location){
			$distance = $this->distance_haversine($latitude, $longitude, $potential_location->locLatitude, $potential_location->locLongitude);
			if($distance <= $potential_location->intRadius){
				$arr_locations_in_radius[$potential_location->strName] = $potential_location;
			}
		}

		return $arr_locations_in_radius;
	}

}

