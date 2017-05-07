<?php
defined('ABSPATH') or die("ERROR: You do not have permission to access this page");

if(!class_exists('WPAbstract_Abstracts_Table')){
    require_once( WPABSTRACTS_PLUGIN_DIR . 'inc/wpabstracts_classes.php' );
}
if(!class_exists('WPAbstracts_Emailer')){
    require_once( WPABSTRACTS_PLUGIN_DIR . 'inc/wpabstracts_emailer.php' );
}

if(is_admin()) { //&& isset($_GET['tab']) && $_GET["tab"]=="abstracts"){
    if(isset($_GET["task"])){
        $task = sanitize_text_field($_GET["task"]);
        switch($task){
            case 'new':
                wpabstracts_addAbstract();
                break;
            case 'edit':
                wpabstracts_editAbstract(intval($_GET['id']));
                break;
            case 'approve':
                wpabstracts_changeStatus(intval($_GET['id']),"Approved");
                wpabstracts_showAbstracts();
                break;
            case 'pending':
                wpabstracts_changeStatus(intval($_GET['id']),"Pending");
                wpabstracts_showAbstracts();
                break;
            case 'reject':
                wpabstracts_changeStatus(intval($_GET['id']),"Rejected");
                wpabstracts_showAbstracts();
                break;
            case 'assign':
                if(isset($_POST['rid']) && isset($_POST['aid'])){
                    wpabstracts_assignReviewer(intval($_POST['aid']), $_POST['rid']);
                    wpabstracts_showAbstracts();
                }
                break;
            case 'delete':
                if(current_user_can(WPABSTRACTS_ACCESS_LEVEL)){
                    wpabstracts_deleteAbstract(intval($_GET['id']), true );
                    wpabstracts_showAbstracts();
                }else{
                    echo "You do not have permission to delete";
                }
                break;
            case 'multiassign':
              if(isset($_POST['abstractids']) && isset($_POST['rid'])) {
                  wpabstracts_assignMultiReviewer($_POST['abstractids'],$_POST['rid']);
                  wpabstracts_showAbstracts();
              }
              break;
            default :
                wpabstracts_showAbstracts();

              break;

        }

    }else{
      if(!isset($_POST["action"])) {
        wpabstracts_showAbstracts();
      }
    }
}
/*
 * Submission form for new abstracts
 */
function wpabstracts_addAbstract($event_id = null) {
    if($_POST){
        if(is_super_admin()){
            $redirect = '?page=wpabstracts&tab=abstracts';
        }else{
            $redirect = '?task=done';
        }
        // inserts submission to DB
        $id = wpabstracts_manageAbstracts(0, $_POST, 'insert');

        /*
        if(!empty($_POST['image'])) {
          wpabstracts_manageImageUploaded($id, $_POST['image'], 'insert');
        }
        else if(!empty($_POST['imagedata'])) {
          wpabstracts_manageProfileImage($id, $_POST['imagedata'], 'insert');
        }
        */

        // sends author email if option is enabled
        if(get_option('wpabstracts_email_author')=="Yes"){

            $emailer = new WPAbstracts_Emailer($id, NULL, $template = 1);
            $emailer->send();
        }
         // sends system admin an email if enabled
        if(get_option('wpabstracts_email_admin')=="Yes"){
            $super_admins = get_users( array('role'=>'administrator', 'fields'=>'ID') );
            foreach ($super_admins as $super_admin_id) {
                $emailer = new WPAbstracts_Emailer($id, $super_admin_id, $template = 3);
                $emailer->send();
            }
        }
        /*
         * Process attachments
         */
        if($_FILES){
            wpabstracts_manageAttachments($id, $_FILES, 'insert');
        }
        wpabstracts_redirect($redirect);
    }
    else {
        $focus = is_admin() ? 'backend' : 'frontend';
        wpabstracts_getAddView('Abstract', $event_id, $focus);
    }
}

/*
  * edit form for existing abstracts
  * @id id of abstract to be edited
  */
 function wpabstracts_editAbstract($id) {
    if(is_super_admin()){
        $tab = '?page=wpabstracts&tab=abstracts';
    }
    else{
        $tab = '?dashboard';
    }
    if ($_POST) {
        wpabstracts_manageAbstracts($id, $_POST, 'update');
        if($_FILES){
            wpabstracts_manageAttachments($id, $_FILES, 'insert');
        }
        wpabstracts_redirect($tab);
    }else{
        $focus = is_admin() ? 'backend' : 'frontend';
        wpabstracts_getEditView('Abstract', $id, $focus);
    }
 }

/*
 * Returns mini form for the dialog box when assigning reviewers (AJAX)
 */
