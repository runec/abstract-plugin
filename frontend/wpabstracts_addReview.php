<h2><?php _e('New Review', 'wpabstracts');?></h2>
<div class="wpabstracts container-fluid">
        <form method="post" enctype="multipart/form-data" id="wpabs_review_form">
            <div class="wpabstracts row">

            <div class="wpabstracts col-xs-12 col-sm-12 col-md-8">
                <div class="wpabstracts panel panel-default">
                    <div class="wpabstracts panel-heading">
                        <strong><?php echo esc_attr( htmlspecialchars($abstract['title'])); ?></strong>
                        <input type="hidden" name="abs_title" value="<?php echo $abstract['title']; ?>" />
                    </div>
                    <div class="wpabstracts panel-body">
                          <h4><?php _e('Target Group', 'wpabstracts'); ?></h4>
                          <?php echo wpautop($abstract['target_group']); ?>
                        <div class="wpabstracts resume">
                          <h4><?php _e('Resume', 'wpabstracts'); ?></h4>
                          <?php echo wpautop($abstract['resume']); ?>
                        </div>
                        <div class="wpabstracts abstracttext">
                          <h4><?php _e('Abstract', 'wpabstracts'); ?></h4>
                          <?php echo wpautop($abstract['text']); ?>
                        </div>
                    </div>
                 </div>
                <?php
                /*
                    if (current_user_can(WPABSTRACTS_ACCESS_LEVEL) || get_option('wpabstracts_blind_review') == "No") { ?>
                        <div class="wpabstracts panel panel-default">
                            <div class="wpabstracts panel-heading">
                                <strong><?php _e('Presenter Information', 'wpabstracts'); ?></strong>
                            </div>
                            <div class="wpabstracts panel-body">
                                    <?php _e('Name','wpabstracts');?>: <?php echo esc_attr($abstract['presenter']);?><br>
                                    <?php _e('Email', 'wpabstracts'); ?>: <?php echo esc_attr($abstract['presenter_email']); ?><br>
                                    <?php _e('Phone', 'wpabstracts'); ?>: <?php echo esc_attr($abstract['presenter_phone']); ?><br>
                                    <?php _e('LinkedIn', 'wpabstracts'); ?>: <?php echo esc_attr($abstract['presenter_linkedin']); ?><br>
                            </div>
                        </div>
                <?php }
                */?>


                <div class="wpabstracts panel panel-default">
                    <div class="wpabstracts panel-heading">
                         <strong><?php _e('Add Comments', 'wpabstracts');?></strong>
                    </div>
                    <div class="wpabstracts panel-body" id="abs_review_comments_error">
                        <?php
                            $settings = array( 'media_buttons' => false, 'wpautop'=>true, 'dfw' => true, 'editor_height' => 90, 'quicktags' => false);
                            wp_editor($review['comments'], 'abs_comments', $settings);
                        ?>

                    </div>

                </div>

            </div>

            <div class="wpabstracts col-xs-12 col-md-4">
                <div class="wpabstracts panel panel-default">
                    <div class="wpabstracts panel-heading">
                         <strong><?php _e('Relevance','wpabstracts');?></strong>
                    </div>
                    <div class="wpabstracts panel-body" id="abs_relevance_error">
                        <div class="wpabstracts radio">
                            <label class="wpabstracts radio">
                                <input type='radio' name='abs_relevance' value='Excellent' /> <?php _e('Excellent', 'wpabstracts'); ?>
                            </label>
                            <label class="wpabstracts radio">
                                <input type='radio' name='abs_relevance'  value='Good'/> <?php _e('Good', 'wpabstracts'); ?>
                            </label>
                            <label class="wpabstracts radio">
                                <input type='radio' name='abs_relevance' value='Average' /> <?php _e('Average', 'wpabstracts'); ?>
                            </label>
                            <label class="wpabstracts radio">
                                <input type='radio' name='abs_relevance' value='Poor' /> <?php _e('Poor', 'wpabstracts'); ?>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="wpabstracts panel panel-default">
                    <div class="wpabstracts panel-heading">
                         <strong><?php _e('Quality','wpabstracts');?></strong>
                    </div>
                    <div class="wpabstracts panel-body" id="abs_quality_error">
                        <div class="wpabstracts radio">
                            <label class="wpabstracts radio">
                                <input type='radio' name='abs_quality' value='Excellent' /> <?php _e('Excellent', 'wpabstracts'); ?>
                            </label>
                            <label class="wpabstracts radio">
                                <input type='radio' name='abs_quality' value='Good' /> <?php _e('Good', 'wpabstracts'); ?>
                            </label>
                            <label class="wpabstracts radio">
                                <input type='radio' name='abs_quality' value='Average' /> <?php _e('Average', 'wpabstracts'); ?>
                            </label>
                            <label class="wpabstracts radio">
                                <input type='radio' name='abs_quality' value='Poor' /> <?php _e('Poor', 'wpabstracts'); ?>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="wpabstracts panel panel-default">
                    <div class="wpabstracts panel-heading">
                         <strong><?php _e('Suggest Status','wpabstracts');?></strong>
                    </div>
                    <div class="wpabstracts panel-body" id="abs_status_error">
                        <div class="wpabstracts radio">
                            <label class="wpabstracts radio"><input type='radio' name='abs_status' value='Approved' /> <?php _e('Approved', 'wpabstracts');?></label>
                                <label class="wpabstracts radio"><input type='radio' name='abs_status' value='Rejected' /> <?php _e('Rejected', 'wpabstracts');?></label>
                                <label class="wpabstracts radio"><input type='radio' name='abs_status' value='Maybe' /> <?php _e('Maybe', 'wpabstracts');?></label>
                        </div>
                    </div>
                </div>

                <div class="wpabstracts panel panel-default">
                    <div class="wpabstracts panel-heading">
                         <strong><?php _e('Additional Information','wpabstracts');?></strong>
                    </div>
                    <div class="wpabstracts panel-body">
                        <div class="wpabstracts form-group">
                            <strong><?php _e('Name','wpabstracts');?>: </strong><?php echo esc_attr($abstract['presenter']);?><br>
                            <?php /*
                            <strong><?php _e('Submitted', 'wpabstracts'); ?>:  </strong><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($abstract['submit_date'])); ?><br>
                            */ ?>
                            <strong><?php _e('Event', 'wpabstracts'); ?>: </strong><?php echo $event->name; ?><br>
                            <strong><?php _e('Topic', 'wpabstracts'); ?>: </strong><?php echo $abstract['topic']; ?>
                        </div>
                    </div>
                </div>

                <div class="wpabstracts panel panel-default">
                    <div class="wpabstracts panel-heading">
                        <strong><?php _e('Photo', 'wpabstracts');?></strong>
                    </div>
                    <div class="wpabstracts panel-body">
                      <img src="<?php echo (get_site_url().'/wp-content/plugins/wpabstracts_pro/profile-images/'.$abstract['profile_image']); ?>" />
                    </div>
                </div>
        </div>
        </div>

            <button type="button" onclick="wpabstracts_validateReview();" class="wpabstracts btn btn-primary"><?php _e('Save Review', 'wpabstracts'); ?></button>
        </form>

    </div>
