<?php

$almanac_events = new almanac_events;

class almanac_events {

    /**
     *
     */
    function almanac_events(){
		$this->__construct();
	}

    /**
     * almanac_events constructor.
     */
    function __construct(){
	
		add_action("admin_init", [$this, 'admin_js_libs' ]);
		
		add_action("admin_print_styles", [$this, 'style_libs']);
		
		add_action("wp_print_styles", [$this, 'style_libs_front']);
		
		add_action("wp_enqueue_scripts", [$this, 'front_js_libs']);
					
		add_action('init', [$this, 'add_custom_post_type']);

		add_action('init', [$this, 'custom_type_categories']);

		add_action('init', [$this, 'events_tags_tags_taxonomy']);
		
		add_filter('manage_edit-events_columns', [$this, 'add_new_events_columns']);
		
		add_action('manage_events_posts_custom_column', [$this, 'manage_events_columns'], 10, 2);
		
		add_filter('pre_get_posts', [$this, 'show_events_for_current_user_only']);
		
		add_filter('views_edit-events', [$this, 'remove_post_counts']);
		
		add_action( 'admin_init', [$this, 'add_meta_boxes']);
		
		add_action( 'save_post', [$this, 'save_meta_box_data'], 1, 2 );
		
		add_shortcode( 'calendar' , [$this, 'display_calendar']);
		
		add_action('admin_menu', [$this, 'add_pages']);
		
		add_action('wp_head', [$this, 'my_action_javascript']);
		
		add_action('wp_ajax_nopriv_my_special_action', [$this, 'my_action_callback']);
		
		add_action('wp_ajax_my_special_action', [$this, 'my_action_callback']);
	
	}

