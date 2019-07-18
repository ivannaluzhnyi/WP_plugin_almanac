<?php

add_action( 'widgets_init', 'wp_events_register_widgets' );

function wp_events_register_widgets(){
	register_widget("Events");
}

/**
 * Events Class
 */
class Events extends WP_Widget {

    /**
     * Events constructor.
     */
    function __construct() {
		parent::WP_Widget('platform_events', 'Événements',
            ['description' => 'Affichez vos événements à venir dans le calendrier.']
        );
	}

    /**
     * @param $args
     * @param $instance
     */
    function widget($args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		$number = $instance['number'];
		if($number == ''){
			$number = 1;
		}
		echo $before_widget;

		?>
		
			<div class="column_1_3">
			
					<div id="front_page_section_title" class="clear-left">
						<span class="shadow"><?= $title;?></span>
					</div>
					
					<?php global $post, $wpdb; 
					
					$calendar_url = get_bloginfo('url') . '/events/';
										
					$month = date("m");
					$year = date("Y");
					$list_day = date("d");
								
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
					LIMIT " . $number . "
					";
					
					$pageposts = $wpdb->get_results($querystr, OBJECT);
					
					global $post;
					
					if ($pageposts){

                        $almanac_events = new almanac_events;

                        $i = 0;

                        foreach ($pageposts as $post){ ?>
                            <?php setup_postdata($post); ?>
                            <span class="gig-listing float-left">
                                <?php echo '<a id="'.get_the_ID().'" onclick="resize(\'' . get_post_meta($post->ID,'lat',true) . '\', \'' . get_post_meta($post->ID,'lng',true) . '\', \'' . get_the_ID() . '\', \''. get_the_title() .'\', \''.$calendar_url.'\')" title="' . get_the_title() . '" class="light-blue pointer" >'
                                    .get_the_title().
                                    '</a><img class="loading loading_'.get_the_ID().'" src="'.WPE_url.'/img/loading.gif" alt="Loading…" style="display:none;" /><br>'; ?>
                            </span>

                            <br style="clear:both" />
                            <br>

                            <?php echo '
                                <script type="text/javascript">							
                                    function resize(lat, lng, id, title, url){														
                                        ajax_calendar(id, title, url, lat, lng);									
                                        return false;
                                    }
                                </script>';

                            if(isset($_GET['id'])){
                                 echo   '<script type="text/javascript">
                                            jQuery(document).ready(function() {
                                            ajax_calendar(\''.$_GET['id'].'\', \''.urldecode($_GET['t']).'\', \''.get_permalink().'\');	
                                            })
                                        </script>';
                            }
                            $i++;
                        }
					}
				
					wp_reset_query(); ?>
				
				<br style="clear:both" />
							
			</div>		
		
		<?php
		
		echo $after_widget;
	}

    /**
     * @param $new_instance
     * @param $old_instance
     * @return mixed
     */
    function update($new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = strip_tags($new_instance['number']);
		return $instance;
	}

    /**
     * @param $instance
     */
    function form($instance ) {
		if ( $instance ) {
			$title = esc_attr( $instance[ 'title' ] );
			$number = esc_attr( $instance[ 'number' ] );
		}
		else {
			$title = __( 'Nouveau titre', 'theplatform' );
			$number = '3';
		}
		
		?>
		<p>
		    <label for="<?= $this->get_field_id('title'); ?>"><?php _e('Titre:'); ?></label>
		    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?= $title; ?>" />
		</p>
		
		<p>
		    <label for="<?= $this->get_field_id('number'); ?>"><?php _e('Nombre de concerts à venir:'); ?></label>
		    <input class="widefat" id="<?= $this->get_field_id('number'); ?>" name="<?= $this->get_field_name('number'); ?>" type="text" value="<?= $number; ?>" />
		</p>
		<?php 
	}

} 
?>