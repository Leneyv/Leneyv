<?php
namespace W3speedup\Optimization\Model\includes;

class W3speedup{
    var $add_settings;
	var $settings,$hooks;
	
	public function __construct($settings,$hooks){
		$this->settings = $settings;
		/*if(!empty($_REQUEST['delete-wnw-cache'])){
            add_action('admin_init',array( $this, 'w3_remove_cache_files_hourly_event_callback') );
            add_action('admin_init',array( $this, 'w3_remove_cache_redirect') );
        }*/
		//add_action( 'wp_ajax_w3speedup_validate_license_key', array($this,'w3speedup_validate_license_key') );
		
		//$this->settings = $this->get_option( 'w3_speedup_option', true );
		$this->settings = !empty($this->settings) && is_array($this->settings) ? $this->settings : array();
        $this->add_settings = array();
		$this->add_settings['secure'] = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
		$this->add_settings['home_url'] = $this->add_settings['secure'].$_SERVER['HTTP_HOST'];/* edit */
        
        $this->add_settings['image_home_url'] = !empty($this->settings['cdn']) ? $this->settings['cdn'] : $this->add_settings['home_url'];
        $this->add_settings['w3_api_url'] = !empty($this->settings['w3_api_url']) ? $this->settings['w3_api_url'] : 'https://cloud.w3speedup.com/optimize/';
		//$sitename = 'home';
        $this->add_settings['document_root'] = $_SERVER['DOCUMENT_ROOT'];/* edit */
		$this->add_settings['w3speedup_url'] = $this->add_settings['home_url'].'/images';
		$this->add_settings['false_url'] = 0;
		$this->add_settings['full_url'] = $this->add_settings['secure'] . "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		//$this->add_settings['full_url'] = !empty($_REQUEST['opt_url']) ? $_REQUEST['opt_url'] : '';
        $full_url_array = explode('?',$this->add_settings['full_url']);
        $this->add_settings['full_url_without_param'] = $full_url_array[0];
        $this->add_settings['root_cache_path'] = (!empty($this->settings['cache_path']) ? $this->settings['cache_path'] : $this->add_settings['document_root'].'/cache');
		$this->add_settings['critical_css_path'] = $this->add_settings['root_cache_path'].'/critical-css';
        $this->add_settings['cache_url'] = str_replace($this->add_settings['document_root'],$this->add_settings['home_url'],$this->add_settings['root_cache_path']);
		$this->add_settings['upload_path'] = $this->add_settings['document_root'];
		$this->add_settings['webp_path'] = $this->add_settings['upload_path'].'/w3-webp';/* edit */
		$this->add_settings['upload_url'] = $this->add_settings['home_url'];
		$this->add_settings['webp_url'] = $this->add_settings['upload_url'].'/w3-webp';/* edit */
		$this->add_settings['upload_base_url'] = $this->add_settings['home_url'];
		$this->add_settings['upload_base_dir'] = $this->add_settings['document_root'];
		$useragent= @$_SERVER['HTTP_USER_AGENT'];
		$this->add_settings['is_mobile'] = 0;
		if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
			$this->add_settings['is_mobile'] = 1 ;
		}

        
        $this->add_settings['load_ext_js_before_internal_js'] = !empty($this->settings['load_external_before_internal']) ? explode("\r\n", $this->settings['load_external_before_internal']) : array();
        $this->add_settings['load_js_for_mobile_only'] = !empty($this->settings['load_js_for_mobile_only']) ? $this->settings['load_js_for_mobile_only'] : '';
		$this->add_settings['w3_rand_key'] = $this->get_option('w3_rand_key', 0);
        if($this->add_settings['is_mobile'] && $this->add_settings['load_js_for_mobile_only'] == 1){
            $this->settings['load_combined_js'] = 'after_page_load';
        }
        if(!empty($this->settings['css_mobile']) && $this->add_settings['is_mobile']){
            $this->add_settings['css_ext'] = 'mob.css';
            $this->add_settings['js_ext'] = 'mob.js';
            $this->add_settings['preload_css'] = !empty($this->settings['preload_css_mobile']) ? explode("\r\n", $this->settings['preload_css_mobile']) : array();
        }else{
            $this->add_settings['css_ext'] = '.css';
            $this->add_settings['js_ext'] = '.js';
            $this->add_settings['preload_css']  = !empty($this->settings['preload_css']) ? explode("\r\n", $this->settings['preload_css']) : array();
        }
		$this->add_settings['preload_css_url'] = array();
		$this->add_settings['headers'] = function_exists('getallheaders') ? getallheaders() : array();
        $this->add_settings['main_css_url'] = array();
        $this->add_settings['lazy_load_js'] = array();
        $this->add_settings['exclude_cdn'] = !empty($this->settings['exclude_cdn']) ? explode(',',str_replace(' ','',$this->settings['exclude_cdn'])) : '';
		$this->add_settings['webp_enable'] = array();
		$this->add_settings['webp_enable_instance'] = array($this->add_settings['upload_url']);
		$this->add_settings['webp_enable_instance_replace'] = array($this->add_settings['webp_url']);
		$this->settings['webp_png'] = isset($this->settings['webp_png']) ? $this->settings['webp_png'] : '';
		$this->settings['webp_jpg'] = !empty($this->settings['webp_jpg']) ? $this->settings['webp_jpg'] : '';
		if($this->settings['webp_jpg'] == 1){
			$this->add_settings['webp_enable'] = array_merge($this->add_settings['webp_enable'],array('.jpg','.jpeg'));
			$this->add_settings['webp_enable_instance'] = array_merge($this->add_settings['webp_enable_instance'],array('.jpg?','.jpeg?','.jpg','.jpeg','.jpg"','.jpeg"',".jpg'",".jpeg'"));
			$this->add_settings['webp_enable_instance_replace'] = array_merge($this->add_settings['webp_enable_instance_replace'],array('.jpgw3.webp?','.jpegw3.webp?','.jpgw3.webp','.jpegw3.webp','.jpgw3.webp"','.jpegw3.webp"',".jpgw3.webp'",".jpegw3.webp'"));
		}
		if($this->settings['webp_png'] == 1){
			$this->add_settings['webp_enable'] = array_merge($this->add_settings['webp_enable'],array('.png'));
			$this->add_settings['webp_enable_instance'] = array_merge($this->add_settings['webp_enable_instance'],array('.png?','.png','.png"',".png'"));
			$this->add_settings['webp_enable_instance_replace'] = array_merge($this->add_settings['webp_enable_instance_replace'],array('.pngw3.webp?','.pngw3.webp','.pngw3.webp"',".pngw3.webp'"));
		}
		$this->add_settings['htaccess'] = 0;
		if(is_file($this->add_settings['document_root']."/.htaccess")){
			$htaccess = file_get_contents($this->add_settings['document_root']."/.htaccess");
			if(strpos($htaccess,'W3WEBP') !== false){
				$this->add_settings['htaccess'] = 1;
			}
		}
		$this->add_settings['crictal_css'] = '';
		$this->add_settings['starttime'] = microtime();
        
