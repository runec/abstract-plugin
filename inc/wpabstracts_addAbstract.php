<?php
wp_enqueue_style('cropper-css', plugins_url('../inc/cropper-master/cropper.min.css',__FILE__));
wp_enqueue_style('imagebutton-css', plugins_url('../inc/cropper-master/cropper-main.css',__FILE__));
wp_enqueue_script('cropper-js', plugins_url('../inc/cropper-master/cropper.min.js',__FILE__), array());
wp_enqueue_script('imagebutton-js', plugins_url('../inc/cropper-master/cropper-main.js',__FILE__), array());

?>
<script>
    jQuery(document).ready(function (){
        wpabstracts_updateCharacterCount();
    });
</script>
<?php
  $task = isset($_GET["task"]) ? sanitize_text_field($_GET["task"]) : 'submit';
    if($id && !wpabstracts_is_event_active($id)) {
        _e('<h4>Abstract submission for this event has past</h4>', 'wpabstracts');
        return;
    }
    else if($task == "done" ) {
        echo _e('Thanks for your submission','wpabstracts');
        return;
    }
?>
<h1><?php _e('New abstract','wpabstracts') ?> </h1>
<div id="overlay">
     <div class="overlay-content">
          <!-- Upload image and data -->
          <div class="avatar-upload">
            <input class="avatar-src" name="avatar_src" type="hidden">
            <input class="avatar-data" name="avatar_data" type="hidden">
            <label for="avatarInput">Local upload</label>
            <input class="avatar-input" id="avatarInput" name="avatar_file" type="file" onChange='showImageAndAddCropper();'>
          </div>
          <div class="avatar-wrapper"></div>
          <button class="avatar-save" type="submit" onClick="getCroppedImage();"><?php _e('Done','wpabstracts') ?></button>
          <button class="avatar-save" type="button" onClick="closeCropper();"><?php _e('Cancel','wpabstracts') ?></button>
     </div>
