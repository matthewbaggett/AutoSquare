<?php
class Zend_View_Helper_LoggedInAs extends Zend_View_Helper_Abstract 
{
    public function loggedInAs ()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $username 	= $auth->getIdentity()->strUsername;
            $firstname 	= $auth->getIdentity()->strFirstname;
            $surname 	= $auth->getIdentity()->strSurname;
            $email 		= $auth->getIdentity()->strEmail;
            $logoutUrl = $this->view->url(array('controller'=>'Login', 'action'=>'logout'), null, true);
            return "Welcome {$firstname} {$surname} <a href=\"{$logoutUrl}\">Logout</a>";
        } 

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        if($controller == 'Login' && $action == 'index') {
            return '';
        }
        $loginUrl = $this->view->url(array('controller'=>'Login', 'action'=>'index'),null,true);
        $registerUrl = $this->view->url(array('controller'=>'Login', 'action'=>'register'),null,true);
        return "Hello Anonymous user! You can <a href=\"{$loginUrl}\">Login</a> or <a href=\"{$registerUrl}\">Register</a>!";
    }
}