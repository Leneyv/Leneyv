<?php
namespace W3speedup\Optimization\Model\includes;
//use W3speedup\Optimization\Model\includes\w3speedup_js;
require_once('w3speedup_js.php');
class w3speed_html_optimize extends w3speedup_js{
	protected $_curl;
	public $hooks;
	public function __construct($settings,$hooks){
		$this->hooks = $hooks;
		parent::__construct($settings,$hooks);
	}
    function w3_speedup($html){
		global $hooks;
        if($this->w3_no_optimization($html)){
            return $html;
        }
		
		if(function_exists('w3speedup_customize_add_settings')){
			$this->add_settings = w3speedup_customize_add_settings($this->add_settings);
		}
		
		$this->add_settings['disable_htaccess_webp'] = function_exists('w3_disable_htaccess_wepb') ? w3_disable_htaccess_wepb() : 0;
		$html = $this->w3_custom_js_enqueue($html);
        $html = str_replace(array('<script type="text/javascript"',"<script type='text/javascript'",'<style type="text/css"',"<style type='text/css'"),array('<script','<script','<style','<style'),$html);
			//print_r($this->hooks['before_optimization']);exit;
        if(isset($this->hooks['before_optimization'])){
            eval($this->hooks['before_optimization']);
        }
		
		
		
		
		
		
		/*if(isset($this->hooks['no_critical_css'])){
            eval($this->hooks['no_critical_css']);
        }*/
				
		
        $js_json_exists = 0;
        /*if(is_file($file = $this->w3_get_full_url_cache_path().'/js.json')){
            $rep_js = json_decode(file_get_contents($file));
            if(is_array($rep_js[0]) && is_array($rep_js[1])){
                $js_json_exists = 1;
                if(is_file($file = $this->w3_get_full_url_cache_path().'/main_js.json')){
                    global $internal_js;
                    $internal_js = json_decode(file_get_contents($file));
                }
            }
        }*/
        $img_json_exists = 0;
        if(is_file($file = $this->w3_get_full_url_cache_path().'/img.json')){
            $rep_img = json_decode(file_get_contents($file));
            if(is_array($rep_img[0]) && is_array($rep_img[1])){
                $img_json_exists = 1;
            }
        }
        $rep_main_css = array();
        $css_json_exists = 0;
        if(is_file($file = $this->w3_get_full_url_cache_path().'/main_css.json')){
            $rep_main_css = json_decode(file_get_contents($file));
        }
        if(is_file($file = $this->w3_get_full_url_cache_path().'/css.json')){
            $rep_css = json_decode(file_get_contents($file), true);
            if(is_array($rep_css[0]) && is_array($rep_css[1])){
                $css_json_exists = 1;
            }
        }
        if(is_file($file = $this->w3_get_full_url_cache_path().'/content_head.json') && $css_json_exists){
            $rep_content_head = json_decode(file_get_contents($file));
            if(is_array($rep_content_head) && count($rep_content_head) > 0){
                $css_json_exists = 1;
            }else{
                $css_json_exists = 0;
            }
        }
        
        //$startm = microtime();
        if($img_json_exists && $css_json_exists){
			$html = $this->w3_debug_time($html,'before create all links');
            $all_links = $this->w3_setAllLinks($html,array('script'));
			$html = $this->w3_debug_time($html,'after create all links');
            $html = $this->minify($html, $all_links['script']);
            $html = $this->w3_debug_time($html,'minify script');
            for($i = 0; $i < count($rep_content_head); $i++){
                $html = $this->w3_insert_content_head($html,$rep_content_head[$i][0],$rep_content_head[$i][1]);
            }
			$html = $this->w3_debug_time($html,'after replace json data');
            global $main_css_url;
            $main_css_url = $rep_main_css;
			$html = $this->w3_str_replace_bulk($html);
            $html = $this->w3_str_replace_bulk_json($html,array_merge($rep_css[0],$rep_img[0]),array_merge($rep_css[1],$rep_img[1]));
        }else{
			$html = $this->w3_debug_time($html,'before create all links');
            $all_links = $this->w3_setAllLinks($html,array('script','link','iframe','video','img','url'));
			$html = $this->w3_debug_time($html,'after create all links');
            $html = $this->minify($html, $all_links['script']);
			$html = $this->w3_debug_time($html,'minify script');
           
            $html = $this->lazyload($html, array('iframe'=>$all_links['iframe'],'video'=>$all_links['video'],'img'=>$all_links['img'],'url'=>$all_links['url'] ) );
            $html = $this->w3_debug_time($html,'lazyload images');
            $html = $this->minify_css($html,$all_links['link']);
			$html = $this->w3_debug_time($html,'minify css');
            $html = $this->w3_str_replace_bulk($html);
            $html = $this->w3_str_replace_bulk_img($html);
            $html = $this->w3_str_replace_bulk_css($html);
            $html = $this->w3_debug_time($html,'replace json');
            $this->w3_insert_content_head_in_json();
            $this->w3_main_css_url_to_json();
		}
		
        //$starte = microtime();
        //$html .= 'rocket';
		$position = strrpos($html,'</body>');
		$html =	substr_replace( $html, '<script>'.$this->w3_lazy_load_images().'</script>', $position, 0 );
        $html = $this->w3_debug_time($html,'w3 script');
        if(isset($this->hooks['after_optimization'])){
            eval($this->hooks['after_optimization']);
        }
		$ignore_critical_css = 0;
		if(function_exists('is_user_logged_in')){
			$ignore_critical_css = 1;
		}
		if(function_exists('w3_no_critical_css')){
			 if(w3_no_critical_css($this->add_settings['full_url'])){
				$ignore_critical_css = 1; 
			 }
		}
	
		if(!empty($_REQUEST['w3_get_css_post_type'])){
			$html .= 'rocket22'.$this->w3_preload_css_path().'--'.$this->add_settings['crictal_css'];
		}
		//	$html .= 'rocket 1'.$this->settings['load_critical_css'];
		if(!empty($this->settings['load_critical_css']) && $this->settings['load_critical_css'] == '1' && !$ignore_critical_css){
			if(!is_file($this->w3_preload_css_path().'/'.$this->add_settings['crictal_css'])){
				if(!empty($this->settings['optimization_on'])){
					$preload_css = $this->get_option('w3speedup_preload_css',1);
					$preload_css = !empty($preload_css) ? $preload_css : array();
					if(!array_key_exists($this->add_settings['full_url_without_param'],$preload_css)){
						$preload_css[$this->add_settings['full_url_without_param']] = array($this->add_settings['crictal_css'],2,$this->w3_preload_css_path());
						$this->update_option('w3speedup_preload_css',$preload_css,1);
					}
				}
			}else{
				$html = $this->w3_insert_content_head($html , '{{main_w3_critical_css}}',2);
				$critical_css = file_get_contents($this->w3_preload_css_path().'/'.$this->add_settings['crictal_css']);
				if(isset($this->hooks['customize_critical_css'])){
					eval($this->hooks['customize_critical_css']);
				}
				$html = str_replace("{{main_w3_critical_css}}",'<style>'.$critical_css.'</style>', $html);
			}
		}
		
		$html = $this->w3_debug_time($html,'before final output');
        return $html;
    } 
    
