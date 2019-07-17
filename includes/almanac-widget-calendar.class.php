<?php

add_action( 'widgets_init', 'wp_calandar_register_widgets' );

function wp_calandar_register_widgets(){

	register_widget("Almanac");

}


class Almanac extends WP_Widget {

    /**
     * Almanac constructor.
     */
    function __construct() {
		parent::WP_Widget( 'platform_events_calandar', 'Calendrier Almanac',
            [
                    'description' => 'Affichez vos événements à venir'
            ]
        );
    }

    /**
     * @param $args
     * @param $instance
     */
    function widget($args, $instance ) {
        extract( $args );

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
	
		<span class="float-left calendar_nav"><a href="' . add_query_arg( [ 'mm' => date('m',strtotime($year."-".$month."-01 -1 months")), 'yy' => date('Y',strtotime($year."-".$month."-01 -1 months")) ], get_permalink() ) . '"><< ' . date("F Y",strtotime($year."-".$month."-01 -1 months")) . '</a></span>
		
		<span class="float-right calendar_nav"><a href="' . add_query_arg( [ 'mm' => date('m',strtotime($year."-".$month."-01 +1 months")), 'yy' => date('Y',strtotime($year."-".$month."-01 +1 months")) ], get_permalink() ) . '">' . date("F Y",strtotime($year."-".$month."-01 +1 months")) . ' >></a></span>
		
		<br style="clear:both;">';
		
		/* draw table */
		$calendar .= '<table cellpadding="0" cellspacing="0" class="calendar">';
		
		/* table headings */
		$headings = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
		$calendar.= '<tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';
		
		/* days and weeks vars now ... */
		$running_day = date('w',mktime(0,0,0,$month,1,$year));
		$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
		$days_in_this_week = 1;
		$day_counter = 0;
		$dates_array = array();
		
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
		
		//echo $querystr.'<br><br>';
		
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
						
						$calendar .= '</span></a><img class="loading loading_'.get_the_ID().'" src="'.WPE_url.'/img/loading.gif" alt="Loading…" style="display:none;" />
					
					</span>
					
					<br style="clear:both" />
					
					<br>';
							
				}
				
				$i++;
			
			} 
		
		}else{
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
		
		echo $calendar;	

        echo $after_widget;

    }


    /**
     * @param $new_instance
     * @param $old_instance
     * @return mixed
     */
    function update($new_instance, $old_instance ) {
		$instance = $old_instance;

		return $instance;
    }

    /**
     * @param $instance
     */
    function form($instance ) {
		?>
		<p>
            test calandar
        </p>
        
		<?php 
	}
    
}