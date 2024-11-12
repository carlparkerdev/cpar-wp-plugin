<?php

/*
 *   CPAR PLUGIN: Configs (WordPress)
 *
 *   Author:   Carl A. Parker
 *   Website:  https://CarlParker.dev
*/

if ( ! defined( 'ABSPATH' ) ) : exit; endif; // SILENCE IS GOLDEN


/*
 *   DEFAULTS
 *   configure features to improve performance and/or experience
*/

     // DISABLE FILE EDITOR

     define( 'DISALLOW_FILE_EDIT', true );

     // ADMIN BAR

     add_filter( 'show_admin_bar', '__return_false' );

     // ADMIN EMAIL CHECK

     add_filter( 'admin_email_check_interval', '__return_false' );

     // LOAD BLOCK ASSETS ONLY IF USED

     add_filter( 'should_load_separate_core_block_assets', '__return_true' );

     // LOAD JQUERY IN FOOTER

     add_action( 'wp_enqueue_scripts', 'cpar_wp_jquery_footer' );

     function cpar_wp_jquery_footer() {

          wp_scripts()->add_data( 'jquery', 'group', 1 );
          wp_scripts()->add_data( 'jquery-core', 'group', 1 );
          wp_scripts()->add_data( 'jquery-migrate', 'group', 1 );

     }

     // DASHBOARD WIDGETS

     add_action( 'wp_dashboard_setup', 'cpar_wp_dashboard_widgets' );

     function cpar_wp_dashboard_widgets() {

          remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
          remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );
          remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
          remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );

          remove_meta_box( 'dashboard_php_nag', 'dashboard', 'normal' );
          remove_meta_box( 'dashboard_browser_nag', 'dashboard', 'normal' );
          remove_meta_box( 'health_check_status', 'dashboard', 'normal' );
          remove_meta_box( 'dashboard_site_health', 'dashboard', 'normal' );
          remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
          remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
          remove_meta_box( 'network_dashboard_right_now', 'dashboard', 'normal' );
          remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
          remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
          remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );

     }

     // DISABLE CORE SIZES

     add_filter( 'intermediate_image_sizes_advanced', 'cpar_wp_media_sizes' );

     function cpar_wp_media_sizes( $sizes ) {

          unset( $sizes[ 'thumbnail' ] );
          unset( $sizes[ 'medium' ] );
          unset( $sizes[ 'medium_large' ] );
          unset( $sizes[ 'large' ] );
          unset( $sizes[ '1536x1536' ] );
          unset( $sizes[ '2048x2048' ] );

          return $sizes;

     }

     // LOGIN ERROR MESSAGE

     add_filter( 'login_errors', 'cpar_wp_login_error_message' );

     function cpar_wp_login_error_message( $error ) {

          return __( 'incorrect login', 'cpar-wp-plugin' );

     }

     // EMOJI SUPPORT

     remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
     remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
     remove_action( 'wp_print_styles', 'print_emoji_styles' );
     remove_action( 'admin_print_styles', 'print_emoji_styles' );

     remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
     remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
     remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

     add_filter( 'tiny_mce_plugins', 'cpar_wp_emojis_tinymce' );


/*
 *   EMOJI DISABLED
 *   disable use of the emojis functionality
*/

     function cpar_wp_emojis_tinymce( $plugins ) {

          if ( is_array( $plugins ) ) :

               return array_diff( $plugins, array( 'wpemoji' ) );

          else :

               return array();

          endif;

     }


/*
 *   SOURCE URL VERSION
 *   improve caching conflicts with version removal from source url
*/

     add_filter( 'script_loader_src', 'cpar_wp_sourceurl_ver', 10, 2 );
     add_filter( 'style_loader_src', 'cpar_wp_sourceurl_ver', 10, 2 );

     function cpar_wp_sourceurl_ver(  $src, $handle ) {

          return remove_query_arg( 'ver', $src );

     }


/*
 *   ADMIN BRANDING
 *   enhance layout and user experience
*/

     // LOGIN LOGO URL

     add_filter( 'login_headerurl', 'cpar_wp_branding_login_logourl' );

     function cpar_wp_branding_login_logourl() {

          return get_site_url();

     }

     // FAVICON

     add_action( 'admin_head', 'cpar_wp_branding_admin_favicon' );
     add_action( 'login_head', 'cpar_wp_branding_admin_favicon' );

     function cpar_wp_branding_admin_favicon() {

          $cparFavicon = CPAR_PLUGIN_URL . CPAR_ASSETS . 'images/cpar-icon.png';

          echo '<link rel="icon" href="'. $cparFavicon .'" sizes="180x180" />';
          echo '<link rel="apple-touch-icon" href="'. $cparFavicon .'">';

     }


/*
 *   BLOCKS CATEGORY
 *   configure for gutenberg blocks
*/

     add_filter( 'block_categories_all', 'cpar_wp_blocks_category', 10, 2 );

     function cpar_wp_blocks_category( $block_categories, $block_editor_context ) {

          return array_merge(

               $block_categories, array(

                    array(

                         'slug'    =>  'cpar-blocks',
                         'title'   =>  __( 'CPar Blocks', 'cpar-wp-plugin' )

                    )

               )

          );

     }


