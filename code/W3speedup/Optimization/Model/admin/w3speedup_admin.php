<?php
namespace W3speedup\Optimization\Model\admin;
use W3speedup\Optimization\Model\includes\W3speedup;

class w3speedup_admin extends W3speedup{
	var $w3_images;
    function launch($settings){
		$this->settings = $settings;
		print_r($this->settings); echo 'sfdafds';
		if(!empty($_REQUEST['action']) && $_REQUEST['action'] == 'w3speedup_validate_license_key'){
			$this->w3speedup_validate_license_key();
		}
		if(!empty($_REQUEST['import_text'])){
			$import_text = (array)json_decode($_REQUEST['import_text']);
			if($import_text !== null){
				$this->update_option( 'w3_speedup_option',  $import_text, 1);
			}
		}
		if(isset($_REQUEST['license_key'])){
			$this->w3_save_options();
		}
		if(!empty($_REQUEST['w3_reset_preload_css'])){
			$this->update_option('w3speedup_preload_css','');
			add_action( 'admin_notices', array($this,'w3_admin_notice_import_success') );
		}
		if(!empty($_REQUEST['restart'])){
			$this->update_option('w3speedup_img_opt_status',0);
		}
		if(!empty($_REQUEST['reset'])){
			$this->update_option('w3speedup_opt_offset',0);
		}
		$this->w3_speedup_options_page();
	}
	
