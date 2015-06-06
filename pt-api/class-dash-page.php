<?php
if ( ! defined( 'WPINC' ) ) { die; }

class GMEPLG_dash_page {

	static $instances = array(); 

	public $menu_pos; 
	public $name; 
	public $name_s; 

	public function __construct( $name, $name_s ){
		GMEPLG_dash_page::$instances[] = $this; 
		$this->name = $name;
		$this->name_s = $name; 
		$this->menu_pos = 82.1 + ( count( GMEPLG_dash_page::$instances )/10 );


		add_action('admin_menu', array( $this, 'page_enqueue' ));
	}

	public function page_enqueue(){
		add_menu_page( $this->name . " Info Page", 
						$this->name . " Info", 
						'read', 
						$this->name_s . "_menu", 
						array($this, 'page_function') , 
						"", 
						$this->menu_pos );
	}

	public function page_function(){

		if(!current_user_can('read')){
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		$out = "";

		$out .= "<style>";
			$out .= "#message, .updated, .notice, .error, .update-nag{ display: none; }";
		$out .= "</style>";

		$out .= "<div class='wrap'>";
			$out .= "<h1>Post-Types Information</h1>";

			$out .= "<p>This is the informational page for the custom post-types on this site.</p>";

			foreach( GMEPLG_post_type::$instances as $pt  ){

				$out .= "<div style='padding: 25px' class='postbox'>";
					//Title/Description Output
						$out .= "<h1>" . $pt->name . "</h1>";
						$out .= "<p>" . $pt->desc . "</p>";

					//Shortcodes Info Output
						$out .= "<h2>Shortcodes</h2>";
						$out .= "<hr>";
						foreach ( GMEPLG_pt_sc::$instances as $sc ){
							if ( $sc->par->name == $pt->name ){
								$out .= "<div>";
									$out .= "<h3>[" . $sc->name . "]</h3>";
									$out .= "<p>" . $sc->desc . "</p>";
								$out .= "</div>";
							}
						}

					//Technical Info Output
						$out .= "<h2>Technical Info</h2>";
						$out .= "<hr>";

						$out .= "<h3>Main Information</h3>";
						$out .= "<ul>";
							$out .= "<li>Post-Type Slug: " . $pt->pt_slug . "</li>";
							$out .= "<li>Plugin Slug: " . "GMEPLG</li>";
						$out .= "</ul>";

						$out .= "<h3>Meta</h3>";
						$out .= "<ul>";
							foreach( GMEPLG_pt_meta::$instances as $meta ){
								if ($meta->pt == $pt->pt_slug){
									$out .= "<li>" . $meta->title . " Key: " . $meta->val_key . "</li>";
								}
							}
						$out .= "</ul>";

						$out .= "<h3>Taxonomies</h3>";
						$out .= "<ul>";
							foreach( GMEPLG_pt_tax::$instances as $tax ){
								if ($tax->pt_slug == $pt->pt_slug){
									$out .= "<li>" . $tax->name . " Slug: " . $tax->tax_slug . "</li>";
								}
							}
						$out .= "</ul>";

				$out .= "</div>";
			}
		$out .= "</div>";

		$out .= "<sub>";
			$out .= "These features were created using an API developed by 
					<a target='_blank' href='http://www.dhgodfrey.net'>Devon Godfrey</a>, 
					please show your appreciation by <a target='_blank' href='http://www.dhgodfrey.net'>visiting 
					my website</a>.";
		$out .= "</sub>";

		echo $out; 
	}
}