/*
 *   PATTERNS CATEGORY
 *   configure for gutenberg patterns
*/

     add_action( 'init', 'cpar_wp_patterns_category' );

     function cpar_wp_patterns_category( ) {

          register_block_pattern_category(

               'cpar-patterns',
               array( 'label' => __( 'CPar Patterns', 'cpar-wp-plugin' ) )

          );

     }


/*
 *   CLEANUP GUTENBERG
 *   remove unnecessary blocks
*/

     // ASSETS

     add_action( 'wp_enqueue_scripts', 'cpar_wp_gutenberg_cleanup_assets' );

     function cpar_wp_gutenberg_cleanup_assets() {

          wp_dequeue_style( 'wp-block-library' );
          wp_dequeue_style( 'wp-block-library-theme' );
          wp_dequeue_style( 'global-styles' );
          wp_dequeue_style( 'classic-theme-styles');

          remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
          remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );

     }

     // BLOCKS

     add_filter( 'allowed_block_types_all', 'cpar_wp_gutenberg_cleanup_blocks' );

     function cpar_wp_gutenberg_cleanup_blocks( $allowed_blocks ) {

          // GET ALL REGISTERED BLOCKS

          $blocks = WP_Block_Type_Registry::get_instance()->get_all_registered();

          // DEFINE BLOCKS TO DISABLE

          unset( $blocks[ 'core/verse' ] );
          unset( $blocks[ 'core/preformatted' ] );
          unset( $blocks[ 'core/pullquote' ] );
          unset( $blocks[ 'core/quote' ] );
          unset( $blocks[ 'core/freeform' ] );
          unset( $blocks[ 'core/details' ] );
          unset( $blocks[ 'core/table' ] );
          unset( $blocks[ 'core/footnotes' ] );

          unset( $blocks[ 'core/cover' ] );
          unset( $blocks[ 'core/file' ] );
          unset( $blocks[ 'core/gallery' ] );
          unset( $blocks[ 'core/media-text' ] );

          unset( $blocks[ 'core/buttons' ] );
          unset( $blocks[ 'core/more' ] );

          unset( $blocks[ 'core/archives' ] );
          unset( $blocks[ 'core/calendar' ] );
          unset( $blocks[ 'core/categories' ] );
          unset( $blocks[ 'core/html' ] );
          unset( $blocks[ 'core/latest-comments' ] );
          unset( $blocks[ 'core/latest-posts' ] );
          unset( $blocks[ 'core/page-list' ] );
          unset( $blocks[ 'core/rss' ] );
          unset( $blocks[ 'core/latest-rss' ] );
          unset( $blocks[ 'core/latest-search' ] );
          unset( $blocks[ 'core/search' ] );
          unset( $blocks[ 'core/shortcode' ] );
          unset( $blocks[ 'core/social-links' ] );
          unset( $blocks[ 'core/tag-cloud' ] );

          unset( $blocks[ 'core/navigation' ] );
          unset( $blocks[ 'core/site-logo' ] );
          unset( $blocks[ 'core/site-title' ] );
          unset( $blocks[ 'core/site-tagline' ] );
          unset( $blocks[ 'core/query' ] );
          unset( $blocks[ 'core/posts-list' ] );
          unset( $blocks[ 'core/avatar' ] );
          unset( $blocks[ 'core/post-title' ] );
          unset( $blocks[ 'core/post-excerpt' ] );
          unset( $blocks[ 'core/post-featured-image' ] );
          unset( $blocks[ 'core/post-content' ] );
          unset( $blocks[ 'core/post-author' ] );
          unset( $blocks[ 'core/post-date' ] );
          unset( $blocks[ 'core/post-terms' ] );
          unset( $blocks[ 'core/post-navigation-link' ] );
          unset( $blocks[ 'core/read-more' ] );
          unset( $blocks[ 'core/comments-query-loop' ] );
          unset( $blocks[ 'core/post-comments-form' ] );
          unset( $blocks[ 'core/loginout' ] );
          unset( $blocks[ 'core/term-description' ] );
          unset( $blocks[ 'core/query-title' ] );
          unset( $blocks[ 'core/post-author-biography' ] );
          unset( $blocks[ 'core/post-author-name' ] );
          unset( $blocks[ 'core/comments' ] );

          // EMBED CATEGORY

          unset( $blocks[ 'core/embed' ] );

          // OTHERS + INTEGRATIONS

          unset( $blocks[ 'wpseopress/breadcrumbs' ] );
          unset( $blocks[ 'wpseopress/faq-block' ] );
          unset( $blocks[ 'wpseopress/how-to' ] );
          unset( $blocks[ 'wpseopress/local-business' ] );
          unset( $blocks[ 'wpseopress/sitemap' ] );
          unset( $blocks[ 'wpseopress/table-of-contents' ] );

          // RETURN APPROVED BLOCKS

          return array_keys( $blocks );

     }