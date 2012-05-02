<?php

class Game_Model_DbTable_AchievementLocationsSearch extends Zend_Db_Table_Abstract
{
	private $earth_radius = 3960.00; # in miles

	protected $_name = 'viewAchievementLocations';

	private function distance_haversine($lat1, $lon1, $lat2, $lon2) {
		$delta_lat = $lat_2 - $lat_1 ;
		$delta_lon = $lon_2 - $lon_1 ;
		$alpha    = $delta_lat/2;
		$beta     = $delta_lon/2;
		$a        = sin(deg2rad($alpha)) * sin(deg2rad($alpha)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin(deg2rad($beta)) * sin(deg2rad($beta)) ;
		$c        = asin(min(1, sqrt($a)));
		$distance = 2 * $this->earth_radius * $c;
		$distance = round($distance, 4);

		return $distance;
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

