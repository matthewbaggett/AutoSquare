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
    	
    	$this->view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
    	$this->view->headTitle()->setSeparator(' - ');
    	$this->view->headTitle('AutoSquare');
    	
    	$this->view->headScript()
	    	->appendFile("http://platform.twitter.com/widgets.js")
	    	->appendFile($this->view->baseUrl() . "/js/jquery.js")
	    	->appendFile($this->view->baseUrl() . "/js/google-code-prettify/prettify.js")
	    	->appendFile($this->view->baseUrl() . "/js/bootstrap-transition.js")
	    	->appendFile($this->view->baseUrl() . "/js/bootstrap-alert.js")
	    	->appendFile($this->view->baseUrl() . "/js/bootstrap-modal.js")
	    	->appendFile($this->view->baseUrl() . "/js/bootstrap-dropdown.js")
	    	->appendFile($this->view->baseUrl() . "/js/bootstrap-scrollspy.js")
	    	->appendFile($this->view->baseUrl() . "/js/bootstrap-tab.js")
	    	->appendFile($this->view->baseUrl() . "/js/bootstrap-tooltip.js")
	    	->appendFile($this->view->baseUrl() . "/js/bootstrap-popover.js")
	    	->appendFile($this->view->baseUrl() . "/js/bootstrap-button.js")
	    	->appendFile($this->view->baseUrl() . "/js/bootstrap-collapse.js")
	    	->appendFile($this->view->baseUrl() . "/js/bootstrap-carousel.js")
	    	->appendFile($this->view->baseUrl() . "/js/bootstrap-typeahead.js")
	    	->appendFile($this->view->baseUrl() . "/js/application.js")
	    	;
    }

}

