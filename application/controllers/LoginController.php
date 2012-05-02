<?php

class LoginController extends Turbo_Controller_Login
{

	protected function _login_redirect(){
		header("Location: /Me");
	}


}





