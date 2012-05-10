<?php

class LoginController extends Game_Controller_Login
{

	protected function _login_redirect(){
		header("Location: /Me");
	}


}





