<?php
defined('ABSPATH') or die("ERROR: You do not have permission to access this page");?>
<div class="metabox-holder has-right-sidebar">
            <div class="inner-sidebar">
                <div class="postbox"><!-- Event -->
                    <h3><?php _e('Important Information', 'wpabstracts'); ?></h3>
                    <div class="inside">
                        <p><?php _e('At least one <strong>event</strong> must be created to allow users to submit abstracts.', 'wpabstracts'); ?></p>
                    </div>
                </div><!-- End Event -->
                <div class="postbox"> <!-- Author -->
                    <h3><?php _e('Support', 'wpabstracts'); ?></h3>
                    <div class="inside">
                        <p><?php _e('For more help on questions and issues please visit the <a href="http://www.wpabstracts.com/support" target="_blank">support forums</a> or <a href="mailto:support@wpabstracts.com">email us</a>. We would be happy to hear form you.', 'wpabstracts'); ?></p>
                    </div>
                </div> <!-- End Author -->
                <div class="postbox">
                    <h3><span><?php _e('Help us Improve', 'wpabstracts'); ?></span></h3>
                    <div class="inside">
                        <p><a href="http://www.wpabstracts.com/wishlist" target="_blank"><?php _e('Suggest', 'wpabstracts'); ?></a> <?php _e('features', 'wpabstracts'); ?>.</p>
                        <p><a href="http://wordpress.org/plugins/wp-abstracts-manuscripts-manager/" target="_blank"><?php _e('Rate', 'wpabstracts'); ?></a> <?php _e('the plugin 5 stars on WordPress.org', 'wpabstracts'); ?>.</p>
                        <p><a href="http://www.facebook.com/wpabstracts" target="_blank"><?php _e('Like us', 'wpabstracts'); ?></a> <?php _e('on Facebook', 'wpabstracts'); ?>. </p>
                    </div>
		</div>
            </div>
            <div id="post-body">
                <div id="post-body-content">
                    <div class="postarea">
                        <h2><?php _e('Get setup and running in three simple steps', 'wpabstracts'); ?></h2>
                        <div class="steps"><img src="<?php echo plugins_url('images/one.png', __FILE__); ?>" />
                            <p><?php _e('Create an event. This may be the name of a conference or anything relating to the submission of abstracts.', 'wpabstracts'); ?></p>
                        </div>
			    <div class="steps"><img src="<?php echo plugins_url('images/two.png', __FILE__);?>" />
                                <p><?php _e('Visit the settings page and choose your preference for the plugin. Also ensure your permanent links are set to post-name or custom.', 'wpabstracts'); ?></p>
                            </div>
			    <div class="steps"><img src="<?php echo plugins_url('images/three.png', __FILE__);?>" />
                                <p><?php _e('Create a dashboard page and add [wpabstracts event_id="EVENT_ID_HERE"] and link this page to your site menu to allow frontend access.', 'wpabstracts'); ?></p>
                            </div>
                    </div>
                </div>
            </div>
    </div>