<?php
if ( ! defined( 'WPINC' ) ) { die; }
/*
 * Plugin Name:       Devons Tools - Post-Type API
 * Plugin URI:        
 * Description:       This is the Core for Devons Tools Framework
 * Version:           v0.9.5
 * Author:            Devon Godfrey
 * Author URI:        http://playfreygames.net
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt

	*IMPORTANT*
	do a "find/replace" accross the directory for "MYPLUGIN" and replace
	with your plugin name. 

	Plugin slug: MYPLUGIN

*/

//Include the core class of the post type api
    include_once('pt-api/class-core.php');
    register_activation_hook( __FILE__, 'MYPLUGIN_ptapi_activate' );

//Create Post-Type Object
    $pt_books = new MYPLUGIN_post_type( "Books", "Book", "This post-type is for books." ); 

//Register Taxonomies
    $pt_books->reg_tax("Genres", "Genre" );
    $pt_books->reg_tax("Authors", "Author" );

//Modify Hooks
    $pt_books->add_hook_single( array("MYPLUGIN_pt_pcs",'pc_media') );

//Add Meta
    $pt_books->reg_meta('Price', 'The Cost of the Book', true);
    $pt_books->reg_meta('Color1', 'Color', true, 'color');
    $pt_books->reg_meta('Color2', 'Color2', true, 'color');
    $pt_books->reg_meta('Number', 'Input a Number', true, 'number');
    $pt_books->reg_meta('Link', 'Input a Link', true, 'link');
    $pt_books->reg_meta('Media', 'Input media', true, 'media');

//Movies Post-Type
    $pt_movies = new MYPLUGIN_post_type( "Movies", "Movie", "This post-type is for books." ); 
    $pt_movies->reg_tax("Genres", "Genre" );