	public function w3_header_check() {
        return $this->isSpecialContentType()
	    	|| $this->isSpecialRoute()
	    	|| $_SERVER['REQUEST_METHOD'] === 'POST'
	    	|| $_SERVER['REQUEST_METHOD'] === 'PUT'
			|| $_SERVER['REQUEST_METHOD'] === 'DELETE';
	}

    private function isSpecialContentType() {
		if(isset($this->add_settings['headers']['Accept']) ) {
			$contentType = explode(',',$this->add_settings['headers']['Accept']);

			if( is_array($contentType) ) {
				foreach( $contentType as $name => $value ) {
					if( $value == "application/json" ) {
						return true;
					}
				}
			}
		}

		return false;
    }

    private function isSpecialRoute() {
		$current_url = $this->add_settings['full_url'];
		return false;
		if( preg_match('/(.*\/wp\/v2\/.*)/', $current_url) ) {
			return true;
		}

		if( preg_match('/(.*wp-login.*)/', $current_url) ) {
			return true;
		}

		if( preg_match('/(.*wp-admin.*)/', $current_url) ) {
			return true;
		}

		return false;
    }
	function w3_custom_js_enqueue($html){
		if(!empty($this->settings['custom_js'])){
			$custom_js = stripslashes($this->settings['custom_js']);
		}else{
			$custom_js = 'console.log("js loaded");';
		}
		$js_file_name1 = 'custom_js_after_load.js';
		if(!is_file($this->w3_get_cache_path('js').'/'.$js_file_name1)){
			$this->w3_create_file($this->w3_get_cache_path('js').'/'.$js_file_name1, $custom_js);
		}
		$html = $this->w3_str_replace_last('</body>','<script src="'.$this->add_settings['cache_url'].'/js/'.$js_file_name1.'"></script></body>',$html);
		return $html;
		
	}
    function w3_no_optimization($html){
		
        if(function_exists('w3speedup_exclude_page_optimization')){
            return w3speedup_exclude_page_optimization($html);
        }
		
        if(!empty($_REQUEST['orgurl']) || strpos($html,'<body') === false || strpos($html,'<?xml version=') !== false){
            return true;
        }
		
        if($this->w3_header_check()){
			return true;
		}
		
        //if(empty($this->settings['optimization_on']) && empty($_REQUEST['tester']) && empty($_REQUEST['testing'])){
        if(empty($this->settings['optimization_on']) && empty($_REQUEST['tester']) && empty($_REQUEST['testing'])){
             return true;
        }
		
        if($this->w3_check_if_page_excluded($this->settings['exclude_pages_from_optimization'])){
            return true;
        }
		
        if(!empty($_REQUEST['testing'])){
            return false;
        }
		return false;
    }
    function start($html,$curl){
		$this->_curl = $curl;
		
		//if(!empty($this->add_settings['full_url'])){
			//$html = file_get_contents($this->add_settings['full_url']);
			
			return $this->w3_speedup($html);
		//}
		
		
    }
    function w3_start_optimization_callback(){
        ob_start(array($this,"w3_speedup") );
		//add_action( 'shutdown', array($this,'w3_ob_end_flush'));
        //register_shutdown_function(array($this,'w3_ob_end_flush') );
    }
    
