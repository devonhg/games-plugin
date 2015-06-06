<?php
if ( ! defined( 'WPINC' ) ) { die; }

//This is a static class that contains functions important to the plugin. 
class MYPLUGIN_func{

	//This is the core function behind the hooks
	public static function get_post( $quer = null ){
		if (!$quer == null){ $post = $quer; }
		else{ global $post; }	
		
		return get_post($post->ID); 	
	}

	//This is the function that prints the meta for any given post.
	public static function print_meta( $ID ){
		$out = "";
		$pst_meta = MYPLUGIN_pt_meta::$instances;
		$out .= "<ul>";
			foreach($pst_meta as $key){
				if ( ($key->get_val( $ID ) != null && $key->get_val( $ID ) != "") && 
				$key->pt == get_post_type( $ID ) && 
				!( $key->hidden ) && 
				$key->type !== "media" ){

					$out .= "<li>";
						$out .= "<label class='meta-key' >" . ucfirst( $key->title ) . "</label>";
						$out .= ' : <label class="meta-value"> ' . $key->get_val() . '</label>';
					$out .= "</li>";
				}
			} 
		$out .= "</ul>";

		return $out; 
	}

	//Much like meta, only for media (images, video, etc.)
	public static function print_media( $ID ){
		$out = "";
		$pst_meta = MYPLUGIN_pt_meta::$instances;
		$out .= "<ul class='media-list'>";
			foreach($pst_meta as $key){
				if ( $key->pt == get_post_type( $ID ) && !( $key->hidden ) && $key->type == "media" ){
					$link = $key->get_val();
					$out .= MYPLUGIN_func::media_check( $link );
				}
			} 
		$out .= "</ul>";

		return $out; 
	}

	//This function checks the media meta type, and modifies the output to match it. 
	public static function media_check( $link ){
		if ( strpos($link,'.jpeg') !== false || strpos($link,'.jpg') !== false ||  strpos($link,'.png') !== false || 
				strpos($link,'.gif') || strpos($link,'.ico') || strpos($link,'.svg') ){
			return "<img class='media-element' style='max-width: 100%; height:auto' src='" . $link . "'>";
		}
		if ( strpos($link,'.mp4') !== false || strpos($link,'.m4v') !== false || strpos($link,'.mov') !== false || 
				strpos($link,'.wmv') !== false || strpos($link,'.avi') !== false || strpos($link,'.mpg') !== false ||
				strpos($link,'.ogv') !== false || strpos($link,'.3gp') !== false || strpos($link,'.3g2') !== false){
			return "<video class='media-element' style='max-width: 100%; height:auto' src='" . $link . 
					"' controls> <source type='video/" . substr( $link, -3 ) . "' src='" . $link . "'> </video>";
		}		
	}

	// Function by David Paulsson, get ID from slug. Modified to also consider post type by Devon Godfrey.  
	// MYPLUGIN_get_id_by_slug('any-page-slug');
	public static function get_id_by_slug($page_slug, $pt) {
		$page = get_page_by_path($page_slug, OBJECT, $pt);
		if ($page) {
			return $page->ID;
		} else {
			return null;
		}
	} 

	//This is a helper function, used to gather the categories for a given post. 
	public static function get_cats( $tax , $post_slug = null, $pt = null ){
	    $output = "";
	    $tax_terms = get_terms( $tax );
	    $tax_info = get_taxonomy( $tax ); 

	    $pID = MYPLUGIN_func::get_id_by_slug( $post_slug, $pt );

	    if ( ! empty( $tax_terms ) ){
	            foreach ($tax_terms as $tax_term){
	            	if (has_term($tax_term->slug, $tax, $pID)){
				        $output .= "<h3>" . $tax_info->labels->name . "</h3>";
				        $output .= "<ul>";
				        break; 
	            	}
	            }

	            foreach ($tax_terms as $tax_term){
	            	if (has_term($tax_term->slug, $tax, $pID)){
	                	$output .= '<li>' . '<a href="' . esc_attr(get_term_link($tax_term, $tax)) . '" title="' . 
	                		sprintf( __( "View all items in %s" ), $tax_term->name ) . '" ' . '>' . $tax_term->name . 
	                		'</a></li>';
	            	}
	            }
	        $output .= "</ul>"; 
	    }

	    return $output;	
	}

	public static function asc_string_to_array( $string ){
		if ( $string != "" && $string != null && is_string( $string ) ){
			$string = str_replace(" ", "", $string );
			$string = str_replace("'", "", $string );
			$string = str_replace('"', "", $string );
			
			$arr = array(); 
			$and = explode( "&", $string );
			foreach( $and as $a ){
				//If the entry contains ":", its a keyed array
				if (strpos($a,':') !== false ) {
					//Break key from first value
						$keyArr = explode( ":", $a );
					//Break out each key of assoc. array. 
						$assArr = explode( ",", $keyArr[1]);
					//Key/Value array
						foreach( $assArr as $asar ){ 
							$break = explode( "=", $asar );
							if ( isset( $break[1] ) ){
								$kv[$break[0]] = $break[1]; 
								$assarr = true; 
							}else{
								$kv[] = $break[0];
							}
						}
					//Add the array to our array
						if ( isset($assarr) ){
							$arr[$keyArr[0]][] = $kv;
						}else{
							$arr[$keyArr[0]] = $kv;
						}
				}
				//if the entry does not contain ":" but contains ",", it's a regular array. 
				else if( strpos($a,':') !== true && strpos($a,',') !== false ){
					//Break out each key of assoc. array. 
						$assArr = explode( ",", $a);
					//Key/Value array
						foreach( $assArr as $asar ){ 
							$break = explode( "=", $asar );
							$kv[$break[0]] = $break[1]; 
						}
					//Add the array to our array
						$arr[] = $kv;
				}
				//If it contains neither, it's simply a value pair. 
				else if( strpos($a,':') !== true && strpos($a,',') !== true ){
					//Key/Value array
						$break = explode( "=", $a );
						$kv[$break[0]] = $break[1]; 
					//Add the array to our array
						$arr = array_merge( $arr, $kv );
				}

				unset($kv);
			}

			return $arr;
		}else{
			if ( is_array($string) ){
				return $string;
			}else{
				return array(); 
			}
		} 
	}

	//This function checks if a page is currently running a post type created by this plugin. 
	public static function is_pt(){
		global $post; 

		foreach( MYPLUGIN_post_type::$instances as $instance ){
			if ( $post->post_type == $instance->pt_slug ){
				return true; 
			}
		}

		return false; 
	}
}