function wpabstracts_getReviewers(){
    $users = get_users();
    $id = intval($_POST['aid']);
    $reviewers = array();
    $unassigned = __("-- Not Assigned --",'wpabstracts');
    foreach($users as $user){
        //if ($user->roles[0] == 'administrator' OR $user->roles[0] == 'editor'){
        if($user->roles[0] == 'abstract-reviewer') {
            $reviewers[] = $user;
        }
    }

    // if a reviewer exist, display reviewer, else not assigned or deleted
    $abstract = wpabstracts_getAbstracts('abstract_id', $id, null);

    $assignees = [];

    //Assumme 15 reviewers max
    for($i = 0; $i < 15; $i++) {
      $prop = 'reviewer_id'.($i+1);
      if($abstract[0]->$prop){
          $assignees[$i] = get_userdata($abstract[0]->$prop);
      }else{
          $assignees[$i] = (object) array('ID'=>'', 'display_name'=>$unassgined);
      }
    }

    ?>

    <form method="post" id="assign_form" action="?page=wpabstracts&tab=abstracts&task=assign">
        <table width="100%">
            <tr>
                <th></th>
                <th><?php _e('Select Reviewer', 'wpabstracts');?></th>
                <th><?php _e('Send Email', 'wpabstracts');?></th>
            </tr>
    <?php
    //Output assignee rows
    for($i = 0; $i < count($assignees); $i++) {
      ?>

      <tr>
        <td><?php _e('Reviewer', 'wpabstracts'); ?> #<?php echo($i+1) ?></td>
        <td><select name="rid[]">
            <option value=""><?php echo $unassigned; ?></option>

                <?php
                if(is_super_admin()){
                    foreach($reviewers as $reviewer){
                      $selected = $assignees[$i]->ID == $reviewer->ID ? 'selected' : '';
                      ?>
                        <option <?php echo $selected ?> value="<?php echo $reviewer->ID;?>"><?php echo $reviewer->display_name;?></option>
                    <?php
                    }
                }
                ?>
        </select>
      </td>
          </td>
          <td><input type="checkbox" class="wpabs_email" name="wpabstracts_email_reviewer<?php echo($i+1)?>" value="true" <?php checked(get_option('wpabstracts_email_reviewer'), "Yes"); ?>></td>
      </tr>

      <?php
    }
    ?>
    </table>

    <input type="hidden" id="aid" name="aid" value="<?php echo $id; ?>">
            <input type="Submit" id="assignBtn" class="button button-primary button-large" value="<?php _e('Assign Reviewers', 'wpabstracts');?>" />
    </form>
       <?php
}

/*
 * Returns mini form for the dialog box when assigning reviewers as bulk action (AJAX)
 */
function wpabstracts_getMultiReviewers(){
    $users = get_users();
    $ids = array_map('intval', $_POST['ids']);
    $reviewers = array();
    $unassigned = __("-- Not Assigned --",'wpabstracts');
    foreach($users as $user){
        //if ($user->roles[0] == 'administrator' OR $user->roles[0] == 'editor'){
        if($user->roles[0] == 'abstract-reviewer') {
            $reviewers[] = $user;
        }
    }
    $assignees = [];

    //Assumme 15 reviewers max
    for($i = 0; $i < 15; $i++) {
      $assignees[$i] = $unassigned;
    }
    //Output form and table headers
    ?>
    <form method="post" id="assign_form" action="?page=wpabstracts&tab=abstracts&task=multiassign">
        <table width="100%">
            <tr>
                <th></th>
                <th><?php _e('Select Reviewer', 'wpabstracts');?></th>
                <th><?php _e('Send Email', 'wpabstracts');?></th>
            </tr>
    <?php
    //Output assignee rows
    for($i = 0; $i < count($assignees); $i++) {
      ?>

      <tr>
              <td><?php _e('Reviewer', 'wpabstracts'); ?> #<?php echo($i+1)?></td>
              <td><select name="rid[]">
                  <option value=""><?php echo $unassigned; ?></option>
                      <?php
                          foreach($reviewers as $reviewer){ ?>
                              <option value="<?php echo $reviewer->ID;?>"><?php echo $reviewer->display_name;?></option>
                          <?php
                          }
                      ?>
              </select>
          </td>
          <td><input type="checkbox" class="wpabs_email" name="wpabstracts_email_reviewer<?php echo($i+1)?>" value="true" <?php checked(get_option('wpabstracts_email_reviewer'), "Yes"); ?>></td>
      </tr>

      <?php
    }
    ?>
    </table>

    <?php
    foreach($ids as $id) {
      ?>
      <input type="hidden" name="abstractids[]" value="<?php echo $id; ?>">
      <?php
    }
     ?>
            <input type="Submit" id="assignBtn" class="button button-primary button-large" value="<?php _e('Assign Reviewers', 'wpabstracts');?>" />
    </form>
       <?php
}