</div>
<div class="wpabstracts container-fluid">

        <form method="post" enctype="multipart/form-data" id="abs_form">
            <div class="wpabstracts row">

            <div class="wpabstracts col-xs-12 col-sm-12 col-md-8">
                <div class="wpabstracts panel panel-default">
                    <div class="wpabstracts panel-heading">
                        <h2><?php _e('Title of Abstract', 'wpabstracts');?></h2>
                    </div>
                    <div class="wpabstracts panel-body">
                        <input class="wpabstracts form-control" type="text" name="abs_title" placeholder="<?php _e('Enter title','wpabstracts');?>" value="" id="title" maxlength="60"/>
                    </div>
                 </div>
                 <div class="wpabstracts panel panel-default">
                    <div class="wpabstracts panel-heading">
                        <h2><?php _e('Target Group', 'wpabstracts');?></h2>
                    </div>
                    <div class="wpabstracts panel-body">
                        <?php
                            $settings = array( 'media_buttons' => false, 'wpautop'=>true, 'dfw' => true, 'editor_height' => 90, 'quicktags' => false);
                            wp_editor('', 'targetgroup', $settings);
                        ?>
                        <span class="wpabstracts max-targetgroup-count" style="display: none;"><?php echo get_option('wpabstracts_targetgroup_count'); ?></span>
                        <table id="post-status-info" cellspacing="0">
                            <tbody><tr><td id="wp-targetgroup-count"><?php printf( __( 'Character count: %s', 'wpabstracts'  ), '<span class="wpabstracts targetgroup-word-count">0</span>' ); ?></td></tr></tbody>
                        </table>

                    </div>
                 </div>
                 <div class="wpabstracts panel panel-default">
                    <div class="wpabstracts panel-heading">
                        <h2><?php _e('Resume', 'wpabstracts');?></h2>
                    </div>
                    <div class="wpabstracts panel-body">
                        <?php
                            $settings = array( 'media_buttons' => false, 'wpautop'=>true, 'dfw' => true, 'editor_height' => 180, 'quicktags' => false);
                            wp_editor('', 'resumetext', $settings);
                        ?>
                        <span class="wpabstracts max-resume-count" style="display: none;"><?php echo get_option('wpabstracts_resume_count'); ?></span>
                        <table id="post-status-info" cellspacing="0">
                            <tbody><tr><td id="wp-resume-count"><?php printf( __( 'Character count: %s' , 'wpabstracts' ), '<span class="wpabstracts resume-word-count">0</span>' ); ?></td></tr></tbody>
                        </table>

                    </div>
                 </div>
                 <div class="wpabstracts panel panel-default">
                     <div class="wpabstracts panel-heading">
                         <h2><?php _e('Abstract', 'wpabstracts');?></h2>
                         (<?php printf(__('Minimum %s characters', 'wpabstracts'), get_option('wpabstracts_chars_count_min')); ?>)
                     </div>
                     <div class="wpabstracts panel-body">
                         <?php
                             $settings = array( 'media_buttons' => false, 'wpautop'=>true, 'dfw' => true, 'editor_height' => 360, 'quicktags' => false);
                             wp_editor('', 'abstext', $settings);
                         ?>
                         <span class="wpabstracts min-word-count" style="display: none;"><?php echo get_option('wpabstracts_chars_count_min'); ?></span>
                         <span class="wpabstracts max-word-count" style="display: none;"><?php echo get_option('wpabstracts_chars_count'); ?></span>
                         <table id="post-status-info" cellspacing="0">
                             <tbody><tr><td id="wp-word-count"><?php printf( __( 'Character count: %s', 'wpabstracts' ), '<span class="wpabstracts abs-word-count">0</span>' ); ?></td></tr></tbody>
                         </table>

                     </div>
                  </div>

                  <div class="wpabstracts panel panel-default">
                      <div class="wpabstracts panel-heading">
                          <h2><?php _e('Comments (optional)', 'wpabstracts');?></h2>
                      </div>
                      <div class="wpabstracts panel-body">
                          <?php
                              $settings = array( 'media_buttons' => false, 'wpautop'=>true, 'dfw' => true, 'editor_height' => 360, 'quicktags' => false);
                              wp_editor('', 'comments', $settings);
                          ?>
                          <span class="wpabstracts max-comment-count" style="display: none;"><?php echo get_option('wpabstracts_comments_count'); ?></span>
                          <table id="post-status-info" cellspacing="0">
                              <tbody><tr><td id="wp-comment-count"><?php printf( __( 'Character count: %s', 'wpabstracts' ), '<span class="wpabstracts comment-word-count">0</span>' ); ?></td></tr></tbody>
                          </table>

                      </div>
                   </div>

                <?php
                if(get_option('wpabstracts_show_attachments') == 'Yes'){ ?>
                    <div class="wpabstracts panel panel-default">

                        <div class="wpabstracts panel-heading">
                           <?php _e('Attachments','wpabstracts');?>
                           <p><span><?php _e('Add up to ', 'wpabstracts')?><?php echo get_option('wpabstracts_upload_limit');?> <?php _e('Attachments', 'wpabstracts');?></span></p>
                        </div>

                        <div class="wpabstracts panel-body">

                            <div class="wpabstracts form-group">
                                <?php _e('Use this form to upload your images, photos or tables.', 'wpabstracts'); ?>
                                <?php _e('Supported formats', 'wpabstracts'); ?>: <strong><?php echo implode(' ', explode(' ', get_option('wpabstracts_permitted_attachments'))); ?></strong>
                                <?php _e('Maximum attachment size', 'wpabstracts'); ?>: <strong><?php echo number_format((get_option('wpabstracts_max_attach_size') / 1048576)); ?>MB</strong>
                            </div>

                            <div class="wpabstracts form-group">
                                <?php
                                    for($i = 0; $i < get_option('wpabstracts_upload_limit'); $i++){ ?>
                                        <div>
                                            <input type="file" name="attachments[]">
                                        </div>
                                <?php } ?>
                            </div>

                        </div>
                    </div>
                <?php } ?>
            </div>

            <div class="wpabstracts col-xs-12 col-md-4">
                <div class="wpabstracts panel panel-default">

                    <div class="wpabstracts panel-heading">
                        <?php _e('Event Information','wpabstracts');?>
                    </div>

                    <div class="wpabstracts panel-body">

                        <div class="wpabstracts form-group">
                            <label class="wpabstracts control-label" for="abs_event"><?php _e('Event','wpabstracts');?></label>
                            <input type="hidden" id="abs_event" name="abs_event" value=<?php echo esc_attr($id); ?>>
                            <span><?php var_dump(wpabstracts_getEvents('event_id', $id, ARRAY_A)); ?></span>
                            <?php /*
                            <select name="abs_event" id="abs_event" class="wpabstracts form-control" onchange="wpabstracts_getTopics(this.value);">
                                    <option value="" style="display:none;"><?php _e('Select an event','wpabstracts');?></option>
                                    <?php
                                        foreach($events as $event){ ?>
                                            <option value="<?php echo esc_attr($event->event_id);?>"><?php echo esc_attr($event->name);?></option>
                                    <?php }
                                    ?>
                            </select>
                            */ ?>
                        </div>
                        <div class="wpabstracts form-group">
                            <label class="wpabstracts control-label" for="abs_topic"><?php _e('Topic','wpabstracts');?></label>
                            <select name="abs_topic" id="abs_topic" class="wpabstracts form-control">
                                <option value="" style="display:none;"><?php _e('Select a topic','wpabstracts');?></option>
                            </select>
                        </div>

                    </div>
                </div>
                <?php
                /*
                <div class="wpabstracts panel panel-default">

                    <div class="wpabstracts panel-heading">
                        <?php _e('Author Information','wpabstracts');?>
                    </div>

                    <div class="wpabstracts panel-body" id="coauthors_table">

                        <div class="wpabstracts form-group">
                            <label class="wpabstracts control-label" for="abs_author[]"><?php _e('Name','wpabstracts');?></label>
                            <input class="wpabstracts form-control" type="text" name="abs_author[]" id="abs_author[]"  />

                            <label class="wpabstracts control-label" for="abs_author_email[]"><?php _e('Email','wpabstracts');?></label>
                            <input class="wpabstracts form-control" type="text" name="abs_author_email[]" id="abs_author_email[]" />

                            <label class="wpabstracts control-label" for="abs_author_affiliation[]"><?php _e('Affiliation','wpabstracts');?></label>
                            <input class="wpabstracts form-control" type="text" name="abs_author_affiliation[]" id="abs_author_affiliation[]"/>

                        </div>
                    </div>

                    <div class="wpabstracts panel-body">
                        <button type="button" onclick="wpabstracts_add_coauthor();" class="wpabstracts btn btn-info" style="float: left;"><?php _e('add author','wpabstracts');?></button>
                        <button type="button" onclick="wpabstracts_delete_coauthor();" class="wpabstracts btn btn-danger" style="float: right;"><?php _e('delete author','wpabstracts');?></button>
                    </div>

                </div>
                */
                ?>
                <div class="wpabstracts panel panel-default">

                    <div class="wpabstracts panel-heading">
                        <?php _e('Presenter Information','wpabstracts');?>
                    </div>

                    <div class="wpabstracts panel-body">

                        <div class="wpabstracts form-group">
                            <label class="wpabstracts control-label" for="abs_presenter"><?php _e('Name','wpabstracts');?></label>
                            <input class="wpabstracts form-control" type="text" name="abs_presenter" id="abs_presenter"/>
                        </div>

                        <div class="wpabstracts form-group">
                          <label class="wpabstracts control-label" for="abs_presenter_company"><?php _e('Company','wpabstracts');?></label>
                          <input class="wpabstracts form-control" type="text" name="abs_presenter_company" id="abs_presenter_company"/>
                        </div>

                        <div class="wpabstracts form-group">
                            <label class="wpabstracts control-label" for="abs_presenter_email"><?php _e('Email','wpabstracts');?></label>
                            <input class="wpabstracts form-control" type="text" name="abs_presenter_email" id="abs_presenter_email"/>
                        </div>

                        <div class="wpabstracts form-group">
                            <label class="wpabstracts control-label" for="abs_presenter_phone"><?php _e('Phone','wpabstracts');?></label>
                            <input class="wpabstracts form-control" type="text" name="abs_presenter_phone" id="abs_presenter_phone"/>
                        </div>

                        <div class="wpabstracts form-group">
                            <label class="wpabstracts control-label" for="abs_presenter_linkedin"><?php _e('LinkedIn','wpabstracts');?></label>
                            <input class="wpabstracts form-control" type="text" name="abs_presenter_linkedin" id="abs_presenter_linkedin"/>
                        </div>
                        <div class="wpabstracts form-group">
                            <input type="checkbox" name="abs_presenter_perspektiv" id="abs_presenter_perspektiv"/>
                            <?php _e('I would like to send a message to Geoforum Perspektiv','wpabstracts');?>

                        </div>


                        <?php /*
                        <div class="wpabstracts form-group">
                            <label class="wpabstracts control-label" for="abs_presenter_preference"><?php _e('Presenter Preference','wpabstracts');?></label>
                            <select class="wpabstracts form-control" name="abs_presenter_preference" id="abs_presenter_preference">
                                <option value="" style="display:none;"><?php _e('Preference','wpabstracts');?></option>
                                <?php
                                    $presenter_preference = explode(',', get_option('wpabstracts_presenter_preference'));
                                    foreach($presenter_preference as $preference){ ?>
                                        <option value="<?php echo $preference; ?>"><?php echo $preference; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        */ ?>
                        <div class="wpabstracts form-group">
                            <label class="wpabstracts control-label" for="abs_presenter_photo"><?php _e('Photo','wpabstracts');?></label>
                            <div id='abstract-photo-preview'><img src='http://www.placehold.it/100x150'></div>
                            <button id='image-button' type='button' onClick='overlay();'><?php _e('Choose photo...','wpabstracts'); ?></button>
                            <?php  ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>

            <button type="button" onclick="appendImageToForm();wpabstracts_validateAbstract();" class="wpabstracts btn btn-primary"><?php _e('Submit Abstract','wpabstracts');?></button>
        </form>

    </div>
