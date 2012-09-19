<?php
require_once(APPLICATION_PATH . "/../library/Eden/eden.php");
require_once(APPLICATION_PATH . "/../library/Eden/eden/foursquare.php");

class FoursquareController extends Turbo_Controller_LoggedInAction
{
	private $client_id = 'A0E0UFLSVUV45SMMCD0O0J4OIO0I3OFJJB5U3SZ0IFSVNYJC';
	private $client_secret = '2UZWRFA2WL2UK4N1LCF3SBLPLNF0IBMI4BGD04S1QWC0VNTA';
	private $redirect_uri = 'http://gamitu.de/Foursquare/add-foursquare-callback';
	private $auth;

	public function init(){
		parent::init();
		$this->auth = eden('foursquare')->auth($this->client_id, $this->client_secret, $this->redirect_uri);
	}
	private function _include_foursquare_api(){
		require_once dirname(__FILE__) . "/../../library/simple-api-clients/foursquare.php";
	}
	
	private function _set_up_foursquare_api(){
		$this->_include_foursquare_api();
	}
	
	private function _update_visited_locations(){
		$users = eden('foursquare')->users(Application_Model_User::getCurrentUser()->settingGet('foursquare_access_token'));
		$venueHistory = $users->getVenuehistory();
		foreach($venueHistory['response']['venues']['items'] as $key => $venue){
			$tblFoursquareKnownLocations = new Game_Model_DbTable_FoursquareKnownLocations();
			$sel = $tblFoursquareKnownLocations->select(true);
			$sel->where('intUserID', Game_Model_User::getCurrentUser()->intUserID);
			$sel->where('strFoursquareID', $venue['venue']['id']);
			$matches = $tblFoursquareKnownLocations->fetchAll($sel);
			
			$data = array(
					'intUserID'				=> Game_Model_User::getCurrentUser()->intUserID,
					'strFoursquareID'		=> $venue['venue']['id'],
					'strName' 				=> $venue['venue']['name'],
					'strLocationAddress' 	=> $venue['venue']['location']['address'],
					'locLocationLatitude' 	=> $venue['venue']['location']['lat'],
					'locLocationLongitude' 	=> $venue['venue']['location']['lng'],
					'strLocationPostcode' 	=> $venue['venue']['location']['postalCode'],
					'strLocationCity' 		=> $venue['venue']['location']['city'],
					'strLocationState' 		=> $venue['venue']['location']['state'],
					'strLocationCountry' 	=> $venue['venue']['location']['country'],
					'intVisits' 			=> $venue['beenHere'],
			);
			
			if(count($matches) > 0){
				$tblFoursquareKnownLocations->update($data, $sel);
			}else{
				$tblFoursquareKnownLocations->insert($data);
			}
		}
	}
	
	public function addFoursquareAction(){
		$login = $this->auth->getLoginUrl();
		header("Location: $login");
		exit;
	}
	
	public function addFoursquareCallbackAction(){
		$access = $this->auth->getAccess($_GET['code']);
		
		Application_Model_User::getCurrentUser()->settingSet('foursquare_access_token',$access['access_token']);
		
		$this->_update_visited_locations();
		
		$this->_helper->redirector('add-foursquare-complete');
	}

	public function addFoursquareCompleteAction(){}
	
	public function updateFoursquareHistoryAction(){
		$this->_update_visited_locations();
	}
	
}





