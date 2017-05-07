<?php
wp_enqueue_style('slim-cropper-css', plugins_url('../slim/slim.min.css', __FILE__));
wp_enqueue_script('slim-cropper-js', plugins_url('../slim/slim.kickstart.min.js', __FILE__));

 ?>
<script>
    jQuery(document).ready(function (){
      jQuery('#profile_image').slim();
        wpabstracts_updateWordCount();
    });
</script>
<h2><?php _e('New Abstract', 'wpabstracts');?></h2>
<div class="metabox-holder has-right-sidebar">
    <form method="post" enctype="multipart/form-data" id="abs_form">
            <div class="inner-sidebar">
            <div class="misc-pub-section">
                <input type="button" onclick="wpabstracts_validateAbstract();" class="button button-primary button-large btn btn-primary" value="<?php _e('Submit Abstract','wpabstracts');?>" />
            </div>
            <div class="postbox"><!-- Event -->
                <h3><?php _e('Event Information','wpabstracts');?> <span class="form-invalid" style="display: none;" id="abs_event_error"><?php _e('All fields required','wpabstracts');?></span></h3>
                <div class="inside">
                    <table width="100%">
                        <tr>
                            <td><?php _e('Event','wpabstracts');?></td>
                            <td><select name="abs_event" id="abs_event" onchange="wpabstracts_getTopics(this.value);">
                                    <option value="" style="display:none;"><?php _e('Select an event','wpabstracts');?></option>
                                    <?php
                                            foreach($events as $event){ ?>
                                                <option value="<?php echo esc_attr($event->event_id);?>"><?php echo esc_attr($event->name);?></option>
                                            <?php }
                                        ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><?php _e('Topic','wpabstracts');?></td>
                            <td><select name="abs_topic" id="abs_topic">
                                    <option value="" style="display:none;"><?php _e('Select a topic','wpabstracts');?></option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php
            /* Author info (not used for now..)
            <div class="postbox">
                <h3><?php _e('Author Information','wpabstracts');?> <span class="form-invalid" style="display: none;" id="abs_author_error"><?php _e('All fields required', 'wpabstracts'); ?></span></h3>
                <div class="inside">
                    <table width="100%" id="coauthors_table">
                        <tr>
                            <td><?php _e('Name','wpabstracts');?></td>
                            <td><input type="text" name="abs_author[]" id="abs_author[]"  /></td>
                        </tr>
                        <tr>
                            <td><?php _e('Email','wpabstracts');?></td>
                            <td><input type="text" name="abs_author_email[]" id="abs_author_email[]" /></td>
                        </tr>
                        <tr>
                            <td><?php _e('Affiliation','wpabstracts');?></td>
                            <td><input type="text" name="abs_author_affiliation[]" id="abs_author_affiliation[]"/></td>
                        </tr>
                    </table>
                    <input type="button" onclick="wpabstracts_add_coauthor();" class="button-secondary btn btn-primary btn-xs" style="float: left;" value="<?php _e('add author','wpabstracts');?>" />
                    <input type="button" onclick="wpabstracts_delete_coauthor();" class="button-secondary btn btn-primary btn-xs" style="float: right;" value="<?php _e('delete author','wpabstracts');?>" />
                </div>
            </div>
            */
            ?>
            <div class="postbox">
                <h3><?php _e('Presenter Information','wpabstracts');?> <span class="form-invalid" style="display: none;" id="abs_presenter_error"><?php _e('All fields required','wpabstracts');?></span></h3>
                <div class="inside">
                    <table width="100%">
                        <tr>
                            <td><?php _e('Name','wpabstracts');?></td>
                            <td><input type="text" name="abs_presenter" id="abs_presenter"/></td>
                        </tr>
                        <tr>
                            <td><?php _e('Email','wpabstracts');?></td>
                            <td><input type="text" name="abs_presenter_email" id="abs_presenter_email"/></td>
                        </tr>
                        <tr>
                            <td><?php _e('Phone','wpabstracts');?></td>
                            <td><input type="text" name="abs_presenter_phone" style="width:150px;" id="abs_presenter_phone"/></td>
                        </tr>
                        <tr>
                            <td><?php _e('LinkedIn','wpabstracts');?></td>
                            <td><input type="text" name="abs_presenter_linkedin" style="width:150px;" id="abs_presenter_linkedin"/></td>
                        </tr>
                        <?php /*
                        <tr>
                            <td><?php _e('Presenter Preference','wpabstracts');?></td>
                            <td><select name="abs_presenter_preference" id="abs_presenter_preference">
                                    <option value="" style="display:none;"><?php _e('Preference','wpabstracts');?></option>
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
            <div class="postbox">
                <h3><span><?php _e('Add up to ', 'wpabstracts')?><?php echo get_option('wpabstracts_upload_limit');?> <?php _e('Attachments', 'wpabstracts');?></span></h3>
                <div class="inside">
                    <div class="abstract_form_explanation"><?php _e('Use this form to upload your images, photos or tables.', 'wpabstracts'); ?>
                        <br/><?php _e('Supported formats', 'wpabstracts'); ?>: <strong><?php echo implode(' ', explode(' ', get_option('wpabstracts_permitted_attachments'))); ?></strong>
                        <br/><?php _e('Maximum attachment size', 'wpabstracts'); ?>: <strong><?php echo number_format((get_option('wpabstracts_max_attach_size') / 1048576)); ?>MB</strong></div>
                    <div id="wpabstract_form_attachments">
                        <?php
                            for($i = 0; $i < get_option('wpabstracts_upload_limit'); $i++){ ?>
                                <div>
                                    <input type="file" name="attachments[]">
                                </div>
                            <?php } ?>
                    </div>
                </div>
            </div>
            */ ?>
            <div class="postbox">
              <h3><?php _e('Photo', 'wpabstracts'); ?></h3>
              <div class="slim"
                   data-label="<?php _e('Click or drag image here to upload', 'wpabstracts'); ?>"
                   data-ratio="2:3"
                   data-min-size="200,300">
                  <input type="file" id="profile_image" name="slim[]"/>
              </div>
            </div>

        </div>
        <div id="post-body">
                <div id="post-body-content">
                    <div id="titlediv">
                        <div id="titlewrap">
                            <input type="text" name="abs_title" placeholder="Enter title" value="" id="title" />
                        </div>
                    </div>
                    <h2><?php echo _e('Target Group'); ?></h2>
                    <div class="postarea">
                        <span class="form-invalid" style="display:none;" id="targetgroup_txt_error"> <?php _e('Please add a target group description','wpabstracts');?></span>
                        <?php
                            $settings = array( 'media_buttons' => false, 'wpautop'=>true, 'dfw' => true, 'editor_height' => 360);
                            wp_editor('', 'targetgroup', $settings);
                        ?>
                         <span class="max-targetgroup-count" style="display: none;"><?php echo get_option('wpabstracts_targetgroup_count'); ?></span>
                        <table id="post-status-info" cellspacing="0">
                        <tbody><tr><td id="wp-word-count"><?php printf( __( 'Word count: %s' ), '<span class="targetgroup-word-count">0</span>' ); ?></td></tr></tbody>
                    </table>
                    </div>
                    <h2><?php echo _e('Resume'); ?></h2>
                    <div class="postarea">
                        <span class="form-invalid" style="display:none;" id="resume_txt_error"> <?php _e('Please add a resume','wpabstracts');?></span>
                        <?php
                            $settings = array( 'media_buttons' => false, 'wpautop'=>true, 'dfw' => true, 'editor_height' => 360);
                            wp_editor('', 'resumetext', $settings);
                        ?>
                         <span class="max-resume-count" style="display: none;"><?php echo get_option('wpabstracts_resume_count'); ?></span>
                        <table id="post-status-info" cellspacing="0">
                        <tbody><tr><td id="wp-word-count"><?php printf( __( 'Word count: %s' ), '<span class="resume-word-count">0</span>' ); ?></td></tr></tbody>
                    </table>
                    </div>
                    <h2><?php echo _e('Abstract'); ?></h2>
                    <div class="postarea">
                        <span class="form-invalid" style="display:none;" id="abs_txt_error"> <?php _e('Please add your abstract here','wpabstracts');?></span>
                        <?php
                            $settings = array( 'media_buttons' => false, 'wpautop'=>true, 'dfw' => true, 'editor_height' => 360);
                            wp_editor('', 'abstext', $settings);
                        ?>
                         <span class="max-word-count" style="display: none;"><?php echo get_option('wpabstracts_chars_count'); ?></span>
                        <table id="post-status-info" cellspacing="0">
                        <tbody><tr><td id="wp-word-count"><?php printf( __( 'Word count: %s' ), '<span class="abs-word-count">0</span>' ); ?></td></tr></tbody>
                    </table>
                    </div>
                    <div class="abstract-comments">
                      <h2><?php _e('Comments for abstract'); ?></h2>
                      <div class="postarea">
                          <?php
                          $settings = array( 'media_buttons' => false, 'wpautop'=>true, 'dfw' => true, 'editor_height' => 360);
                          wp_editor('', 'manager-comments', $settings);
                          ?>
                      </div>
                    </div>
                </div>
            </div>
    </form>
</div>
