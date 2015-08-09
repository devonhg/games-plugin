<?php
if ( ! defined( 'WPINC' ) ) { die; }
/*
 * Plugin Name:       Games Plugin
 * Plugin URI:        dhgodfrey.net
 * Description:       This plugin is designed to track games developed.
 * Version:           1.2.1
 * Author:            Devon Godfrey
 * Author URI:        http://playfreygames.net
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI: https://github.com/devonhg/games-plugin
 * GitHub Branch: master


	*IMPORTANT*
	do a "find/replace" accross the directory for "GMEPLG" and replace
	with your plugin name. 

	Plugin slug: GMEPLG

*/

//Enqueue Style
    function GMEPLG_styles(){
        wp_enqueue_style( "GMEPLG_style", plugin_dir_url( __FILE__ ) . "/build/styles.min.css" );
    }

    add_action( "wp_enqueue_scripts", "GMEPLG_styles" );

//Include the core class of the post type api
    include_once('pt-api/class-core.php');
    register_activation_hook( __FILE__, 'GMEPLG_ptapi_activate' );

//Create Post-Type Object
    $pt_games = new GMEPLG_post_type( "Games", "Game", "This post-type tracks games developed by Play Frey Games." ); 

//Modify Hooks
    $pt_games->remove_hook_single(); 
    $pt_games->add_hook_single( array("GMEPLG_pt_pcs",'pc_media') );
    $pt_games->add_hook_single( array("GMEPLG_pt_pcs",'pc_meta') );
    $pt_games->add_hook_single( "GMEPLG_gamejolt" );
    $pt_games->add_hook_single( array("GMEPLG_pt_pcs",'pc_content') );
    $pt_games->add_hook_single( "GMEPLG_latest_news" );

    $pt_games->remove_hook_archive();
    $pt_games->add_hook_sc( array("GMEPLG_pt_pcs",'pc_title_a') );
    $pt_games->add_hook_sc( array("GMEPLG_pt_pcs",'pc_fi_a') );
    $pt_games->add_hook_sc( array("GMEPLG_pt_pcs",'pc_excerpt') );

    $pt_games->remove_hook_sc();
    $pt_games->add_hook_sc( array("GMEPLG_pt_pcs",'pc_title_a') );
    $pt_games->add_hook_sc( array("GMEPLG_pt_pcs",'pc_fi_a') );
    $pt_games->add_hook_sc( array("GMEPLG_pt_pcs",'pc_excerpt') );

//Add Meta
    $pt_games->reg_meta('Release Date', 'Specify the release date for this title.');
    $pt_games->reg_meta('Development Status', 'Specify the development status of this title.');
    $pt_games_gj = $pt_games->reg_meta('GameJolt', 'The Gamejolt Link', true, 'link');
    $pt_games->reg_meta('Trailer', 'Link to the Trailer', false, 'media');
    $pt_games->reg_meta('Screenshot 1', 'Screenshot Image 1', false, 'media');
    $pt_games->reg_meta('Screenshot 2', 'Screenshot Image 2', false, 'media');
    $pt_games->reg_meta('Screenshot 3', 'Screenshot Image 3', false, 'media');
    $pt_games->reg_meta('Screenshot 4', 'Screenshot Image 4', false, 'media');

function GMEPLG_latest_news( $quer = null ){
    $post = GMEPLG_func::get_post( $quer );

    $out = "<h1 class='GMEPLG-news-main-header'>News</h1>";

    $args = array(
        "category_name" => $post->post_title, 
        "posts_per_page" => 10,
    );

    $query = new WP_Query( $args );

    $out .= "<div class='GMEPLG-updates'>";
        if ( $query->have_posts() ){
            while ( $query->have_posts() ) : $query->the_post();

                $i = 0;

                $out .= "<article class='GMEPLG-news'>";
                    
                    $out .= "<div class='GMEPLG-news-header'>";
                        $out .= "<a href='" . get_permalink( $query->post->ID ) . "'>";
                            $out .= "<h2>" . $query->post->post_title . "</h2>";
                        $out .= "</a>";
                        $out .= GMEPLG_posted_on();
                    $out .= "</div>";
                    if ( get_the_post_thumbnail( $query->post->ID ) != "" ){
                        $out .= "<a href='" . get_permalink( $query->post->ID ) . "'>";
                            $out .= "<div class='GMEPLG-news-img'>";
                                $out .= get_the_post_thumbnail( $query->post->ID );  
                            $out .= "</div>";
                        $out .= "</a>";
                    }

                    $out .= "<div class='GMEPLG-update-content'>";
                        $out .= get_the_excerpt();
                        $out .= " <a href='" . get_the_permalink( $query->post->ID ) . "' title='Read More'>Read More</a>";
                    $out .= "</div>";
                $out .= "</article>";

                $i++;
            endwhile;       

            wp_reset_postdata(); 
        }else{
            $out .= "<h2>There are no posts to display</h2>";
        }

    $out .= "</div>";

    echo $out; 

}

function GMEPLG_gamejolt( $quer = null ){
    $post = GMEPLG_func::get_post( $quer );
    global $pt_games_gj;

    $link = $pt_games_gj->get_val(); 

    if ( $link != "" ){
        $out = "<div class='GMEPLG-gj'>";
            $out .= "<a target='_blank' title='Get the Game at Gamejolt' href='" . $link . "'>";
                $out .= "<h1>Download " . $post->post_title . "</h1>";
            $out .= "</a>";
        $out .= "</div>";

        echo $out; 
    }
}

function GMEPLG_posted_on() {
    $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
    if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
        $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
    }

    $time_string = sprintf( $time_string,
        esc_attr( get_the_date( 'c' ) ),
        esc_html( get_the_date() ),
        esc_attr( get_the_modified_date( 'c' ) ),
        esc_html( get_the_modified_date() )
    );

    $posted_on = sprintf(
        esc_html_x( 'Posted on %s', 'post date', '_s' ),
        '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
    );

    $byline = sprintf(
        esc_html_x( 'by %s', 'post author', '_s' ),
        '<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
    );

    return '<span class="posted-on">' . $posted_on . '</span><span class="byline"> ' . $byline . '</span>'; // WPCS: XSS OK.

}