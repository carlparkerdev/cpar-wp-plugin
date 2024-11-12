<?php

/*
 *   CPAR PLUGIN: Configs (GitHub Repo Updates)
 *
 *   Author:   Carl A. Parker
 *   Website:  https://CarlParker.dev
*/

if ( ! defined( 'ABSPATH' ) ) : exit; endif; // SILENCE IS GOLDEN


/*
 *   DEFINITIONS
 *   configure github details
*/

     define( 'CPAR_GITHUB_USERNAME', 'carlparkerdev' );

     define( 'CPAR_PLUGIN_GITHUB_REPO', CPAR_PLUGIN );
     define( 'CPAR_PLUGIN_GITHUB_API_URL', 'https://api.github.com/repos/' . CPAR_GITHUB_USERNAME . '/' . CPAR_PLUGIN_GITHUB_REPO );

     if ( ! function_exists( 'get_plugin_data' ) ) :

          require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

          define( 'CPAR_PLUGIN_VER', get_plugin_data( CPAR_PLUGIN_PATH . CPAR_PLUGIN_FILE )[ 'Version' ] );

     endif;


/*
 *   WORDPRESS UPDATES
 *   hook into the plugin update system
*/

     add_filter( 'pre_set_site_transient_update_plugins', 'cpar_github_updates' );

     function cpar_github_updates( $transient ) {

          if ( empty( $transient->checked ) ) :

               return $transient;

          endif;

          // FETCH THE LATEST RELEASE

          $response = wp_remote_get( CPAR_PLUGIN_GITHUB_API_URL . '/releases/latest' );

          if ( is_wp_error( $response ) ) :

               return $transient;

          endif;

          $release_info = json_decode( wp_remote_retrieve_body( $response ) );

          if ( empty( $release_info->tag_name ) ) :

               return $transient;

          endif;

          $latest_version = $release_info->tag_name;

          // CHECK IF AN UPDATE IS AVAILABLE

          if ( version_compare( CPAR_PLUGIN_VER, $latest_version, '<' ) ) :

               $plugin_info = array(

                    'slug' => CPAR_PLUGIN_SLUG,
                    'new_version' => $latest_version,
                    'url' => $release_info->html_url,
                    'package' => $release_info->assets[0]->browser_download_url

               );

               $transient->response[CPAR_PLUGIN_SLUG] = (object) $plugin_info;

          endif;

          return $transient;

     }


/*
 *   PLUGIN ROW META
 *   displaying GitHub link
*/

     add_filter( 'plugin_row_meta', 'cpar_github_link', 10, 2 );

     function cpar_github_link( $links, $file ) {

          if ( $file == CPAR_PLUGIN_SLUG ) :

               $github_link = '<a href="' . CPAR_PLUGIN_GITHUB_API_URL . '" target="_blank">GitHub Repository</a>';
               $links[] = $github_link;

          endif;

          return $links;

     }