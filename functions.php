<?php
/***********************************************
* REMOVE ADMIN BAR
***********************************************/
add_filter('show_admin_bar', '__return_false');

/***********************************************
* MENUS
***********************************************/
if ( function_exists( 'add_theme_support' ) )
add_theme_support( 'nav-menus' );
	register_nav_menus(array(
		'primary' => 'Primary Navigation'
));

/***********************************************
* POST THUMBNAILS
***********************************************/
if ( function_exists( 'add_theme_support' ) )
add_theme_support( 'post-thumbnails' );

/* Custom image sizes */
add_image_size( 'custom-thumb', 470, 470, array( 'center', 'top' ) );
add_image_size( 'custom-medium', 1200, 10000, false);
add_image_size( 'custom-large', 1880, 15000, false);

/* URL THUMBNAILS */
function url_thumbnail($tamagno){
	$src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), $tamagno);
	return $src[0];
}

// Override img caption shortcode to fix 10px issue.
function fix_img_caption_shortcode($val, $attr, $content = null) {
    extract(shortcode_atts(array(
        'id'    => '',
        'align' => '',
        'width' => '',
        'caption' => ''
    ), $attr));

    if ( 1 > (int) $width || empty($caption) ) return $val;

    return '<div id="' . $id . '" class="wp-caption ' . esc_attr($align) . '" style="max-width: ' . (0 + (int) $width) . 'px">' . do_shortcode( $content ) . '<p class="wp-caption-text">' . $caption . '</p></div>';
}
add_filter('img_caption_shortcode', 'fix_img_caption_shortcode', 10, 3);

/***********************************************
* WOOCOMMERCE
***********************************************/
add_theme_support( 'woocommerce' );
// hooks
/*
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
add_action('woocommerce_before_main_content', 'my_theme_wrapper_start', 10);
add_action('woocommerce_after_main_content', 'my_theme_wrapper_end', 10);
function my_theme_wrapper_start() {
  echo '<section id="pablito">';
}
function my_theme_wrapper_end() {
  echo '</section>';
}
*/

// Remove each style one by one
/*add_filter( 'woocommerce_enqueue_styles', 'jk_dequeue_styles' );
function jk_dequeue_styles( $enqueue_styles ) {
    unset( $enqueue_styles['woocommerce-general'] );    // Remove the gloss
    unset( $enqueue_styles['woocommerce-layout'] );     // Remove the layout
    unset( $enqueue_styles['woocommerce-smallscreen'] );    // Remove the smallscreen optimisation
    return $enqueue_styles;
}*/

// Or just remove them all in one line
add_filter( 'woocommerce_enqueue_styles', '__return_false' );

// Ensure cart contents update when products are added to the cart via AJAX (place the following in functions.php)
/*
add_filter('add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment');

function woocommerce_header_add_to_cart_fragment( $fragments ) {
    global $woocommerce;
    
    ob_start();
    
    ?>
    <a class="cart-contents" href="<?php echo $woocommerce->cart->get_cart_url(); ?>" title="<?php _e('View your shopping cart', 'woothemes'); ?>"><?php echo sprintf(_n('%d item', '%d items', $woocommerce->cart->cart_contents_count, 'woothemes'), $woocommerce->cart->cart_contents_count);?> - <?php echo $woocommerce->cart->get_cart_total(); ?></a>
    <?php
    
    $fragments['a.cart-contents'] = ob_get_clean();
    
    return $fragments;
    
}*/



// Ensure cart contents update when products are added to the cart via AJAX (place the following in functions.php)
add_filter('add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment');

function woocommerce_header_add_to_cart_fragment( $fragments ) {
    global $woocommerce;
    
    ob_start();
    
    ?>
    <a class="cart-contents" href="<?php echo $woocommerce->cart->get_cart_url(); ?>" title="<?php _e('View your shopping cart', 'woothemes'); ?>"><?php echo sprintf(_n('%d item', '%d items', $woocommerce->cart->cart_contents_count, 'woothemes'), $woocommerce->cart->cart_contents_count);?> - <?php echo $woocommerce->cart->get_cart_total(); ?></a>
    <?php
    
    $fragments['a.cart-contents'] = ob_get_clean();
    
    return $fragments;
    
}