    /**
     *
     */
    function admin_js_libs(){
	
		wp_enqueue_script('jquery');
	
		wp_enqueue_script('jquery-ui-1.8.16.custom.min', WPE_url . '/js/jquery-ui-1.8.16.custom.min.js', ['jquery-ui-core'], 1.0 );
		
		wp_enqueue_script('timepicker', WPE_url . '/js/jquery-ui-timepicker-addon.js', ['jquery-ui-1.8.16.custom.min'], 1.0 );
		
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

    /**
     *
     */
    function style_libs(){
		wp_enqueue_style('jquery.ui.theme', WPE_url . '/js/smoothness/jquery-ui-1.8.17.custom.css');
	}

    /**
     *
     */
    function style_libs_front(){
		wp_enqueue_style('wpe', WPE_url . '/css/wpe.css');
		
		wp_enqueue_style('jquery.ui.theme', WPE_url . '/js/smoothness/jquery-ui-1.8.17.custom.css');
	}

    /**
     *
     */
    function front_js_libs(){
		wp_enqueue_script('google_maps_api', 'http://maps.google.com/maps/api/js?sensor=true', '', 1.0 );
		
		wp_enqueue_script('jquery');
		
		wp_enqueue_script('jquery-ui-core');
		
		wp_enqueue_script('jquery-ui-dialog');
	}

    /**
     *
     */
    function add_custom_post_type() {
	
		$labels = [
			'name' => _x('Événements', 'post type general name'),
			'singular_name' => _x('Événements', 'post type singular name'),
			'add_new' => _x('Ajouter un nouvel événement', 'Testimonial'),
			'add_new_item' => __('Ajouter un nouvel événement'),
			'edit_item' => __('Modifier l\'événement'),
			'new_item' => __('Nouvel évènement'),
			'view_item' => __('Voir l\'événement'),
			'search_items' => __('Rechercher un événement'),
			'not_found' =>  __('Rien n\'a été trouvé'),
			'not_found_in_trash' => __('Rien trouvé dans la corbeille'),
			'parent_item_colon' => ''
		];
		
		$args = [
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
			'capabilities' => [
				'publish_posts' => 'publish_calendars',
				'edit_posts' => 'edit_calendars',
				'edit_others_posts' => 'edit_others_calendars',
				'delete_posts' => 'delete_calendars',
				'delete_others_posts' => 'delete_others_calendars',
				'read_private_posts' => 'read_private_calendars',
				'edit_post' => 'edit_calendar',
				'delete_post' => 'delete_calendar',
				'read_post' => 'read_calendar',
			],
			'has_archive' => true, 
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array('title', 'editor', 'thumbnail')
		];
		
		register_post_type('events',$args);
	}

	// CATEGORIES

    /**
     *
     */
    function custom_type_categories()
	{
		$props = [
			'name' => _x(' Categories', 'mythemlg'),
			'singular_name' => _x(' Categorie', 'mythemlg'),
			'search_items' =>  __('Rechercher  Categories'),
			'all_items' => __('All  Categories'),
			'parent_item' => __('Parent  Category'),
			'parent_item_colon' => __('Parent  Category:'),
			'edit_item' => __('Edit  Category'),
			'update_item' => __('Update  Category'),
			'add_new_item' => __('Add New  Category'),
			'new_item_name' => __('New  Category'),
			'menu_name' => __(' Categories'),
		];

		// Now register the taxonomy
		register_taxonomy('events_categories', ['events'], [
			'hierarchical' => true,
			'labels' => $props,
			'show_ui' => true,
			'show_in_rest' => true,
			'show_admin_column' => true,
			'query_var' => true,
			'rewrite' => ['slug' => 'events_categories'],
		]);
	}

	// TAGS

    /**
     *
     */
    function events_tags_tags_taxonomy()
	{
		// Labels part for the GUI
		$props = [
			'name' => _x(' Tags', 'taxonomy general name'),
			'singular_name' => _x(' Tag', 'taxonomy singular name'),
			'search_items' =>  __('Search  Tags'),
			'popular_items' => __('Popular  Tags'),
			'all_items' => __('All  Tags'),
			'parent_item' => null,
			'parent_item_colon' => null,
			'edit_item' => __('Edit  Tag'),
			'update_item' => __('Update  Tag'),
			'add_new_item' => __('Add New  Tag'),
			'new_item_name' => __('New  Tag Name'),
			'separate_items_with_commas' => __('Separate  tags with commas'),
			'add_or_remove_items' => __('Add or remove  tags'),
			'choose_from_most_used' => __('Choose from the most used  tags'),
			'menu_name' => __(' Tags'),
		];


		register_taxonomy('events_tags', ['events'], [
			'hierarchical' => false,
			'labels' => $props,
			'show_ui' => true,
			'show_in_rest' => true,
			'show_admin_column' => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var' => true,
			'rewrite' => ['slug' => 'events_tags'],
		]);
	}

    /**
     * @param $events_columns
     * @return mixed
     */
    function add_new_events_columns($events_columns) {
	
		$new_columns['cb'] = '<input type="checkbox" />';
 
		$new_columns['date_time'] = 'Date de l\'événement';
		
		$new_columns['title'] = 'Nom de l\'événement';

		$new_columns['categories'] = 'Categories';

		$new_columns['tags'] = 'Tags';
 
		return $new_columns;
	}

    /**
     * @param $column_name
     * @param $id
     */
    function manage_events_columns($column_name, $id) {
	
		global $wpdb;

		switch ($column_name) {
		case 'date_time':
			$events_date_meta = get_post_meta($id,'events_date',true);
			if($events_date_meta == ''){
				$events_date = '';
				$events_time = '';
			} else {
				$events_date = date('jS \o\f F Y', $events_date_meta);
				$events_time = date('g.ia', $events_date_meta);
			}
			echo $events_date . '<br>' . $events_time; 
			break;
		default:
			break;
		} // end switch
	}

    /**
     * @param $query
     * @return mixed
     */
    function show_events_for_current_user_only($query) {
	 
	  if($query->is_admin) {
	 
        if ($query->get('post_type') == 'events'){
        
        	$current_user = wp_get_current_user();
        	
        	$admin = false;
        	
        	foreach($current_user->roles as $key => $val){
        		if($val == 'administrator'){
        			$admin = true;
        		}
        	}
        	
        	if(!$admin){
				$query->set('meta_key', 'events_user_id');
          		$query->set('meta_value', $current_user->ID);
        	}
        }
        
	  }
	  
	  return $query;
	}

    /**
     * @param $posts_count_disp
     * @return mixed
     */
    function remove_post_counts($posts_count_disp){
	
		$current_user = wp_get_current_user();
	        	
    	$admin = false;
    	
    	foreach($current_user->roles as $key => $val){
    		if($val == 'administrator'){
    			$admin = true;
    		}
    	}
    	
    	if(!$admin){
    		unset($posts_count_disp['all']);
   			unset($posts_count_disp['publish']);
   			unset($posts_count_disp['draft']);
   			unset($posts_count_disp['trash']);
   			unset($posts_count_disp['mine']);
 
 		}
 
        return $posts_count_disp;
	}

    /**
     *
     */
    function add_meta_boxes() {
		add_meta_box( 
	        'events_details',
	        'Détails de l\'évènement',
	        [$this, 'events_details'],
	        'events',
	        'normal',
	        'core'
	    );
	}

    /**
     *
     */
    function events_details(){
	
		global $post;
		
		$current_user = wp_get_current_user(); 
		
		if (get_post_meta($post->ID,'events_user_id',true) == ''){
			$userID = $current_user->ID;
		} else {
			$userID = get_post_meta($post->ID,'events_user_id',true);
		}
		
		?>
				
		<input type="hidden" id="events_user_id" name="events_user_id" value="<?= $userID  ?>" />
	
		<?php
		
		wp_nonce_field( plugin_basename( __FILE__ ), 'events_nonce' );
		
		?>
		
	  <script>
	 	jQuery(document).ready(function() {
	    	jQuery(".datepicker").datetimepicker({
				timeFormat: 'h:mm',
				separator: ' @ ',
				dateFormat: 'dd-mm-yy'
			});
			<?php if(get_post_meta($post->ID,'events_date',true) == ''){ ?>
				jQuery(".datepicker").datetimepicker('setDate', (new Date()) );
				jQuery('#ui-datepicker-div').hide();
			<?php } ?>
	  	});
	  </script>

		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<label for="venue_name">Nom descriptif</label>
					</th>
					<td>
						<input type="text" id="events_venu_name" name="events_venue_name" value="<?= get_post_meta($post->ID,'events_venue_name',true); ?>" size="25" />
					</td>
				</tr>
				
				<tr valign="top">
				
					<th scope="row">
						<label for="venue_name">Adresse de l'évènement</label>
					</th>
				
					<td>
					
						<textarea name="venue_location_address" id="member_info_address" cols="50" rows="9" class="input" ><?= get_post_meta($post->ID,'venue_location_address',true); ?></textarea>
						
						<br>
						
						<a class="showhide" style="cursor:pointer;">Afficher / masquer la map</a>
						
						<div id="showhide">
						
							<input type="text" class="input" name="mi_location" id="member_info_location" value="<?= get_post_meta($post->ID,'mi_location',true); ?>" />
							<input type="button" class="button-primary button" value="Rechercher" onClick="codeAddress('YES')" />
							<br>
							<span class="description" style="float: left;">
							    Entrez un lieu dans n’importe quel format et cliquez sur le bouton "Rechercher".
							</span>
							<br>
							<br>
							<div id="map_canvas" style="float:left; width:500px; height:400px; margin-right: 10px;"></div>
							<div style="width: 35%;float:left;clear:left;" id="didyoumean"></div>
							<br style="clear:both;">
							<br>
							<br>
							<input type="hidden" name="lng" id="lng" value="<?= get_post_meta($post->ID,'lng',true); ?>" />
							<input type="hidden" name="lat" id="lat" value="<?= get_post_meta($post->ID,'lat',true); ?>" />
							<span class="member_info_label">Afficher la map ?</span>
							<select name="show_map" id="mi_show_map">
								<option value=""> Veuillez choisir </option>
								<option value="true" <?php if(get_post_meta($post->ID,'show_map',true) == 'true'){ echo 'selected'; } ?>> True </option>
								<option value="false" <?php if(get_post_meta($post->ID,'show_map',true) == 'false'){ echo 'selected'; } ?>> False </option>
							</select>
							<span class="description">
								La map sera affichée dans la description de l'évènement.
							</span>
						</div>
						<br style="clear:both;">
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row">
						<label for="date">Date & Heure</label>
					</th>
					<td>
						<?php
                            if(get_post_meta($post->ID,'events_date',true) != ''){
                                $date = date('d-m-Y @ H:i', get_post_meta($post->ID,'events_date',true));
                            } else {
                                $date = '';
                            }
                        ?>
						<input class="datepicker" type="text" id="events_date" name="events_date" value="<?= $date; ?>" size="25" />
					</td>
				</tr>
				
			
				
			</tbody>
		</table>
		
		<?php
	
	}

    /**
     * @param $post_id
     * @throws Exception
     */
    function save_meta_box_data($post_id){
	
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return;
			
		if(isset($_POST['events_venue_name'])){
		
			if ( !wp_verify_nonce( $_POST['events_nonce'], plugin_basename( __FILE__ ) ) )
		  		return;
		  		
		  	update_post_meta($post_id, 'venue_location_address', $_POST['venue_location_address']);
		  			  	
			list($day, $month, $year, $hours, $minutes) = sscanf($_POST['events_date'], '%02d-%02d-%04d @ %02d:%02d');
			
			$date = new DateTime("$year-$month-$day $hours:$minutes");
		  	
		  	update_post_meta($post_id, 'events_date', strtotime($date->format('r')));
		  			  	
		  	update_post_meta($post_id, 'events_venue_name', $_POST['events_venue_name']);
		  	
		  	update_post_meta($post_id, 'lat', $_POST['lat']);
			
			update_post_meta($post_id, 'lng', $_POST['lng']);
			
			update_post_meta($post_id, 'show_map', $_POST['show_map']);
			
			update_post_meta($post_id, 'events_user_id', $_POST['events_user_id']);

			$postarr = get_post($post_id,'ARRAY_A');
									
			if($postarr['post_date'] != $date->format('Y-m-d H:i:s')){
			
				$postarr['post_date'] = $date->format(DATE_RFC3339);

				$post_id = wp_update_post($postarr);
			}
		}
	}

    /**
     * @param $atts
     * @return string
     */
    function display_calendar($atts){
	
		$display_author = get_option('display_author');
		
		$author_meta = get_option('author_meta');
		
		$prepend_author = get_option('prepend_author');
		
		if(!isset($atts['user'])){
			$user = 'all';
		} else {
			$user = $atts['user'];
		}
			
		if(isset($_GET['mm'])){
			$month = intval($_GET['mm']);
		} else {
			$month = date("m");
		}
		
		if(isset($_GET['yy'])){
			$year = intval($_GET['yy']);
		} else {
			$year = date("Y");
		} 
		
		$calendar = '<span class="events_date_now">' . date("F Y",strtotime($year."-".$month."-01")) . '</span>
	
		<span class="float-left calendar_nav">
		    <a href="' . add_query_arg(
		            [
                        'mm' => date('m',strtotime($year."-".$month."-01 -1 months")),
                        'yy' => date('Y',strtotime($year."-".$month."-01 -1 months"))
                    ], get_permalink() ) . '
            ">
                << ' . date("F Y",strtotime($year."-".$month."-01 -1 months")) . '
            </a>
        </span>
		
		<span class="float-right calendar_nav">
		    <a href="' . add_query_arg(
		            [
                        'mm' => date('m',strtotime($year."-".$month."-01 +1 months")),
                        'yy' => date('Y',strtotime($year."-".$month."-01 +1 months"))
                    ], get_permalink() ) . '
            ">
                ' . date("F Y",strtotime($year."-".$month."-01 +1 months")) . ' >>
            </a>
        </span>
		