	function w3_calc_images($dir) {
		if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object){
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) != "dir"){
						if(pathinfo($dir."/".$object, PATHINFO_EXTENSION) == "jpeg" || pathinfo($dir."/".$object, PATHINFO_EXTENSION) == "jpg" || pathinfo($dir."/".$object, PATHINFO_EXTENSION) == "png"){
							$this->w3_images[] = $dir."/".$object;
						}
                    }else{
						$this->w3_calc_images($dir."/".$object);
                    }
                }
            }
            reset($objects);
        }
	}
	function w3_save_options(){
		if(isset($_POST['ws_action']) && $_POST['ws_action'] == 'cache'){
			unset($_POST['ws_action']);
			foreach($_POST as $key=>$value){
				$array[$key] = $value;
			}
			if(empty($array['license_key'])){
				$array['is_activated'] = '';
			}
			$this->update_option( 'w3_speedup_option', $array );		
			$this->settings = $this->get_option( 'w3_speedup_option');
			//$this->w3_modify_htaccess();
			
		}
	}
    function get_curl_url($url, $curl){
      return parent::get_curl_url($url, $curl);
    }
    function w3_speedup_options_page(){
		include_once realpath($_SERVER['DOCUMENT_ROOT']) .'/app/code/Meetanshi/DeferJS/Model/admin/admin.php';	
    }
    

    function w3_cache_purge_action_js() { ?>
    <script type="text/javascript" >
        jQuery("li#wp-admin-bar-w3_speedup_purge_cache .ab-item, #del_js_css_cache").on( "click", function() {
            jQuery('#del_js_css_cache').attr('disabled',true);
			jQuery('#w3_speedup_cache_purge').show();
            jQuery('.w3-speedup-cache-purge-text').hide();
            var data = {
                        'action': 'w3_speedup_cache_purge',
						'_wpnonce':'<?php echo wp_create_nonce("purge_cache");?>'
                        };

            jQuery.get(ajaxurl, data, function(response) {
                jQuery('#w3_speedup_cache_purge').hide();
                jQuery('.w3-speedup-cache-purge-text').show();
                jQuery('.w3-speedup-cache-purge-text').text('Cache Deleted!');
                jQuery('.cache_folder_size').text(response+" MB");
				jQuery('#del_js_css_cache').attr('disabled',false);
                setTimeout(() => {
                    jQuery('.w3-speedup-cache-purge-text').text('Delete W3Speedup cache');
                }, 2000);
            }).fail(function() {
                jQuery('#w3_speedup_cache_purge').hide();
                jQuery('.w3-speedup-cache-purge-text').show();
                jQuery('.w3-speedup-cache-purge-text').text('try again');
                jQuery('.cache_folder_size').text(response+" MB");
				jQuery('#del_js_css_cache').attr('disabled',false);
                setTimeout(() => {
                    jQuery('.w3-speedup-cache-purge-text').text('Delete W3Speedup cache');
                }, 2000);
            });

        });
		jQuery("#del_critical_css_cache").on( "click", function() {
            jQuery('#del_critical_css_cache').attr('disabled',true);
			var data = {
                        'action': 'w3_speedup_critical_cache_purge',
						'_wpnonce':'<?php echo wp_create_nonce("purge_critical_css");?>'
						};

            jQuery.get(ajaxurl, data, function(response) {
                jQuery('#del_critical_css_cache').attr('disabled',false);
            }).fail(function() {
				jQuery('#del_critical_css_cache').attr('disabled',false);
            });

        });
    </script> <?php
    }

    function w3_speedup_cache_purge_callback() {
		if ( !isset( $_REQUEST['_wpnonce'] ) || !wp_verify_nonce( $_REQUEST['_wpnonce'],'purge_cache') ) {
			if(!empty($_REQUEST['resource_url'])){
				$url = str_replace(array($this->add_settings['home_url'],$this->add_settings['image_home_url']),'',$_REQUEST['resource_url']);
				if(is_file($this->add_settings['document_root'].'/'.ltrim($url,'/'))){
					echo 'Request not valid'; exit;
				}
			}else{
				echo 'Request not valid'; exit;
			}
		}
        $w3speedup_init = new w3speedup();
        $response =round( (int)$w3speedup_init->w3_remove_cache_files_hourly_event_callback(),2);
        //$response = round( (int)$this->get_option('w3_speedup_filesize') / 1024/1024 , 2);
        echo $response;
        wp_die();
    }
	function w3_speedup_critical_cache_purge_callback() {
		if ( !isset( $_REQUEST['_wpnonce'] ) || !wp_verify_nonce( $_REQUEST['_wpnonce'],'purge_critical_css') ) {
			return 'Request not valid';
		}
        $w3speedup_init = new w3speedup();
        $response =round( (int)$w3speedup_init->w3_remove_critical_css_cache_files(),2);
        //$response = round( (int)$this->get_option('w3_speedup_filesize') / 1024/1024 , 2);
        echo $response;
        wp_die();
    }
    function w3_toolbar_link_to_delete_cache( $wp_admin_bar ) {

        $filesize = round($this->get_option('w3_speedup_filesize',false),2);
    
        $args = array(
    
            'id'    => 'w3_speedup_purge_cache',
    
            'title' => '<div class="w3-speedup-spinner-container">
            <div id="w3_speedup_cache_purge"></div></div>
          <style>#w3_speedup_cache_purge {
            width: 20px;
            height: 20px;
            margin: 4px 0px 0px 0px;
            background: transparent;
            border-top: 4px solid #009688;
            border-right: 4px solid transparent;
            border-radius: 50%;
            -webkit-animation: 1s spin linear infinite;
            animation: 1s spin linear infinite;
            display:none;
          }
          .w3-speedup-spinner-container{
          overflow:hidden;
            }
          
          
          
          -webkit-@keyframes spin {
            -webkit-from {
              -webkit-transform: rotate(0deg);
              -ms-transform: rotate(0deg);
              transform: rotate(0deg);
            }
            -webkit-to {
              -webkit-transform: rotate(360deg);
              -ms-transform: rotate(360deg);
              transform: rotate(360deg);
            }
          }
          
          @-webkit-keyframes spin {
            from {
              -webkit-transform: rotate(0deg);
              transform: rotate(0deg);
            }
            to {
              -webkit-transform: rotate(360deg);
              transform: rotate(360deg);
            }
          }
          
          @keyframes spin {
            from {
              -webkit-transform: rotate(0deg);
              transform: rotate(0deg);
            }
            to {
              -webkit-transform: rotate(360deg);
              transform: rotate(360deg);
            }
          }
          }</style>
         <div class="w3-speedup-cache-purge-text">Delete W3Speedup cache</div><div class="cache_size"><div></div><div><span>File Size</span>&nbsp;&nbsp;&nbsp;<span class="cache_folder_size">'.$filesize.'&nbsp;MB</span></div></div><style>.wp-speedup-page .cache_size{display:none;}.wp-speedup-page:hover .cache_size{background: #000;padding: 5px 10px !important;display:block;}</style>',
    
            'href'  => '#',
    
            'meta'  => array( 'class' => 'wp-speedup-page' )
    
        );
    
        $wp_admin_bar->add_node( $args );
    
    }  
	
	function w3_modify_htaccess(){
			

		}
		
		function w3_insert_rewrite_rule($htaccess){
			if(!empty($this->settings['html_cache'])){
				$htaccess = preg_replace("/#\s?BEGIN\s?W3Cache.*?#\s?END\s?W3Cache/s", "", $htaccess);
				$htaccess = $this->w3_get_htaccess().$htaccess;
			}else{
				$htaccess = preg_replace("/#\s?BEGIN\s?W3Cache.*?#\s?END\s?W3Cache/s", "", $htaccess);
				$this->deleteCache();
			}

			return $htaccess;
		}
		function w3_insert_gzip_rule($htaccess){
			if(!empty($this->settings['lbc'])){
		    	$data = "# BEGIN W3Gzip"."\n".
		          		"<IfModule mod_deflate.c>"."\n".
		          		"AddType x-font/woff .woff"."\n".
		          		"AddType x-font/ttf .ttf"."\n".
		          		"AddOutputFilterByType DEFLATE image/svg+xml"."\n".
		  				"AddOutputFilterByType DEFLATE text/plain"."\n".
		  				"AddOutputFilterByType DEFLATE text/html"."\n".
		  				"AddOutputFilterByType DEFLATE text/xml"."\n".
		  				"AddOutputFilterByType DEFLATE text/css"."\n".
		  				"AddOutputFilterByType DEFLATE text/javascript"."\n".
		  				"AddOutputFilterByType DEFLATE application/xml"."\n".
		  				"AddOutputFilterByType DEFLATE application/xhtml+xml"."\n".
		  				"AddOutputFilterByType DEFLATE application/rss+xml"."\n".
		  				"AddOutputFilterByType DEFLATE application/javascript"."\n".
		  				"AddOutputFilterByType DEFLATE application/x-javascript"."\n".
		  				"AddOutputFilterByType DEFLATE application/x-font-ttf"."\n".
		  				"AddOutputFilterByType DEFLATE x-font/ttf"."\n".
						"AddOutputFilterByType DEFLATE application/vnd.ms-fontobject"."\n".
						"AddOutputFilterByType DEFLATE font/opentype font/ttf font/eot font/otf"."\n".
		  				"</IfModule>"."\n";

				$data = $data."# END W3Gzip"."\n";

				$htaccess = preg_replace("/\s*\#\s?BEGIN\s?W3Gzip.*?#\s?END\s?W3Gzip\s*/s", "", $htaccess);
				return $data.$htaccess;

			}else{
				//delete gzip rules
				$htaccess = preg_replace("/\s*\#\s?BEGIN\s?W3Gzip.*?#\s?END\s?W3Gzip\s*/s", "", $htaccess);
				return $htaccess;
			}
		}
		function w3_insert_LBC_rule($htaccess){
			if(!empty($this->settings['lbc'])){


				$data = "# BEGIN W3LBC"."\n".
					'<FilesMatch "\.(webm|ogg|mp4|ico|pdf|flv|jpg|jpeg|png|gif|webp|js|css|swf|x-html|css|xml|js|woff|woff2|otf|ttf|svg|eot)(\.gz)?$">'."\n".
					'<IfModule mod_expires.c>'."\n".
					'AddType application/font-woff2 .woff2'."\n".
					'AddType application/x-font-opentype .otf'."\n".
					'ExpiresActive On'."\n".
					'ExpiresDefault A0'."\n".
					'ExpiresByType video/webm A10368000'."\n".
					'ExpiresByType video/ogg A10368000'."\n".
					'ExpiresByType video/mp4 A10368000'."\n".
					'ExpiresByType image/webp A10368000'."\n".
					'ExpiresByType image/gif A10368000'."\n".
					'ExpiresByType image/png A10368000'."\n".
					'ExpiresByType image/jpg A10368000'."\n".
					'ExpiresByType image/jpeg A10368000'."\n".
					'ExpiresByType image/ico A10368000'."\n".
					'ExpiresByType image/svg+xml A10368000'."\n".
					'ExpiresByType text/css A10368000'."\n".
					'ExpiresByType text/javascript A10368000'."\n".
					'ExpiresByType application/javascript A10368000'."\n".
					'ExpiresByType application/x-javascript A10368000'."\n".
					'ExpiresByType application/font-woff2 A10368000'."\n".
					'ExpiresByType application/x-font-opentype A10368000'."\n".
					'ExpiresByType application/x-font-truetype A10368000'."\n".
					'</IfModule>'."\n".
					'<IfModule mod_headers.c>'."\n".
					'Header set Expires "max-age=A10368000, public"'."\n".
					'Header unset ETag'."\n".
					'Header set Connection keep-alive'."\n".
					'FileETag None'."\n".
					'</IfModule>'."\n".
					'</FilesMatch>'."\n".
					"# END W3LBC"."\n";

				$htaccess = preg_replace("/#\s?BEGIN\s?W3LBC.*?#\s?END\s?W3LBC/s", "", $htaccess);
				$htaccess = $data.$htaccess;
				return $htaccess;
			}else{
				//delete levere browser caching
				$htaccess = preg_replace("/#\s?BEGIN\s?W3LBC.*?#\s?END\s?W3LBC/s", "", $htaccess);
				return $htaccess;
			}
		}
		function w3_insert_webp($htaccess){
			if(!empty($this->settings['webp_png']) || !empty($this->settings['webp_jpg']))
				$webp = true;
				if($webp){
					$wp_content_arr = explode('/',trim($this->add_settings['wp_content_path'],'/'));
					$wp_content = array_pop($wp_content_arr);
					$wp_content_webp = $wp_content."/w3-webp/";
					$basename = $wp_content_webp."$1w3.webp";
					/* 
						This part for sub-directory installation
						WordPress Address (URL): site_url() 
						Site Address (URL): home_url()
					*/
					if(preg_match("/https?\:\/\/[^\/]+\/(.+)/", site_url(), $siteurl_base_name)){
						if(preg_match("/https?\:\/\/[^\/]+\/(.+)/", home_url(), $homeurl_base_name)){
							/*
								site_url() return http://example.com/sub-directory
								home_url() returns http://example.com/sub-directory
							*/

							$homeurl_base_name[1] = trim($homeurl_base_name[1], "/");
							$siteurl_base_name[1] = trim($siteurl_base_name[1], "/");

							if($homeurl_base_name[1] == $siteurl_base_name[1]){
								if(preg_match("/".preg_quote($homeurl_base_name[1], "/")."$/", trim(ABSPATH, "/"))){
									$basename = $homeurl_base_name[1]."/".$basename;
								}
							}
						}else{
							/*
								site_url() return http://example.com/sub-directory
								home_url() returns http://example.com/
							*/
							$siteurl_base_name[1] = trim($siteurl_base_name[1], "/");
							$basename = $siteurl_base_name[1]."/".$basename;
						}
					}

					if(ABSPATH == "//"){
						$RewriteCond = "RewriteCond %{DOCUMENT_ROOT}/".$basename." -f"."\n";
					}else{
						// to escape spaces
						$tmp_ABSPATH = str_replace(" ", "\ ", ABSPATH);

						$RewriteCond = "RewriteCond %{DOCUMENT_ROOT}/".$basename." -f [or]"."\n";
						$RewriteCond = $RewriteCond."RewriteCond ".$tmp_ABSPATH.$wp_content_webp."$1w3.webp -f"."\n";
					}
					
					$data = "# BEGIN W3WEBP"."\n".
							"<IfModule mod_rewrite.c>"."\n".
							"RewriteEngine On"."\n".
							"RewriteCond %{HTTP_ACCEPT} image/webp"."\n".
							"RewriteCond %{REQUEST_URI} \.(jpe?g|png)"."\n".
							$RewriteCond.
							"RewriteRule ^".$wp_content."/(.*) /".$basename." [L]"."\n".
							"</IfModule>"."\n".
							"<IfModule mod_headers.c>"."\n".
							"Header append Vary Accept env=REDIRECT_accept"."\n".
							"</IfModule>"."\n".
							"AddType image/webp .webp"."\n".
							"# END W3WEBP"."\n";
					$htaccess = preg_replace("/#\s?BEGIN\s?W3WEBP.*?#\s?END\s?W3WEBP/s", "", $htaccess);
					$htaccess = $data.$htaccess;
					
		
					
					return $htaccess;
			}else{
				$htaccess = preg_replace("/#\s?BEGIN\s?W3WEBP.*?#\s?END\s?W3WEBP/s", "", $htaccess);
				return $htaccess;
			}
		}
		function createimageinstantly($imges=array()){
		$x=$y=300;
		
		$uploads = wp_upload_dir();
		
	
		//header('Content-Type: image/png');
		//$targetFolder = '/gw/media/uploads/processed/';
		//$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
		$targetPath = $uploads['basedir'];		
		
		if(!empty($imges)){
			$height_array = array();
			$max_width = 0;
			$images_detail = array();
			foreach($imges as $key=>$img){
				$size = getimagesize($img);
				//$size2 = getimagesize($img2);
				//$size3 = getimagesize($img3);			
				//$height_array = array($size1[1], $size2[1] ,$size3[1]);				
				//$max_width = ($size1[0]+$size2[0]+$size3[0])+60 ;	
				$size['src'] = $img ;				
				$height_array[] = $size[1];				
				$max_width = $max_width+$size[0]+20 ;
				$images_detail[$key] = 	$size ;
			}
			$max_height = max($height_array);
			
			
			$outputImage = imagecreatetruecolor( $max_width, $max_height);

			// set background to white
			$white = imagecolorallocate($outputImage, 0, 0, 0);
			//imagefill($outputImage, 0, 0, $white);
			imagecolortransparent($outputImage, $white);
			
			/*
			$first = imagecreatefrompng($img1);
			$second = imagecreatefrompng($img2);
			$third = imagecreatefrompng($img3);

			//imagecopyresized ( resource $dst_image , resource $src_image , int $dst_x , int $dst_y , int $src_x , int $src_y , int $dst_w , int $dst_h , int $src_w , int $src_h )
			
			
			imagecopyresized($outputImage,$first,0,0,0,0, $size1[0], $size1[1],$size1[0], $size1[1]);
			
			imagecopyresized($outputImage,$second,($size1[0]+20),0,0,0, $size2[0], $size2[1], $size2[0], $size2[1]);
			
			imagecopyresized($outputImage,$third,($size1[0]+$size2[1]+40),0,0,0, $size3[0], $size3[1],$size3[0], $size3[1]); */
			
						
			$new_coordinates = 0;
			$new_images_detail = array();
			foreach($images_detail as $key=>$img){					
				$new_image = imagecreatefrompng($img['src']);
				imagecopyresized($outputImage,$new_image,$new_coordinates,0,0,0, $img[0], $img[1],$img[0], $img[1]);
				$new_coordinates = $new_coordinates+$img[0]+20;					
			}			
			
			// Add the text
			//imagettftext ( resource $image , float $size , float $angle , int $x , int $y , int $color , string $fontfile , string $text )
			//$white = imagecolorallocate($im, 255, 255, 255);
			$text = 'School Name Here';
			$font = 'OldeEnglish.ttf';
			//imagettftext($outputImage, 32, 0, 150, 150, $white, $font, $text);
			
			$wp_upload_dir = wp_upload_dir();
			
			$image_name = 'combine_image_'.round(microtime(true)).'.png';
			$filename = $wp_upload_dir['path'].'/'.$image_name;
			imagepng($outputImage, $filename);			
			
			// create attachment post			
			$filetype = wp_check_filetype( basename( $image_name ), null );

			// Prepare an array of post data for the attachment.
			$attachment = array(
				'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
				'post_mime_type' => $filetype['type'],
				'post_title'     => sanitize_title(preg_replace( '/\.[^.]+$/', '', basename( $filename ) )),
				'post_content'   => '',
				'post_status'    => 'inherit'
			);
			
			
			$attach_id = wp_insert_attachment( $attachment, $filename, 0 );
			// Include image.php
			require_once(ABSPATH . 'wp-admin/includes/image.php');

			// Define attachment metadata
			$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );

			// Assign metadata to attachment
			wp_update_attachment_metadata( $attach_id, $attach_data );
			
			$this->update_option( 'w3_speedup_combine_image_id', $attach_id, 'no' );
			
			imagedestroy($outputImage);
		}
	}

  function get_ws_optimize_image($image_url, $image_width){
		$w3_speedup_img = new w3speedup_optimize_image(); 
		$result = $w3_speedup_img->w3_optimize_attachment($image_url, $image_width, false,'',true);
		return $result['img'] == 1 ? 'success' : 'failed' ;
	}
	
	function w3_save_combined_images(){
		if(isset($_POST['ws_action']) && $_POST['ws_action'] == 'combine_image_save'){
			$c_array['combine_images'] = $_POST['combine_images'];		
			$this->update_option( 'w3_speedup_combine_images', $c_array,'no' );
			
			$c_images_src = array();
			if(isset($c_array['combine_images']) && !empty($c_array['combine_images'])){
				foreach($c_array['combine_images'] as $value){ 
					if(!empty($value['src'])){
						$c_images_src[] = $value['src'] ;
					}
				}		
			}
					
			if(!empty($c_images_src)){		
				createimageinstantly($c_images_src);
			}
			
		}
		
		
		$combine_images = $this->get_option( 'w3_speedup_combine_images' );
	}
	function notify($message = array()){
			if(isset($message[0]) && $message[0]){
				if(function_exists("add_settings_error")){
					add_settings_error('wpfc-notice', esc_attr( 'settings_updated' ), $message[0], $message[1]);
				}
			}
		}
}