/**
 * Optimize WooCommerce Scripts
 * Remove WooCommerce Generator tag, styles, and scripts from non WooCommerce pages.
 */
add_action( 'wp_enqueue_scripts', 'child_manage_woocommerce_styles', 99 );

function child_manage_woocommerce_styles() {
    //remove generator meta tag
    remove_action( 'wp_head', array( $GLOBALS['woocommerce'], 'generator' ) );

    //first check that woo exists to prevent fatal errors
    if ( function_exists( 'is_woocommerce' ) ) {
        //dequeue scripts and styles
        if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() ) {
            wp_dequeue_style( 'woocommerce_frontend_styles' );
            wp_dequeue_style( 'woocommerce_fancybox_styles' );
            wp_dequeue_style( 'woocommerce_chosen_styles' );
            wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
            wp_dequeue_script( 'wc_price_slider' );
            wp_dequeue_script( 'wc-single-product' );
            wp_dequeue_script( 'wc-add-to-cart' );
            wp_dequeue_script( 'wc-cart-fragments' );
            wp_dequeue_script( 'wc-checkout' );
            wp_dequeue_script( 'wc-add-to-cart-variation' );
            wp_dequeue_script( 'wc-single-product' );
            wp_dequeue_script( 'wc-cart' );
            wp_dequeue_script( 'wc-chosen' );
            wp_dequeue_script( 'woocommerce' );
            wp_dequeue_script( 'prettyPhoto' );
            wp_dequeue_script( 'prettyPhoto-init' );
            wp_dequeue_script( 'jquery-blockui' );
            wp_dequeue_script( 'jquery-placeholder' );
            wp_dequeue_script( 'fancybox' );
            wp_dequeue_script( 'jqueryui' );
        }
    }

}
/***********************************************
* CUSTOM LENGTH EXCERPT
***********************************************/
function custom_excerpt_length( $length ) {
    global $post;
    if ($post->post_type == 'post'){
        return 14;
    } else if ($post->post_type == 'illustration'){
        return 50;
    }
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

/**********************************************
* To remove <p> before category description
**********************************************/
remove_filter('term_description','wpautop');

/***********************************************
* CUSTOM TYPE: ILLUSTRATION
***********************************************/
function create_illustration_type() {
  $args = array(
    'labels' => array(
      'name' => 'Illustrations',
      'singular_name' => 'Illustration'
    ),
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true, 
    'show_in_menu' => true, 
    'query_var' => true,
    'rewrite' => true,
    'capability_type' => 'post',
    'has_archive' => true, 
    'hierarchical' => false,
    'show_tagcloud' => false,
    'show_in_nav_menus' => true,
    'menu_position' => 5,
    'menu_icon' => 'dashicons-format-gallery',
    'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'custom-fields' )
  ); 
  register_post_type('illustration',$args);
}
add_action( 'init', 'create_illustration_type' );

// Illustration Types
function create_illustration_taxonomies() {
    register_taxonomy(
        'illustration',
        'illustration',
        array(
            'labels' => array(
                'name' => 'Illustration Types',
                'singular_name' => 'Illustration Type'
            ),
            'show_ui' => true,
            'show_tagcloud' => false,
            'hierarchical' => true,
            'show_in_nav_menus' => true
        )
    );
}
add_action( 'init', 'create_illustration_taxonomies', 0 );

/***********************************************
* SIDEBAR
***********************************************/
function sidebar_init() {
    // Area 1, located at the top of the sidebar.
    register_sidebar( array(
        'name' => 'Primary Widget',
        'id' => 'primary-widget-area',
        'description' => 'The primary widget area',
        'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ) );
    // Area 2, located below the Primary Widget Area in the sidebar. Empty by default.
    register_sidebar( array(
        'name' => 'Secondary Widget',
        'id' => 'secondary-widget-area',
        'description' => 'The secondary widget area',
        'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ) );
}
add_action( 'widgets_init', 'sidebar_init' );
add_theme_support( 'html5', array( 'search-form' ) );

?>