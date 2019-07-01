<?php

$almanac_events = new almanac_events;

class almanac_events
{

    function almanac_events()
    {

        $this->__construct();
    } // function

    function __construct()
    {

        add_action("admin_init", array(&$this, 'admin_js_libs'));

        add_action("admin_print_styles", array(&$this, 'style_libs'));

        add_action("wp_print_styles", array(&$this, 'style_libs_front'));

        add_action("wp_enqueue_scripts", array(&$this, 'front_js_libs'));

        add_action( 'init', array( &$this, 'add_custom_post_type' ) );

        // add_filter('manage_edit-events_columns', array( &$this, 'add_new_events_columns' ));

        // add_action('manage_events_posts_custom_column', array( &$this, 'manage_events_columns' ), 10, 2);

        // add_filter('pre_get_posts', array( &$this, 'show_events_for_current_user_only' ));

        // add_filter('views_edit-events', array( &$this, 'remove_post_counts' ));

        // add_action( 'admin_init', array( &$this, 'add_meta_boxes' ) );

        // add_action( 'save_post', array( &$this, 'save_meta_box_data' ), 1, 2 );

        // add_shortcode( 'calendar' , array( &$this, 'display_calendar' ) );

        // add_action('admin_menu', array( &$this, 'add_pages'));

        // add_action('wp_head', array( &$this, 'my_action_javascript') );

        // add_action('wp_ajax_nopriv_my_special_action', array( &$this, 'my_action_callback') ); 

        // add_action('wp_ajax_my_special_action', array( &$this, 'my_action_callback') ); 
    }


    function admin_js_libs()
    {

        wp_enqueue_script('jquery');

        wp_enqueue_script('jquery-ui-1.8.16.custom.min', WPE_url . '/js/jquery-ui-1.8.16.custom.min.js', array('jquery-ui-core'), 1.0);

        wp_enqueue_script('timepicker', WPE_url . '/js/jquery-ui-timepicker-addon.js', array('jquery-ui-1.8.16.custom.min'), 1.0);

        global $typenow;

        if (empty($typenow) && !empty($_GET['post'])) {
            $post = get_post($_GET['post']);
            $typenow = $post->post_type;
        }

        if ($typenow == 'events') {

            wp_enqueue_script('google_maps_api', 'http://maps.google.com/maps/api/js?sensor=true', '', 1.0);

            wp_enqueue_script('location', WPE_url . '/js/location.js', '', 1.0);
        }
    }


    function style_libs()
    {

        wp_enqueue_style('jquery.ui.theme', WPE_url . '/js/smoothness/jquery-ui-1.8.17.custom.css');
    }

    function style_libs_front()
    {

        wp_enqueue_style('wpe', WPE_url . '/css/wpe.css');

        wp_enqueue_style('jquery.ui.theme', WPE_url . '/js/smoothness/jquery-ui-1.8.17.custom.css');
    }

    function front_js_libs()
    {

        wp_enqueue_script('google_maps_api', 'http://maps.google.com/maps/api/js?sensor=true', '', 1.0);

        wp_enqueue_script('jquery');

        wp_enqueue_script('jquery-ui-core');

        wp_enqueue_script('jquery-ui-dialog');
    }


    function add_custom_post_type() {
	
		$labels = array(
			'name' => _x('Events', 'post type general name'),
			'singular_name' => _x('Event', 'post type singular name'),
			'add_new' => _x('Add New Event', 'Testimonial'),
			'add_new_item' => __('Add New Event'),
			'edit_item' => __('Edit Event'),
			'new_item' => __('New Event'),
			'view_item' => __('View Event'),
			'search_items' => __('Search Event'),
			'not_found' =>  __('Nothing found'),
			'not_found_in_trash' => __('Nothing found in Trash'),
			'parent_item_colon' => ''
		);
		
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true, 
			'show_in_menu' => true, 
			'query_var' => true,
			'menu_icon' => WPE_url . '/img/events.png',
			'rewrite' => true,
			//'map_meta_cap' => true,
			'capability_type' => 'calendar',
			'capabilities' => array(
				'publish_posts' => 'publish_calendars',
				'edit_posts' => 'edit_calendars',
				'edit_others_posts' => 'edit_others_calendars',
				'delete_posts' => 'delete_calendars',
				'delete_others_posts' => 'delete_others_calendars',
				'read_private_posts' => 'read_private_calendars',
				'edit_post' => 'edit_calendar',
				'delete_post' => 'delete_calendar',
				'read_post' => 'read_calendar',
			),
			'has_archive' => true, 
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array('title', 'editor', 'thumbnail')
		); 
		
		register_post_type('events',$args);
	}
}
