<?php

class LoginController extends Turbo_Controller_Login
{

	private function _login_redirect(){
		$this->_helper->redirector('index', 'Me');
	}


}