		<br style="clear:both;">';
		
		/* draw table */
		$calendar .= '<table cellpadding="0" cellspacing="0" class="calendar">';
		
		/* table headings */
		$headings = ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'];
		$calendar.= '<tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';
		
		/* days and weeks vars now ... */
		$running_day = date('w',mktime(0,0,0,$month,1,$year));
		$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
		$days_in_this_week = 1;
		$day_counter = 0;
		$dates_array = [];
		
		/* row for week one */
		$calendar.= '<tr class="calendar-row">';
		
		/* print "blank" days until the first of the current week */
		for($x = 0; $x < $running_day; $x++):
		    $calendar.= '<td class="calendar-day-np">&nbsp;</td>';
		    $days_in_this_week++;
		endfor;
		
		/* keep going with days.... */
		for($list_day = 1; $list_day <= $days_in_month; $list_day++):
		$calendar.= '<td class="calendar-day" valign="top">';
		  /* add in the day number */
		  $calendar.= '<div class="day-number">'.$list_day.'</div>';
				  
		global $post, $wpdb; 
		
		$start = strtotime($year."-".$month."-".$list_day."-00-00");
		$end = strtotime($year."-".$month."-".$list_day."-23-59");
					
		$querystr = "
		SELECT $wpdb->posts.* , $wpdb->postmeta.* 
		FROM $wpdb->posts, $wpdb->postmeta
		WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id 
		AND $wpdb->postmeta.meta_key = 'events_date' 
		AND $wpdb->posts.post_status = 'publish' 
		AND $wpdb->posts.post_type = 'events' 
		AND $wpdb->postmeta.meta_value >= " . $start . "
		AND $wpdb->postmeta.meta_value <= " . $end . "
		";
		
