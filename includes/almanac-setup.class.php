<?php

$almanac_events = new almanac_events;

class almanac_events {

	function almanac_events(){
	
		$this->__construct();
		
	} // function
	
	function __construct(){
	
		add_action("admin_init", array( &$this, 'admin_js_libs' ));
		
		add_action("admin_print_styles", array( &$this, 'style_libs' ));
		
		add_action("wp_print_styles", array( &$this, 'style_libs_front' ));
		
		add_action("wp_enqueue_scripts", array( &$this, 'front_js_libs' ));
					

	
    }


    function admin_js_libs(){
	
		wp_enqueue_script('jquery');
	
		wp_enqueue_script('jquery-ui-1.8.16.custom.min', WPE_url . '/js/jquery-ui-1.8.16.custom.min.js', array('jquery-ui-core'), 1.0 );
		
		wp_enqueue_script('timepicker', WPE_url . '/js/jquery-ui-timepicker-addon.js', array('jquery-ui-1.8.16.custom.min'), 1.0 ); 
		
		global $typenow;

		if (empty($typenow) && !empty($_GET['post'])) {
        	$post = get_post($_GET['post']);
            $typenow = $post->post_type;
       	}

		if($typenow == 'events'){
		
			wp_enqueue_script('google_maps_api', 'http://maps.google.com/maps/api/js?sensor=true', '', 1.0 );
		
			wp_enqueue_script('location', WPE_url . '/js/location.js', '', 1.0); 

		
		}
	
    }
    

    function style_libs(){
	
		wp_enqueue_style('jquery.ui.theme', WPE_url . '/js/smoothness/jquery-ui-1.8.17.custom.css');
	
	}
	
	function style_libs_front(){
				
		wp_enqueue_style('wpe', WPE_url . '/css/wpe.css');
		
		wp_enqueue_style('jquery.ui.theme', WPE_url . '/js/smoothness/jquery-ui-1.8.17.custom.css');
	
	}
	
	function front_js_libs(){
	
		wp_enqueue_script('google_maps_api', 'http://maps.google.com/maps/api/js?sensor=true', '', 1.0 );
		
		wp_enqueue_script('jquery');
		
		wp_enqueue_script('jquery-ui-core');
		
		wp_enqueue_script('jquery-ui-dialog');
	
	}
}