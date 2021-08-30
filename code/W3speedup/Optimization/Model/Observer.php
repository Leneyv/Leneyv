<?php

namespace W3speedup\Optimization\Model;
use W3speedup\Optimization\Model\includes\w3speed_html_optimize;
use W3speedup\Optimization\Model\includes\W3speedup;
use Magento\Framework\Event\ObserverInterface;
use W3speedup\Optimization\Helper\Data;
use \Magento\Framework\App\Helper\Context;
use	\Magento\Framework\HTTP\Client\Curl;


if(!defined('W3SPEEDUP_PATH'))
	define('W3SPEEDUP_PATH',__dir__);

class Observer implements ObserverInterface
{
	protected $_helper;
	protected $_curl;
	public function __construct(
        Data $helper,Context $context,Curl $curl
    )
    {		
        $this->_helper = $helper;
		$this->_curl = $curl;
	}

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
       $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $request = $objectManager->get('Magento\Framework\App\Action\Context')->getRequest();
        //print_r($request->getFullActionName());exit;
		$settings = $this->_helper->getLicenseKey();
		$hooks = $this->_helper->getHooksBeforeOpt();		
		
        $response = $observer->getEvent()->getData('response');
		
        if (!$response)
            return;
			
        $html = $response->getBody();
		if ($html == ''){
		return;}
			
		include_once(W3SPEEDUP_PATH .'/includes/W3speedup.php');
		if(!empty($_REQUEST['action']) && $_REQUEST['action'] == 'w3speedup_validate_license_key' ){
			$this->w3speedup_activate_license_key();
			exit;
		}
		if(!empty($_REQUEST['action']) && $_REQUEST['action'] == 'w3speedup_optimize_image'){
			w3speedup_image_optimization_callback();
		}
		if(!empty($_REQUEST['w3_preload_css'])){
			$this->w3speedup_preload_css_callback();
			exit;
		}
		if(!empty($_REQUEST['w3_put_preload_css'])){
			$this->w3speedup_put_preload_css_callback();
			exit;
		}
		if(!empty($_REQUEST['delete_w3_cache'])){
		  
			$this->delete_directory($_SERVER["DOCUMENT_ROOT"].'/cache');
			echo "cache Clear successfully";exit;
		}
		require_once('includes/w3speedup_css.php');
		require_once('includes/w3speedup_js.php');
		require_once('includes/w3speed_html_optimize.php');
		if ($request->getFullActionName() == 'cms_noroute_index') {
           
        } else {
			$w3_optimize = new w3speed_html_optimize($settings,$hooks);
			$html = $w3_optimize->start($html,$this->_curl);;
        }
		$response->setBody($html);
		 
        
    }
	function w3speedup_optimize_image_on_upload($metadata, $attachment_id, $context="create"){
		require_once(W3SPEEDUP_PATH . '/includes/class_image.php');
		$w3_speedup_opt_img = new \W3Speedup\w3speedup_optimize_image();
		return $w3_speedup_opt_img->w3speedup_optimize_single_image($metadata, $attachment_id, $context);
	}
	
	function w3speedup_image_optimization_callback(){
		include_once(W3SPEEDUP_PATH . '/includes/class_image.php');	
		$w3_speedup_opt_img = new w3speedup_optimize_image();
		$w3_speedup_opt_img->w3speedup_optimize_image_callback();
	}
	function w3speedup_activate_license_key(){
		include_once(W3SPEEDUP_PATH .'/admin/w3speedup_admin.php');
		$w3_speedup = new W3speedup(); 
		$w3_speedup->w3speedup_validate_license_key($this->_curl);
		exit;
	}
	function w3speedup_put_preload_css_callback(){
		include_once(W3SPEEDUP_PATH .'/admin/w3speedup_admin.php');
		$w3_speedup = new W3speedup(); 
		$w3_speedup->w3_put_preload_css();
		exit;
	}
	function w3speedup_preload_css_callback(){
		$settings = $this->_helper->getLicenseKey();
		$hooks = $this->_helper->getHooksBeforeOpt();
		include_once(W3SPEEDUP_PATH .'/admin/w3speedup_admin.php');
		include_once(W3SPEEDUP_PATH .'/includes/W3speedup.php');		
		$w3_speedup = new W3speedup($settings,$hooks); 
		$w3_speedup->w3_generate_preload_css($this->_curl);
		exit;
	}
	function w3speedup_add_image_optimization_schedule(){
		
		w3speedup_image_optimization_callback();
		exit;
	}
	function delete_directory($dirname) {
	    
         if (is_dir($dirname))
           $dir_handle = opendir($dirname);
         if (!$dir_handle)
              return false;
         while($file = readdir($dir_handle)) {
               if ($file != "." && $file != "..") {
                    if (!is_dir($dirname."/".$file))
                         unlink($dirname."/".$file);
                    else
                         $this->delete_directory($dirname.'/'.$file);
               }
         }
         closedir($dir_handle);
         rmdir($dirname);
         return true;
    }
	

}