		$calendar_url = get_permalink();
		
		$pageposts = $wpdb->get_results($querystr, OBJECT);
		
		global $post;
		
		if ($pageposts){
		
			$i = 0;
		
			foreach ($pageposts as $post){ 
			
				setup_postdata($post); 
				
				if($user == 'all' || $user == get_post_meta(get_the_ID(),'events_user_id',true)){
				
					$user_data = get_userdata( get_post_meta(get_the_ID(),'events_user_id',true) );
									
					$calendar.= '<span class="float-left">
						
						<a id="'.get_the_ID().'" onclick="resize(\'' . get_post_meta($post->ID,'lat',true) . '\', \'' . get_post_meta($post->ID,'lng',true) . '\', \'' . get_the_ID() . '\', \''. get_the_title() .'\', \''.$calendar_url.'\')" title="' . get_the_title() . '" class="light-blue pointer" >'
					
							.get_the_title().
					
							'<br>
							<span class="grey_666 small">
								'.get_post_meta(get_the_ID(),'events_venue_name',true).'
							</span>';
							
							if(($display_author == 'all') || ($display_author == 'parent' && $user == 'all')){
								
								$calendar .= '<br><span class="wordpress_calendar_author">' . $prepend_author;
								
								switch($author_meta){
									case 'first_last':
										$calendar .= $user_data->first_name . ' ' . $user_data->last_name;
									    break;
									case 'first':
										$calendar .= $user_data->first_name;
									    break;
									case 'username':
										$calendar .= $user_data->user_login;
									    break;
									case 'email':
										$calendar .= $user_data->user_email;
									    break;
									default:
										$calendar .= $user_data->user_login;
									    break;
								}
							}
						
                            $calendar .= '</span>
                            </a>
                        <img class="loading loading_'.get_the_ID().'" src="'.WPE_url.'/img/loading.gif" alt="Loading…" style="display:none;" />
					</span>
					
					<br style="clear:both" />
					<br>';
				}
				$i++;
			} 
		
		} else {
			$calendar.= str_repeat('<p>&nbsp;</p>',2);
		}
	
