<script type="text/javascript">
    jQuery(function() {
        jQuery( "#abs_event_start" ).datepicker({ dateFormat: "yy-mm-dd" });
        jQuery( "#abs_event_end" ).datepicker({ dateFormat: "yy-mm-dd" });
        jQuery( "#abs_event_deadline" ).datepicker({ dateFormat: "yy-mm-dd" });
    });
</script>
<div class="metabox-holder has-right-sidebar">
        <form method="post" enctype="multipart/form-data" id="abs_event_form">
            <div class="inner-sidebar">
                <div class="misc-pub-section">
                    <input type="button" onclick="wpabstracts_validateEvent();" class="button button-primary button-large save_buttons" value="<?php _e('Save Event', 'wpabstracts');?>">
                </div>
                    <div class="postbox"><!-- Host -->
                        <h3><?php _e('Event Information', 'wpabstracts');?> <span class="wpabstract_form_error" style="display: none;" id="abs_event_host_error"><?php _e('All fields required', 'wpabstracts');?></span></h3>
                        <div class="inside">
                    <table width="100%">
                        <tr>
                            <td><?php _e('Host', 'wpabstracts');?></td>
                            <td><input type="text" name="abs_event_host" id="abs_event_host" ></td>
                        </tr>
                        <tr>
                            <td><?php _e('Location', 'wpabstracts');?></td>
                            <td><input type="text" name="abs_event_address" id="abs_event_address"><span class="wpabstract_form_error" style="display: none;" id="abs_event_address_error"><?php _e('Required field', 'wpabstracts');?></span></td>
                        </tr>
                        <tr>
                            <td><?php _e('Start Date', 'wpabstracts');?></td>

                                <td><input type="text" name="abs_event_start" id="abs_event_start" /><br><span class="wpabstract_form_error" style="display: none;" id="abs_event_start_error"><?php _e('Required field', 'wpabstracts');?></span></td>
                        </tr>
                        <tr>
                            <td><?php _e('End Date', 'wpabstracts');?></td>
                            <td><input type="text" name="abs_event_end" id="abs_event_end" /><br><span class="wpabstract_form_error" style="display: none;" id="abs_event_end_error"><?php _e('Required field', 'wpabstracts');?></span></td>
                    </tr>
                     <tr>
                            <td><?php _e('Deadline', 'wpabstracts');?></td>
                            <td><input type="text" name="abs_event_deadline" id="abs_event_deadline" /><br><span class="wpabstract_form_error" style="display: none;" id="abs_event_deadline_error"><?php _e('Required field', 'wpabstracts');?></span></td>
                    </tr>
                    </table>
                </div>
                </div><!-- End Host -->
                <div class="postbox"> <!-- Sessions -->
                    <h3><?php _e('Sessions', 'wpabstracts'); ?><span class="wpabstract_form_error" style="display: none;" id="abs_session_error"> <?php _e('Required field', 'wpabstracts'); ?></span></h3>
                    <div class="inside">
                <table width="100%" id="sessions_table">
                      <tr>
                        <td><?php _e('Session','wpabstracts');?></td>
                        <td><input type="text" name="sessions[]" id="sessions[]" /></td>
                      </tr>
                </table><br>
                <div class="inner_btns">
                    <a class="button-secondary" href="#add-session" onclick="wpabstracts_add_session();" style="float: left;"><?php _e('add session','wpabstracts');?></a>
                    <a class="button-secondary" href="#delete-session" onclick="wpabstracts_delete_session();" style="float: right;"><?php _e('delete session','wpabstracts');?></a>
                </div>
                </div>
            </div>
                <div class="postbox"> <!-- Topics -->
                    <h3><?php _e('Topics', 'wpabstracts');?><span class="wpabstract_form_error" style="display: none;" id="abs_topic_error"> <?php _e('Required field', 'wpabstracts');?></span></h3>
                    <div class="inside">
                <table width="100%" id="topics_table">
                    <tr>
                        <td><?php _e('Topic', 'wpabstracts');?></td>
                        <td><input type="text" name="topics[]" id="topics[]"  /></td>
                    </tr>

                </table><br>
                <div class="inner_btns">
                    <a class="button-secondary" href="#add-topic" onclick="wpabstracts_add_topic();" style="float: left;"><?php _e('add topic', 'wpabstracts');?></a>
                    <a class="button-secondary" href="#delete-topic" onclick="wpabstracts_delete_topic();" style="float: right;"><?php _e('delete topic', 'wpabstracts');?></a>
                </div>
                </div>
            </div> <!-- End Topics -->
            </div> <!-- .inner-sidebar -->
            <div id="post-body">
            <div id="post-body-content">
                    <div id="titlediv">
                        <div id="titlewrap">
                            <label class="screen-reader-text" id="title-prompt-text" for="title"><?php _e('Enter event name here. Eg. ICS Conference 2014', 'wpabstracts');?></label>
                            <input type="text" name="abs_event_name" size="22" value="" id="title" autocomplete="off" />
                        </div>
                    </div>
                    <div class="postarea">
                        <span class="wpabstract_form_error" style="display: none;" id="abs_content_error"> <?php _e('Required field', 'wpabstracts');?></span>
                        <?php wp_editor('', 'abs_event_desc', true, true); ?>
                    <br>
                    </div>
                </div>
            </div>
        </form>
        </div>
