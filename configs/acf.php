<?php

/*
 *   CPAR PLUGIN: Configs (Advanced Custom Fields Pro)
 *
 *   Author:   Carl A. Parker
 *   Website:  https://CarlParker.dev
*/

if ( ! defined( 'ABSPATH' ) ) : exit; endif; // SILENCE IS GOLDEN


/*
 *   LOCAL JSON
 *   https://www.advancedcustomfields.com/resources/local-json/
*/

     // SAVE JSON FILES

     add_filter( 'acf/settings/save_json', 'cpar_acf_localjson_save' );

     function cpar_acf_localjson_save( $path ) {

          $path = CPAR_THEME_PATH . CPAR_COMPONENTS . 'fields/';

          return $path;

     }

     // LOAD JSON FILES

     add_filter( 'acf/settings/load_json', 'cpar_acf_localjson_load' );

     function cpar_acf_localjson_load( $paths ) {

          unset( $paths[ 0 ] );

          $paths[] = CPAR_THEME_PATH . CPAR_COMPONENTS . 'fields/';

          return $paths;

     }


/*
 *   DASHBOARD INTERFACE
 *   enhance the wp dashboard
*/

     // ADMIN OPTIONS MENU

     add_action( 'acf/init', 'cpar_acf_options_settings' );

     function cpar_acf_options_settings() {

          $website = acf_add_options_page( array(

               'page_title'      => __( 'Custom Website Configurations', 'cpar-wp-plugin' ),
               'menu_title'      => __( 'CPar Website', 'cpar-wp-plugin' ),
               'menu_slug'       => 'cpar-website',
               'capability'      => 'edit_theme_options',
               'position'        => 2,
               'icon_url'        => 'dashicons-wordpress',
               'redirect'        => false,
               'autoload'        => true,
               'update_button'   => __( 'Update Settings', 'cpar-wp-plugin' ),
               'updated_message' => __( 'Your custom website settings have been updated', 'cpar-wp-plugin' ),

          ) );

     }


/*
 *   WP QUERIES
 *   include acf fields/values to be included in queries
*/

     // POSTS_JOIN -- https://developer.wordpress.org/reference/hooks/posts_join/

     add_filter( 'posts_join', 'cpar_acf_queries_join' );

     function cpar_acf_queries_join( $join ) {

          global $wpdb;

          if ( is_search() ) :

               $join .=' LEFT JOIN '.$wpdb->postmeta. ' ON '. $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';

          endif;

          return $join;

     }

     // POSTS_WHERE -- https://developer.wordpress.org/reference/hooks/posts_where/

     add_filter( 'posts_where', 'cpar_acf_queries_where' );

     function cpar_acf_queries_where( $where ) {

          global $pagenow, $wpdb;

          if ( is_search() ) :

               $where = preg_replace(

                    "/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
                    "(".$wpdb->posts.".post_title LIKE $1) OR (".$wpdb->postmeta.".meta_value LIKE $1)", $where

               );

          endif;

          return $where;

     }

     // POSTS_DISTINCT -- https://developer.wordpress.org/reference/hooks/posts_distinct/

     add_filter( 'posts_distinct', 'cpar_acf_queries_distinct' );

     function cpar_acf_queries_distinct( $where ) {

          global $wpdb;

          if ( is_search() ) :

               return "DISTINCT";

          endif;

          return $where;

     }


/*
 *   ACF BLOCK PREVIEW
 *   generate standard block preview in Gutenberg Editor
*/

     // PREPARE SETTINGS

     function cpar_acf_blocks_setup( $block, $post_id, $is_preview ) {

          // ANCHOR ID

          if ( ! empty( $block[ 'anchor' ] ) ) :

               $block_id = esc_attr( $block[ 'anchor' ] );

          else :

               $block_id = '';

          endif;

          // DEFINE ATTRIBUTES

          $blockATTR = array(

               'data-cpar-block' => $block[ 'name' ],
               'id'              => $block_id,
               'class'           => ''

          );

          return $blockATTR;

     }

     // RENDER PREVIEW

     function cpar_acf_blocks_preview( $block, $innerblock = false ) {

          echo '<div class="cpar-block-preview">';

               echo '<span class="cpar-block-preview-name">'. $block[ 'name' ] .'</span>';

               if ( $innerblock == true ) :

                    echo '<span class="cpar-block-preview-inner">';

                         echo '<InnerBlocks />';

                    echo '</span>';

               endif;

          echo '</div>';

     }


