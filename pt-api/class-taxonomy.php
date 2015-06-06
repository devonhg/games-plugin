<?php
if ( ! defined( 'WPINC' ) ) { die; }

class MYPLUGIN_pt_tax{

	static $instances = array(); 

    var $name;
    var $name_s;
    var $tax_slug;
    var $pt_slug; 
    var $par;

    public $args; 

    //Name, Name Singular, Slug, Post-type Slug to use. 
	public function __construct($name, $name_s, $pt_slug, $args = null){
        $this->name = $name;
        $this->name_s = $name_s;
        $this->pt_slug = $pt_slug;
        $this->tax_slug = "tax_" . trim(strtolower($name_s)) . "_" . substr($pt_slug, 3);
        $this->args = $args; 

        MYPLUGIN_pt_tax::$instances[] = $this; 

        //Get Parent
        foreach( MYPLUGIN_post_type::$instances as $inst ){
        	if ( $inst->pt_slug == $this->pt_slug ){
        		$this->par = $inst; 
        		break; 
        	}
        }

        $sc_array = array(
        	'tax_query' => array(
        		array(
	        		'taxonomy' => $this->tax_slug,
	        		'field' => 'slug', 
        		),
        	),
        );

        $this->par->reg_sc( str_replace( "tax_", "", $this->tax_slug ) . "_sc", "This is the shortcode automatically generated for " . $this->name . ". Modify the 'cat' attribute to set the category/categories.", $sc_array );

        add_action( 'init', array($this, 'initiate_cpt_tax'), 0 );
        add_action( 'plugins_loaded', array($this, 'plugins_action') );
    }

	public function initiate_cpt_tax(){
		$name = $this->name;
		$name_s = $this->name_s;

		if ( $this->args == null ){
			$labels = array(
				'name'              => _x( $name, 'taxonomy general name' ),
				'singular_name'     => _x( $name_s, 'taxonomy singular name' ),
				'search_items'      => __( 'Search ' . $name),
				'all_items'         => __( 'All ' . $name ),
				'parent_item'       => __( 'Parent ' . $name_s ),
				'parent_item_colon' => __( 'Parent ' . $name_s . ':' ),
				'edit_item'         => __( 'Edit ' . $name_s ), 
				'update_item'       => __( 'Update ' . $name_s ),
				'add_new_item'      => __( 'Add New ' . $name_s ),
				'new_item_name'     => __( 'New ' . $name_s ),
				'menu_name'         => __( $name ),
			);
			$args = array(
				'labels' => $labels,
				'hierarchical' => true,
			);
		}else{
			$args = $this->args; 
		}
		register_taxonomy( $this->tax_slug , $this->pt_slug , $args );	
	}

	public function plugins_action(){
		register_taxonomy_for_object_type( $this->tax_slug, $this->pt_slug );
	}
}