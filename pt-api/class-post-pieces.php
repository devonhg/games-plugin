<?php
if ( ! defined( 'WPINC' ) ) { die; }


//These are the basic hook functions for setting up the layout. 


class MYPLUGIN_pt_pcs{

	//General
		//Title
		public static function pc_title( $quer = null ){
			$post = MYPLUGIN_func::get_post( $quer );

			$out = "";
	    	$out .= "<h1>" . $post->post_title . "</h1>";

	    	echo $out; 
		}

		//Content
		public static function pc_content( $quer = null ){
			$post = MYPLUGIN_func::get_post( $quer );

			$out = "";
	   		$out .= "<div class=' MYPLUGIN-content '>";
		    			$out .= $post->post_content; 
		    $out .= "</div>";
		
			echo $out;
		}

		public static function pc_excerpt( $quer = null ){
			$post = MYPLUGIN_func::get_post( $quer );

			$out = "";
	   		$out .= "<div class=' MYPLUGIN-excerpt '>";
	   			if ( $post->post_excerpt ) {
	   				$out .= $post->post_excerpt; 
	   			}else{
	   				$out .= $post->post_content; 
	   			}
		    			
		    $out .= "</div>";
		
			echo $out;
		}

		//Featured Image
		public static function pc_fi( $quer = null ){
			$post = MYPLUGIN_func::get_post( $quer );


			$out = "";
	    	if ( has_post_thumbnail( $post->ID ) ){
		    	$out .= "<div class='" . "MYPLUGIN-image" . "'>";
		    		$out .= get_the_post_thumbnail( $post->ID, "full" ); 
		    	$out .= "</div>";
			}		

			echo $out; 	
		}

		//Featured image, medium sized
		public static function pc_fimed( $quer = null ){
			$post = MYPLUGIN_func::get_post( $quer );

			$out = "";
	    	if ( has_post_thumbnail( $post->ID ) ){
		    	$out .= "<div class='" . "MYPLUGIN-image" . "'>";
		    		$out .= get_the_post_thumbnail( $post->ID , "medium" ); 
		    	$out .= "</div>";
			}		

			echo $out; 	
		}

		//Output unhidden meta
		public static function pc_meta( $quer = null ){
			$post = MYPLUGIN_func::get_post( $quer );

			$meta = MYPLUGIN_func::print_meta( $post->ID );

			$out = "";

			//Check if there is actually any content, if not don't output.
			if ( strlen( $meta ) > 10 ){
		   		$out .= "<div class='" . "MYPLUGIN-meta" . "'>";
				$out .= $meta;
			    $out .= "</div>";
			}

		    echo $out; 	
		}

		//Output unhidden media
		public static function pc_media( $quer = null ){
			$post = MYPLUGIN_func::get_post( $quer );

			$media = MYPLUGIN_func::print_media( $post->ID );

			$out = "";

			if ( strlen( $media ) > 29 ){
		   		$out .= "<div class='" . "MYPLUGIN-media" . "'>";
				$out .= $media;
			    $out .= "</div>";
			}

		    echo $out; 	
		}

		//Output Categories
		public static function pc_cats( $quer = null ){
			$post = MYPLUGIN_func::get_post( $quer );

			$out = "";
			$pc_values = get_object_taxonomies( $post->post_type );

			$has_terms = false; 

			//Check if the post has any categories.
			foreach( $pc_values as $tax ){
				if ( get_the_terms( $post->ID, $tax ) ){
					$has_terms = true; 
				}
			}

			if ($has_terms){
		    	$out .= "<div class='" . "MYPLUGIN-categories" . "'>";
			    	foreach($pc_values as $tax){
			    		$out .= MYPLUGIN_func::get_cats( $tax , $post->ID , $post->post_type );
			    	}
		    	$out .= "</div>";
	    	}	

	    	echo $out; 	
		}

	//Archive Versions
		//Title hyperlinked
		public static function pc_title_a( $quer = null ){
			$post = MYPLUGIN_func::get_post( $quer );

			$out = "";
			$out .= "<a title='" . $post->post_title . "' href='" . get_permalink( $post->ID ) .  "'>";
	    		$out .= "<h1>" . $post->post_title . "</h1>";
	    	$out .= "</a>";

	    	echo $out; 
		}

		//Featured Image
		public static function pc_fi_a( $quer = null ){
			$post = MYPLUGIN_func::get_post( $quer );


			$out = "";
	    	if ( has_post_thumbnail( $post->ID ) ){
		    	$out .= "<div class='" . "MYPLUGIN-image" . "'>";
		    		$out .= "<a title='" . $post->post_title . "' href='" . 
		    				get_permalink( $post->ID ) .  "' alt='" . $post->post_title . "'>"; 

		    			$out .= get_the_post_thumbnail( $post->ID );
		    		$out .= "</a>";
		    	$out .= "</div>";
			}		

			echo $out; 	
		}

		//Featured image, medium sized
		public static function pc_fimed_a( $quer = null ){
			$post = MYPLUGIN_func::get_post( $quer );

			$out = "";
	    	if ( has_post_thumbnail( $post->ID ) ){
		    	$out .= "<div class='" . "MYPLUGIN-image" . "'>";
		    		$out .= "<a title='" . $post->post_title . "' href='" . 
		    				get_permalink( $post->ID ) .  "' alt='" . $post->post_title . "'>"; 
		    				
		    			$out .= get_the_post_thumbnail( $post->ID, 'medium' );
		    		$out .= "</a>";
		    	$out .= "</div>";
			}		

			echo $out; 	
		}

	//Special Wrapper Functions
		public static function pc_div_start( $quer = null ){
			echo "<div>"; 	
		}		
		public static function pc_div_end( $quer = null ){
			echo "</div>"; 	
		}	
}