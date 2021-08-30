<?php
namespace W3speedup\Optimization\Model\includes;
//use W3speedup\Optimization\Model\includes\W3speedup;

class w3speedup_css extends W3speedup{
    public function __construct($settings,$hooks){
		parent::__construct($settings,$hooks);
	}
    function w3_css_compress( $minify ){
    	$minify = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $minify );
        $minify = str_replace( array("\r\n", "\r", "\n", "\t",'  ','    ', '    '), ' ', $minify );
    	return $minify;
    }
	function w3_relative_to_absolute_path($url, $string){
		$url_new = $url;
		$url_arr = $this->w3_parse_url($url);
        $url = $this->add_settings['home_url'].$url_arr['path'];
        $matches = $this->w3_get_tags_data($string,'url(',')');
		return $this->w3_convert_arr_relative_to_absolute($string, $url, $matches);
    
    }
	function w3_convert_arr_relative_to_absolute($string, $url, $matches){
		$webp_enable = $this->add_settings['webp_enable'];
		$webp_enable_instance = $this->add_settings['webp_enable_instance'];
		$webp_enable_instance_replace = $this->add_settings['webp_enable_instance_replace'];
		$replaced = array();
		$replaced_new = array();
		$replace_array = explode('/',str_replace('\'','/',$url));
		array_pop($replace_array);
		$url_parent_path = implode('/',$replace_array);
		foreach($matches as $match){
			if(strpos($match,'data:') !== false || strpos($match,'chrome-extension:') !== false){
    
                continue;
    
			}
		    $org_match = $match;
    
            $match1 = str_replace(array('url(',')',"url('","')",')',"'",'"','&#039;'), '', html_entity_decode($match));
    
            $match1 = trim($match1);
    
            if(strpos($match1,'//') > 7){
    
                $match1 = substr($match1, 0, 7).str_replace('//','/', substr($match1, 7));
    
            }
    
            if(strpos($match1[0],'#') !== false){
				continue;
			}
    
            
            if(strpos($match,'fonts.googleapis.com') !== false){
                $string = $this->w3_combine_google_fonts($match1) ? str_replace('@import '.$match.';','', $string) : $string;
                continue;
			}
			if(strpos($match,'../fonts/fontawesome-webfont.') !== false){
                $font_text = str_replace('../','',$match1);
                $font_text = str_replace('fonts/fontawesome-webfont.','',$font_text);
                $string = str_replace($match,'url(https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/fonts/fontawesome-webfont.'.$font_text.')',$string);
                continue;
			}
			if($this->w3_is_external($match1)){
                continue;
			}
			$match_arr = $this->w3_parse_url($match1);
			if($match1[0] == '/' || strpos($match1,'http') !== false){
				$match1 = $this->add_settings['home_url'].'/'.trim($match_arr['path'],'/');
				$import_match = $match1;
			}else{
				$match1 = $url_parent_path.'/'.trim($match_arr['path'],'/');
				$import_match = $url_parent_path.'/'.trim($match_arr['path'],'/');
			}
			if(strpos($match1,'.css')!== false && strpos($string,'@import '.$match)!== false && $url != $this->add_settings['home_url'].'/index.php'){
                $string = str_replace('@import '.$match.';','@import "'.$import_match.'";', $string);
                continue;
			}
			
			$img_arr = explode('?',$match1 );
			$ext = '.'.pathinfo($img_arr[0], PATHINFO_EXTENSION);
			if(count($webp_enable) > 0 && in_array($ext, $webp_enable)){
				$imgsrc_filepath = str_replace($this->add_settings['upload_base_url'],$this->add_settings['upload_base_dir'],$img_arr[0]);
				$imgsrc_webpfilepath = str_replace($this->add_settings['upload_path'],$this->add_settings['webp_path'],$imgsrc_filepath).'w3.webp';
				
				if(is_file($imgsrc_webpfilepath)){
					$match1 = str_replace($webp_enable_instance,$webp_enable_instance_replace,$img_arr[0]);
				}
			}
			if($match1[0] == '/' || strpos($match1,'http') !== false){
				if($this->add_settings['image_home_url'] == $this->add_settings['home_url'])
					$replacement = 'url('.$match1.')';
				else{
					$match_arr = $this->w3_parse_url($match1);
					$replacement = 'url('.$this->add_settings['home_url'].'/'.trim($match_arr['path'],'/').')';
				}
			}else{
				$match_arr = $this->w3_parse_url($match1);
				$replacement = 'url('.$url_parent_path.'/'.trim($match_arr['path'],'/').')';
			}
			if($this->add_settings['image_home_url'] != $this->add_settings['home_url']){
				if(empty($this->add_settings['exclude_cdn']) || !in_array($ext,$this->add_settings['exclude_cdn'])){
					$replacement  = str_replace($this->add_settings['home_url'],$this->add_settings['image_home_url'],$replacement );
				}
			}
			if(strpos($url,'index.php') !== false){
				$this->w3_str_replace_set_img($org_match, $replacement);
			}else{
            	$string = str_replace($org_match, $replacement, $string);
			}
        }				
		return $string;
	}
    function w3_create_file_cache_css($path){
        $cache_file_path = $this->w3_get_cache_path('css').'/'.md5($path).'.css';
        if( !file_exists($cache_file_path) ){
            $this->w3_check_if_folder_exists($this->w3_get_cache_path('css'));
			$css = file_get_contents($this->add_settings['document_root'].$path);
			$css = str_replace(array('@charset "utf-8";','@charset "UTF-8";'),'',$css);
			if(isset($this->hooks['internal_css_customize'])){
				eval($this->hooks['internal_css_customize']);
			}
			$minify = $this->w3_relative_to_absolute_path($this->add_settings['home_url'].$path,$css);
			$css_minify = 1;
			if(isset($this->hooks['internal_css_minify'])){
				eval($this->hooks['internal_css_minify']);
			}
			if($css_minify){
				$minify = $this->w3_css_compress($minify);
			}
			$this->w3_create_file($cache_file_path, $minify);
        }
        return str_replace($this->add_settings['document_root'],'',$cache_file_path);
    }
    
    function w3_create_file_cache_css_url($url){
        $cache_file_path = $this->w3_get_cache_path('css').'/'.md5($url).'.css';
        if( !file_exists($cache_file_path) ){
            $css = file_get_contents($url);
			if(isset($this->hooks['internal_css_customize'])){
				eval($this->hooks['internal_css_customize']);
			}
			$minify = $this->w3_css_compress($this->w3_relative_to_absolute_path($url,$css));
            $this->w3_create_file($cache_file_path, $minify);
        }
        return str_replace($this->add_settings['document_root'],'',$cache_file_path);
    }

    function minify_css($html, $css_links){
		if($this->w3_check_if_page_excluded($this->settings['exclude_page_from_load_combined_css'])){
			return $html;
		}
		
		global $main_css_url,$fonts_api_links;
        $all_css1 = '';
		$fonts_api_links = array();
   		$i= 1;
		if(!empty($css_links) && $this->settings['css'] == 1){
			$included_css = array();
			$main_included_css = array();
			$final_merge_css = array();
			$final_merge_main_css = array();
			$css_file_name = '';
			$exclude_css_from_minify = !empty($this->settings['exclude_css']) ? explode("\r\n", $this->settings['exclude_css']) : array();
			$preload_css = $this->add_settings['preload_css'];
			$force_lazyload_css	= !empty($this->settings['force_lazyload_css']) ? explode("\r\n", $this->settings['force_lazyload_css']) : array();
			$enable_cdn = 0;
			if($this->add_settings['image_home_url'] != $this->add_settings['home_url']){
				$ext = '.css';
				if(empty($this->add_settings['exclude_cdn']) || !in_array($ext,$this->add_settings['exclude_cdn'])){
					$enable_cdn = 1;
				}
			}
			
			$create_css_file = 0;
			$combined_css_file_counter = 0;
			$css_links_arr = array();
			foreach($css_links as $key => $css){
				$css_obj = $this->w3_parse_link('link',str_replace($this->add_settings['image_home_url'],$this->add_settings['home_url'],$css));
				if( (!empty($css_obj['rel']) && $css_obj['rel'] == 'stylesheet' && !empty($css_obj['href'])) || empty($css_obj['rel']) ){
					$css_links_arr[] = array('arr'=>$css_obj,'css'=>$css);
				}
			}
			foreach($css_links_arr as $key => $link_arr){
				$css = $link_arr['css'];
				$css_obj = $link_arr['arr'];
				
				if(!empty($css_obj['rel']) && $css_obj['rel'] == 'stylesheet' && !empty($css_obj['href'])){
					$css_next_obj = !empty($css_links_arr[$key+1]['arr']) ? $css_links_arr[$key+1]['arr'] : array();
					if(!$create_css_file && (empty($css_next_obj['rel']) || (!empty($css_next_obj['href']) && $this->w3_is_external($css_next_obj['href']))) ){
						$create_css_file = 1;
					}
				
					$org_css = '';
					$media = '';
					$exclude_css1 = 0;
					if(!empty($exclude_css_from_minify)){
						foreach($exclude_css_from_minify as $ex_css){
							if(!empty($ex_css) && strpos($css, $ex_css) !== false){
								$exclude_css1 = 1;
							}
						}
					}
					if($exclude_css1){
						if($enable_cdn && $this->w3_endswith($css_obj['href'], '.css')){
							$this->w3_str_replace_set_css($css,str_replace($this->add_settings['home_url'],$this->add_settings['image_home_url'],$css));
						}
						continue;
					}
					$force_lazy_load = 0;
					if(!empty($force_lazyload_css)){
						foreach($force_lazyload_css as $ex_css){
							if(!empty($ex_css) && strpos($css, $ex_css) !== false){
								$force_lazy_load = 1;
							}
						}
					}
					if($force_lazy_load){
						$this->w3_str_replace_set_css($css,str_replace(' href=',' href="data:text/css;charset=utf-8;base64,LypibGFuayov" data-href=',$css));
						continue;
					}
					if(!empty($css_obj['media']) && $css_obj['media'] != 'all' && $css_obj['media'] != 'screen'){
						$media = $css_obj['media'];
					}
					$url_array = $this->w3_parse_url($css_obj['href']);
					$pppp = explode("/",$url_array['path']);
					$cleanTags= preg_grep("/version/", $pppp, PREG_GREP_INVERT);
					$url_array['path'] = implode("/",$cleanTags);
					$url_array['path'] = '/'.ltrim($url_array['path'],'/');
					if(!$this->w3_is_external($css_obj['href'])){
						if($this->w3_endswith($css_obj['href'], '.php') || strpos($css_obj['href'], '.php?') !== false ){
							$org_css = $url_array['path'];
							$url_array['path'] = $this->w3_create_file_cache_css_url($css_obj['href']);
							$css_obj['href'] = $this->add_settings['home_url'].$url_array['path'];
						}elseif(!is_file($this->add_settings['document_root'].$url_array['path'])){
							if($this->w3_endswith($css_obj['href'], '.css') || strpos($css_obj['href'], '.css?') !== false ){
								$this->w3_str_replace_set_css($css,'');
								continue;
							}
							$org_css = $url_array['path'];
							$url_array['path'] = $this->w3_create_file_cache_css_url($css_obj['href']);
							$css_obj['href'] = $this->add_settings['home_url'].$url_array['path'];
						}elseif(filesize($this->add_settings['document_root'].$url_array['path']) > 0){
							$org_css = $url_array['path'];
							$url_array['path'] = $this->w3_create_file_cache_css($url_array['path']);
							$css_obj['href'] = $url_array['path'];
						}else{
							$this->w3_str_replace_set_css($css,'');
							continue;
						}
					}
					if(!empty($url_array['host']) && $url_array['host'] == 'fonts.googleapis.com'){
						$response = $this->w3_combine_google_fonts($css_obj['href']);
						if($response){
							$this->w3_str_replace_set_css($css,'');
						}
						continue;
					}
					$src = $css_obj['href'];
					if(!empty($src) && !$this->w3_is_external($src) && $this->w3_endswith($src, '.css')){
						$filename = $this->add_settings['document_root'].$url_array['path'];
						if(file_exists($filename) && filesize($filename) > 0){
							$final_merge_css[$combined_css_file_counter][] = array('media'=>$media,'src'=>$url_array['path'],'org-src'=>$org_css);
						}
						if($create_css_file){
							
							$combined_css_file = $this->w3_generate_combined_css($final_merge_css[$combined_css_file_counter], $enable_cdn);
							$this->w3_str_replace_set_css($css,'{{'.$combined_css_file.'}}');
							$combined_css_files[] = $combined_css_file;
							$combined_css_file_counter++;
							$create_css_file = 0;
						}else{
							$remove_css_tags[] = $css;
						}
					}elseif($this->w3_endswith($src, '.css') || strpos($src, '.css?')){
						//$main_css_url[] = $src;
						$this->w3_str_replace_set_css($css,'{{'.$css_obj['href'].'}}');
						$combined_css_files[] = $css_obj['href'];
						$this->w3_str_replace_set_css($css,'');
					}
				}
			}
			if(!empty($final_merge_css[$combined_css_file_counter])){
				$first_css = $remove_css_tags[0];
				$combined_css_file = $this->w3_generate_combined_css($final_merge_css[$combined_css_file_counter], $enable_cdn);
				$this->w3_str_replace_set_css($first_css,'{{'.$combined_css_file.'}}');
				$combined_css_files[] = $combined_css_file;
			}
			if(!empty($remove_css_tags)){
				foreach($remove_css_tags as $css){
					$this->w3_str_replace_set_css($css,'');
				}
			}
			$appendonstyle = $this->w3_get_pointer_to_inject_files($html);
			
			if(is_array($final_merge_css) && count($final_merge_css) > 0){
				$file_name = '';
				foreach($final_merge_css as $css_arr){
					$file_name = '';
					if(function_exists('w3speedup_customize_critical_css_filename')){
						$final_merge_css = w3speedup_customize_critical_css_filename($final_merge_css);
					}
					foreach($css_arr as $css){
						if(!empty($css['org-src'])){
							$css_url = explode('?',$css['org-src']);
							$file_name .= '-'.$css_url[0];
						}
					}
				}
				$main_css_file_name = md5($file_name).$this->add_settings['css_ext'];
				$this->add_settings['crictal_css'] = $main_css_file_name;
				$css_defer = '';
				$ignore_critical_css = 0;
				if(function_exists('w3_no_critical_css')){
					 if(w3_no_critical_css($this->add_settings['full_url'])){
						$ignore_critical_css = 1; 
					 }
				}
				if(is_file($this->w3_preload_css_path().'/'.$this->add_settings['crictal_css']) && !empty($this->settings['load_critical_css']) && $this->settings['load_critical_css'] == 1 && !$ignore_critical_css){
					$css_defer = 'href="data:text/css;charset=utf-8;base64,LypibGFuayov" data-';
				}
				$all_inline_css = (!empty($this->settings['custom_css']) ? $this->w3_css_compress($this->settings['custom_css']) : '').'@keyframes fadeIn {  to {    opacity: 1;  }}.fade-in {  opacity: 0;  animation: fadeIn .5s ease-in 1 forwards;}.is-paused {  animation-play-state: paused;}';
				foreach($combined_css_files as $css){
					$this->w3_str_replace_set_css('{{'.$css.'}}','<link rel="stylesheet" '.$css_defer.'href="'.$css.'" />');
				}
				$this->w3_str_replace_set_css('</head>','<style id="w3speedup-custom-css">'.$all_inline_css.'</style></head>');
			}
		}
		return $html;
	}
	function w3_generate_combined_css($final_merge_css,$enable_cdn){
		if(is_array($final_merge_css) && count($final_merge_css) > 0){
			$file_name = $this->get_option('w3_rand_key',0);
			foreach($final_merge_css as $css){
				$file_name .= '-'.$css['src'];
			}
		
			if(!empty($file_name)){
				$css_file_name = md5($file_name).$this->add_settings['css_ext'];
				if(!file_exists($this->w3_get_cache_path('all-css').'/'.$css_file_name)){
					$all_css = '';
					foreach($final_merge_css as $key => $css){
						$inline_css_var = file_get_contents($this->add_settings['document_root'].$css['src'])."\n";
						$all_css .= !empty($css['media']) ? '@media '.$css['media'].'{'.$inline_css_var.'}' : $inline_css_var ;
					}
					$this->w3_create_file($this->w3_get_cache_path('all-css').'/'.$css_file_name, $all_css);
				}
				$secondary_css = $this->add_settings['cache_url'].'/all-css/'.$css_file_name;
				if($enable_cdn){
					$secondary_css = str_replace($this->add_settings['home_url'],$this->add_settings['image_home_url'],$secondary_css);
				}
				return $secondary_css;
			}
		}
	}
}