    function w3_ob_end_flush() {
    
        if (ob_get_level() != 0) {
    
            ob_end_flush();
    
         }
    
    }

    function lazyload($html, $all_links){
		$excluded_img = !empty($this->settings['exclude_lazy_load']) ? explode("\r\n",stripslashes($this->settings['exclude_lazy_load'])) : array();
	    if(!empty($this->settings['lazy_load_iframe'])){
            $iframe_links = $all_links['iframe'];
            foreach($iframe_links as $img){
				if(strpos($img,'\\') !== false){
					continue;
				}
                $exclude_image = 0;
                foreach( $excluded_img as $ex_img ){
                    if(!empty($ex_img) && strpos($img,$ex_img)!==false){
                        $exclude_image = 1;
                    }
                }
                if($exclude_image){
                    continue;
                }
                $img_obj = $this->w3_parse_link('iframe',$img);
                if(strpos($img_obj['src'],'youtu') !== false){
                    preg_match("#([\/|\?|&]vi?[\/|=]|youtu\.be\/|embed\/)([a-zA-Z0-9_-]+)#", $img_obj['src'], $matches);
                    if(empty($img_obj['style'])){
                        $img_obj['style'] = '';
                    }
                    $img_obj['style'] .= 'background-image:url(https://i.ytimg.com/vi/'.trim(end($matches)).'/sddefault.jpg)';
                }
                $img_obj['data-src'] = $img_obj['src'];
                $img_obj['src'] = 'about:blank';
                $img_obj['data-class'] = 'LazyLoad';
                $this->w3_str_replace_set_img($img,$this->w3_implode_link_array('iframe',$img_obj));
            }
	    }
        if(!empty($this->settings['lazy_load_video'])){
            $iframe_links = $all_links['video'];
			if(strpos($this->add_settings['upload_base_url'],$this->add_settings['home_url']) !== false){
				$v_src = $this->add_settings['image_home_url'].str_replace($this->add_settings['home_url'],'',$this->add_settings['upload_base_url']).'/blank.mp4';
			}else{
				$v_src = $this->add_settings['upload_base_url'].'/blank.mp4';
			}
            foreach($iframe_links as $img){
				if(strpos($img,'\\') !== false){
					continue;
				}
                $exclude_image = 0;
                foreach( $excluded_img as $ex_img ){
                    if(!empty($ex_img) && strpos($img,$ex_img)!==false){
                        $exclude_image = 1;
                    }
                }
                if($exclude_image){
                    continue;
                }
				
                $img_new = str_replace('src=','data-class="LazyLoad" src="'.$v_src.'" data-src=',$img);
                $this->w3_str_replace_set_img($img,$img_new);
            }
        }
        $img_links = $all_links['img'];
        if(!empty($all_links['img'])){
			$lazy_load_img = !empty($this->settings['lazy_load']) ? 1 : 0;
            $enable_cdn = 0;
            if($this->add_settings['image_home_url'] != $this->add_settings['home_url'] ){
                $enable_cdn = 1;
            }
            $exclude_cdn_arr = !empty($this->add_settings['exclude_cdn']) ? $this->add_settings['exclude_cdn'] : array();
			/*$blank_image_url = (!in_array('.png',$exclude_cdn_arr) ? $this->add_settings['image_home_url'] : $this->add_settings['home_url']).str_replace($this->add_settings['home_url'],'',$upload_dir['baseurl']).'/blank.png';*/
			$blank_image_url = $this->add_settings['upload_base_url'].'/blank.png';
            /*if($this->settings['webp_png'] == 'on'){
				$blank_image_url .= 'w3.webp';
			}*/
			$webp_enable = $this->add_settings['webp_enable'];
			$webp_enable_instance = $this->add_settings['webp_enable_instance'];
			$webp_enable_instance_replace = $this->add_settings['webp_enable_instance_replace'];
			//echo '<pre>';print_r($webp_enable_instance); print_r($webp_enable_instance_replace); exit;
			//include_once(W3SPEEDUP_PATH . '/includes/class_image.php');
			//$w3_speedup_opt_img = new w3speedup_optimize_image();
			foreach($img_links as $img){
			
			    if(strpos($imgnn_arr['src'],'?') !== false){
			        
			    }
                $exclude_image = 0;
                $imgnn = $img;
				$imgnn_arr = $this->w3_parse_link('img',str_replace($this->add_settings['image_home_url'],$this->add_settings['home_url'],$imgnn));
			//	echo '<!--start<pre>';
			//	print_r($imgnn_arr);
			//	echo '</pre>-->';
				if(empty($imgnn_arr['width'])){
				    $imgnn_arr['width'] = '';
				}
				if(empty($imgnn_arr['height'])){
				    $imgnn_arr['height'] = '';
				}
			//	echo '<!--END<pre>';
				//print_r($imgnn_arr);
			//	echo '</pre>--><br><br><br>';exit;
				if(empty($imgnn_arr['src'])){
					continue;
				}
                if(strpos($imgnn_arr['src'],'?') !== false){
					$temp_src = explode('?',$imgnn_arr['src']);
					$imgnn_arr['src'] = $temp_src[0];
				}
				if(strpos($imgnn_arr['src'],$_SERVER['HTTP_HOST']) === false && !$this->w3_is_external($imgnn_arr['src'])){
					$imgnn_arr['src'] = $this->add_settings['home_url'].'/'.ltrim($imgnn_arr['src'],'/');
					$imgnn = $this->w3_implode_link_array('img',$imgnn_arr);
				}
				$w3_img_ext = '.'.pathinfo($imgnn_arr['src'], PATHINFO_EXTENSION);
				$imgsrc_filepath = str_replace($this->add_settings['upload_base_url'],$this->add_settings['upload_base_dir'],$imgnn_arr['src']);
				$imgsrc_webpfilepath = str_replace($this->add_settings['upload_path'],$this->add_settings['webp_path'],$imgsrc_filepath).'w3.webp';
				if($enable_cdn){
                    $image_home_url = $this->add_settings['image_home_url'];
                    foreach($exclude_cdn_arr as $cdn){
                        if(strpos($img,$cdn) !== false){
                            $image_home_url = $this->add_settings['home_url'];
                            break;
                        }
                    }
                    $imgnn = str_replace($this->add_settings['home_url'],$image_home_url,$imgnn);
                }else{
                    $image_home_url = $this->add_settings['home_url'];
                }
				
				if(strpos($img, ' srcset=') === false){
					$img_size = is_file($imgsrc_filepath) ? @getimagesize($imgsrc_filepath) : array();
				}
				if(count($webp_enable) > 0 && in_array($w3_img_ext, $webp_enable)){
					if(is_file($imgsrc_webpfilepath)){
						$imgnn = str_replace($webp_enable_instance,$webp_enable_instance_replace,$imgnn);
					}
				}
				
                if($lazy_load_img){
					foreach( $excluded_img as $ex_img ){
						if(!empty($ex_img) && strpos($img,$ex_img)!==false){
							$exclude_image = 1;
						}
					}
				}else{
					$exclude_image = 1;
				}
				
                if($exclude_image){
                    if($img != $imgnn){
                        $this->w3_str_replace_set_img($img,$imgnn);
                    }
                    continue;
                }
                
            /*    if(strpos($imgnn, ' width=') === false){
                  
                    $imgnn = str_replace(' src=',' width="100%" src=',$imgnn);
                
                }
                if(strpos($imgnn, ' height=') === false){
                    $imgnn = str_replace(' src=',' height="100%" src=',$imgnn);
                }*/
                if(strpos($imgnn, ' srcset=') !== false){
                    $imgnn = str_replace(' srcset=',' data-srcset=',$imgnn);
                }
                $imgnn = str_replace(' src=',' data-class="LazyLoad" src="'. $blank_image_url .'" data-src=',$imgnn);
                $this->w3_str_replace_set_img($img,$imgnn);
            }
		}
       
        $html = $this->w3_convert_arr_relative_to_absolute($html, $this->add_settings['home_url'].'/index.php',$all_links['url']);
        return $html;
    }
    
}