<?php

/*
 *   CPAR PLUGIN: Configs (SEOPress Pro)
 *
 *   Author:   Carl A. Parker
 *   Website:  https://CarlParker.dev
*/

if ( ! defined( 'ABSPATH' ) ) : exit; endif; // SILENCE IS GOLDEN


/*
 *   PLUGIN VERIFICATION
 *   display admin notice when ACF Pro is not active
*/

     // ADMIN NOTICE

     add_action( 'admin_notices', 'cpar_seopress_plugin_inactive' );

     function cpar_seopress_plugin_inactive() {

          if ( ! function_exists( 'seopress_pro_loaded' ) ) :

               echo '<div class="notice notice-error cpar-notice">';

                    echo '<p>' . __( '<span>SEOPRESS PRO</span> plugin is not active. This website strongly recommends this to function properly.', 'cpar-wp-plugin' ) . '</p>';

               echo '</div>';

          endif;

     }