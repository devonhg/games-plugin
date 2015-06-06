<?php
if ( ! defined( 'WPINC' ) ) { die; }

class MYPLUGIN_pt_sc{
    
    static $instances = array(); 
	public $pt;
    public $par; 
    public $name;
    public $desc;
    public $query;

    public function __construct($pt, $par, $name, $desc, $query = array() ){
    
        MYPLUGIN_pt_sc::$instances[] = $this; 
    	$this->pt = $pt;
        $this->par = $par; 
        $this->name = $name; 
        $this->desc = $desc;
        $this->query = $query; 

        add_shortcode( str_replace( " ", "_", $name ), array( $this, 'display_archive_f'));
    }

    public static function action(){
        do_action( $this->par->$name_s . "pt_shortcode" );
    }
    
    public function display_archive_f($atts){
        extract( shortcode_atts( array(
            'wpargs' => '', 
            'cats' => '',
        ), $atts ) ); 
        $out = ""; 
        $parse = array();

        //Set up Query Arrays
            $query_att = $this->query;
            $sc_att = MYPLUGIN_func::asc_string_to_array( htmlspecialchars_decode($wpargs) );
            $base_att = array( "post_type" => $this->pt );

            if ( $cats !== '' && isset( $query_att['tax_query'] ) ){
                $query_att['tax_query'][0]['terms'] = explode( ",", $cats); 
            }

            $argOut = array_merge( $sc_att, $base_att, $query_att );

        $this->par->reg_hooks_sc();
        
        $quer = new WP_Query ( $argOut );
            while ( $quer->have_posts() ) : $quer->the_post();
                ob_start();
                do_action( $this->par->name_s . 'pt_shortcode' , $quer );
                $out .= ob_get_clean();
            endwhile; 
        wp_reset_postdata();
        

        return $out;
    }
}