<?php
if ( ! defined( 'WPINC' ) ) { die; }

if ( ! get_theme_support( 'post-thumbnails' )) add_theme_support('post-thumbnails');


//Include all files in directory
	foreach (glob( plugin_dir_path( __FILE__ ) . "*." . "php" ) as $filename){
		include_once( $filename );
	}

//Front end hooks
	function GMEPLG_pt_archive(){
		global $post;
		$cl = null; 
		foreach( GMEPLG_post_type::$instances as $instance ){
			if ( $instance->pt_slug == $post->post_type ){
				$cl = $instance;
			}
		}

		if ( $cl !== null ){
			remove_all_actions('GMEPLG_pt_archive');
			$cl->reg_hooks_archive();

			$classes = get_post_class();

			do_action('GMEPLG_pt_archive');

		}else{
			"No posts for this post-type.";
		}
	}

	function GMEPLG_pt_single(){
		global $post;
		$cl = null; 

		foreach( GMEPLG_post_type::$instances as $instance ){
			if ( $instance->pt_slug == $post->post_type ){
				$cl = $instance;
			}
		}

		if ( $cl !== null ){
			remove_all_actions('GMEPLG_pt_single');
			$cl->reg_hooks_single();

			do_action('GMEPLG_pt_single');
		}else{
			echo "No posts for this post-type.";
		}
	}

//Initation Hooks
	function GMEPLG_ptapi_activate() {
	    if ( ! get_option( 'GMEPLG_flush_flag' ) ) {
	        add_option( 'GMEPLG_flush_flag', true );
	    }
	}

	add_action( 'init', 'GMEPLG_ptapi_rewrite', 20 );
	function GMEPLG_ptapi_rewrite() {
	    if ( get_option( 'GMEPLG_flush_flag' ) ) {
	        flush_rewrite_rules();
	        delete_option( 'GMEPLG_flush_flag' );
	    }
	}


//Create Admin Page
    new GMEPLG_dash_page( "Post Types", "Post Type" );