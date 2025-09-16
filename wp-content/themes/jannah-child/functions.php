<?php

add_action( 'wp_enqueue_scripts', 'tie_theme_child_styles_scripts', 80 );
function tie_theme_child_styles_scripts() {

	/* Load the RTL.css file of the parent theme */
	if ( is_rtl() ) {
		wp_enqueue_style( 'tie-theme-rtl-css', get_template_directory_uri().'/rtl.css', '' );
	}

	/* THIS WILL ALLOW ADDING CUSTOM CSS TO THE style.css */
	wp_enqueue_style( 'tie-theme-child-css', get_stylesheet_directory_uri().'/style.css', '' );

	/* Uncomment this line if you want to add custom javascript */
	//wp_enqueue_script( 'jannah-child-js', get_stylesheet_directory_uri() .'/js/scripts.js', '', false, true );
}

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );

// END ENQUEUE PARENT ACTION

/*-----------------------------------------------------------------------------------*/
# Block Feed and Comments robots.txt
/*-----------------------------------------------------------------------------------*/
add_filter( 'robots_txt', 'kavkaz_custom_robots_txt', 20, 2 );
function kavkaz_custom_robots_txt( $output, $public ) {
	if ( '1' == $public ) {
        $output .= "\n" . "# Block Woocommerce assets" . "\n" . "User-agent: *" . "\n" . "Disallow: /cart/" . "\n" . "Disallow: /checkout/" . "\n" . "Disallow: /my-account/" . "\n" . "Disallow: /*?orderby=price" . "\n" . "Disallow: /*?orderby=rating" . "\n" . "Disallow: /*?orderby=date" . "\n" . "Disallow: /*?orderby=price-desc" . "\n" . "Disallow: /*?orderby=popularity" . "\n" . "Disallow: /*?filter" . "\n" . "Disallow: /*add-to-cart=*" . "\n" . "Disallow: /*?add_to_wishlist=*" . "\n";		
		$output .= "\n" . "# Block Feed and Comments" . "\n" . "User-agent: *" . "\n" . "Disallow: /feed/" . "\n" . "Disallow: /feed/$" . "\n" . "Disallow: /comments/feed" . "\n" . "Disallow: /trackback/" . "\n" . "Disallow: */?author=*" . "\n" . "Disallow: */author/*" . "\n" . "Disallow: /author*" . "\n" . "Disallow: /author/" . "\n" . "Disallow: */comments$" . "\n" . "Disallow: */feed" . "\n" . "Disallow: */feed/*" . "\n" . "Disallow: */feed$" . "\n" . "Disallow: */trackback" . "\n" . "Disallow: */trackback$" . "\n" . "Disallow: /?feed=" . "\n" . "Disallow: /wp-comments" . "\n" . "Disallow: /wp-feed" . "\n" . "Disallow: /wp-trackback" . "\n" . "Disallow: */replytocom=" . "\n";
		$output .= "\n" . "# Block Search assets" . "\n" . "User-agent: *" . "\n" . "Disallow: /search/" . "\n" . "Disallow: *?s=*" . "\n" . "Disallow: *?p=*" . "\n" . "Disallow: *&p=*" . "\n" . "Disallow: *&preview=*" . "\n" . "Disallow: /search" . "\n";
		$output .= "\n" . "# Block Files" . "\n" . "User-agent: *" . "\n" . "Disallow: /wp-admin/*" . "\n" . "Disallow: /wp-includes/*" . "\n" . "Disallow: /wp-content/plugins/*" . "\n" . "Disallow: /cdn-cgi/" . "\n" . "Disallow: *?service=*" . "\n" . "Disallow: /*ref=*" . "\n" . "Disallow: /*attachment_id*" . "\n" . "Disallow: /refer/*" . "\n" . "Disallow: /cgi-bin/*" . "\n" . "Disallow: /author/*" . "\n" . "Disallow: /*author=*" . "\n" . "Disallow: */print*" . "\n" . "Disallow: /*.zip*" . "\n" . "Disallow: /*.rar*" . "\n" . "Disallow: /*.pdf*" . "\n" . "Disallow: /*.log*" . "\n" . "Disallow: /page/" . "\n" . "Disallow: */page/*" . "\n";
    }
	return $output;
}