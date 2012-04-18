<?php
class Application_View_Helper_GetAssetBase extends Zend_View_Helper_Abstract 
{
    protected function getServer ()
    {
    	$protocol = $_SERVER['SERVER_PORT'] == 443 ? 'https://':'http://';
    	
    	if($_SERVER['HTTP_HOST'] == 'beta.bunnehbutt.com'){
    		$arr_servers = array(
    				"beta.bunnehbutt.com"
    		);
    	}else{
    		$arr_servers = array(
    				"renamon.img.bunnehbutt.com",
    				"terriermon.img.bunnehbutt.com",
    				"guilmon.img.bunnehbutt.com",
    		);
    	}
    	
    	if($protocol != "https://"){
        	$server = $arr_servers[rand(0,count($arr_servers)-1)];
    	}else{
    		$server = "ssl.bunnehbutt.com";
    	}
    	return $protocol.$server;
        
    }
}