/*
* Assigns a review to an abstract in the DB
* @abstract_id the abstract to be assigned
* @reviewers an array of reviewers being assigned
* @wpdb WP DB object
*/
function wpabstracts_assignReviewer($abstract_id, $reviewers){
    global $wpdb;
    $wpdb->show_errors();
    foreach((Array)$reviewers AS $key => $reviewer_id){
        $key += 1;
        if(is_numeric($reviewer_id)){
            $wpdb->query("UPDATE ".$wpdb->prefix."wpabstracts_abstracts
                      SET reviewer_id" .$key. " = " . $reviewer_id . "
                      WHERE abstract_id = " . $abstract_id);
        }else{ // remove
            $wpdb->query("UPDATE ".$wpdb->prefix."wpabstracts_abstracts
                      SET reviewer_id" .$key. " = ''
                      WHERE abstract_id = " . $abstract_id);
        }
        //send email
        if(isset($_POST['wpabstracts_email_reviewer'.$key])){
            $emailer = new WPAbstracts_Emailer($abstract_id, $reviewer_id, $template = 2);
            $emailer->send();
        }
    }
}

/*
* Assigns reviewers to multiple abstracts in the DB
* @abstract_id array of abstracts to be assigned to
* @reviewers an array of reviewers being assigned
* @wpdb WP DB object
*/
function wpabstracts_assignMultiReviewer($abstract_ids, $reviewers){
    global $wpdb;
    $abstract_ids = (Array)$abstract_ids;
    $reviewers = (Array)$reviewers;
    $wpdb->show_errors();
    foreach($abstract_ids as $abkey => $abstract_id) {
      foreach($reviewers AS $key => $reviewer_id){
          $key += 1;
          if(is_numeric($reviewer_id)){
              $wpdb->query("UPDATE ".$wpdb->prefix."wpabstracts_abstracts
                        SET reviewer_id" .$key. " = " . $reviewer_id . "
                        WHERE abstract_id = " . $abstract_id);
          }else{ // remove
              $wpdb->query("UPDATE ".$wpdb->prefix."wpabstracts_abstracts
                        SET reviewer_id" .$key. " = ''
                        WHERE abstract_id = " . $abstract_id);
          }

      }
    }

    foreach($reviewers AS $key => $reviewer_id){
      //send email
      if(isset($_POST['wpabstracts_email_reviewer'.($key+1)]) && count($abstract_ids) > 0){
          $emailer = new WPAbstracts_Emailer($abstract_ids, $reviewer_id, $template = 7);
          $emailer->send();
      }
    }
}

/*
 * Updates the status of an abstract
 * @id the abstract id to update
 * @status the status being inserted
 */
function wpabstracts_changeStatus($id, $status){
    global $wpdb;

    $abstract = wpabstracts_getAbstracts('abstract_id', $id, 'ARRAY_N')[0];

    if($abstract->status != $status) {
      $wpdb->show_errors();
      $wpdb->query("UPDATE ".$wpdb->prefix."wpabstracts_abstracts
                       SET status = '$status'
                       WHERE abstract_id = $id");
      if(get_option('wpabstracts_status_notification') =='Yes' && $status != 'Pending'){
        $templateId = null;
        if($status == "Approved") {
          $templateId = 4;
        } else if($status == "Rejected") {
          $templateId = 5;
        }
        $emailer = new WPAbstracts_Emailer($id, NULL, $template = $templateId);
        $emailer->send();
      }
    }
    else {
      var_dump("lol!");
    }


}
/*
  * deletes abstracts by id
  * @id the abstract id to delete from the DB
  * @message boolean value to show confirmation message or not.
  * PS. single deletions show messages while bulk deletions don't.
  */
function wpabstracts_deleteAbstract($id, $message){
    wpabstracts_manageAbstracts($id, NULL, 'delete');
    if($message){
        wpabstracts_showMessage("Abstract ID ". $id . " was successfully deleted");
    }
}

/*
 * displays abstracts submissions using WP_TABLE_LIST class
 */
function wpabstracts_showAbstracts(){?>
    <form id="showsAbstracts" method="get">
    <input type="hidden" name="page" value="wpabstracts" />
    <input type="hidden" name="tab" value="abstracts" />
       <?php
            $showAbtracts = new WPAbstract_Abstracts_Table();
            $showAbtracts ->prepare_items();
            $showAbtracts ->display();
        ?>
</form>
    <?php
}