		wp_reset_query(); 
		  
		$calendar.= '</td>';

		if($running_day == 6):
		  $calendar.= '</tr>';
		  if(($day_counter+1) != $days_in_month):
		    $calendar.= '<tr class="calendar-row">';
		  endif;
		  $running_day = -1;
		  $days_in_this_week = 0;
		endif;
		$days_in_this_week++; $running_day++; $day_counter++;
		endfor;
		
		/* finish the rest of the days in the week */
		if($days_in_this_week < 8):
		for($x = 1; $x <= (8 - $days_in_this_week); $x++):
		  $calendar.= '<td class="calendar-day-np">&nbsp;</td>';
		endfor;
		endif;
		
		$calendar.= '</tr>';
		
		$calendar.= '</table>';
		
		$calendar .= '<div id="wordpress_events_ajax_div" style="display:none;"></div>';
		
		$calendar .= '
		
			<script type="text/javascript">
			
				function resize(lat, lng, id, title, url){
					//jQuery(\'#\' + id).dialog({modal: true, minWidth: 700, minHeight: 500, title: title });
					ajax_calendar(id, title, url, lat, lng);
					return false;
				}
			</script>';
		
		if(isset($_GET['id'])){
				
			 $calendar .= '<script type="text/javascript">
						
				jQuery(document).ready(function() {
					//jQuery(\'#'.$_GET['id'].'\').dialog({modal: true, minWidth: 700, minHeight: 500, title: \''. urldecode($_GET['t']) .', zIndex: 5000\' });
					ajax_calendar(\''.$_GET['id'].'\', \''.urldecode($_GET['t']).'\', \''.get_permalink().'\');
				})
			
			</script>';
		}
		return $calendar;
	}

