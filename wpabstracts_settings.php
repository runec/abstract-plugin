<?php
defined('ABSPATH') or die("ERROR: You do not have permission to access this page");

function wpabstracts_save_option($option) {
    if ($_POST['options'][$option]) {
        if ($option == 'wpabstracts_permitted_attachments') {
            $_POST['options'][$option] = str_replace(" ", "", $_POST['options'][$option]);
        }
        if (get_option($option)) {
            update_option($option, $_POST['options'][$option]);
        } else {
            add_option($option, $_POST['options'][$option]);
        }
    }
}

if ($_POST) {
    foreach ($_POST['options'] as $option => $value) {
        wpabstracts_save_option($option);
    }
    ?>
        <div id="message" class="updated fade"><p><strong><?php _e('Settings saved', 'wpabstracts'); ?></strong></p></div>
    <?php
}
?>

<div class="wrap">
    <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
    <div id="icon-options-general" class="icon32"><br /></div>
    <h2><?php _e('Settings', 'wpabstracts'); ?> <input type="submit" name="Submit" class="button-primary" value="<?php _e('Save options', 'wpabstracts'); ?>" /></h2>

    <div id="dashboard-widgets" class="metabox-holder">

        <div class="postbox-container">
            <div class="postbox" style="background: none; border: none;">
                <div class="inside">
                    <table class="widefat" style="background: none; border: none;">

            <tr valign="top">
                <th scope="row"><label for="options[wpabstracts_email_admin]"><?php _e('Email Administrator on submission', 'wpabstracts'); ?></label>
                <span class="settings_tip" data-tip="<?php _e('Enable this to send an email to the site admin on submission of a new abstract.', 'wpabstracts'); ?>">
                        <img src="<?php echo plugins_url('images/settings_help.png', __FILE__); ?>" height="16" width="16" alt="Help">
                    </span></th>
                <td><select name="options[wpabstracts_email_admin]" id="email_admin">
                        <option value="Yes" <?php selected(get_option('wpabstracts_email_admin'), 'Yes'); ?>><?php _e('Yes', 'wpabstracts'); ?></option>
                        <option value="No" <?php selected(get_option('wpabstracts_email_admin'), 'No'); ?>><?php _e('No', 'wpabstracts'); ?></option>
                    </select>

                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="options[wpabstracts_email_reviewer]"><?php _e('Email Reviewers on assignment', 'wpabstracts'); ?></label>
                <span class="settings_tip" data-tip="<?php _e('Enable this to send an email to reviewers when they are assigned to an abstract.', 'wpabstracts'); ?>">
                        <img src="<?php echo plugins_url('images/settings_help.png', __FILE__); ?>" height="16" width="16" alt="Help">
                    </span></th>
                <td><select name="options[wpabstracts_email_reviewer]" id="email_admin">
                        <option value="Yes" <?php selected(get_option('wpabstracts_email_reviewer'), 'Yes'); ?>><?php _e('Yes', 'wpabstracts'); ?></option>
                        <option value="No" <?php selected(get_option('wpabstracts_email_reviewer'), 'No'); ?>><?php _e('No', 'wpabstracts'); ?></option>
                    </select>

                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="options[wpabstracts_email_author]"><?php _e('Email Author on submission', 'wpabstracts'); ?></label>
                 <span class="settings_tip" data-tip="<?php _e('Enable this to send an email to authors when they submit an abstract (email is sent to Author\'s email).', 'wpabstracts'); ?>">
                        <img src="<?php echo plugins_url('images/settings_help.png', __FILE__); ?>" height="16" width="16" alt="Help">
                    </span></th>
                <td><select name="options[wpabstracts_email_author]" id="email_author">
                        <option value="Yes" <?php selected(get_option('wpabstracts_email_author'), 'Yes'); ?>><?php _e('Yes', 'wpabstracts'); ?></option>
                        <option value="No" <?php selected(get_option('wpabstracts_email_author'), 'No'); ?>><?php _e('No', 'wpabstracts'); ?></option>
                    </select>

                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="options[wpabstracts_status_notification]"><?php _e('Email Author on status change', 'wpabstracts'); ?></label>
                 <span class="settings_tip" data-tip="<?php _e('Enable this to send an email to authors when their abstracts has been approved or rejected (email is sent to submitter\'s email only).', 'wpabstracts'); ?>">
                        <img src="<?php echo plugins_url('images/settings_help.png', __FILE__); ?>" height="16" width="16" alt="Help">
                    </span></th>
                <td><select name="options[wpabstracts_status_notification]" id="email_author">
                        <option value="Yes" <?php selected(get_option('wpabstracts_status_notification'), 'Yes'); ?>><?php _e('Yes', 'wpabstracts'); ?></option>
                        <option value="No" <?php selected(get_option('wpabstracts_status_notification'), 'No'); ?>><?php _e('No', 'wpabstracts'); ?></option>
                    </select>

                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="options[wpabstracts_blind_review]"><?php _e('Enable Blind Reviews', 'wpabstracts'); ?></label>
                <span class="settings_tip" data-tip="<?php _e('Enable this to hide author information from reviewers (does not apply to admin).', 'wpabstracts'); ?>">
                        <img src="<?php echo plugins_url('images/settings_help.png', __FILE__); ?>" height="16" width="16" alt="Help">
                    </span></th>
                <td><select name="options[wpabstracts_blind_review]" id="blind_review">
                        <option value="Yes" <?php selected(get_option('wpabstracts_blind_review'), 'Yes'); ?>><?php _e('Yes', 'wpabstracts'); ?></option>
                        <option value="No"  <?php selected(get_option('wpabstracts_blind_review'), 'No'); ?>><?php _e('No', 'wpabstracts'); ?></option>
                    </select>

                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="options[wpabstracts_reviewer_submit]"><?php _e('Allow Reviewers to submit abstracts', 'wpabstracts'); ?></label>
                <span class="settings_tip" data-tip="<?php _e('Enable this to allow reviewers to submit new abstracts.', 'wpabstracts'); ?>">
                        <img src="<?php echo plugins_url('images/settings_help.png', __FILE__); ?>" height="16" width="16" alt="Help">
                    </span></th>
                <td><select name="options[wpabstracts_reviewer_submit]" id="email_admin">
                        <option value="Yes" <?php selected(get_option('wpabstracts_reviewer_submit'), 'Yes'); ?>><?php _e('Yes', 'wpabstracts'); ?></option>
                        <option value="No" <?php selected(get_option('wpabstracts_reviewer_submit'), 'No'); ?>><?php _e('No', 'wpabstracts'); ?></option>
                    </select>

                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="options[wpabstracts_reviewer_edit]"><?php _e('Allow Reviewers to edit abstracts', 'wpabstracts'); ?></label>
                 <span class="settings_tip" data-tip="<?php _e('Enable this to allow reviewers to edit abstracts.', 'wpabstracts'); ?>">
                        <img src="<?php echo plugins_url('images/settings_help.png', __FILE__); ?>" height="16" width="16" alt="Help">
                    </span></th>
                <td><select name="options[wpabstracts_reviewer_edit]" id="email_admin">
                        <option value="Yes" <?php selected(get_option('wpabstracts_reviewer_edit'), 'Yes'); ?>><?php _e('Yes', 'wpabstracts'); ?></option>
                        <option value="No" <?php selected(get_option('wpabstracts_reviewer_edit'), 'No'); ?>><?php _e('No', 'wpabstracts'); ?></option>
                    </select>

                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="options[wpabstracts_change_ownership]"><?php _e('Allow Change Ownership', 'wpabstracts'); ?></label>
                <span class="settings_tip" data-tip="<?php _e('Enable this to allow reviewers to change ownership (the author) of a submission (useful if a reviewer submits an abstract on behalf of an author but the option above to enable reviewers to edit abstracts must be enabled for this to work).', 'wpabstracts'); ?>">
                        <img src="<?php echo plugins_url('images/settings_help.png', __FILE__); ?>" height="16" width="16" alt="Help">
                    </span></th>
                <td><select name="options[wpabstracts_change_ownership]" id="change_ownership">
                        <option value="Yes" <?php selected(get_option('wpabstracts_change_ownership'), 'Yes'); ?>><?php _e('Yes', 'wpabstracts'); ?></option>
                        <option value="No" <?php selected(get_option('wpabstracts_change_ownership'), 'No'); ?>><?php _e('No', 'wpabstracts'); ?></option>
                    </select>

                </td>
            </tr>
            <tr valign="top">
            <th scope="row"><label for="options[wpabstracts_frontend_dashboard]"><?php _e('Allow Dashboard Access', 'wpabstracts'); ?></label>
            <span class="settings_tip" data-tip="<?php _e('Disables users from accessing Wordpress Admin dashboard. Enable this if you want to allow frontend access only.', 'wpabstracts'); ?>">
            <img src="<?php echo plugins_url('images/settings_help.png', __FILE__); ?>" height="16" width="16" alt="Help">
            </span></th>
            <td><select name="options[wpabstracts_frontend_dashboard]" id="wpabstracts_frontend_dashboard">
            <option value="Yes" <?php selected(get_option('wpabstracts_frontend_dashboard'), 'Yes'); ?>><?php _e('Yes', 'wpabstracts'); ?></option>
            <option value="No" <?php selected(get_option('wpabstracts_frontend_dashboard'), 'No'); ?>><?php _e('No', 'wpabstracts'); ?></option>
            </select>

            </td>
            </tr>
            <tr valign="top">
            <th scope="row"><label for="options[wpabstracts_frontend_dashboard]"><?php _e('Show Admin Bar', 'wpabstracts'); ?></label>
            <span class="settings_tip" data-tip="<?php _e('Disables users from seeing the Wordpress Admin Bar after sign in.', 'wpabstracts'); ?>">
            <img src="<?php echo plugins_url('images/settings_help.png', __FILE__); ?>" height="16" width="16" alt="Help">
            </span></th>
            <td><select name="options[wpabstracts_show_adminbar]" id="wpabstracts_show_adminbar">
            <option value="Yes" <?php selected(get_option('wpabstracts_show_adminbar'), 'Yes'); ?>><?php _e('Yes', 'wpabstracts'); ?></option>
            <option value="No"  <?php selected(get_option('wpabstracts_show_adminbar'), 'No'); ?>><?php _e('No', 'wpabstracts'); ?></option>
            </select>

            </td>
            </tr>

            <tr valign="top">
            <th scope="row">
            <label for="options[wpabstracts_show_attachments]" ><?php _e('Show Attachment Uploads', 'wpabstracts'); ?></label>
            <span class="settings_tip" data-tip="<?php _e('Use this setting to hide the attachment box completely from the submission page.', 'wpabstracts'); ?>">
            <img src="<?php echo plugins_url('images/settings_help.png', __FILE__); ?>" height="16" width="16" alt="Help">
            </span>
            </th>
            <td><select name="options[wpabstracts_show_attachments]" id="wpabstracts_show_attachments">
            <option value="Yes" <?php selected(get_option('wpabstracts_show_attachments'), 'Yes'); ?>><?php _e('Yes', 'wpabstracts'); ?></option>
            <option value="No"  <?php selected(get_option('wpabstracts_show_attachments'), 'No'); ?>><?php _e('No', 'wpabstracts'); ?></option>
            </select>
            </td>
            </tr>
        </table>
                </div>
            </div>
	</div>

        <div class="postbox-container">
            <div class="postbox" style="background: none; border: none;">
                <div class="inside">
                    <table class="widefat" style="background: none; border: none;">

            <tr valign="top">
                <th scope="row"><label for="options[wpabstracts_upload_limit]"><?php _e('Maximum Attachments', 'wpabstracts'); ?></label>
                <span class="settings_tip" data-tip="<?php _e('Set the maximum attachment upload allowed per submission.', 'wpabstracts'); ?>">
                        <img src="<?php echo plugins_url('images/settings_help.png', __FILE__); ?>" height="16" width="16" alt="Help">
                    </span></th>
                <td><input name="options[wpabstracts_upload_limit]" type="text" id="wpabstracts_upload_limit" value="<?php echo get_option('wpabstracts_upload_limit'); ?>" class="regular-small" />

                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="options[wpabstracts_max_attach_size]" ><?php _e('Maximum Attachment Size', 'wpabstracts'); ?></label>
                    <span class="settings_tip" data-tip="<?php _e('Maxmium size allowed for attachments (in bytes).', 'wpabstracts'); ?>">
                        <img src="<?php echo plugins_url('images/settings_help.png', __FILE__); ?>" height="16" width="16" alt="Help">
                    </span>
                </th>

                <td><input name="options[wpabstracts_max_attach_size]" type="text" value="<?php echo get_option('wpabstracts_max_attach_size'); ?>" class="regular-small"/>

                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="options[wpabstracts_permitted_attachments]"><?php _e('Permitted Attachments', 'wpabstracts'); ?></label>
                <span class="settings_tip" data-tip="<?php _e('File extentions allowed for uploading (separate extentions with a comma).', 'wpabstracts'); ?>">
                        <img src="<?php echo plugins_url('images/settings_help.png', __FILE__); ?>" height="16" width="16" alt="Help">
                    </span></th>
                <td><input name="options[wpabstracts_permitted_attachments]" type="text" id="attachments_permitted" value="<?php echo get_option('wpabstracts_permitted_attachments'); ?>" class="regular-small" />

                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="options[wpabstracts_chars_count_min]"><?php _e('Minimum Abstract Character Count', 'wpabstracts'); ?></label>
                <span class="settings_tip" data-tip="<?php _e('Minimum character count allowed in a submission.', 'wpabstracts'); ?>">
                        <img src="<?php echo plugins_url('images/settings_help.png', __FILE__); ?>" height="16" width="16" alt="Help">
                    </span></th>
                <td><input name="options[wpabstracts_chars_count_min]" type="text" id="mincharscount" value="<?php echo get_option('wpabstracts_chars_count_min'); ?>" class="regular-small" />

                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="options[wpabstracts_chars_count]"><?php _e('Maximum Abstract Character Count', 'wpabstracts'); ?></label>
                <span class="settings_tip" data-tip="<?php _e('Maximum character count allowed in a submission.', 'wpabstracts'); ?>">
                        <img src="<?php echo plugins_url('images/settings_help.png', __FILE__); ?>" height="16" width="16" alt="Help">
                    </span></th>
                <td><input name="options[wpabstracts_chars_count]" type="text" id="charscount" value="<?php echo get_option('wpabstracts_chars_count'); ?>" class="regular-small" />

                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="options[wpabstracts_resume_count]"><?php _e('Maximum Resume Character Count', 'wpabstracts'); ?></label>
                <span class="settings_tip" data-tip="<?php _e('Maximum character count allowed in resumes.', 'wpabstracts'); ?>">
                        <img src="<?php echo plugins_url('images/settings_help.png', __FILE__); ?>" height="16" width="16" alt="Help">
                    </span></th>
                <td><input name="options[wpabstracts_resume_count]" type="text" id="resumecount" value="<?php echo get_option('wpabstracts_resume_count'); ?>" class="regular-small" />

                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="options[wpabstracts_targetgroup_count]"><?php _e('Maximum Target Group Character Count', 'wpabstracts'); ?></label>
                <span class="settings_tip" data-tip="<?php _e('Maximum character count allowed in target group descriptions.', 'wpabstracts'); ?>">
                        <img src="<?php echo plugins_url('images/settings_help.png', __FILE__); ?>" height="16" width="16" alt="Help">
                    </span></th>
                <td><input name="options[wpabstracts_targetgroup_count]" type="text" id="targetgroupcount" value="<?php echo get_option('wpabstracts_targetgroup_count'); ?>" class="regular-small" />

                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="options[wpabstracts_comments_count]"><?php _e('Maximum Abstract Comment Character Count', 'wpabstracts'); ?></label>
                <span class="settings_tip" data-tip="<?php _e('Maximum character count allowed in abstract author comments.', 'wpabstracts'); ?>">
                        <img src="<?php echo plugins_url('images/settings_help.png', __FILE__); ?>" height="16" width="16" alt="Help">
                    </span></th>
                <td><input name="options[wpabstracts_comments_count]" type="text" id="commentscount" value="<?php echo get_option('wpabstracts_comments_count'); ?>" class="regular-small" />

                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="options[wpabstracts_presenter_preference]" ><?php _e('Presenter Preferences', 'wpabstracts'); ?></label>
                    <span class="settings_tip" data-tip="<?php _e('Set the types of presentation allowed (separated by commas), Eg. Poster, Panel, Round Table', 'wpabstracts'); ?>">
                        <img src="<?php echo plugins_url('images/settings_help.png', __FILE__); ?>" height="16" width="16" alt="Help">
                    </span>
                </th>

                <td><input name="options[wpabstracts_presenter_preference]" type="text" value="<?php echo get_option('wpabstracts_presenter_preference'); ?>" class="regular-small"/>

                </td>
            </tr>


            </table>
                </div>
            </div>
        </div>

    </div>
    <?php
    /*
    <div class="metabox-holder has-right-sidebar">

        <div id="post-body">

            <div id="post-body-content">
                <div class="postarea">
                    <p><?php _e('Author Instructions', 'wpabstracts'); ?>
                    <span class="settings_tip" data-tip="<?php _e('Enter specific instructions for authors to follow for submissions', 'wpabstracts'); ?>">
                        <img src="<?php echo plugins_url('images/settings_help.png', __FILE__); ?>" height="16" width="16" alt="Help">
                    </span></p>

                    <?php
                        $settings = array( 'media_buttons' => false, 'wpautop'=>true, 'dfw' => true, 'editor_height' => 160, 'quicktags' => false);
                        wp_editor(get_option('wpabstracts_author_instructions'), 'options[wpabstracts_author_instructions]', $settings);
                    ?>

                </div>
            </div>
        </div>
    </div>
    */
    ?>
    </form>
</div>
