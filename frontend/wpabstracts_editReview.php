<h2><?php _e('Edit Review', 'wpabstracts');?></h2>

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
                        <div class="wpabstracts targetgroup">
                          <h4><?php _e('Target Group', 'wpabstracts'); ?></h4>
                          <?php echo wpautop($abstract['target_group']); ?>
                        </div>
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


                <div class="wpabstracts panel panel-default">
                    <div class="wpabstracts panel-heading">
                         <strong><?php _e('Add Comments', 'wpabstracts');?></strong>
                    </div>
                    <div class="wpabstracts panel-body" id="abs_review_comments_error">
                        <?php
                            $settings = array( 'media_buttons' => false, 'wpautop'=>true, 'dfw' => true, 'editor_height' => 90);
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
                                <input type='radio' name='abs_relevance' value='Excellent' <?php checked($review['relevance'], 'Excellent'); ?> /> <?php _e('Excellent', 'wpabstracts'); ?>
                            </label>
                            <label class="wpabstracts radio">
                                <input type='radio' name='abs_relevance'  value='Good' <?php checked($review['relevance'], "Good"); ?> /> <?php _e('Good', 'wpabstracts'); ?>
                            </label>
                            <label class="wpabstracts radio">
                                <input type='radio' name='abs_relevance' value='Average' <?php checked($review['relevance'], "Average"); ?> /> <?php _e('Average', 'wpabstracts'); ?>
                            </label>
                            <label class="wpabstracts radio">
                                <input type='radio' name='abs_relevance' value='Poor' <?php checked($review['relevance'], "Poor" ); ?> /> <?php _e('Poor', 'wpabstracts'); ?>
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
                                <input type='radio' name='abs_quality' value='Excellent' <?php checked($review['quality'], "Excellent"); ?> /> <?php _e('Excellent', 'wpabstracts'); ?>
                            </label>
                            <label class="wpabstracts radio">
                                <input type='radio' name='abs_quality' value='Good' <?php checked($review['quality'], "Good"); ?> /> <?php _e('Good', 'wpabstracts'); ?>
                            </label>
                            <label class="wpabstracts radio">
                                <input type='radio' name='abs_quality' value='Average' <?php checked($review['quality'],"Average"); ?> /> <?php _e('Average', 'wpabstracts'); ?>
                            </label>
                            <label class="wpabstracts radio">
                                <input type='radio' name='abs_quality' value='Poor' <?php checked($review['quality'], "Poor"); ?> /> <?php _e('Poor', 'wpabstracts'); ?>
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
                            <label class="wpabstracts radio"><input type='radio' name='abs_status' value='Approved' <?php checked($review['status'], "Approved"); ?> /> <?php _e('Approved', 'wpabstracts');?></label>
                                <label class="wpabstracts radio"><input type='radio' name='abs_status' value='Rejected' <?php checked($review['status'], "Rejected"); ?> /> <?php _e('Rejected', 'wpabstracts');?></label>
                                <label class="wpabstracts radio"><input type='radio' name='abs_status' value='Maybe' <?php checked($review['status'], "Maybe"); ?> /> <?php _e('Maybe', 'wpabstracts');?></label>
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
                      <img src='<?php echo get_site_url().'/wp-content/plugins/wpabstracts_pro/profile-images/'.$abstract['profile_image']; ?>' />
                    </div>
                </div>
        </div>
        </div>

            <button type="button" onclick="wpabstracts_validateReview();" class="wpabstracts btn btn-primary"><?php _e('Update Review', 'wpabstracts'); ?></button>
        </form>

    </div>
