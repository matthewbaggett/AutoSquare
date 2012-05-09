<?php
class Application_View_Helper_GetAssetBase extends Zend_View_Helper_Abstract 
{
    protected function getServer ()
    {
    	$protocol = $_SERVER['SERVER_PORT'] == 443 ? 'https://':'http://';
    	
    	
    	
    	if($protocol != "https://"){
        	$server = $arr_servers[rand(0,count($arr_servers)-1)];
    	}else{
    		$server = "ssl.fff.com";
    	}
    	return $protocol.$server;
        
    }
}