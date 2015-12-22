<?php
if ( ! defined( 'WPINC' ) ) { die; }
/*
 * Plugin Name:       Games Plugin
 * Plugin URI:        dhgodfrey.net
 * Description:       This plugin is designed to track games developed.
 * Version:           2.1.1
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

/**************************************
*    Allow the uploading of Zip files
**************************************/
    add_filter('upload_mimes', 'allow_uploads_gmc');
     
    function allow_uploads_gmc( $existing_mimes ){
      $existing_mimes['zip'] = 'application/zip';
      return $existing_mimes;
    }

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
    //$pt_games->add_hook_single( "GMEPLG_paypal" );
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
            $out .= "<a target='_blank' title='Get the Game!' href='" . $link . "'>";
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



/************************
* Extensions Post Type
************************/
    $pt_ext = new GMEPLG_post_type( "Extensions", "Extension", "This post-type tracks extensions developed by Play Frey Games." ); 


//Modify Hooks
    $pt_ext->remove_hook_single(); 
    $pt_ext->add_hook_single( "GMEPLG_youtube" );
    $pt_ext->add_hook_single( array("GMEPLG_pt_pcs",'pc_media') );
    $pt_ext->add_hook_single( array("GMEPLG_pt_pcs",'pc_meta') );
    $pt_ext->add_hook_single( array("GMEPLG_pt_pcs",'pc_content') );
    $pt_ext->add_hook_single( "GMEPLG_download_ext" );
    //$pt_ext->add_hook_single( "GMEPLG_paypal" );
    $pt_ext->add_hook_single( "GMEPLG_latest_news" );

//Register Meta
    $pt_ext_dl = $pt_ext->reg_meta('File', 'The file download.', true, 'media');
    $pt_ext_yt = $pt_ext->reg_meta('Youtube Video Link', 'Link to the youtube video.', true, 'link');
    $pt_ext->reg_meta('Version', 'Enter the version of this extension.', false, 'text');

//Functions
    function GMEPLG_paypal( $quer = null ){
        $post = GMEPLG_func::get_post( $quer );
        ?>
            <div class='paypal-donations'>
                <h2>Do you like this?</h2>
                <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                    <input type="hidden" name="cmd" value="_s-xclick">
                    <input type="hidden" name="hosted_button_id" value="B9Q3ESP947GFY">
                        <input type="hidden" name="on0" value="Buy me some coffee">
                        <span class='paypal-text'>Show your support! Buy me some coffee.</span><br>
                        <select name="os0">
                            <option value="One Cup ( Yay! )">One Cup ( Yay! ) $1.00 USD</option>
                            <option value="Espresso ( Whoohoo! )">Espresso ( Whoohoo! ) $5.00 USD</option>
                            <option value="2 Espressos ( I can handle it )">2 Espressos ( I can handle it ) $10.00 USD</option>
                            <option value="4 Espressos ( Hands shaking )">4 Espressos ( Hands shaking ) $20.00 USD</option>
                            <option value="10 Espressos ( I can see sound )">10 Espressos ( I can see sound ) $50.00 USD</option>
                        </select> </td></tr>
                    <input type="hidden" name="currency_code" value="USD">
                    <input class='paypal-submit' type="submit" value='Buy Me Coffee'  border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                    <!--<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">-->
                </form>
                <div class='paypal-social'>
                    <span>Or hit me up on social media.</span>
                    <a title='Find me on Facebook!' target='_blank' href='https://www.facebook.com/dhgodfrey' class='paypal-social-button'><i class="fa fa-facebook-official fa-5x"></i></a>
                    <a title='Send a tweet!' target='_blank' href='https://twitter.com/PlayFreyGames' class='paypal-social-button'><i class="fa fa-twitter-square fa-5x"></i></a>
                </div>
            </div>
        <?php
    }

    function GMEPLG_youtube( $quer = null ){
        $post = GMEPLG_func::get_post( $quer );
        global $pt_ext_yt; 

        $url_string = $pt_ext_yt->get_val();
        $p = parse_url( $url_string );
        $dom = "https://www.youtube.com/embed/";

        //If a single youtube Video
        if ( strpos($url_string,'list') == false){
            if( isset($p['fragment']) ){
                $link = substr( $p['query'], 2 )  . substr( $p['fragment'], 12 );
                $link = str_replace( '&', '?', $link );
            }else{
                $link = substr( $p['query'], 2 ); 
            }
        }else{//If it's a playlist
            $link = 'videoseries?' . $p['query'];
        }

        $o = "";
        if( $link != "" ){
            $o .= "<div class='youtube_vid'>";
                $o .= "<iframe width='560' height='349' class='yt-vid' src='" . $dom . $link . "' frameborder='0' allowfullscreen></iframe>";
            $o .= "</div>";
        }
        echo $o;  
    }

    function GMEPLG_download_ext( $quer = null ){
    $post = GMEPLG_func::get_post( $quer );
    global $pt_ext_dl;

    $link = $pt_ext_dl->get_val(); 

    if ( $link != "" ){
        $out = "<div class='GMEPLG-gj'>";
            $out .= "<a target='_blank' title='Get the Game!' href='" . $link . "'>";
                $out .= "<h1>Download " . $post->post_title . "</h1>";
            $out .= "</a>";
        $out .= "</div>";

        echo $out; 
    }
}