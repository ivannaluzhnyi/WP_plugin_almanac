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

        add_action("admin_init", array($this, 'admin_js_libs'));

        add_action("admin_print_styles", array($this, 'style_libs'));

        add_action("wp_print_styles", array($this, 'style_libs_front'));

        add_action("wp_enqueue_scripts", array($this, 'front_js_libs'));

        add_action('init', array($this, 'add_custom_post_type'));

        add_filter('manage_edit-events_columns', array($this, 'add_new_events_columns'));

        add_action('manage_events_posts_custom_column', array($this, 'manage_events_columns'), 10, 2);

        add_filter('pre_get_posts', array($this, 'show_events_for_current_user_only'));

        add_filter('views_edit-events', array($this, 'remove_post_counts'));

        add_action('init', array($this, 'custom_type_categories'), 0);

        add_action('init', array($this, 'events_tags_taxonomy'), 0);


        add_action('admin_init', array($this, 'add_meta_boxes'));

        add_action('save_post', array($this, 'save_meta_box_data'), 1, 2);

        // add_shortcode( 'calendar' , array( $this, 'display_calendar' ) );

        // add_action('admin_menu', array( $this, 'add_pages'));

        // add_action('wp_head', array( $this, 'my_action_javascript') );

        // add_action('wp_ajax_nopriv_my_special_action', array( $this, 'my_action_callback') ); 

        // add_action('wp_ajax_my_special_action', array( $this, 'my_action_callback') ); 


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


    function add_custom_post_type()
    {

        $labels = array(
            'name' => _x('Événements', 'post type general name'),
            'singular_name' => _x('Événement', 'post type singular name'),
            'add_new' => _x('Ajouter un nouvel événement', 'Testimonial'),
            'add_new_item' => __('Ajouter un nouvel événement'),
            'edit_item' => __('Modifier l\'événement'),
            'new_item' => __('Nouvel événement'),
            'view_item' => __('Voir événement'),
            'search_items' => __('Trouver  événement'),
            'not_found' =>  __('Rien n\'a été trouvé'),
            'not_found_in_trash' => __('Rien trouvé dans la corbeille'),
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

        register_post_type('events', $args);
    }



    function add_new_events_columns($events_columns)
    {

        $new_columns['cb'] = '<input type="checkbox" />';

        $new_columns['date_time'] = 'Date d\'événement';

        $new_columns['title'] = 'Nom d\'événement ';

        $new_columns['categories'] = 'Categories';

        $new_columns['tags'] = 'Tags';


        // var_dump($new_columns);die;

        return $new_columns;
    }

    function manage_events_columns($column_name, $id)
    {

        global $wpdb;
        switch ($column_name) {
            case 'date_time':
                $events_date_meta = get_post_meta($id, 'events_date', true);
                if ($events_date_meta == '') {
                    $events_date = '';
                    $events_time = '';
                } else {
                    $events_date = date('jS \o\f F Y', $events_date_meta);
                    $events_time = date('g.ia', $events_date_meta);
                }
                echo $events_date . '<br>' . $events_time;
                break;
            case 'categories':
                // $categ = get_the_category($id);
                //  var_dump($categ); die;
                // echo 'test';
                break;
            default:
                break;
        } // end switch
    }

    function custom_type_categories()
    {
        $props = array(
            'name' => _x('Événements Categories', 'mythemlg'),
            'singular_name' => _x('Événements Categorie', 'mythemlg'),
            'search_items' =>  __('Chercher Événements Categories'),
            'all_items' => __('Tots les categories'),
            'parent_item' => __('Parent Événements categorie'),
            'parent_item_colon' => __('Parent Événements categorie:'),
            'edit_item' => __('Modifié  categorie'),
            'update_item' => __('Modifié categorie'),
            'add_new_item' => __('Ajouter nouvelle categorie'),
            'new_item_name' => __('Ajouter nouvelle categorie'),
            'menu_name' => __(' Categories'),
        );

        // Now register the taxonomy
        register_taxonomy('events_categories', array('events'), array(
            'hierarchical' => true,
            'labels' => $props,
            'show_ui' => true,
            'show_in_rest' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'events_categories'),
        ));
    }

    // TAGS
    function events_tags_taxonomy()
    {
        // Labels part for the GUI

        $props = array(
            'name' => _x('Événements Tags', 'taxonomy general name'),
            'singular_name' => _x('Événements Tag', 'taxonomy singular name'),
            'search_items' =>  __('Chercher Événements Tags'),
            'popular_items' => __('Popular Événements Tags'),
            'all_items' => __('Tout les Tags'),
            'parent_item' => null,
            'parent_item_colon' => null,
            'edit_item' => __('Modifié Tag'),
            'update_item' => __('Modifié Tag'),
            'add_new_item' => __('Ajouter Tag'),
            'new_item_name' => __('Ajouter Tag'),
            'separate_items_with_commas' => __('Separate Événements tags with commas'),
            'add_or_remove_items' => __('Add or remove Événements tags'),
            'choose_from_most_used' => __('Choose from the most used Événements tags'),
            'menu_name' => __(' Tags'),
        );

        // Now register the non-hierarchical taxonomy like tag

        register_taxonomy('events_tags', array('events'), array(
            'hierarchical' => false,
            'labels' => $props,
            'show_ui' => true,
            'show_in_rest' => true,
            'show_admin_column' => true,
            'update_count_callback' => '_update_post_term_count',
            'query_var' => true,
            'rewrite' => array('slug' => 'events_tags'),
        ));
    }



    function save_meta_box_data($post_id)
    {

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;

        if (isset($_POST['events_venue_name'])) {

            if (!wp_verify_nonce($_POST['events_nonce'], plugin_basename(__FILE__)))
                return;

            update_post_meta($post_id, 'venue_location_address', $_POST['venue_location_address']);

            list($day, $month, $year, $hours, $minutes) = sscanf($_POST['events_date'], '%02d-%02d-%04d @ %02d:%02d');

            $date = new DateTime("$year-$month-$day $hours:$minutes");

            update_post_meta($post_id, 'events_date', strtotime($date->format('r')));

            update_post_meta($post_id, 'events_tickets', $_POST['events_tickets']);

            update_post_meta($post_id, 'events_venue_name', $_POST['events_venue_name']);

            update_post_meta($post_id, 'lat', $_POST['lat']);

            update_post_meta($post_id, 'lng', $_POST['lng']);

            update_post_meta($post_id, 'show_map', $_POST['show_map']);

            update_post_meta($post_id, 'events_user_id', $_POST['events_user_id']);

            $postarr = get_post($post_id, 'ARRAY_A');

            if ($postarr['post_date'] != $date->format('Y-m-d H:i:s')) {

                $postarr['post_date'] = $date->format(DATE_RFC3339);

                $post_id = wp_update_post($postarr);
            }
        }
    }

    function show_events_for_current_user_only($query)
    {

        if ($query->is_admin) {

            if ($query->get('post_type') == 'events') {

                $current_user = wp_get_current_user();

                $admin = false;

                foreach ($current_user->roles as $key => $val) {
                    if ($val == 'administrator') {
                        $admin = true;
                    }
                }

                if (!$admin) {

                    $query->set('meta_key', 'events_user_id');
                    $query->set('meta_value', $current_user->ID);
                }
            }
        }

        return $query;
    }

    function remove_post_counts($posts_count_disp)
    {

        $current_user = wp_get_current_user();

        $admin = false;

        foreach ($current_user->roles as $key => $val) {
            if ($val == 'administrator') {
                $admin = true;
            }
        }

        if (!$admin) {
            unset($posts_count_disp['all']);
            unset($posts_count_disp['publish']);
            unset($posts_count_disp['draft']);
            unset($posts_count_disp['trash']);
            unset($posts_count_disp['mine']);
        }

        return $posts_count_disp;
    }


    function add_meta_boxes()
    {

        add_meta_box(
            'events_details',
            'Détails de l\'évènement',
            array($this, 'events_details'),
            'events',
            'normal',
            'core'
        );
    }


    function events_details()
    {

        global $post;

        $current_user = wp_get_current_user();

        if (get_post_meta($post->ID, 'events_user_id', true) == '') {
            $userID = $current_user->ID;
        } else {
            $userID = get_post_meta($post->ID, 'events_user_id', true);
        }

        ?>

    <input type="hidden" id="events_user_id" name="events_user_id" value="<?= $userID  ?>" />

    <?php

    wp_nonce_field(plugin_basename(__FILE__), 'events_nonce');

    ?>

    <script>
        jQuery(document).ready(function() {
            jQuery(".datepicker").datetimepicker({
                timeFormat: 'h:mm',
                separator: ' @ ',
                dateFormat: 'dd-mm-yy'
            });
            <?php if (get_post_meta($post->ID, 'events_date', true) == '') { ?>
                jQuery(".datepicker").datetimepicker('setDate', (new Date()));
                jQuery('#ui-datepicker-div').hide();
            <?php } ?>
        });
    </script>

    <table class="form-table">
        <tbody>

            <tr valign="top">
                <th scope="row">
                    <label for="venue_name">Nom de la place</label>
                </th>
                <td>
                    <input type="text" id="events_venu_name" name="events_venue_name" value="<?php echo get_post_meta($post->ID, 'events_venue_name', true); ?>" size="25" />
                </td>
            </tr>

            <tr valign="top">

                <th scope="row">
                    <label for="venue_name"> Adresse</label>
                </th>

                <td>

                    <textarea name="venue_location_address" id="member_info_address" cols="50" rows="9" class="input"><?php echo get_post_meta($post->ID, 'venue_location_address', true); ?></textarea>

                    <br>

                    <a class="showhide" style="cursor:pointer;">Afficher / masquer l'entrée de carte</a>

                    <div id="showhide">

                        <input type="text" class="input" name="mi_location" id="member_info_location" value="<?php echo get_post_meta($post->ID, 'mi_location', true); ?>" />
                        <input type="button" class="button-primary button" value="Lookup" onClick="codeAddress('YES')" />
                        <br>
                        <span class="description" style="float: left;">
                            Entrez un emplacement dans n’importe quel format et cliquez sur le bouton "Rechercher".
                        </span>
                        <br>
                        <br>
                        <div id="map_canvas" style="float:left; width:500px; height:400px; margin-right: 10px;">
                        </div>
                        <div style="width: 35%;float:left;clear:left;" id="didyoumean">
                        </div>
                        <br style="clear:both;">
                        <br>
                        <br>

                        <input type="hidden" name="lng" id="lng" value="<?php echo get_post_meta($post->ID, 'lng', true); ?>" />
                        <input type="hidden" name="lat" id="lat" value="<?php echo get_post_meta($post->ID, 'lat', true); ?>" />
                        <span class="member_info_label">Afficher la carte?</span>
                        <select name="show_map" id="mi_show_map">
                            <option value="">Veuillez choisir</option>
                            <option value="true" <?php if (get_post_meta($post->ID, 'show_map', true) == 'true') {
                                                        echo 'selected';
                                                    } ?>>True</option>
                            <option value="false" <?php if (get_post_meta($post->ID, 'show_map', true) == 'false') {
                                                        echo 'selected';
                                                    } ?>>False</option>
                        </select>
                        <span class="description">
                            Afficher une carte du lieu de l'événement?
                        </span>

                    </div>

                    <br style="clear:both;">

                </td>

            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="date">Date et l'heure</label>
                </th>
                <td>
                    <?php if (get_post_meta($post->ID, 'events_date', true) != '') {

                        $date = date('d-m-Y @ H:i', get_post_meta($post->ID, 'events_date', true));
                    } else {

                        $date = '';
                    } ?>

                    <input class="datepicker" type="text" id="events_date" name="events_date" value="<?php echo $date; ?>" size="25" />
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="tickets">Tickets URL</label>
                </th>
                <td>
                    <input type="text" id="events_tickets" name="events_tickets" value="<?php echo get_post_meta($post->ID, 'events_tickets', true); ?>" size="50" />
                </td>
            </tr>

        </tbody>
    </table>

<?php

}
}
