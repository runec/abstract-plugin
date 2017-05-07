<script>
    jQuery(document).ready(function (){
        wpabstracts_updateWordCount();
    });
</script>
<style>
    hr.soften {
        height: 1px;
        background-image: -webkit-linear-gradient(left, rgba(0,0,0,0), rgba(0,0,0,.8), rgba(0,0,0,0));
        background-image:    -moz-linear-gradient(left, rgba(0,0,0,0), rgba(0,0,0,.8), rgba(0,0,0,0));
        background-image:     -ms-linear-gradient(left, rgba(0,0,0,0), rgba(0,0,0,.8), rgba(0,0,0,0));
        background-image:      -o-linear-gradient(left, rgba(0,0,0,0), rgba(0,0,0,.8), rgba(0,0,0,0));
        border: 0;
    }
</style>
<div class="wpabstracts container-fluid">

        <form method="post" enctype="multipart/form-data" id="abs_form">
            <div class="wpabstracts row">

            <div class="wpabstracts col-xs-12 col-sm-12 col-md-8">
                <div class="wpabstracts panel panel-default">
                    <div class="wpabstracts panel-heading">
                        <h4><?php _e('Edit Abstract', 'wpabstracts');?></h4>
                    </div>
                    <div class="wpabstracts panel-body">
                        <input class="wpabstracts form-control" type="text" name="abs_title" placeholder="<?php _e('Enter title','wpabstracts');?>" value="<?php echo esc_attr( htmlspecialchars( $abstract[0]->title ) ); ?>" id="title" />
                        <span class="wpabstracts has-error" style="display:none;" id="abs_txt_error"> <?php _e('Please add a description','wpabstracts');?></span>
                        <?php
                            $settings = array( 'media_buttons' => false, 'wpautop'=>true, 'dfw' => true, 'editor_height' => 360);
                            wp_editor($abstract[0]->text, 'abstext', $settings);
                        ?>
                        <span class="wpabstracts max-word-count" style="display: none;"><?php echo get_option('wpabstracts_chars_count'); ?></span>
                        <table id="post-status-info" cellspacing="0">
                            <tbody><tr><td id="wp-word-count"><?php printf( __( 'Word count: %s' ), '<span class="wpabstracts abs-word-count">0</span>' ); ?></td></tr></tbody>
                        </table>
                    </div>
                 </div>

                <div class="wpabstracts panel panel-default">
                    <div class="wpabstracts panel-heading">
                         <h5><?php _e('Manage Attachments', 'wpabstracts');?> </h5>
                    </div>
                    <div class="wpabstracts panel-body">
                        <?php
                        if(count($attachments) < 1) {
                              _e('<p>No Attachments uploaded</p>', 'wpabstracts');
                        }
                        else{
                            foreach($attachments as $attachment) { ?>
                                    <p><span id="attachment_<?php echo $attachment->attachment_id;?>"><strong><?php echo $attachment->filename; ?></strong> [<?php number_format(($attachment->filesize/1048576), 2);?>MB]
                                    <a class="wpabstracts btn btn-danger" href="#removeAttachment" onclick="wpabstracts_remove_attachment('<?php echo $attachment->attachment_id; ?>');">Remove</a></span></p>
                                <?php
                            }
                        } ?>
                    </div>

                </div>
                <?php
                if(get_option('wpabstracts_change_ownership') == "Yes"){
                    $currentUser = wp_get_current_user();
                    if($currentUser->roles[0]=='administrator' || $currentUser->roles[0]=='editor'){ ?>

                    <div class="wpabstracts panel panel-default">
                        <div class="wpabstracts panel-heading">
                            <strong><?php _e('Manage Ownership', 'wpabstracts');?></strong>
                        </div>
                        <div class="wpabstracts panel-body">
                            <p><?php _e('Use this box to assign a new user / owner of this submission','wpabstracts');?></p>
                                <?php
                                    $users = get_users('role=subscriber');
                                    $current_user = get_userdata($abstract[0]->submit_by);
                                ?>
                                <select class="wpabstracts form-control" name="abs_user" id="abs_user">
                                    <option selected value="<?php echo esc_attr($current_user->ID);?>" selected="disabled" style="display:none;"><?php echo esc_attr($current_user->display_name);?></option>
                                  <?php foreach($users as $user){ ?>
                                    <option value="<?php echo esc_attr($user->ID);?>"><?php echo esc_attr($user->display_name);?></option>
                                  <?php } ?>
                                </select>
                        </div>

                    </div>
                <?php }

                } ?>
            </div>

            <div class="wpabstracts col-xs-12 col-md-4">
                <div class="wpabstracts panel panel-default">

                    <div class="wpabstracts panel-heading">
                         <strong><?php _e('Event Information','wpabstracts');?> </strong><span class="wpabstracts has-error" style="display: none;" id="abs_event_error"><?php _e('All fields required','wpabstracts');?></span></strong>
                    </div>

                    <div class="wpabstracts panel-body">

                        <div class="wpabstracts form-group">
                            <label class="wpabstracts control-label" for="abs_event"><?php _e('Event','wpabstracts');?></label>
                            <select name="abs_event" id="abs_event" class="wpabstracts form-control" onchange="wpabstracts_getTopics(this.value);">
                                    <option value="<?php echo esc_html($event['event_id']);?>" style="display:none;"><?php echo esc_attr($event['name']);?></option>
                                    <?php
                                        foreach($events as $event){ ?>
                                            <option value="<?php echo esc_attr($event->event_id);?>"><?php echo esc_attr($event->name);?></option>
                                    <?php }
                                    ?>
                            </select>
                        </div>
                        <div class="wpabstracts form-group">
                            <label class="wpabstracts control-label" for="abs_topic"><?php _e('Topic','wpabstracts');?></label>
                            <select name="abs_topic" id="abs_topic" class="wpabstracts form-control">
                                <option value="<?php echo esc_attr($abstract[0]->topic) ;?>" style="display:none;"><?php echo esc_attr($abstract[0]->topic) ;?></option>
                                <?php
                                    foreach($events as $event){ ?>
                                        <option value="<?php echo esc_attr($event->event_id);?>"><?php echo $event->name;?></option>
                                    <?php }
                                ?>
                            </select>
                        </div>

                    </div>

                </div>

                <div class="wpabstracts panel panel-default">

                <div class="wpabstracts panel-heading">
                    <strong><?php _e('Author Information','wpabstracts');?> </strong><span class="wpabstracts has-error" style="display: none;" id="abs_author_error"><?php _e('All fields required', 'wpabstracts'); ?></span>
                </div>

                <div class="wpabstracts panel-body" id="coauthors_table">

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

                        <div class="wpabstracts form-group divider">
                            <label class="wpabstracts control-label" for="abs_author[]"><?php _e('Name','wpabstracts');?></label>
                            <input class="wpabstracts form-control" type="text" name="abs_author[]" id="abs_author[]" value="<?php echo esc_attr($author['name']); ?>"/>

                            <label class="wpabstracts control-label" for="abs_author_email[]"><?php _e('Email','wpabstracts');?></label>
                            <input class="wpabstracts form-control" type="text" name="abs_author_email[]" id="abs_author_email[]" value="<?php echo esc_attr($author['email']); ?>" />

                            <label class="wpabstracts control-label" for="abs_author_affiliation[]"><?php _e('Affiliation','wpabstracts');?></label>
                            <input class="wpabstracts form-control" type="text" name="abs_author_affiliation[]" id="abs_author_affiliation[]" value="<?php echo esc_attr($author['affiliation']); ?>" />
                        </div>
                    <?php } ?>

                </div>

                <div class="wpabstracts panel-body">
                    <button type="button" onclick="wpabstracts_add_coauthor();" class="wpabstracts btn btn-info" style="float: left;"><?php _e('add author','wpabstracts');?></button>
                    <button type="button" onclick="wpabstracts_delete_coauthor();" class="wpabstracts btn btn-danger" style="float: right;"><?php _e('delete author','wpabstracts');?></button>
                </div>

                </div>

                <div class="wpabstracts panel panel-default">

                    <div class="wpabstracts panel-heading">
                        <strong><?php _e('Presenter Information','wpabstracts');?> </strong><span class="wpabstracts has-error" style="display: none;" id="abs_presenter_error"><?php _e('All fields required','wpabstracts');?></span>
                    </div>

                    <div class="wpabstracts panel-body">

                        <div class="wpabstracts form-group">
                            <label class="wpabstracts control-label" for="abs_presenter"><?php _e('Name','wpabstracts');?></label>
                            <input class="wpabstracts form-control" type="text" name="abs_presenter" id="abs_presenter" value="<?php echo esc_attr($abstract[0]->presenter);?>"/>
                        </div>

                        <div class="wpabstracts form-group">
                            <label class="wpabstracts control-label" for="abs_presenter_email"><?php _e('Email','wpabstracts');?></label>
                            <input class="wpabstracts form-control" type="text" name="abs_presenter_email" id="abs_presenter_email" value="<?php echo esc_attr($abstract[0]->presenter_email);?>"/>
                        </div>

                        <div class="wpabstracts form-group">
                            <label class="wpabstracts control-label" for="abs_presenter_preference"><?php _e('Presenter Preference','wpabstracts');?></label>
                            <select class="wpabstracts form-control" name="abs_presenter_preference" id="abs_presenter_preference" >
                                <option value="<?php echo $abstract[0]->presenter_preference;?>" selected style="display:none;"><?php echo esc_attr($abstract[0]->presenter_preference);?></option>
                                <?php
                                    $presenter_preference = explode(',', get_option('wpabstracts_presenter_preference'));
                                    foreach($presenter_preference as $preference){ ?>
                                        <option value="<?php echo $preference; ?>"><?php echo $preference; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                    </div>
                </div>

                <div class="wpabstracts panel panel-default">

                    <div class="wpabstracts panel-heading">
                       <strong><?php _e('Attachments','wpabstracts');?></strong>
                        <?php _e('(add up to '. get_option('wpabstracts_upload_limit') .' attachments)','wpabstracts');?>
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



        </div>
        </div>

            <button type="button" onclick="wpabstracts_validateAbstract();" class="wpabstracts btn btn-primary"><?php _e('Update Abstract','wpabstracts');?></button>
        </form>

    </div>