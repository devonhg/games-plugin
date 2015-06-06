<?php
if ( ! defined( 'WPINC' ) ) { die; }
/*
 * Plugin Name:       Games Plugin
 * Plugin URI:        dhgodfrey.net
 * Description:       This plugin is designed to track games developed.
 * Version:           v0.9.5
 * Author:            Devon Godfrey
 * Author URI:        http://playfreygames.net
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt

	*IMPORTANT*
	do a "find/replace" accross the directory for "GMEPLG" and replace
	with your plugin name. 

	Plugin slug: GMEPLG

*/

//Include the core class of the post type api
    include_once('pt-api/class-core.php');
    register_activation_hook( __FILE__, 'GMEPLG_ptapi_activate' );

//Create Post-Type Object
    $pt_games = new GMEPLG_post_type( "Games", "Game", "This post-type tracks games developed by Play Frey Games." ); 

//Modify Hooks
    $pt_games->remove_hook_single(); 
    $pt_games->add_hook_single( array("GMEPLG_pt_pcs",'pc_media') );
    $pt_games->add_hook_single( array("GMEPLG_pt_pcs",'pc_content') );

    $pt_games->remove_hook_archive();
    $pt_games->add_hook_archive( array("GMEPLG_pt_pcs",'pc_fi_a') );
    $pt_games->add_hook_archive( array("GMEPLG_pt_pcs",'pc_excerpt') );

    $pt_games->remove_hook_sc();
    $pt_games->add_hook_sc( array("GMEPLG_pt_pcs",'pc_title_a') );
    $pt_games->add_hook_sc( array("GMEPLG_pt_pcs",'pc_fi_a') );
    $pt_games->add_hook_sc( array("GMEPLG_pt_pcs",'pc_excerpt') );

//Add Meta
    $pt_games->reg_meta('Release Date', 'Specify the release date for this title.');
    $pt_games->reg_meta('GameJolt', 'The Gamejolt Link', true, 'link');
    $pt_games->reg_meta('Trailer', 'Link to the Trailer', false, 'media');
    $pt_games->reg_meta('Screenshot 1', 'Screenshot Image 1', false, 'media');
    $pt_games->reg_meta('Screenshot 2', 'Screenshot Image 2', false, 'media');
    $pt_games->reg_meta('Screenshot 3', 'Screenshot Image 3', false, 'media');
    $pt_games->reg_meta('Screenshot 4', 'Screenshot Image 4', false, 'media');

function GMEPLG_latest_news( $quer = null ){
    $post = GMEPLG_func::get_post( $quer );

}

function GMEPLG_gamejolt( $quer = null ){
    $post = GMEPLG_func::get_post( $quer );

}

function GMEPLG_gallery( $quer = null ){
    
}