/*
 *   FORM ENTRIES
 *   custom post type for record keeping
*/

     // SCREENS

     add_filter( 'query_vars', 'cpar_acf_entries_queryvar_screen' );

     function cpar_acf_entries_queryvar_screen( $vars ) {

          $vars[] .= 'cpar_screen';

          return $vars;

     }

     // REGISTER POST TYPE

     add_action( 'init', 'cpar_acf_entries_register' );

     function cpar_acf_entries_register() {

          // CUSTOM POST TYPE

          $labels = array(

               'menu_name'                   => __( 'Form Entries', 'cpar-wp-plugin' ),
               'name'                        => __( 'Web Form Entries', 'cpar-wp-plugin' ),
               'singular_name'               => __( 'Entry', 'cpar-wp-plugin' ),
               'add_new'                     => __( 'Add New Entry', 'cpar-wp-plugin' ),
               'add_new_item'                => __( 'Add New Entry', 'cpar-wp-plugin' ),
               'edit_item'                   => __( 'Edit Entry', 'cpar-wp-plugin' ),
               'new_item'                    => __( 'New Entry', 'cpar-wp-plugin' ),
               'view_item'                   => __( 'View Entry', 'cpar-wp-plugin' ),
               'view_items'                  => __( 'View Entries', 'cpar-wp-plugin' ),
               'search_items'                => __( 'Search Entries', 'cpar-wp-plugin' ),
               'not_found'                   => __( 'No entries found', 'cpar-wp-plugin' ),
               'not_found_in_trash'          => __( 'No entries found in Trash', 'cpar-wp-plugin' ),
               'parent_item_colon'           => __( 'Parent Entry:', 'cpar-wp-plugin' ),
               'all_items'                   => __( 'All Entries', 'cpar-wp-plugin' ),
               'archives'                    => __( 'Entry Archives', 'cpar-wp-plugin' ),
               'attributes'                  => __( 'Entry Attributes', 'cpar-wp-plugin' ),
               'insert_into_item'            => __( 'Insert into entry', 'cpar-wp-plugin' ),
               'uploaded_to_this_item'       => __( 'Uploaded to this entry', 'cpar-wp-plugin' ),
               'featured_image'              => __( 'Featured Image', 'cpar-wp-plugin' ),
               'set_featured_image'          => __( 'Set Featured Image', 'cpar-wp-plugin' ),
               'remove_featured_image'       => __( 'Remove Featured Image', 'cpar-wp-plugin' ),
               'use_featured_image'          => __( 'Use as Featured Image', 'cpar-wp-plugin' ),
               'filter_items_list'           => __( 'Filter Entries list', 'cpar-wp-plugin' ),
               'filter_by_date'              => __( 'Filter by Date', 'cpar-wp-plugin' ),
               'items_list_navigation'       => __( 'Entries list navigation', 'cpar-wp-plugin' ),
               'items_list'                  => __( 'Entries list', 'cpar-wp-plugin' ),
               'item_published'              => __( 'Entry published', 'cpar-wp-plugin' ),
               'item_published_privately'    => __( 'Entry published privately', 'cpar-wp-plugin' ),
               'item_reverted_to_draft'      => __( 'Entry reverted to draft', 'cpar-wp-plugin' ),
               'item_scheduled'              => __( 'Entry scheduled', 'cpar-wp-plugin' ),
               'item_updated'                => __( 'Entry updated', 'cpar-wp-plugin' ),
               'item_link'                   => __( 'Entry Link', 'cpar-wp-plugin' ),
               'item_link_description'       => __( 'A link to an entry', 'cpar-wp-plugin' ),

          );

          $args = array(

               'labels'                 => $labels,
               'description'            => 'collection of web form entries',
               'public'                 => false,
               'hierarchical'           => false,
               'exclude_from_search'    => true,
               'publicly_queryable'     => false,
               'show_ui'                => true,
               'show_in_menu'           => true,
               'show_in_nav_menus'      => false,
               'show_in_admin_bar'      => false,
               'show_in_rest'           => true,
               'menu_position'          => 40,
               'menu_icon'              => 'dashicons-feedback',
               'capabilities'           => array(

                    'edit_post'              => 'edit_theme_options',
                    'read_post'              => 'edit_theme_options',
                    'delete_post'            => 'edit_theme_options',
                    'edit_posts'             => 'edit_theme_options',
                    'edit_others_posts'      => 'edit_theme_options',
                    'delete_posts'           => 'edit_theme_options',
                    'publish_posts'          => 'edit_theme_options',
                    'read_private_posts'     => 'edit_theme_options'

               ),

               'supports'               => array( 'title' ),
               'taxonomies'             => array( 'cpar_entries_type' ),
               'has_archive'            => false,
               'rewrite'                => array( 'slug' => 'cpar_entries' ),
               'query_var'              => false,
               'can_export'             => true,
               'delete_with_user'       => false,
               'template'               => array(),
               'template_lock'          => false,

          );

          register_post_type( 'cpar_entries', $args );

          // CUSTOM TAXONOMIES

          $labels = array(

               'name'                        => __( 'Entry Types', 'cpar-wp-plugin' ),
               'singular_name'               => __( 'Entry Type', 'cpar-wp-plugin' ),
               'search_items'                => __( 'Search Entry Types', 'cpar-wp-plugin' ),
               'popular_items'               => __( 'Popular Entry Types', 'cpar-wp-plugin' ),
               'all_items'                   => __( 'All Entry Types', 'cpar-wp-plugin' ),
               'parent_item'                 => __( 'Parent Type', 'cpar-wp-plugin' ),
               'parent_item_colon'           => __( 'Parent Type:', 'cpar-wp-plugin' ),
               'edit_item'                   => __( 'Edit Entry Type', 'cpar-wp-plugin' ),
               'view_item'                   => __( 'View Entry Type', 'cpar-wp-plugin' ),
               'update_item'                 => __( 'Update Entry Type', 'cpar-wp-plugin' ),
               'add_new_item'                => __( 'Add New Entry Type', 'cpar-wp-plugin' ),
               'new_item_name'               => __( 'New Entry Type Name', 'cpar-wp-plugin' ),
               'separate_items_with_commas'  => __( 'Separate entry types with commas', 'cpar-wp-plugin' ),
               'add_or_remove_items'         => __( 'Add or remove entry types', 'cpar-wp-plugin' ),
               'choose_from_most_used'       => __( 'Choose from the most used entry types', 'cpar-wp-plugin' ),
               'not_found'                   => __( 'No entry types found', 'cpar-wp-plugin' ),
               'no_terms'                    => __( 'No entry types', 'cpar-wp-plugin' ),
               'filter_by_item'              => __( 'Filter by entry type', 'cpar-wp-plugin' ),
               'items_list_navigation'       => __( 'Items list navigation', 'cpar-wp-plugin' ),
               'items_list'                  => __( 'Items list', 'cpar-wp-plugin' ),
               'most_used'                   => __( 'Most Used', 'cpar-wp-plugin' ),
               'back_to_items'               => __( 'Back to Entry Types', 'cpar-wp-plugin' ),
               'item_link'                   => __( 'Entry Type link', 'cpar-wp-plugin' ),
               'item_link_description'       => __( 'A link to an entry type', 'cpar-wp-plugin' ),

          );

          $args = array(

               'labels'                 => $labels,
               'description'            => 'organize web form entries by type',
               'public'                 => false,
               'publicly_queryable'     => false,
               'hierarchical'           => true,
               'show_ui'                => true,
               'show_in_menu'           => true,
               'show_in_nav_menus'      => false,
               'show_in_rest'           => true,
               'show_tagcloud'          => false,
               'show_in_quick_edit'     => false,
               'show_admin_column'      => true,
               'rewrite'                => array( 'slug' => 'cpar_entries_type' ),
               'query_var'              => false,
               'update_count_callback'  => '_update_post_term_count',
               'sort'                   => null,
               'capabilities'           => array(

                    'manage_terms'       => 'edit_theme_options',
                    'edit_terms'         => 'edit_theme_options',
                    'delete_terms'       => 'edit_theme_options',
                    'assign_terms'       => 'edit_theme_options'

               ),

               'default_term'          => array(

                    'name'         => 'Contact Form',
                    'slug'         => 'cpar_entries_contact',
                    'description'  => ''

               )

          );

          register_taxonomy( 'cpar_entries_type', array( 'cpar_entries' ), $args );

     }

     // ENTRIES ID GENERATOR

     function cpar_acf_entries_id( $post_id = null ) {

          if ( $post_id ) :

               // PREFIX

               $ticketPrefix = 'CPar';

               // TICKET FORMAT

               $ticketID = $ticketPrefix . '-0000000';

               // UPDATE WITH POST ID

               $ticketID = substr( $ticketID, 0, strlen( $post_id ) * -1 ) . $post_id;

               // RETURN VALUE

               return $ticketID;

          endif;

     }

     // POST TITLE AS ENTRY ID

     add_action( 'save_post_cpar_entries', 'cpar_acf_entries_title' );

     function cpar_acf_entries_title( $post_id ) {

          if ( is_admin() ) :

               // GENERATE TICKET NUMBER

               $ticketID = cpar_acf_entries_id( $post_id );

               // UN-HOOK TO ELIMINATE INFINITE LOOP

               remove_action( 'save_post_cpar_entries', 'cpar_acf_entries_title' );

               // UPDATE META

               $entryMeta = array(

                    'ID'           => $post_id,
                    'post_title'   => $ticketID

               );

               wp_update_post( $entryMeta, false, false );

               // RE-HOOK

               add_action( 'save_post_cpar_entries', 'cpar_acf_entries_title' );

          endif;

     }

     // EDIT SCREEN - ADD ENTRY COLUMNS

     add_filter ( 'manage_cpar_entries_posts_columns', 'cpar_acf_entries_admincols_manage' );

     function cpar_acf_entries_admincols_manage( $columns ) {

          return array_merge ( $columns, array (

               'entries_status'    => __ ( 'Status' )

          ) );

     }

     // EDIT SCREEN - DISPLAY ENTRY COLUMN VALUES

     add_action ( 'manage_cpar_entries_posts_custom_column', 'cpar_acf_entries_admincols_values', 10, 2 );

     function cpar_acf_entries_admincols_values ( $column, $post_id ) {

          switch ( $column ) {

               case 'entries_status' :

                    if ( ! empty( get_field( 'entries_status', $post_id ) ) ) :

                         $appStatus = get_field( 'entries_status' );

                         echo '<span class="entries-status '. $appStatus .'">'. $appStatus . '</span>';

                    endif;

               break;

          }

     }

     // EDIT SCREEN - MAKE ENTRY COLUMNS SORTABLE

     add_filter( 'manage_edit-cpar_entries_sortable_columns', 'cpar_acf_entries_admincols_sortable' );

     function cpar_acf_entries_admincols_sortable( $columns ) {

          $columns[ 'entries_status' ] = 'entries_status';

          return $columns;

     }

     // EDIT SCREEN - RE-ORDER ENTRIES COLUMNS

     add_filter( 'manage_cpar_entries_posts_columns', 'cpar_acf_entries_admincols_reorder' );

     function cpar_acf_entries_admincols_reorder( $columns ) {

          $columnOrder = array(

               'cb'                => $columns[ 'cb' ],
               'title'             => $columns[ 'title' ],
               'entries_status'    => $columns[ 'entries_status' ]

          );

          return $columnOrder;

     }


/*
 *   ACF_FORM_HEAD
 *   run function if any blocks include acf form
*/

     add_action( 'wp', 'cpar_acf_head_markup' );

     function cpar_acf_head_markup() {

          if ( ! is_admin() ) :

               global $post;

               // HAVE BLOCKS?

               if ( $post && has_blocks( $post->post_content ) ) :

                    $blocks = parse_blocks( $post->post_content );

                    // ANY INCLUDE 'FORM' IN NAME

                    foreach ( $blocks as $block ) :

                         if ( isset( $block[ 'blockName' ] ) && strpos( $block[ 'blockName' ], 'form' ) !== false ) :

                              acf_form_head();

                              break;

                         endif;

                    endforeach;

               endif;

          endif;

     }