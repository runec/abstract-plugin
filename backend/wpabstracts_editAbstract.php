<?php
wp_enqueue_style('slim-cropper-css', plugins_url('../slim/slim.min.css', __FILE__));
wp_enqueue_script('slim-cropper-js', plugins_url('../slim/slim.kickstart.min.js', __FILE__));

 ?>
<script>
    jQuery(document).ready(function (){
        wpabstracts_updateWordCount();
    });
</script>
<h2><?php _e('Edit Abstract', 'wpabstracts');?></h2>
<div class="metabox-holder has-right-sidebar">
    <form method="post" enctype="multipart/form-data" id="abs_form">
        <div class="inner-sidebar">
            <div class="misc-pub-section">
                <input type="button" onclick="wpabstracts_validateAbstract();" class="button button-primary button-large" value="<?php _e('Update Abstract', 'wpabstracts');?>" />
            </div>
            <div class="postbox"><!-- Event -->
                <h3><?php _e('Event Information','wpabstracts');?><span class="form-invalid" style="display: none;" id="abs_event_error"><?php _e(' All fields required','wpabstracts');?></span></h3>
                <div class="inside">
                    <table width="100%">
                        <tr>
                            <td><?php _e('Event','wpabstracts');?></td>
                            <td><select name="abs_event" id="abs_event" onchange="wpabstracts_getTopics(this.value);wpabstracts_getSessions(this.value);">
                                    <option value="<?php echo esc_html($event['event_id']);?>" style="display:none;"><?php echo esc_attr($event['name']);?></option>
					    <?php
						foreach($events as $event){ ?>
                                                    <option value="<?php echo esc_attr($event->event_id);?>"><?php echo $event->name;?></option>
						<?php }
					    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><?php _e('Topic','wpabstracts');?></td>
                            <td><select name="abs_topic" id="abs_topic">
                                    <option value="<?php echo esc_attr($abstract[0]->topic) ;?>" style="display:none;"><?php echo esc_attr($abstract[0]->topic) ;?></option>
                                <?php
                                foreach($topics as $topic){ ?>
                                    <option value="<?php echo esc_attr($topic);?>"><?php echo esc_attr($topic);?></option>
                                <?php
                                } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><?php _e('Session','wpabstracts');?></td>
                            <td><select name="abs_session" id="abs_session">
                                    <option value="<?php echo esc_attr($abstract[0]->session) ;?>" style="display:none;"><?php echo esc_attr($abstract[0]->session) ;?></option>
                                    <option value=""><?php _e('No session', 'wpabstracts'); ?></option>
                                <?php
                                foreach($sessions as $session){ ?>
                                    <option value="<?php echo esc_attr($session);?>"><?php echo esc_attr($session);?></option>
                                <?php
                                } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                          <td><?php _e('Priority', 'wpabstracts');?></td>
                          <td><input type="number" name="abs_priority" id="abs_priority" min="1" value='<?php echo $abstract[0]->priority; ?>'></input></td>
                        </tr>
                    </table>
                </div>
            </div><!-- End Event -->
            <?php
            /* Author info
            <div class="postbox"> <!-- Author -->
                <h3><?php _e('Author Information','wpabstracts');?><span class="form-invalid" style="display: none;" id="abs_author_error"> <?php _e(' All fields required','wpabstracts');?></span></h3>
                <div class="inside">
                    <table width="100%" id="coauthors_table">
			<?php
			    $authors_name = explode(' | ', $abstract[0]->author);
			    $authors_emails = explode(' | ', $abstract[0]->author_email);
                            $authors_affiliation = explode(' | ', $abstract[0]->author_affiliation);
                            foreach ($authors_name as $id => $key) {
                                $authors[$key] = array(
                                    'name'  => $authors_name[$id],
                                    'email' => $authors_emails[$id],
                                    'affiliation'    => $authors_affiliation[$id],
                                );
                            }

			    foreach($authors as $author){ ?>
                                <tr style="border-bottom: dotted 1px #ccc;">
                                    <td><?php _e('Name','wpabstracts');?></td>
                                    <td><input type="text" name="abs_author[]" id="abs_author[]" value="<?php echo esc_attr($author['name']); ?>"/></td>
                                </tr>
                                <tr>
                                            <td><?php _e('Email','wpabstracts');?></td>
                                        <td style="border-bottom: dotted 1px #ccc;"><input type="text" name="abs_author_email[]" id="abs_author_email[]" value="<?php echo esc_attr($author['email']); ?>" /></td>
                                </tr>
                                <tr>
                                            <td><?php _e('Affiliation','wpabstracts');?></td>
                                        <td style="border-bottom: dotted 1px #ccc;"><input type="text" name="abs_author_affiliation[]" id="abs_author_affiliation[]" value="<?php echo esc_attr($author['affiliation']); ?>" /></td>
                                </tr>
                                <?php
                            } ?>
                    </table>
                    <div class="inner_btns">
                        <a class="button-secondary" href="#add-author" onclick="wpabstracts_add_coauthor();" style="float: left;"><?php _e('add author','wpabstracts');?></a>
                        <a class="button-secondary" href="#delete-author" onclick="wpabstracts_delete_coauthor();" style="float: right;"><?php _e('delete author','wpabstracts');?></a>
                    </div>
                </div>
            </div> <!-- End Author -->
            */
            ?>
            <div class="postbox"><!-- Presenter -->
                <h3><?php _e('Presenter Information', 'wpabstracts'); ?><span class="form-invalid" style="display: none;" id="abs_presenter_error"><?php _e(' All fields required','wpabstracts');?></span></h3>
                <div class="inside">
                    <table width="100%">
                        <tr>
                            <td><?php _e('Name','wpabstracts');?></td>
                            <td><input type="text" name="abs_presenter" style="width:150px;" id="abs_presenter" value="<?php echo esc_attr($abstract[0]->presenter);?>" /></td>
                        </tr>
                        <tr>
                            <td><?php _e('Company','wpabstracts');?></td>
                            <td><input type="text" name="abs_presenter_company" style="width:150px;" id="abs_presenter_company" value="<?php echo esc_attr($abstract[0]->presenter_company);?>" /></td>
                        </tr>
                        <tr>
                            <td><?php _e('Email','wpabstracts');?></td>
                            <td><input type="text" name="abs_presenter_email" style="width:150px;" id="abs_presenter_email" value="<?php echo esc_attr($abstract[0]->presenter_email);?>" /></td>
                        </tr>
                        <tr>
                            <td><?php _e('Phone','wpabstracts');?></td>
                            <td><input type="text" name="abs_presenter_phone" style="width:150px;" id="abs_presenter_phone" value="<?php echo esc_attr($abstract[0]->presenter_phone);?>" /></td>
                        </tr>
                        <tr>
                            <td><?php _e('LinkedIn','wpabstracts');?></td>
                            <td><input type="text" name="abs_presenter_linkedin" style="width:150px;" id="abs_presenter_linkedin" value="<?php echo esc_attr($abstract[0]->presenter_linkedin);?>" /></td>
                        </tr>
                        <?php /*
                        <tr>
                            <td><?php _e('Presenter Preference','wpabstracts');?></td>
                            <td><select name="abs_presenter_preference" id="abs_presenter_preference">
                                    <option selected value="<?php echo $abstract[0]->presenter_preference;?>" selected="disabled" style="display:none;"><?php echo esc_attr($abstract[0]->presenter_preference);?></option>
                                    <?php
                                        $presenter_preference = explode(',', get_option('wpabstracts_presenter_preference'));
                                        foreach($presenter_preference as $preference){ ?>
                                            <option value="<?php echo $preference; ?>"><?php echo $preference; ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        */ ?>
                    </table>
                </div>
            </div>
            <?php /*
            $available = get_option('wpabstracts_upload_limit') - count($attachments);
            if($available > 0){ ?>
            <div class="postbox">
                <h3><span><?php _e('Add Attachments (' . $available . ' remaining)','wpabstracts');?></span></h3>
                <div class="inside">
                    <div class="abstract_form_explanation"><?php _e('Use this form to upload your images, photos or tables.', 'wpabstracts'); ?><br/><?php _e('Supported formats', 'wpabstracts'); ?>: <strong><?php echo implode(', ', explode(', ', get_option('wpabstracts_permitted_attachments'))); ?></strong><br/><?php _e('Maximum attachment size', 'wpabstracts'); ?>: <strong><?php echo number_format((get_option('wpabstracts_max_attach_size') / 1048576)) ?>MB</strong></div>
                    <div id="wpabstract_form_attachments">
                        <?php
                            for($i = 0; $i < (get_option('wpabstracts_upload_limit') - count($attachments)); $i++){ ?>
                                <div>
                                    <input type="file" name="attachments[]">
                                </div>
                            <?php } ?>
                    </div>
                </div>
            </div>
            <?php } */?>
            <?php
            if(strpos($abstract[0]->profile_image, "/") === false) {
              $profile_image_url = WPABSTRACTS_PROFILE_IMAGE_URL.$abstract[0]->profile_image;

            } else {
              $profile_image_url = $abstract[0]->profile_image;
            }
             ?>
            <div class="postbox">
              <h3><?php _e('Photo', 'wpabstracts'); ?></h3>
              <div id="profile_image"
                  class="slim"
                   data-label="<?php _e('Click or drag image here to upload', 'wpabstracts'); ?>"
                   data-ratio="2:3"
                   data-size="400,600"
                   >
                   <?php if($abstract[0]->profile_image) { ?>
                     <img id="profile_image_src" src="<?php echo $profile_image_url; ?>" />
                   <?php } ?>
                  <input type="file" name="slim[]"/>
              </div>
                <input id="upload-button" type="button" class="button" value="Choose image from media library" />
            </div>
        </div>
        <div id="post-body">
            <div id="post-body-content">
                <div id="titlediv">
                    <div id="titlewrap">
                        <input type="text" name="abs_title" value="<?php echo esc_attr( htmlspecialchars( $abstract[0]->title ) ); ?>" placeholder="Enter title" id="title" autocomplete="off" />
                    </div>
                </div>
                <h2><?php _e('Target Group'); ?></h2>
                <div class="postarea">
                    <span class="form-invalid" style="display: none;" id="targetgroup_txt_error"><?php _e(' All fields required','wpabstracts');?></span>
                    <?php
      $settings = array( 'media_buttons' => false, 'wpautop'=>true, 'dfw' => true, 'editor_height' => 360);
      wp_editor($abstract[0]->target_group, 'targetgroup', $settings);
        ?>
                    <span class="max-targetgroup-count" style="display: none;"><?php echo get_option('wpabstracts_targetgroup_count'); ?></span>
                    <table id="post-status-info" cellspacing="0">
                        <tbody><tr><td id="wp-targetgroup-count"><?php printf( __( 'Word count: %s' ), '<span class="targetgroup-word-count">0</span>' ); ?></td></tr></tbody>
                    </table>
                </div>
                <h2><?php _e('Resume'); ?></h2>
                <div class="postarea">
                    <span class="form-invalid" style="display: none;" id="resume_txt_error"><?php _e(' All fields required','wpabstracts');?></span>
                    <?php
      $settings = array( 'media_buttons' => false, 'wpautop'=>true, 'dfw' => true, 'editor_height' => 360);
      wp_editor($abstract[0]->resume, 'resumetext', $settings);
        ?>
                    <span class="max-resume-count" style="display: none;"><?php echo get_option('wpabstracts_resume_count'); ?></span>
                    <table id="post-status-info" cellspacing="0">
                        <tbody><tr><td id="wp-resume-count"><?php printf( __( 'Word count: %s' ), '<span class="resume-word-count">0</span>' ); ?></td></tr></tbody>
                    </table>
                </div>
                <h2><?php _e('Abstract'); ?></h2>
                <div class="postarea">
                    <span class="form-invalid" style="display: none;" id="abs_txt_error"><?php _e(' All fields required','wpabstracts');?></span>
                    <?php
			$settings = array( 'media_buttons' => false, 'wpautop'=>true, 'dfw' => true, 'editor_height' => 360);
			wp_editor($abstract[0]->text, 'abstext', $settings);
		    ?>
                    <span class="max-word-count" style="display: none;"><?php echo get_option('wpabstracts_chars_count'); ?></span>
                    <table id="post-status-info" cellspacing="0">
                        <tbody><tr><td id="wp-word-count"><?php printf( __( 'Word count: %s' ), '<span class="abs-word-count">0</span>' ); ?></td></tr></tbody>
                    </table>
                </div>

                <div class="presenter-comments">
                  <h2><?php _e('Presenter comments for abstract','wpabstracts'); ?></h2>
                  <div class="postarea">
                      <?php
                      $settings = array( 'media_buttons' => false, 'wpautop'=>true, 'dfw' => true, 'editor_height' => 360);
                      wp_editor($abstract[0]->presenter_comments, 'comments', $settings);
                      ?>
                  </div>
                </div>

                <div class="abstract-comments">
                  <h2><?php _e('Manager comments for abstract','wpabstracts'); ?></h2>
                  <div class="postarea">
                      <?php
                      $settings = array( 'media_buttons' => false, 'wpautop'=>true, 'dfw' => true, 'editor_height' => 360);
                      wp_editor($abstract[0]->manager_comments, 'manager-comments', $settings);
                      ?>
                  </div>
                </div>
                <?php /*
                <div class="postbox" id="manage_attachments">
                    <h3><?php _e('Manage Attachments','wpabstracts');?></h3>
                    <div class="inside">
                    <?php
                        if(count($attachments) < 1) {
                              _e('<p>No Attachments uploaded</p>', 'wpabstracts');
}
                        else{
                            foreach($attachments as $attachment) { ?>
                                    <p><span id="attachment_<?php echo $attachment->attachment_id;?>"><strong><?php echo $attachment->filename; ?></strong> [<?php number_format(($attachment->filesize/1048576), 2);?>MB]
                                    <a class="button-secondary" href="#removeAttachment" onclick="wpabstracts_remove_attachment('<?php echo $attachment->attachment_id; ?>');">Remove</a></span></p>
                                <?php
                            }
                        } ?>
                    </div>
                </div>
                */ ?>

                <?php
                if(get_option('wpabstracts_change_ownership') == "Yes"){
                    $currentUser = wp_get_current_user();
                    if($currentUser->roles[0]=='administrator' || $currentUser->roles[0]=='editor'){ ?>
                    <div class="postbox">
                                        <h3><?php _e('Change Ownership', 'wpabstracts');?></h3>
                                <div class="inside">
                                            <p><?php _e('Use this box to assign a new user / owner of this submission','wpabstracts');?></p>
                                    <?php
                                $users = get_users('role=subscriber');
                                $current_user = get_userdata($abstract[0]->submit_by);
                            ?>
                            <table><td>
                                    <select name="abs_user" id="abs_user">
                                        <option selected value="<?php echo esc_attr($current_user->ID);?>" selected="disabled" style="display:none;"><?php echo esc_attr($current_user->display_name);?></option>
                                      <?php foreach($users as $user){ ?>
                                        <option value="<?php echo esc_attr($user->ID);?>"><?php echo esc_attr($user->display_name);?></option>
                                      <?php } ?>
                                    </select></td>
                            </table>
                        </div>
                    </div>
                    <?php }

                } ?>
            </div>
        </div>
    </form>
</div>
<div class="clear"></div>
