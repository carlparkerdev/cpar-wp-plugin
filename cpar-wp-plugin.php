<?php

/*
 *   Plugin Name:        CPar Website Framework
 *   Plugin URI:         https://carlparker.dev
 *   Description:        a WordPress Framework Plugin for building custom websites with ACF Pro
 *   Version:            1.0.1
 *   Tested up to:       6.7
 *   Requires at least:  6.6
 *   Requires PHP:       8.3
 *   Author:             Carl A. Parker
 *   Author URI:         https://github.com/carlparkerdev
 *   License:            GPL v3 or later
 *   License URI:        https://www.gnu.org/licenses/gpl-2.0.html
 *   Update URI:         https://github.com/carlparkerdev/cpar-wp-plugin
 *   Text Domain:        cpar-wp-plugin
 *   Domain Path:        /languages
 *   Requires Plugins:   advanced-custom-fields-pro
*/


/*
 *   DEFINITIONS
 *   define values needed for website usage
*/

     define( 'CPAR_PLUGIN', 'cpar-wp-plugin' );
     define( 'CPAR_PLUGIN_FILE', 'cpar-wp-plugin.php' );
     define( 'CPAR_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
     define( 'CPAR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
     define( 'CPAR_PLUGIN_SLUG', plugin_basename( __FILE__ ) );

     define( 'CPAR_THEME', 'cpar-wp-theme' );
     define( 'CPAR_THEME_PATH', trailingslashit( get_template_directory() ) );
     define( 'CPAR_THEME_URL', trailingslashit( get_template_directory_uri() ) );

     define( 'CPAR_ASSETS', trailingslashit( 'assets' ) );
     define( 'CPAR_COMPONENTS', trailingslashit( 'components' ) );
     define( 'CPAR_CONFIGS', trailingslashit( 'configs' ) );
     define( 'CPAR_INCLUDES', trailingslashit( 'includes' ) );
     define( 'CPAR_TEMPLATES', trailingslashit( 'templates' ) );

     define( 'CPAR_DOMAIN', $_SERVER[ 'SERVER_NAME' ] );
     define( 'CPAR_WEBSITE', 'https://carlparker.dev' );


/*
 *   WEBSITE FUNCTIONALITY
 *   initialize theme functionality
*/

     function cpar_website_functionality() {

          // INCLUSIONS

          foreach ( glob( CPAR_THEME_PATH . CPAR_INCLUDES . '*/init.php' ) as $feature ) :

               require_once $feature;

          endforeach;

          // HELPERS

          foreach ( glob( CPAR_THEME_PATH . CPAR_COMPONENTS . 'helpers/helper-*.php' ) as $helper ) :

               require_once $helper;

          endforeach;

          // FORMS

          foreach ( glob( CPAR_THEME_PATH . CPAR_COMPONENTS . 'forms/form-*.php' ) as $form ) :

               require_once $form;

          endforeach;

          // REPORTS

          foreach ( glob( CPAR_THEME_PATH . CPAR_COMPONENTS . 'reports/report-*.php' ) as $report ) :

               require_once $report;

          endforeach;

          // BLOCKS

          foreach ( glob( CPAR_THEME_PATH . CPAR_COMPONENTS . 'blocks/*/block.php' ) as $block ) :

               require_once $block;

          endforeach;


     }


/*
 *   WEBSITE ASSETS
 *   initialize theme assets
*/

     // FRONT-END

     function cpar_website_assets_front() {

          // STYLESHEET

          wp_enqueue_style(

               'CPar Website (Front)',
               CPAR_THEME_URL . CPAR_ASSETS . 'css/front.css',
               array(),
               null,
               'all'

          );

          // SCRIPTS

          wp_enqueue_script(

               'CPar Website (Front)',
               CPAR_THEME_URL . CPAR_ASSETS . 'js/front.js',
               array( 'jquery' ),
               null,
               true

          );

     }

     // ADMIN AREA

     function cpar_website_assets_admin() {

          // STYLESHEET

          wp_enqueue_style(

               'CPar Website (Admin)',
               CPAR_PLUGIN_URL . CPAR_ASSETS . 'css/admin.css',
               array(),
               null,
               'all'

          );

     }


/*
 *   VALIDATION
 *   check for active `cpar-wp-theme` before running framework
*/

     if ( wp_get_theme()->get( 'TextDomain' ) === CPAR_THEME ) :

          require_once CPAR_CONFIGS . 'wordpress.php';
          require_once CPAR_CONFIGS . 'github.php';
          require_once CPAR_CONFIGS . 'acf.php';
          require_once CPAR_CONFIGS . 'seopress.php';

          add_action( 'init', 'cpar_website_functionality' );
          add_action( 'wp_enqueue_scripts', 'cpar_website_assets_front', 999999999 );
          add_action( 'admin_enqueue_scripts', 'cpar_website_assets_admin', 999999999 );

     endif;