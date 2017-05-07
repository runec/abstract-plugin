<?php
defined('ABSPATH') or die("ERROR: You do not have permission to access this page");

wpabstracts_summary();

function wpabstracts_summary() {
    global $wpdb;
    $abstracts = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM ".$wpdb->prefix."wpabstracts_abstracts ORDER BY abstract_id DESC LIMIT 0,5");
    $abs_count = $wpdb->get_var("SELECT FOUND_ROWS()");
    $reviews = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM ".$wpdb->prefix."wpabstracts_reviews ORDER BY review_id DESC LIMIT 0,5");
    $approved_abs = $wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."wpabstracts_abstracts WHERE status = 'Approved'");
    $rejected_abs = $wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."wpabstracts_abstracts WHERE status = 'Rejected'");
    $pending_abs = $wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."wpabstracts_abstracts WHERE status = 'Pending'");
    $events = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."wpabstracts_events ORDER BY event_id DESC LIMIT 0,5");
    $events_count = $wpdb->get_var("SELECT FOUND_ROWS()");
    $attachments = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM ".$wpdb->prefix."wpabstracts_attachments");
    $attachments_count = $wpdb->get_var("SELECT FOUND_ROWS()");?>
<!--- Side bar-->
<div class="metabox-holder has-right-sidebar">
    <form method="post" enctype="multipart/form-data" id="abs_form">
        <div class="inner-sidebar">
            <div class="postbox">
                <h3><?php _e('Stats', 'wpabstracts'); ?></h3>
                    <div class="inside">
                    <table width="100%">
                        <tr>
                            <td><?php _e('Total Abstracts', 'wpabstracts'); ?>: <?php echo esc_attr($abs_count); ?></td>
                            </tr>
                        <tr>
                            <td><?php _e('Approved', 'wpabstracts'); ?>: <?php echo $approved_abs; ?></td>
                            </tr>
                        <tr>
                            <td><?php _e('Pending', 'wpabstracts'); ?>: <?php echo $pending_abs; ?></td>
                            </tr>
                        <tr>
                            <td><?php _e('Rejected', 'wpabstracts'); ?>: <?php echo $rejected_abs; ?></td>
                            </tr>
                        <tr>
                            <td><?php _e('Events', 'wpabstracts'); ?>: <?php echo esc_attr($events_count); ?></td>
                            </tr>
                        <tr>
                            <td><?php _e('Attachments', 'wpabstracts'); ?>: <?php echo esc_attr($attachments_count); ?></td>
                            </tr>
                    </table>
                </div>
            </div> <!-- End Quick Reports -->
            <div class="postbox"><!-- Attachments -->
                <h3><span><?php _e('Help us Improve', 'wpabstracts'); ?></span></h3>
                    <div class="inside">
                                <p><a href="http://www.wpabstracts.com/wishlist" target="_blank"><?php _e('Suggest', 'wpabstracts'); ?></a> <?php _e('features', 'wpabstracts'); ?>.</p>
                                <p><a href="http://wordpress.org/plugins/wp-abstracts-manuscripts-manager/" target="_blank"><?php _e('Rate', 'wpabstracts'); ?></a> <?php _e('the plugin 5 stars on WordPress.org.', 'wpabstracts');?></p>
                                    <p><a href="http://www.facebook.com/wpabstracts" target="_blank"><?php _e('Like us', 'wpabstracts'); ?></a> on Facebook. </p>
                        </div>
            </div> <!-- End Attachments -->
        </div> <!-- .inner-sidebar -->
        <div id="post-body">
            <div id="post-body-content">
                <div class="postarea">
                    <!--Recent abstracts -->
                    <table class="widefat page fixed" cellspacing="0">
                        <thead>
                            <tr>
                                        <th scope="col" id="title" class="manage-column column-title" style="width: 25%;"><h3><?php _e('Recent Abstracts', 'wpabstracts'); ?></h3></th>
                        <th scope="col" id="title" class="manage-column column-title" style="width: 15%;"><?php _e('Author', 'wpabstracts'); ?></th>
                        <th scope="col" id="title" class="manage-column column-title" style="width: 25%;"><?php _e('Author Email', 'wpabstracts'); ?></th>
                        <th scope="col" id="date" class="manage-column column-date" style="width: 15%;"><?php _e('Submit by', 'wpabstracts'); ?></th>
                        <th scope="col" id="date" class="manage-column column-date" style="width: 20%;"><?php _e('Date', 'wpabstracts'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                    <?php
                    foreach($abstracts as $abstract) {
                    ?>
                            <tr class="alternate">
                                                <td><a href="?page=wpabstracts&tab=abstracts&task=edit&id=<?php echo $abstract->abstract_id ?>"><?php echo $abstract->title ?></a></td>
                                        <td><?php echo$abstract->author?></a></td>
                                <td><a href="mailto:<?php echo$abstract->author_email?>"><?php echo$abstract->author_email?></a></td>
                                <td><?php $user_info = get_userdata($abstract->submit_by);?><a href="<?php echo admin_url( 'user-edit.php?user_id=');?><?php echo$user_info->ID?>"><?php echo$user_info->display_name?></td>
                                <td><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($abstract->submit_date)); ?></td>

                            </tr>
                        <?php }
                        ?>
                        </tbody>
                    </table><!--End recent abstracts -->
                    <br>
                    <!--Recent Reviews-->
                    <table class="widefat page fixed" cellspacing="0">
                        <thead>
                            <tr>
                                <th scope="col" id="title" class="manage-column column-title" style="width: 25%;"><h3><?php _e('Recent Reviews', 'wpabstracts'); ?></h3></th>
                        <th scope="col" id="title" class="manage-column column-title" style=""><?php _e('Comments', 'wpabstracts'); ?></th>
                        <th scope="col" id="date" class="manage-column column-date" style=""><?php _e('Reviewer', 'wpabstracts'); ?></th>
                        <th scope="col" id="date" class="manage-column column-date" style=""><?php _e('Suggested Status', 'wpabstracts'); ?></th>
                                <th scope="col" id="date" class="manage-column column-date" style=""><?php _e('Date', 'wpabstracts'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                    <?php foreach($reviews as $review) {
                        $currentAbstract = $wpdb->get_row("SELECT title FROM ".$wpdb->prefix."wpabstracts_abstracts WHERE abstract_id = $review->abstract_id");
                        ?>
                            <tr class="alternate">
                                <td><a href="?page=wpabstracts&tab=reviews&task=edit&id=<?php echo$review->review_id?>"><?php echo $currentAbstract->title; ?></a></td>
                                <td><?php echo $review->comments; ?></td>
                            <?php
                            $user_info = get_userdata($review->user_id);
                            if($user_info){
                                $reviewer = $user_info->display_name;
                            }
                            else{
                                $reviewer = __("Not Assigned",'wpabstracts');
                            }
                        ?>
                                <td><?php echo $reviewer; ?></td>
                                 <td><?php echo $review->status; ?></td>
                                <td><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($review->review_date)); ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <br>
                    <!--Recent Events-->
                    <table class="widefat page fixed" cellspacing="0">
                        <thead>
                            <tr>
                                <th scope="col" id="title" class="manage-column column-title" style=""><h3><?php _e('Recent Events', 'wpabstracts'); ?></h3></th>
                        <th scope="col" id="title" class="manage-column column-title" style=""><?php _e('Hosted by', 'wpabstracts'); ?></th>
                        <th scope="col" id="date" class="manage-column column-date" style=""><?php _e('From', 'wpabstracts'); ?></th>
                        <th scope="col" id="date" class="manage-column column-date" style=""><?php _e('To', 'wpabstracts'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($events as $event) { ?>
                            <tr class="alternate">
                                <td><a href="?page=wpabstracts&tab=events"><?php echo $event->name; ?></a></td>
                                <td><?php echo $event->host; ?></td>
                                <td><?php echo $event->start_date; ?></td>
                                <td><?php echo $event->end_date; ?></td>
                            </tr>
                      <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>

 <?php
 }