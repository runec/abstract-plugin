<h2><?php _e('New Review', 'wpabstracts');?></h2>
<div class="metabox-holder has-right-sidebar">
    <form method="post" enctype="multipart/form-data" id="wpabs_review_form">
        <div class="inner-sidebar">
            <div class='misc-pub-section'>
                <input type="hidden" name="abs_title" value="<?php echo $abstract['title']; ?>" />
                <input type="button" onclick="wpabstracts_validateReview();" class="button button-primary button-large" value="<?php _e('Save Review','wpabstracts');?>" />
            </div>
            <span class="form-invalid" style="display: none;" id="abs_submit_review_error"><?php _e('All fields required', 'wpabstracts'); ?></span>
            <div class="postbox">
                <h3><?php _e('Relevance', 'wpabstracts'); ?> </h3>
                <div class="inside">
                    <input type='radio' name='abs_relevance' value='Excellent' /> <?php _e('Excellent', 'wpabstracts'); ?><br>
                    <input type='radio' name='abs_relevance' value='Good' /> <?php _e('Good', 'wpabstracts'); ?><br>
                    <input type='radio' name='abs_relevance' value='Average' /> <?php _e('Average', 'wpabstracts'); ?><br>
                    <input type='radio' name='abs_relevance' value='Poor' /> <?php _e('Poor', 'wpabstracts'); ?><br>
                </div>
            </div>
            <div class="postbox">
                <h3><?php _e('Quality', 'wpabstracts'); ?> </h3>
                <div class="inside">
                    <input type='radio' name='abs_quality' value='Excellent' /> <?php _e('Excellent', 'wpabstracts'); ?><br>
                    <input type='radio' name='abs_quality' value='Good' /> <?php _e('Good', 'wpabstracts'); ?><br>
                    <input type='radio' name='abs_quality' value='Average' /> <?php _e('Average', 'wpabstracts'); ?><br>
                    <input type='radio' name='abs_quality' value='Poor' /> <?php _e('Poor', 'wpabstracts'); ?><br>
                </div>
            </div>
            <div class="postbox">
                <h3><?php _e('Suggest Status', 'wpabstracts'); ?> </h3>
                <div class="inside">
                    <input type='radio' name='abs_status' value='Pending' <?php checked($abstract['status'], 'Pending'); ?> /> <?php _e('Pending', 'wpabstracts'); ?><br>
                    <input type='radio' name='abs_status' value='Approved' <?php checked($abstract['status'], 'Approved'); ?> /> <?php _e('Approved', 'wpabstracts'); ?><br>
                    <input type='radio' name='abs_status' value='Rejected' <?php checked($abstract['status'], 'Rejected'); ?> /> <?php _e('Rejected', 'wpabstracts'); ?><br>
                </div>
            </div>
            <?php
            if (current_user_can(WPABSTRACTS_ACCESS_LEVEL) || get_option('wpabstracts_blind_review') == "No") { ?>
            <div class="postbox">
                <h3><?php _e('Presenter Information','wpabstracts');?></h3>
                <div class="inside">
                    <table width="100%">
                        <tr><td><?php _e('Name','wpabstracts');?>: <?php echo esc_attr($abstract['presenter']);?></td></tr>
                        <tr><td><?php _e('Email', 'wpabstracts'); ?>: <?php echo esc_attr($abstract['presenter_email']); ?></td></tr>
                        <tr><td><?php _e('Phone', 'wpabstracts'); ?>: <?php echo esc_attr($abstract['presenter_phone']); ?></td></tr>
                        <tr><td><?php _e('LinkedIn', 'wpabstracts'); ?>: <?php echo esc_attr($abstract['presenter_linkedin']); ?></td></tr>
                    </table>
                </div>
            </div>
           <?php } ?>
            <div class="postbox">
                <h3><?php _e('Photo', 'wpabstracts'); ?></h3>
                <div class="inside">
                  <img src='<?php echo get_site_url().'/wp-content/plugins/wpabstracts_pro/profile-images/'.$abstract['profile_image']; ?>' />

                  <?php //Photo ?>
                </div>
            </div>
            </div>

        <div id="post-body">
            <div id="post-body-content">
                <div class="postarea">
                    <div class="postbox">
                        <h3><?php echo esc_attr( htmlspecialchars($abstract['title'])); ?></h3>
                        <div class="inside">
                            <h4><?php _e('Target Group', 'wpabstracts'); ?></h4>
                            <?php echo wpautop($abstract['target_group']); ?>
                            <h4><?php _e('Resume', 'wpabstracts'); ?></h4>
                            <?php echo wpautop($abstract['resume']); ?>
                            <h4><?php _e('Abstract', 'wpabstracts'); ?></h4>
                            <?php echo wpautop($abstract['text']); ?>
                        </div>
                    </div>
                    <div class="postbox">
                        <h3><?php _e('Add Comments', 'wpabstracts'); ?> <span class="form-invalid" style="display: none;" id="abs_review_comments_error"> <?php _e('Please add some comments about this review', 'wpabstracts');?></span></h3>
                        <div class="inside">
                        <?php
                            $settings = array( 'media_buttons' => false, 'wpautop'=>true, 'dfw' => true, 'editor_height' => 90);
                            wp_editor("", 'abs_comments', $settings);
                        ?>
                        </div>
                    </div>
                    <div class="postbox">
                        <h3><?php _e('Additional Information', 'wpabstracts'); ?></h3>
                            <div class="inside">
                                <strong><?php _e('Submitted', 'wpabstracts'); ?>:  </strong><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($abstract['submit_date'])); ?><br>
                                <strong><?php _e('Event', 'wpabstracts'); ?>: </strong><?php echo $event->name; ?><br>
                                <strong><?php _e('Topic', 'wpabstracts'); ?>: </strong><?php echo $abstract['topic']; ?>
                            </div>
                    </div>
                </div>
            </div>
        </div>
   </form>
</div>