    /**
     * @param $url
     * @return false|string
     */
    function getTinyUrl($url) {
	    $tinyurl = file_get_contents("http://tinyurl.com/api-create.php?url=".$url);
	    return $tinyurl;
	}

    /**
     *
     */
    function add_pages(){
		add_options_page('WordPress Events Settings', 'WordPress Events', 'manage_options', 'wordpress-events', [$this, 'settings_page']);
	}

    /**
     *
     */
    function settings_page(){
	
		if(isset($_POST['submit_wordpress_events_settings'])){
			
			$updated = false;
			
			if(update_option('display_author', $_POST['display_author'])){
				$updated = true;
			}
			
			if(update_option('author_meta', $_POST['author_meta'])){
				$updated = true;
			}
			
			if(update_option('prepend_author', $_POST['prepend_author'])){
				$updated = true;
			}
			
			if($updated){
				echo '<div class="updated">Settings saved</div>';
			}
		}
		
		$display_author = get_option('display_author');
		
		$author_meta = get_option('author_meta');
		
		$prepend_author = get_option('prepend_author');
		
		?>

		<div class="wrap">
		    		    
	    	<h2><img src="<?=  WPE_url.'/img/events_large.png'; ?>" /> Événements WordPress - Paramètres</h2>
	    	
	    	<form method="POST" action="<?= admin_url( 'options-general.php?page=wordpress-events' ); ?>">
	    	
	    		<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<label for="display_author">Afficher l'auteur pour ces calendriers: </label>
							</th>
							
							<td>
					    		<select name="display_author">
					    			<option <?php if($display_author == 'none'){ echo 'selected="selected" '; } ?>value="none">Aucune</option>
					    			
					    			<option <?php if($display_author == 'parent'){ echo 'selected="selected" '; } ?>value="parent">Un calendrier parent qui montre les événements de tous les utilisateurs</option>
					    			
					    			<option <?php if($display_author == 'all'){ echo 'selected="selected" '; } ?>value="all">Tout</option>
					    		</select>
							</td>
						
						</tr>
						
						<tr valign="top">
							<th scope="row">
								<label for="display_author">Lors de l'affichage de l'auteur show: </label>
							</th>
							
							<td>
					    		<select name="author_meta">
					    			<option <?php if($author_meta == 'first_last'){ echo 'selected="selected" '; } ?>value="first_last">Prénom nom de famille</option>
					    			
					    			<option <?php if($author_meta == 'first'){ echo 'selected="selected" '; } ?>value="first">Prénom</option>
					    			
					    			<option <?php if($author_meta == 'username'){ echo 'selected="selected" '; } ?>value="username">Username</option>
					    			
					    			<option <?php if($author_meta == 'email'){ echo 'selected="selected" '; } ?>value="email">Adresse électronique</option>
					    		</select>
							</td>
						</tr>
						
						<tr valign="top">
							<th scope="row">
								<label for="link_word">Ajoutez le nom de l'auteur avec ce mot / cette phrase: </label>
							</th>
							
							<td>
					    		<input type="text" name="prepend_author" value="<?= $prepend_author; ?>">
							</td>
						</tr>
					</tbody>
				</table>
				
				<p class="submit">
					<input type="submit" name="submit_wordpress_events_settings" id="submit" class="button-primary" value="Sauvegarder les modifications">
				</p>
	    		
	    	</form>
				    
		</div>
	
	<?php }