		if(!empty($this->settings['image_home_url'])){
			$this->settings['image_home_url'] = rtrim(rtrim($this->settings['image_home_url']),'/');
		}
		if(!is_dir($this->add_settings['document_root'].'/images/w3-assets')){
			$this->w3_recurse_copy(W3SPEEDUP_PATH.'/assets',$this->add_settings['document_root'].'/images/w3-assets');
		}
		//echo W3SPEEDUP_PATH.'<pre>'; print_r($this->add_settings); exit;
	}
	function w3_recurse_copy($src,$dst) { 
		$dir = opendir($src); 
		@mkdir($dst); 
		while(false !== ( $file = readdir($dir)) ) { 
			if (( $file != '.' ) && ( $file != '..' )) { 
				if ( is_dir($src . '/' . $file) ) { 
					$this->w3_recurse_copy($src . '/' . $file,$dst . '/' . $file); 
				} 
				else { 
					//copy($src . '/' . $file,$dst . '/' . $file); 
				} 
			} 
		} 
		closedir($dir); 
	} 
	function update_option($filename,$html,$array = 1){
		$path = W3SPEEDUP_PATH.'/'.$filename.'.php';
		$file = fopen($path,'w');

		fwrite($file,($array ? json_encode($html) : $html));

		fclose($file);
	}
	function get_option($filename,$is_array =1){
		if(is_file(W3SPEEDUP_PATH.'/'.$filename.'.php')){
			if($is_array){
				return (array)json_decode(file_get_contents(W3SPEEDUP_PATH.'/'.$filename.'.php'));
			}else{
				return file_get_contents(W3SPEEDUP_PATH.'/'.$filename.'.php');
			}
		}
		if($is_array){
			return array();
		}else{
			return '';
		}
	}

	function w3_remove_ver_css_js( $src, $handle ){
		$src = remove_query_arg( 'ver', $src );
		return $src;
	}
	function w3_debug_time($html,$process){
		if(!empty($_REQUEST['w3_debug'])){
			$starttime = !empty($this->add_settings['starttime']) ? $this->add_settings['starttime'] : microtime(true);
			$endtime = microtime(true);
			return $html.$process.'-'.$this->formatPeriod($endtime ,$starttime);
		}
		return $html;
	}
	function formatPeriod($endtime, $starttime){

		$duration = $endtime - $starttime;

		$hours = (int) ($duration / 60 / 60);

		$minutes = (int) ($duration / 60) - $hours * 60;

		$seconds = (int) $duration - $hours * 60 * 60 - $minutes * 60;

		return ($hours == 0 ? "00":$hours) . ":" . ($minutes == 0 ? "00":($minutes < 10? "0".$minutes:$minutes)) . ":" . ($seconds == 0 ? "00":($seconds < 10? "0".$seconds:$seconds));
	}
	function w3_str_replace_last( $search , $replace , $str ) {
		if( ( $pos = strrpos( $str , $search ) ) !== false ) {
			$search_length  = strlen( $search );
			$str    = substr_replace( $str , $replace , $pos , $search_length );
		}
		return $str;
	}
	function w3speedup_validate_license_key($curl){
		if(!empty($_REQUEST['key'])){
			$response = $this->get_curl_url($this->add_settings['w3_api_url'].'get_license_detail.php?'.'license_id='.$_REQUEST['key'].'&domain='.base64_encode($this->add_settings['home_url']), $curl);
			if(!empty($response)){
				$res_arr = json_decode($response);				
				if($res_arr[0] == 'success'){
					echo json_encode(array('success','verified',$res_arr[1])); exit;
				}else{
					echo json_encode(array('fail','could not verify-1'.$response)); exit;
				}
			}else{
				echo json_encode(array('fail','could not verify-2')); exit;
			}
		}else{
			echo json_encode(array('fail','could not verify-3')); exit;
		}
		exit;
	}
	function w3_parse_url($src){
		$pattern = '/(.*)\/\/'.str_replace('/','\/',str_replace($this->add_settings['secure'],'',rtrim($this->add_settings['home_url'],'/'))).'(.*)/';
		$src = preg_replace($pattern, '$2', $src);
		$src_arr = parse_url($src);
		return $src_arr;
	}
	function get_home_path() {
		$home    = set_url_scheme( get_option( 'home' ), 'http' );
		$siteurl = set_url_scheme( get_option( 'siteurl' ), 'http' );
		if ( ! empty( $home ) && 0 !== strcasecmp( $home, $siteurl ) ) {
			$wp_path_rel_to_home = str_ireplace( $home, '', $siteurl ); /* $siteurl - $home */
			$pos                 = strripos( str_replace( '\\', '/', $_SERVER['SCRIPT_FILENAME'] ), trailingslashit( $wp_path_rel_to_home ) );
			$home_path           = substr( $_SERVER['SCRIPT_FILENAME'], 0, $pos );
			$home_path           = trailingslashit( $home_path );
		} else {
			$home_path = ABSPATH;
		}
	 
		return str_replace( '\\', '/', $home_path );
	}
    function w3_is_external($url) {
       $components = parse_url($url);
        return !empty($components['host']) && strcasecmp($components['host'], $_SERVER['HTTP_HOST']);
    }

    function w3_endswith($string, $test) {
        $str_arr = explode('?',$string);
        $string = $str_arr[0];
        $ext = '.'.pathinfo($str_arr[0], PATHINFO_EXTENSION);
        if($ext == $test)
            return true;
        else
            return false;
    }
	
	function w3_generate_preload_css($curl){
		
		if(empty($this->settings['optimization_on'])){
			return;
		}
		$preload_css = $this->get_option('w3speedup_preload_css',1);
		//echo "priload css";
		//print_r($preload_css);
		if(!empty($preload_css)){
			foreach($preload_css as $key => $url){
				echo 'rocket'.$key.$url[0].$url[1];
				if(empty($url[2])){
					unset($preload_css[$key]);
					continue;
				}
				$response = $this->w3_create_preload_css($key, $url[0], $url[2],$curl);
				if(!empty($response) && $response === "hold"){
					echo 'rocket5'.$response;
					break;
				}
				if($response || $preload_css[$key][1] == 1){
					echo 'rocket4';
					unset($preload_css[$key]);
				}else{
					echo 'rocket6';
					$preload_css[$key][1] = 1;
				}
				break;
			}
		}
		$this->update_option('w3speedup_preload_css',$preload_css,1);
	}
	
	
	function w3_css_compress_init( $minify ){
    	$minify = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $minify );
        $minify = str_replace( array("\r\n", "\r", "\n", "\t",'  ','    ', '    '), ' ', $minify );
    	return $minify;
    }
	function w3_create_preload_css($url,$filename, $css_path,$curl){
		if(!empty($_REQUEST['key']) && $this->settings['license_key'] == $_REQUEST['key']){
			$this->w3_remove_critical_css_cache_files();
		}
		echo 'rocket2'.$filename.$url;
		echo 'rocket3'.$css_path;
		if(is_file($css_path.'/'.$filename)){
			echo 'rocket9';
			return true;
		}
		$nonce = rand(10,100000);
		$this->update_option("purge_critical_css",$nonce,0);
		if($this->add_settings['home_url'] != $this->add_settings['image_home_url']){
			$css_urls = $this->add_settings['home_url'].','.$this->add_settings['image_home_url'];
		}else{
			$css_urls = $this->add_settings['home_url'];
		}
		/*$url_html = $this->get_curl_url($url);
		if ( !empty( $url_html ) ) {
			$url_html = $url_html;		
		}else{
			$url_html = '';		
		}*/
		$options = 'url='.$url.'&'.'key='.$this->settings['license_key'].'&_wpnonce='.$nonce.'&filename='.$filename.'&css_url='.$css_urls.'&path='.$css_path.'&html=';
			$options1 = $options;
			
		$response = $this->get_curl_url($this->add_settings['w3_api_url'].'css/index-magento.php?'.$options, $curl);
		//$options1['html'] = '';
		echo '<pre>'; print_r($options1);
		echo $response;
		if( !empty( $response ) ) {
			echo 'rocket3'.$css_path.'/'.$filename;
			echo $response;
			if(!empty($response)){
				$response_arr = (array)json_decode($response);
				if(!empty($response_arr['result']) && $response_arr['result'] == 'success'){
					$this->w3_create_file($css_path.'/'.$filename, $response_arr['w3_css']);
					$preload_css = $this->get_option('w3speedup_preload_css',1);
					unset($preload_css[$response_arr['url']]);
					$this->update_option('w3speedup_preload_css',$preload_css,1);
					if(is_file($file = $this->w3_get_full_url_cache_path($url).'/main_css.json')){
						unlink($file);
					}
					return true;
				}elseif(!empty($response_arr['error']) && $response_arr['error'] == 'process already running'){
					return 'hold';
				}
				return false;
			}else{
				return false;
			}
		}else{
			return false;
		}
		
	}
	
	function w3_preload_css_path($url=''){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$page_request = $objectManager->get('Magento\Framework\App\Action\Context')->getRequest();
		$url = empty($url) ? $this->add_settings['full_url_without_param'] : $url;
		if(!empty($this->add_settings['preload_css_url'][$url])){
			return $this->add_settings['preload_css_url'][$url];
		}
		if(rtrim($url,'/') == rtrim($this->add_settings['home_url'],'/')){
			
		}else{
			if ($page_request->getFullActionName() == 'catalog_product_view') {
				$url = rtrim($this->add_settings['home_url'],'/').'/'.'catalog_product_view';
			}
			if ($page_request->getFullActionName() == 'catalog_category_view') {
				$url = rtrim($this->add_settings['home_url'],'/').'/'.'catalog_category_view';
			}
		}
		$full_url = str_replace($this->add_settings['secure'],'',rtrim($url,'/'));
		$path = $this->w3_get_critical_cache_path($full_url);
		$this->add_settings['preload_css_url'][$url] = $path;
		return $path;
	}
	function w3_put_preload_css(){
		if ( !isset( $_REQUEST['_wpnonce'] ) || $_REQUEST['_wpnonce'] != get_option('purge_critical_css')) {
			echo 'Request not valid'; exit;
		}
		if(!empty($_REQUEST['url']) && !empty($_REQUEST['filename']) && !empty($_REQUEST['w3_css'])){
			$url = $_REQUEST['url'];
			$preload_css = get_option('w3speedup_preload_css');
			echo $path = !empty($preload_css[$_REQUEST['filename']][2]) ? $preload_css[$_REQUEST['filename']][2] : $_REQUEST['path'];
			$this->w3_create_file($path.'/'.$_REQUEST['filename'], stripslashes($_REQUEST['w3_css']));
			unset($preload_css[$_REQUEST['url']]);
			update_option('w3speedup_preload_css',$preload_css,'no');
			if(is_file($file = $this->w3_get_full_url_cache_path($url).'/main_css.json')){
				unlink($file);
			}
			echo 'saved';
		}
		echo false;
		exit;
	}
    function w3_create_file($path, $text = '//'){
        $file = fopen($path,'w');
        fwrite($file,$text);
        fclose($file);
        chmod($path, 0644); 
        return true;
    }

    function w3_parse_link($tag,$link){
        $xmlDoc = new \DOMDocument();
        if (@$xmlDoc->loadHTML($link) === false){
            return array();
        }
        $tag_html = $xmlDoc->getElementsByTagName($tag);
        $link_arr = array();
        if(!empty($tag_html[0])){
            foreach ($tag_html[0]->attributes as $attr) {
                $link_arr[$attr->nodeName] = $attr->nodeValue;
            }
        }
        return $link_arr;
    }

    function w3_implode_link_array($tag,$array){
        $link = '<'.$tag.' ';
        foreach($array as $key => $arr){
            $link .= $key.'="'.$arr.'" ';
        }
        if($tag == 'script'){
            $link .= '></script>';
        }else{
            $link .= '/>';
        }
        return $link;
    }
    function w3_insert_content_head_in_json(){
        global $insert_content_head;
        $file = $this->w3_get_full_url_cache_path().'/content_head.json';
        $this->w3_create_file($file,json_encode($insert_content_head));
    }
    
    function w3_insert_content_head($html, $content, $pos){
        global $insert_content_head;
        $insert_content_head[] = array($content,$pos);
        if($pos == 1){
    
            $html = preg_replace('/<style/',  $content.'<style', $html, 1);
    
        }elseif($pos == 2){
    
            $html = preg_replace('/<link(.*)href="([^"]*)"(.*)>/',$content.'<link$1href="$2"$3>',$html,1);
    
        }else{
    
            $html = preg_replace('/<script/',  $content.'<script', $html, 1);
    
        }
    
        return $html;
    
    }
    function w3_main_css_url_to_json(){
        global $main_css_url;
        if(empty($main_css_url)){
            $main_css_url = array();
        }
        $file = $this->w3_get_full_url_cache_path().'/main_css.json';
        $this->w3_create_file($file,json_encode($main_css_url));
    }
    function w3_internal_js_to_json(){
        global $internal_js;
        if(empty($internal_js)){
            $internal_js = array();
        }
        $file = $this->w3_get_full_url_cache_path().'/main_js.json';
        $this->w3_create_file($file,json_encode($internal_js));
    }
    function w3_str_replace_set($str,$rep){
        global $str_replace_str_array, $str_replace_rep_array;
        $str_replace_str_array[] = $str;
        $str_replace_rep_array[] = $rep;
        //echo '<pre>'; print_r($this->str_replace_str_array);
    }
    
    function w3_str_replace_set_img($str,$rep){
        global $str_replace_str_img, $str_replace_rep_img;
        $str_replace_str_img[] = $str;
        $str_replace_rep_img[] = $rep;
    }
    function w3_str_replace_bulk_img($html){
        global $str_replace_str_img, $str_replace_rep_img;
        $this->w3_create_file($this->w3_get_full_url_cache_path().'/img.json',json_encode(array($str_replace_str_img,$str_replace_rep_img)));
        $html = str_replace($str_replace_str_img,$str_replace_rep_img,$html);
        return $html;
    }

    function w3_str_replace_set_js($str,$rep){
        global $str_replace_str_js, $str_replace_rep_js;
        $str_replace_str_js[] = $str;
        $str_replace_rep_js[] = $rep;
    }
    function w3_str_replace_bulk_js($html){
        global $str_replace_str_js, $str_replace_rep_js;
        $this->w3_create_file($this->w3_get_full_url_cache_path().'/js.json',json_encode(array($str_replace_str_js,$str_replace_rep_js)));
        $html = str_replace($str_replace_str_js,$str_replace_rep_js,$html);
        return $html;
    }
    
    function w3_str_replace_bulk_json($html,$str=array(), $rep=array()){
        if(!empty($rep['php'])){
            $rep['php'] = '<style>'.file_get_contents($rep['php']).'</style>';
        }
        $html = str_replace($str,$rep,$html);
        return $html;
    }
    
    function w3_str_replace_set_css($str,$rep,$key=''){
        global $str_replace_str_css, $str_replace_rep_css;
        if($key){
            $str_replace_str_css[$key] = $str;
            $str_replace_rep_css[$key] = $rep;
        }else{
            $str_replace_str_css[] = $str;
            $str_replace_rep_css[] = $rep;
        }
    }
    function w3_str_replace_bulk_css($html){
        global $str_replace_str_css, $str_replace_rep_css;
        $this->w3_create_file($this->w3_get_full_url_cache_path().'/css.json',json_encode(array($str_replace_str_css,$str_replace_rep_css)));
        if(!empty($str_replace_rep_css['php'])){
            $str_replace_rep_css['php'] = '<style>'.file_get_contents($str_replace_rep_css['php']).'</style>';
        }
        $html = str_replace($str_replace_str_css,$str_replace_rep_css,$html);
        return $html;
    }

    function w3_str_replace_bulk($html){
        global $str_replace_str_array, $str_replace_rep_array;
        $html = str_replace($str_replace_str_array,$str_replace_rep_array,$html);
        return $html;
    }
    
    function w3_get_wp_cache_path($path=''){
        $cache_path = $this->add_settings['wp_cache_path'].(!empty($path) ? '/'.$path : '');
        $this->w3_check_if_folder_exists($cache_path);
        return $cache_path;
    }
	function w3_get_cache_path($path=''){
        $cache_path = $this->add_settings['root_cache_path'].(!empty($path) ? '/'.$path : '');
        $this->w3_check_if_folder_exists($cache_path);
        return $cache_path;
    }
	function w3_get_critical_cache_path($path=''){
        $cache_path = $this->add_settings['critical_css_path'].(!empty($path) ? '/'.$path : '');
        $this->w3_check_if_folder_exists($cache_path);
        return $cache_path;
    }
	
	function w3_get_full_url_cache_path($full_url=''){
		$full_url = !empty($full_url) ? $full_url : $this->add_settings['full_url'];
        $url_array = parse_url($full_url);
		$query = !empty($url_array['query']) ? '/?'.$url_array['query'] : '';
        $full_url_arr = explode('/',trim($url_array['path'],'/').$query);
        $cache_path = $this->w3_get_cache_path('all');
        foreach($full_url_arr as $path){
            $cache_path .= '/'.md5($path);
        }
        
        $this->w3_check_if_folder_exists($cache_path);
        return $cache_path;
    }
    function w3_check_if_folder_exists($path){
		
		if(is_dir($path)){
			return $path;
		}
		try {
		  mkdir($path,0755,true); 
		}
		catch(Exception $e) {
		  echo 'Message: '.$path .$e->getMessage();
		}
        return $path;
    }
	function post_curl_url($url, $query){
		$URL = $this->add_settings['w3_api_url'].'css/index-magento.php';
		//set curl options
		$curl->setOption(CURLOPT_HEADER, 0);
		$curl->setOption(CURLOPT_TIMEOUT, 10);
		$curl->setOption(CURLOPT_RETURNTRANSFER, true);
		$curl->setOption(CURLOPT_CUSTOMREQUEST, 'GET');
		//set curl header
		$curl->addHeader("Content-Type", "application/json");
		//$curl->addHeader("Content-Length", 200);
		//post request with url and data
		$curl->post($URL, $params);
		//read response
		$response = $curl->getBody();
		return $response;

	}
    function get_curl_url($url, $curl){
		//set curl options
		$curl->setOption(CURLOPT_HEADER, 0);
		$curl->setOption(CURLOPT_TIMEOUT, 10);
		$curl->setOption(CURLOPT_RETURNTRANSFER, true);
		$curl->setOption(CURLOPT_CUSTOMREQUEST, 'GET');
		//set curl header
		$curl->addHeader("Content-Type", "application/json");
		//$curl->addHeader("Content-Length", 200);
		//post request with url and data
		$curl->get($url);
		//read response
		$response = $curl->getBody();
		return $response;
    }
    
    function optimize_image($width,$url,$is_webp = false){
		$key = $this->settings['license_key'];
		$key_activated = $this->settings['is_activated'];
		if(empty($key) || empty($key_activated)){
			return "License key not activated.";
		}
        $width = $width < 1920 ? $width : 1920;
        if($is_webp){
			$q = !empty($this->settings['webp_quality']) ? $this->settings['webp_quality'] : '';
			return $this->get_curl_url($this->add_settings['w3_api_url'].'basic.php?key='.$key.'&width='.$width.'&q='.$q.'&url='.urlencode($url).'&webp=1');
		}else{
			$q = !empty($this->settings['img_quality']) ? $this->settings['img_quality'] : '';
			return $this->get_curl_url($this->add_settings['w3_api_url'].'basic.php?key='.$key.'&width='.$width.'&q='.$q.'&url='.urlencode($url));
		}
    }

    function w3_combine_google_fonts($full_css_url){
        if(empty($this->settings['google_fonts'])){		
            return false;
        }
        global $fonts_api_links,$fonts_api_links_css2;
		$url_arr = parse_url(str_replace('#038;','&',$full_css_url));
		if(strpos($url_arr['path'],'css2') !== false){
			$query_arr = explode('&',$url_arr['query']);
			if(!empty($query_arr) && count($query_arr) > 0){
				foreach($query_arr as $family){
					if(strpos($family,'family') !== false){
						$fonts_api_links_css2[] = $family;
					}
				}
				return true;
			}
			return false;
		
		}elseif(!empty($url_arr['query'])){
			parse_str($url_arr['query'], $get_array);
			if(!empty($get_array['family'])){
				$font_array = explode('|',$get_array['family']);
				foreach($font_array as $font){
					
					if(!empty($font)){
						$font_split = explode(':',$font);
							
						if(empty($font_split[0])){
							continue;
						}
						if(empty($fonts_api_links[$font_split[0]]) || !is_array($fonts_api_links[$font_split[0]])){
							$fonts_api_links[$font_split[0]] = array();
						}
						$fonts_api_links[$font_split[0]] = !empty($font_split[1]) ? array_merge($fonts_api_links[$font_split[0]],explode(',',$font_split[1])) : $fonts_api_links[$font_split[0]];
					}
				}
				return true;
			}
			return false;
		}
		return false;
    }

    function w3_get_tags_data_html($data,$start_tag,$end_tag){
        $data_exists = 0; $i=0;
        $tag_char_len = strlen($start_tag);
        $end_tag_char_len = strlen($end_tag);
        $script_array = array();
        while($data_exists != -1 && $i<500) {
            $data_exists = strpos($data,$start_tag,$data_exists);
            if(!empty($data_exists)){
                $end_tag_pointer = strpos($data,$end_tag,$data_exists);
                $script_array[] = substr($data, $data_exists, $end_tag_pointer-$data_exists+$end_tag_char_len);
                $data_exists = $end_tag_pointer;
            }else{
                $data_exists = -1;
            }
            $i++;
        }
        return $script_array;
    }
	function w3_get_tags_data($data,$start_tag,$end_tag){
        $data_exists = 0; $i=0;
        $tag_char_len = strlen($start_tag);
        $end_tag_char_len = strlen($end_tag);
        $script_array = array();
        while($data_exists != -1 && $i<500) {
            $data_exists = strpos($data,$start_tag,$data_exists);
            if(!empty($data_exists)){
                $end_tag_pointer = strpos($data,$end_tag,$data_exists);
                $script_array[] = substr($data, $data_exists, $end_tag_pointer-$data_exists+$end_tag_char_len);
                $data_exists = $end_tag_pointer;
            }else{
                $data_exists = -1;
            }
            $i++;
        }
        return $script_array;
    }
	
	private function w3_cache_rmdir($dir) {
		if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object){
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir" && $object != 'critical-css'){
                        $this->w3_cache_rmdir($dir."/".$object);
                    }else{
                      @unlink($dir."/".$object);
                    }
                }
            }
            reset($objects);
            @unlink($dir);
        }
	}
	private function w3_rmdir($dir) {
        //echo $dir; exit;
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object){
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir"){
                        $this->w3_rmdir($dir."/".$object);
                    }else{
                      @unlink($dir."/".$object);
                    }
                }
            }
            reset($objects);
            @unlink($dir);
        }
    }

    function w3_remove_cache_files_hourly_event_callback() {
        $this->w3_cache_rmdir($this->w3_get_wp_cache_path());
        $this->w3_create_random_key();
        return $this->w3_cache_size_callback();
    
    }
	function w3_remove_critical_css_cache_files() {
		update_option('critical_css_delete_time',date('d:m:Y::h:i:sa').json_encode($_REQUEST),'no');
        $this->w3_rmdir($this->w3_get_critical_cache_path());
		$this->w3_delete_server_cache();
		update_option('w3speedup_preload_css','','no');
        return true;
    
    }
    function w3_delete_server_cache(){
		$options = array(
			'method'      => 'POST',
			'timeout'     => 10,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => array(),
			'body'        => array(
				'url' => $this->add_settings['home_url'],
				'key' => $this->settings['license_key'],
				'_wpnonce'	=> $nonce
			),
			'cookies'     => array()
			);
			
		$response = wp_remote_post($this->add_settings['w3_api_url'].'css/delete-css.php',$options);
		if( !is_wp_error( $response ) ) {
			return true;
		}else{
			return false;
		}
	}
    function w3_remove_cache_redirect(){
        header("Location:".add_query_arg(array('delete_wp_speedup_cache'=>1),remove_query_arg('delete-wnw-cache',false)));
        exit;
    }

    function w3_optimize_image(){
        $image_url = $_REQUEST['url'];
        $image_width = !empty($_REQUEST['width']) ? $_REQUEST['width'] : '';
        $url_array = parse_url($image_url);
        $image_size = !empty($image_width) ? array($image_width) : getimagesize($document_root.$url_array['path']);
        $optmize_image = optimize_image($image_size[0],$image_url);
        $optimize_image_size = @imagecreatefromstring($optmize_image);
        if(empty($optimize_image_size)){
            echo 'invalid image'; exit;
        }else{    
            $image_type = array('gif','jpg','png','jpeg');
            $type = explode('.',$image_url);
            $type = array_reverse($type);
            if(in_array($type[0],$image_type)){
                rename($document_root.$url_array['path'],$document_root.$url_array['path'].'org.'.$type[0]);
                file_put_contents($document_root.$url_array['path'],$optmize_image);
                chmod($document_root.$url_array['path'], 0775);
                echo $document_root.$url_array['path'];
            }
        }
        exit;
    }

    function w3_setAllLinks($data,$resources=array()){
        $resource_arr = array();
        $comment_tag = $this->w3_get_tags_data($data,'<!--','-->');
        $data = str_replace($comment_tag,'',$data);
        if(!empty($this->settings['js']) && $this->settings['js'] == 1 && in_array('script',$resources)){
            $resource_arr['script'] = $this->w3_get_tags_data($data,'<script','</script>');
           	$data = str_replace($resource_arr['script'],'',$data);
        }else{
			$resource_arr['script'] = array();
		}
        
        if( in_array('img',$resources) ){
            $resource_arr['img'] = $this->w3_get_tags_data($data,'<img','>');
        }else{
			$resource_arr['img'] = array();
		}
        if(!empty($this->settings['css']) && $this->settings['css'] == 1 && in_array('link',$resources) ){
            $resource_arr['link'] = $this->w3_get_tags_data(str_replace('<style','<link',$data),'<link','>');
		}else{
			$resource_arr['link'] = array();
		}
        //$style_tag = $this->w3_get_tags_data($data,'<style','</style>');
        if(in_array('iframe',$resources)){
            $resource_arr['iframe'] = $this->w3_get_tags_data($data,'<iframe','>');
        }else{
			$resource_arr['iframe'] = array();
		}
        if(in_array('video',$resources)){
            $resource_arr['video'] = $this->w3_get_tags_data($data,'<video','</video>');
        }else{
			$resource_arr['video'] = array();
		}
		if(in_array('url',$resources)){
            $resource_arr['url'] = $this->w3_get_tags_data($data,'url(',')');
        }else{
			$resource_arr['url'] = array();
		}
        return $resource_arr;
    }

    function w3_get_cache_file_size(){
        $dir = $this->w3_get_cache_path();
        $size = 0;
        foreach (glob(rtrim($dir, '/').'/*', GLOB_NOSORT) as $each) {
            $size += is_file($each) ? filesize($each) : $this->w3_folderSize($each);
        }
        return ($size / 1024) / 1024;
    }
        
    function w3_foldersize($path) {
        $total_size = 0;
        $files = scandir($path);
        $cleanPath = rtrim($path, '/'). '/';
        foreach($files as $t) {
            if ($t<>"." && $t<>"..") {
                $currentFile = $cleanPath . $t;
                if (is_dir($currentFile)) {
                    $size = $this->w3_foldersize($currentFile);
                    $total_size += $size;
                }
                else {
                    $size = filesize($currentFile);
                    $total_size += $size;
                }
            }   
        }
        return $total_size;
    }
    function w3_cache_size_callback() {
        $filesize = $this->w3_get_cache_file_size();
        update_option('w3_speedup_filesize',$filesize,true);
        return $filesize;
    }
    function w3_create_random_key(){
        $this->update_option('w3_rand_key',rand(10,1000),0);
    }
    
    function w3_get_pointer_to_inject_files($html){
        global $appendonstyle;
        if(!empty($appendonstyle)){
            return $appendonstyle;
        }

        $start_body_pointer = strpos($html,'<body');

        $start_body_pointer = $start_body_pointer ? $start_body_pointer : strpos($html,'</head');

        $head_html = substr($html,0,$start_body_pointer);
        $comment_tag = $this->w3_get_tags_data($head_html,'<!--','-->');
        foreach($comment_tag as $comment){
            $head_html = str_replace($comment,'',$head_html);
        }
        

        if(strpos($head_html,'<style') !== false){

            $appendonstyle=1;

        }elseif(strpos($head_html,'<link') !== false){

            $appendonstyle=2;

        }else{

            $appendonstyle=3;

        }
        return $appendonstyle;
    }

    function w3_check_if_page_excluded($exclude_setting){
        
        $e_p_from_optimization = !empty($exclude_setting) ? explode("\r\n",$exclude_setting) : array();
        
        if(!empty($e_p_from_optimization)){
            foreach( $e_p_from_optimization as $e_page ){
				if(empty($e_page)){
					continue;
				}
                if(empty($_REQUEST['testing']) && $this->add_settings['home_url'] == $e_page){
                    return true;
                }else if($this->add_settings['home_url'] != $e_page){
                    if(strpos($this->add_settings['full_url'], $e_page)!==false){
                        return true;
                    }
                }
            }			
        }
        return false;
    }
	public function w3_is_plugin_active( $plugin ) {
		return in_array( $plugin, (array) get_option( 'active_plugins', array() ) ) || $this->w3_is_plugin_active_for_network( $plugin );
	}
	
	public function w3_is_plugin_active_for_network( $plugin ) {
		if ( !is_multisite() )
			return false;

		$plugins = get_site_option( 'active_sitewide_plugins');
		if ( isset($plugins[$plugin]) )
			return true;

		return false;
	}
	function w3_check_super_cache($path, $htaccess){
		if($this->w3_is_plugin_active('wp-super-cache/wp-cache.php')){
			return array("WP Super Cache needs to be deactive", "error");
		}else{
			@unlink($path."wp-content/wp-cache-config.php");

			$message = "";
			
			if(is_file($path."wp-content/wp-cache-config.php")){
				$message .= "<br>- be sure that you removed /wp-content/wp-cache-config.php";
			}

			if(preg_match("/supercache/", $htaccess)){
				$message .= "<br>- be sure that you removed the rules of super cache from the .htaccess";
			}

			return $message ? array("WP Super Cache cannot remove its own remnants so please follow the steps below".$message, "error") : "";
		}

		return "";
	}

}