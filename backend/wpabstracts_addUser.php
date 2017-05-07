<?php
/*
 * HTML to add Events
 */
?>
<div class="metabox-holder has-right-sidebar">
    <div class="inner-sidebar">
        <div class="postbox">
            <div class="handlediv" title="<?php _e('View', 'wpabstracts');?>"><br /></div>
            <h3><?php _e('Instructions','wpabstracts');?></h3>
            <div class="inside">
                <p><?php _e('1. Enter the user\'s name, username, temporary password and email address.
                    Then select the level and submit. ','wpabstracts');?><br /><?php _e('2. Email the user his/her user name
                    and temporary password and have them update their password after the first login.', 'wpabstracts'); ?><br /><br />
<?php _e('<strong>Authors</strong> are only allowed to submit abstracts from the frontend.','wpabstracts');?><br />
                    <?php _e('<strong>Reviewers</strong> are allowed backend access to review, approve or reject abstracts.','wpabstracts');?><br /><br />
                    <?php _e('You may also manage users from the default WordPress users tab. Authors are equivalent to subscribers
                    while reviewers are editors.','wpabstracts');?>
                </p>
            </div>
        </div>
    </div>
    <div id="post-body">
        <div id="post-body-content">
            <div class="postarea">
                <form method="post" enctype="multipart/form-data" id="new_user">
                    <table class="widefat" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="row-title">
                        <h3><?php _e('User Information','wpabstracts');?></h3>
                    </th>
                                <th class="row-title"></th>
                            </tr>
                        </thead>
                        <tr>
                            <td><?php _e('First Name','wpabstracts');?></td>
                            <td><input name="first_name" type="text" id="first_name" value=""/>
                                <span id="firstname_error" class="description" style="display: none;"> <?php _e('Required','wpabstracts');?></span></td>
                        </tr>
                        <tr>
                            <td><?php _e('Last Name','wpabstracts');?></td>
                            <td><input name="last_name" type="text" id="last_name" value=""/>
                                <span id="lastname_error" class="description" style="display: none;"> <?php _e('Required','wpabstracts');?></span></td>
                        </tr>
                        <tr>
                            <td><?php _e('Username','wpabstracts');?></td>
                            <td><input name="username" type="text" id="username" value=""/>
                                <span id="username_error" class="description form-invalid" style="display: none;"> <?php _e('Required','wpabstracts');?></span></td>
                        </tr>
                        <tr>
                            <td><?php _e('Password','wpabstracts');?></td>
                            <td><input name="password" type="password" id="password" value=""/>
                                <span id="password_error" class="description" style="display: none;"> <?php _e('Required','wpabstracts');?></span></td>
                        </tr>
                        <tr>
                            <td><?php _e('Email', 'wpabstracts');?></td>
                            <td><input name="email" type="text" id="email" value=""/>
                                <span id="email_error" class="description" style="display: none;"> <?php _e('Required', 'wpabstracts'); ?></span></td>
                        </tr>
                        <tr>
                            <td><?php _e('User Level', 'wpabstracts');?></td>
                            <td>
                                <select name="user_level" id="user_level">
                                    <option selected value="" selected="disabled" style="display:none"><?php _e('User Role','wpabstracts');?></option>
                                    <option value="author"><?php _e('Author','wpabstracts');?></option>
                                    <option value="reviewer"><?php _e('Reviewer','wpabstracts');?></option>
                                </select>
                                <span id="userlevel_error" class="description" style="display: none;"> <?php _e('Required', 'wpabstracts'); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <div style="float: right;">
                                    <h2><a onclick="wpabstracts_validateUser();" class="button button-primary button-large"><?php _e('Create User', 'wpabstracts');?></a></h2>
                                </div>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>

