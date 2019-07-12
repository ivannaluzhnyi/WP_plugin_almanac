jQuery(document).ready(function () {

    initialize();

    jQuery('#showhide').hide();

    jQuery('.showhide').click(function () {
        jQuery('#showhide').toggle();
        google.maps.event.trigger(map, 'resize');
    });

});

var geocoder;
var map;
var marker;
var markersArray = [];

function initialize() {

    geocoder = new google.maps.Geocoder();
    if (jQuery('#map_canvas').length > 0 || jQuery('#map_canvas_display').length > 0) {
        if (jQuery('#lat').val() != '' && jQuery('#lng').val() != '') {
            var latlng = new google.maps.LatLng(jQuery('#lat').val(), jQuery('#lng').val());
            codeLatLng(jQuery('#lat').val(), jQuery('#lng').val())
        } else {
            var latlng = new google.maps.LatLng(52.4346383, -1.8942336000000068);
        }
        var myOptions = {
            zoom: 15,
            center: latlng,
            streetViewControl: false,
            mapTypeControl: false,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }
        map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

        google.maps.event.addListener(map, 'click', function (event) {

            if (markersArray) {
                for (i in markersArray) {
                    markersArray[i].setMap(null);
                }
            }

            var marker = new google.maps.Marker({
                map: map,
                position: event.latLng
            });
            markersArray.push(marker);
            jQuery('#lat').val(event.latLng.lat());
            jQuery('#lng').val(event.latLng.lng());
            codeLatLng(event.latLng.lat(), event.latLng.lng());
        });

    }

}

function addMarkerFromDidYouMean(lat, lng) {

    if (jQuery('#map_canvas').length > 0 || jQuery('#map_canvas_display').length > 0) {

        if (markersArray) {
            for (i in markersArray) {
                markersArray[i].setMap(null);
            }
        }
        var location = new google.maps.LatLng(lat, lng);
        var marker = new google.maps.Marker({
            map: map,
            position: location
        });

        map.setCenter(location);

        markersArray.push(marker);

    }

    jQuery('#lat').val(lat);
    jQuery('#lng').val(lng);

    codeLatLng(lat, lng);

}

function codeAddress(update) {

    var address = jQuery('#member_info_location').val();
    geocoder.geocode({ 'address': address }, function (results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            jQuery('#didyoumean').html('');
            console.log(results);
            if (results.length > 1) {
                jQuery('#didyoumean').html('<strong>Did you mean:</strong><br>');
                for (i = 0; i < results.length; i++) {
                    jQuery('#didyoumean').html(jQuery('#didyoumean').html() + '<a style="cursor: pointer;" onClick="addMarkerFromDidYouMean(' + results[i].geometry.location.lat() + ',' + results[i].geometry.location.lng() + ')">' + results[i].formatted_address + '<a/><br>');
                }
                jQuery('#didyoumean').html(jQuery('#didyoumean').html() + '<span class="description">If your location has not been suggested try being more specific with your search. For example add USA or UK or a city name.</span><br>');
            }
            if (markersArray) {
                for (i in markersArray) {
                    markersArray[i].setMap(null);
                }
            }
            if (jQuery('#map_canvas').length > 0 || jQuery('#map_canvas_display').length > 0) {
                map.setCenter(results[0].geometry.location);
            }
            var marker = new google.maps.Marker({
                map: map,
                position: results[0].geometry.location
            });
            markersArray.push(marker);
            if (update == 'YES') {
                jQuery('#member_info_location').val(results[0].formatted_address);
                jQuery('#lat').val(results[0].geometry.location.lat());
                jQuery('#lng').val(results[0].geometry.location.lng());
            }

        } else {
            if (status == 'ZERO_RESULTS') {
                alert("We could not find your location. Please be a little more specific with your search. \n\nAlternatively you can simply type your address in the text box.");
            }
        }
    });
}

function codeLatLng(lat, lng) {
    var latlng = new google.maps.LatLng(lat, lng);
    geocoder.geocode({ 'latLng': latlng }, function (results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            jQuery('#didyoumean').html('');
            if (results.length > 1) {
                jQuery('#didyoumean').html('<strong>Did you mean:</strong><br><br>');
                for (i = 0; i < results.length; i++) {
                    jQuery('#didyoumean').html(jQuery('#didyoumean').html() + '<a style="cursor: pointer;" onClick="addMarkerFromDidYouMean(' + results[i].geometry.location.lat() + ',' + results[i].geometry.location.lng() + ')">' + results[i].formatted_address + '<a/><br>');
                }
                jQuery('#didyoumean').html(jQuery('#didyoumean').html() + '<br><br><span class="description">If your location has not been suggested try being more specific with your search. For example add USA or UK or a city name.</span><br>');
            }
            if (markersArray) {
                for (i in markersArray) {
                    markersArray[i].setMap(null);
                }
            }
            var marker = new google.maps.Marker({
                map: map,
                position: results[0].geometry.location
            });
            markersArray.push(marker);
            jQuery('#member_info_location').val(results[0].formatted_address);
            jQuery('#lat').val(results[0].geometry.location.lat());
            jQuery('#lng').val(results[0].geometry.location.lng());

        } else {
            alert("Geocoder failed due to: " + status);
        }
    });
}

 /*


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

            add_action('init', array($this, 'custom_type_categories'), 0);

        add_action('init', array($this, 'events_tags_taxonomy'), 0);


 */