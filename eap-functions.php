<?php

/**
 * Add event meta to the content
 */

function eap_add_meta_to_event_content( $content ) {

  ob_start();

  if( is_singular( 'eap_event' ) ) {
    include ( plugin_dir_path( __FILE__ ) . 'event-meta.php' );
  }

  $event_meta = ob_get_clean();
  $content = $event_meta . $content;
  return $content;
}
add_filter('the_content', 'eap_add_meta_to_event_content');

/**
 * Custom loops
 */

// display future events
function eap_display_events($atts) {
  ob_start();
  $actual_date = date('Y\-m\-d');
  // Shortcode attributes
  extract(shortcode_atts(array(
    'posts'          => -1,
    'category'       => '',
    'order'          => 'ASC'
  ), $atts));

  $args = array (
     'posts_per_page' => $posts,
     'post_type'      => 'eap_event',
     'order'          => $order,
     'orderby'        => 'meta_value',
     'meta_key'       => 'eap_from_day',
     'category_name'  => $category,
     'meta_query'     => array(
       'key' => 'eap_from_day',
       'value' => $actual_date,
       'compare' => '>='
     ),
   );
   $custom_query = new WP_Query($args);
   if ($custom_query->have_posts()) : ?>
     <div class="eap__list">
       <?php while($custom_query->have_posts()) :
       // Post content
       $custom_query->the_post();
          // Displays event content
          include ( plugin_dir_path( __FILE__ ) . 'event-content.php' );
     endwhile; ?>
     </div>
     <br>
   <?php else :
     _e('There are no events', 'events-as-posts');
   endif;
   wp_reset_postdata();
   $loop_content = ob_get_clean();
   return $loop_content;
}

// display past events
function eap_display_past_events($atts) {
  ob_start();
  $actual_date = date('Y\-m\-d');
  // Shortcode attributes
  extract(shortcode_atts(array(
    'posts'          => -1,
    'category'       => '',
    'order'          => 'DESC'
  ), $atts));

  $args = array (
     'posts_per_page' => $posts,
     'post_type'      => 'eap_event',
     'order'          => $order,
     'orderby'        => 'meta_value',
     'meta_key'       => 'eap_from_day',
     'category_name'  => $category,
     'meta_query'     => array(
       'key' => 'eap_from_day',
       'value' => $actual_date,
       'compare' => '<'
     ),
   );
   $custom_query = new WP_Query($args);
   if ($custom_query->have_posts()) : ?>
     <div class="eap__list">
     <?php while($custom_query->have_posts()) :
       // Post content
       $custom_query->the_post();
          // Displays event content
          include ( plugin_dir_path( __FILE__ ) . 'event-content.php' );
     endwhile; ?>
     </div>
     <br>
   <?php else :
     _e('There are no events', 'events-as-posts');
   endif;
   wp_reset_postdata();
   $loop_content = ob_get_clean();
   return $loop_content;
}

// display all events
function eap_display_all_events($atts) {
  ob_start();
  // Shortcode attributes
  extract(shortcode_atts(array(
    'category'       => '',
    'order'          => 'ASC'
  ), $atts));

  $args = array (
     'posts_per_page' => -1,
     'post_type'      => 'eap_event',
     'order'          => $order,
     'orderby'        => 'meta_value',
     'meta_key'       => 'eap_from_day',
     'category_name'  => $category,
   );

   $custom_query = new WP_Query($args);
   if ($custom_query->have_posts()) : ?>
     <div class="eap__list">
     <?php while($custom_query->have_posts()) :
       // Post content
       $custom_query->the_post();
          // Displays event content
          include ( plugin_dir_path( __FILE__ ) . 'event-content.php' );
     endwhile; ?>
     </div>
     <br>
   <?php else :
     _e('There are no events', 'events-as-posts');
   endif;
   wp_reset_postdata();
   $loop_content = ob_get_clean();
   return $loop_content;
}

/**
 * Registers shortcodes
 */

function eap_register_shortcodes() {
  // shortcodes to display events
  add_shortcode('display_events', 'eap_display_events');
  add_shortcode('display_past_events', 'eap_display_past_events');
  add_shortcode('display_all_events', 'eap_display_all_events');
}
add_action('init', 'eap_register_shortcodes');

/**
 * List styles
 */

function eap_events_style() {
  $setting = get_option('eap_settings_style');

  /* layout */

  // 1 column layout (default)
  if ( $setting['layout'] == 1 || !$setting['layout']) {
    ?>
    <style>
      .eap__list {
        grid-template-columns: 1fr;
      }
      .eap__title {
        margin: 0 0 .6em !important;
      }
      @media all and (min-width: 576px) {
        .eap__event {
          display: grid;
          grid-template-columns: 1fr 2fr;
          grid-gap: 1.6em;
        }
      }
    </style>
    <?php

  // 2 columns layout
  } elseif ( $setting['layout'] == 2 ) {
    ?>
    <style>
      .eap__title {
        margin: .6em 0 .6em;
      }
      @media all and (min-width: 576px) {
        .eap__list {
          grid-template-columns: repeat(2, 1fr);
        }
      }
    </style>
    <?php

  // 3 columns layout
  } elseif ( $setting['layout'] == 3 ) {
    ?>
    <style>
      @media all and (min-width: 576px) {
        .eap__list {
          grid-template-columns: repeat(3, 1fr);
        }
      }
      .eap__title {
        margin: .6em 0 .6em;
      }
    </style>
    <?php
  }

  ?>
  <style>
    /* background color */
    .eap__event {
      background:
      <?php
      if ($setting['bg_color']) {
        echo $setting['bg_color'];
      } else {
        echo '#f4f4f4';
      }
      ?>;
    }
  </style>
  <?php
}
add_action('wp_head', 'eap_events_style');

// shows events in category pages
function eap_category_filter($query) {
  if ( !is_admin() && $query->is_main_query() ) {
    if ($query->is_category) {
      $query->set('post_type', array( 'post', 'eap_event' ) );
    }
  }
}
add_action('pre_get_posts','eap_category_filter');
