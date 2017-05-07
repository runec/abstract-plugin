<div class="metabox-holder has-right-sidebar">
    <form method="post" enctype="multipart/form-data" id="emailtemplate">
        <div class="inner-sidebar">
            <div class="postbox">
                <h3><?php _e('Available Shortcodes','wpabstracts'); ?></h3>
                <div class="inside">
                    <h4>- <?php _e('Receiver data', 'wpabstracts');?></h4>
                    <p><?php _e("Receiver name",'wpabstracts'); ?>: {RECEIVER_NAME}</p>
                    <h4>- <?php _e('User data (only for single abstract)', 'wpabstracts');?></h4>
                    <p><?php _e("User name",'wpabstracts'); ?>: {AUTHOR_NAME}</p>
                    <p><?php _e("User company",'wpabstracts'); ?>: {AUTHOR_COMPANY}</p>
                    <p><?php _e("User email",'wpabstracts'); ?>: {AUTHOR_EMAIL}</p>
                    <p><?php _e("User phone",'wpabstracts'); ?>: {AUTHOR_PHONE}</p>
                    <h4>- <?php _e('Abstract (only for single abstract)', 'wpabstracts');?></h4>
                    <p><?php _e("Abstract title",'wpabstracts'); ?>: {ABSTRACT_TITLE}</p>
                    <p><?php _e("Abstract subject",'wpabstracts'); ?>: {ABSTRACT_SUBJECT}</p>
                    <p><?php _e("Abstract target group",'wpabstracts'); ?>: {ABSTRACT_TARGET_GROUP}</p>
                    <p><?php _e("Abstract resume",'wpabstracts'); ?>: {ABSTRACT_RESUME}</p>
                    <p><?php _e("Abstract content",'wpabstracts'); ?>: {ABSTRACT_CONTENT}</p>
                    <p><?php _e("Manager comments",'wpabstracts'); ?>: {MANAGER_COMMENTS}</p>
                    <h4>- <?php _e('Abstracts (only for multiple abstracts)', 'wpabstracts');?></h4>
                    <p><?php _e("Abstracts list",'wpabstracts'); ?>: {ABSTRACTS_LIST}</p>
                    <p><?php _e("Number of abstracts",'wpabstracts'); ?>: {ABSTRACTS_NUMBER}</p>
                    <h4>- <?php _e('Event data', 'wpabstracts');?></h4>
                    <p><?php _e("Event name",'wpabstracts'); ?>: {EVENT_NAME}</p>
                    <p><?php _e("Event start date",'wpabstracts'); ?>: {EVENT_START}</p>
                    <p><?php _e("Event end date",'wpabstracts'); ?>: {EVENT_END}</p>
                    <h4>- <?php _e('Other shortcodes', 'wpabstracts');?></h4>
                    <p><?php _e("Site name",'wpabstracts'); ?>: {SITE_NAME}</p>
                    <p><?php _e("Site URL",'wpabstracts'); ?>: {SITE_URL}</p>
                    <p><?php _e("One week later",'wpabstracts'); ?>: {ONE_WEEK_LATER}</p>
                    <p><?php _e("Two weeks later",'wpabstracts'); ?>: {TWO_WEEKS_LATER}</p>
                </div>
            </div>
        </div>
        <div id="post-body">
            <div id="post-body-content">
                <div class="postbox">
                    <h3><?php _e('Edit Email Template', 'wpabstracts');?></h3>
                    <div class="inside">
                        <table class="widefat" style="border: none;">
                        <tr>
                            <td><?php _e('Template Name','wpabstracts');?></td>
                            <td><input type="text" name="template_name" id="template_name" value="<?php echo esc_html($template->name);?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <td><?php _e('From Name','wpabstracts');?></td>
                            <td><input type="text" name="from_name" id="from_name" value="<?php echo esc_html($template->from_name);?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <td><?php _e('From Email','wpabstracts');?></td>
                            <td><input type="text" name="from_email" id="from_email" value="<?php echo esc_html($template->from_email);?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <td><?php _e('Email Subject','wpabstracts');?></td>
                            <td><input type="text" name="email_subject" id="email_subject" value="<?php echo esc_html($template->subject);?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <td><?php _e('Email Body','wpabstracts');?></td>
                            <td>
                                <?php
                                $text_settings = array( 'media_buttons' => false, 'wpautop'=>true, 'dfw' => true, 'editor_height' => 240);
                                wp_editor(stripcslashes($template->message), 'email_body', $text_settings);
                            ?>
                            </td>
                        </tr>
                    </table>
                    </div>
                </div>
                 <div class="misc-pub-section">
                    <input type="button" onclick="wpabstracts_validateTemplate();" class="button button-primary button-large btn btn-primary" value="<?php _e('Update Template','wpabstracts');?>" />
                </div>
            </div>
        </div>
    </form>
</div>
