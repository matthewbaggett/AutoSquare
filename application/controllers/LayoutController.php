<?php
//require_once(dirname(__FILE__) . "/../modules/CMS/Models/Sitemap.)
class LayoutController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    	//$arr_sitemap_nodes_main = CMS_Model_CMSSitemap::getSitemapByHandle('MAIN')->getSitemapNodes(6);
    	$arr_sitemap_nodes_main = array();
    	$this->view->assign('arr_sitemap_nodes_main',$arr_sitemap_nodes_main);
    	
    	$this->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
    	$this->headTitle()->setSeparator(' - ');
    	$this->headTitle('AutoSquare');
    	
    	$this->headScript()
	    	->appendFile("http://platform.twitter.com/widgets.js")
	    	->appendFile($this->baseUrl() . "/js/jquery.js")
	    	->appendFile($this->baseUrl() . "/js/google-code-prettify/prettify.js")
	    	->appendFile($this->baseUrl() . "/js/bootstrap-transition.js")
	    	->appendFile($this->baseUrl() . "/js/bootstrap-alert.js")
	    	->appendFile($this->baseUrl() . "/js/bootstrap-modal.js")
	    	->appendFile($this->baseUrl() . "/js/bootstrap-dropdown.js")
	    	->appendFile($this->baseUrl() . "/js/bootstrap-scrollspy.js")
	    	->appendFile($this->baseUrl() . "/js/bootstrap-tab.js")
	    	->appendFile($this->baseUrl() . "/js/bootstrap-tooltip.js")
	    	->appendFile($this->baseUrl() . "/js/bootstrap-popover.js")
	    	->appendFile($this->baseUrl() . "/js/bootstrap-button.js")
	    	->appendFile($this->baseUrl() . "/js/bootstrap-collapse.js")
	    	->appendFile($this->baseUrl() . "/js/bootstrap-carousel.js")
	    	->appendFile($this->baseUrl() . "/js/bootstrap-typeahead.js")
	    	->appendFile($this->baseUrl() . "/js/application.js")
	    	;
    }

}