    /**
     *
     */
    function my_action_javascript() {
		?>
		<script type="text/javascript" >
		function ajax_calendar(id, title, url, lat, lng) { 
		
		jQuery('img.loading_'+id).show();
		  
		var data = {
			action: 'my_special_action',
			id: id,
			title: title,
			url: url,
			lat: lat,
			lng: lng
		};
	
		jQuery.post("<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php", data, function(response) {
			jQuery('#wordpress_events_ajax_div').html(response);
			jQuery('#wordpress_events_ajax_div').dialog({modal: true, minWidth: 700, minHeight: 500, title: title, zIndex: 5000 });
			jQuery('img.loading_'+id).hide();

		});
		  
		};
		</script>
		<?php
	}

    /**
     *
     */
    function my_action_callback() {
		global $post;
  		$id = $_POST['id'];
  		$url = $_POST['url'];
  		$lat = $_POST['lat'];
  		$lng = $_POST['lng'];
  		$post = get_post( $id);
  		setup_postdata(get_post( $post));
  		
  				$display_author = get_option('display_author');
		
		$author_meta = get_option('author_meta');
		
		$prepend_author = get_option('prepend_author');
		
		if(!isset($atts['user'])){
			$user = 'all';
		} else {
			$user = $atts['user'];
		}
			
		if(isset($_GET['mm'])){
			$month = intval($_GET['mm']);
		} else {
			$month = date("m");
		}
		
		if(isset($_GET['yy'])){
			$year = intval($_GET['yy']);
		} else {
			$year = date("Y");
		} 
		
  		$calendar = '<div id="' . str_replace(' ', '_' , preg_replace("/[^a-zA-Z0-9\s]/", "", get_the_title())) . '"">
													
			<span class="float-left event_single_left">
																							
			<h3>' . date('jS \o\f F Y. g.ia', get_post_meta(get_the_ID(),'events_date',true)) . '</h3>';
			
			$calendar.= '<br>
			
			<p>' . nl2br( get_the_content() ) . '</p>
			
			</span>
			
			<span class="float-right event_single_right">
			
			<div>
		
				<!-- AddThis Button BEGIN -->
				<div class="addthis_toolbox addthis_default_style ">
					<a class="addthis_button_preferred_1"></a>
					<a class="addthis_button_preferred_2"></a>
					<a class="addthis_button_preferred_3"></a>
					<a class="addthis_button_preferred_4"></a>
					<a class="addthis_button_compact"></a>
					<a class="addthis_counter addthis_bubble_style"></a>
				</div>
				<script type="text/javascript">
					var addthis_config = {"data_track_addressbar":false};
					var addthis_share = {"url":"'. $turl = $this->getTinyUrl( add_query_arg( ['mm' => $month, 'yy' => $year, 'id' => $id, 't' => urlencode(get_the_title()) ], $url) ) .'"};
				</script>
				<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js?domready=1#pubid=ra-4f0f005c74986ffa"></script>
				 <!-- AddThis Button END -->
	
			</div>';
			
				if(function_exists("has_post_thumbnail") && has_post_thumbnail()) {
					$image_id = get_post_thumbnail_id();  
					$image_url = wp_get_attachment_image_src($image_id,'full');  
					$image_url = $image_url[0];  
		
					$calendar.= '<br><img class="single_image" src="'. WPE_url .'/timthumb/timthumb.php?src='.$image_url .'&w=280" /><br>';
				}
			
				$calendar .= '<h2>' . get_post_meta(get_the_ID(),'events_venue_name',true) . '</h2>
			
				<p>'. nl2br(get_post_meta(get_the_ID(),'venue_location_address',true) ).'</p>';
											
				$calendar.= '<div id="map_canvas' . $id . '" style="float:left; width:274px; height:247px; margin-right: 10px;"></div>';

			    $calendar.= '</span>
			
			<br style="clear:both; width:100%;" />
		
		</div>';
						
		if(get_post_meta($post->ID,'show_map',true) == 'true'){
			$calendar .= '
			<input type="hidden" name="lng" id="lng' . $id . '" value="' . get_post_meta($post->ID,'lng',true) . '" />
			<input type="hidden" name="lat" id="lat' . $id . '" value="' . get_post_meta($post->ID,'lat',true) . '" />
		
			<script src="http://maps.google.com/maps/api/js?sensor=true" type="text/javascript"></script>
			
			<script type="text/javascript">
			
				var geocoder;
				var map;
				var marker;
				var markersArray = [];
			
				geocoder = new google.maps.Geocoder();
				
					var latlng = new google.maps.LatLng(jQuery(\'#lat' . $id .'\').val(),jQuery(\'#lng' . $id . '\').val());
				
					var myOptions = {
			  			zoom: 15,
			  			center: latlng,
			  			mapTypeControl: false,
			  			mapTypeId: google.maps.MapTypeId.ROADMAP
					}
					map = new google.maps.Map(document.getElementById("map_canvas' . $id . '"), myOptions);
				    
				    geocoder.geocode({\'latLng\': latlng}, function(results, status) {
				      if (status == google.maps.GeocoderStatus.OK) {
				  			
						if (markersArray){
					        for (i in markersArray){
					            markersArray[i].setMap(null);
					        }
					    }

				    	var marker = new google.maps.Marker({
				        	map: map, 
				        	position: results[0].geometry.location   
				    	});
				    	markersArray.push(marker);     	
				    	
				      } else {
				        alert("Geocoder failed due to: " + status);
				      }
					});
					
					google.maps.event.trigger(map, \'resize\'); 
					
					var darwin = new google.maps.LatLng('.$lat.','.$lng.');
					
					map.setCenter(darwin);
										
			</script>';
		}
		echo $calendar;
		die();
	}
}

remove_role('personal_calendar');

add_role('personal_calendar', 'Calendar User', [
    'read' => true, 
    'edit_posts' => false,
    'delete_posts' => false, 
    'edit_calendar' => true,
    'edit_calendars' => true,
    'edit_others_posts' => false,
    'edit_others_calendars' => false,
    'publish_calendars' => true,
    'delete_calendar' => true
]);

//grab the admin role
$adapt_admin = get_role('administrator');

//and give them permissions for our plugin too
$adapt_admin -> add_cap('delete_calendar');
$adapt_admin -> add_cap('delete_calendars');
$adapt_admin -> add_cap('delete_private_calendars');
$adapt_admin -> add_cap('delete_published_calendars');
$adapt_admin -> add_cap('edit_calendar');
$adapt_admin -> add_cap('edit_calendars');
$adapt_admin -> add_cap('edit_private_calendars');
$adapt_admin -> add_cap('edit_published_calendars');
$adapt_admin -> add_cap('publish_calendars');
$adapt_admin -> add_cap('read_calendar');
$adapt_admin -> add_cap('read_private_calendars'); 
$adapt_admin -> add_cap('edit_others_calendars'); 
$adapt_admin -> add_cap('delete_others_calendars'); 

?>