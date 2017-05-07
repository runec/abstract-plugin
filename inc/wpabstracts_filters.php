<?php

add_action( 'restrict_manage_posts', 'wpabstracts_events_dropdown_filter' );
/**
 * First create the dropdown
 * make sure to change POST_TYPE to the name of your custom post type
 *
 * @author Ohad Raz
 *
 * @return void
 */
function wpabstracts_events_dropdown_filter(){
    $type = 'post';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }
    //only add filter to post type you want
    //if ('POST_TYPE' == $type){
        //change this to the list of values you want to show
        //in 'label' => 'value' format
        $events = wpabstracts_getEvents('all','',NULL);
        ?>
        <select name="ABSTRACTS_EVENT_FILTER">
        <option value=""><?php _e('All events', 'wpabstracts'); ?></option>
        <?php
            $current_id = isset($_GET['ABSTRACTS_EVENT_FILTER'])? $_GET['ABSTRACTS_EVENT_FILTER']:'';
            foreach ($events as $key => $row) {
                printf
                    (
                        '<option value="%s"%s>%s</option>',
                        $row->event_id,
                        $row->event_id == $current_id? ' selected="selected"':'',
                        $row->name
                    );
                  }
        ?>
        </select>
        <?php
    //}
}


add_filter( 'parse_query', 'wpse45436_posts_filter' );
/**
 * if submitted filter by post meta
 *
 * make sure to change META_KEY to the actual meta key
 * and POST_TYPE to the name of your custom post type
 * @author Ohad Raz
 * @param  (wp_query object) $query
 *
 * @return Void
 */
function wpse45436_posts_filter( $query ){
    global $pagenow;
    $type = 'post';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }
    if ( 'POST_TYPE' == $type && is_admin() && $pagenow=='edit.php' && isset($_GET['ADMIN_FILTER_FIELD_VALUE']) && $_GET['ADMIN_FILTER_FIELD_VALUE'] != '') {
        $query->query_vars['meta_key'] = 'META_KEY';
        $query->query_vars['meta_value'] = $_GET['ADMIN_FILTER_FIELD_VALUE'];
    }
}
?>
