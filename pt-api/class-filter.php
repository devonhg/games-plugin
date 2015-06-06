<?php
if ( ! defined( 'WPINC' ) ) { die; }


class MYPLUGIN_filter{

	public $pt;

	public function __construct( $pt ){
		$this->pt = $pt;

		add_filter( 'the_content', array( $this , 'insert_single' ) );
	}

	public function insert_single($content){
		global $post;
		$out = "";

		if ( is_main_query() && !is_feed() && !is_home() && $post->post_type == $this->pt ){
			if ( is_archive() ){
				ob_start();
				MYPLUGIN_pt_archive();
				$out .= ob_get_clean(); 
			}
			else if ( is_single( $post->ID ) ){
				ob_start();
				MYPLUGIN_pt_single();
				$out .= ob_get_clean(); 
			}else{
				$out .= $content;
			}
		}else{
			$out .= $content;
		}
		return $out;
	}
}
