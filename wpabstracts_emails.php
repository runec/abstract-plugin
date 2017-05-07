<?php
defined('ABSPATH') or die("ERROR: You do not have permission to access this page");

if(!class_exists('WPAbstracts_EmailsTemplates')){
    require_once( WPABSTRACTS_PLUGIN_DIR . 'inc/wpabstracts_classes.php' );
}
if(!class_exists('WPAbstracts_Emailer')){
    require_once( WPABSTRACTS_PLUGIN_DIR . 'inc/wpabstracts_emailer.php' );
}

if(is_admin() && isset($_GET['tab']) && ($_GET["tab"]=="emails")){
    if(isset($_GET['test'])) {
      if($_GET['test'] == 'success') {
        test_notice_success();
      }
      else {
        test_notice_failure();
      }
    }
    if(isset($_GET['task'])){
        $task = sanitize_text_field($_GET['task']);
        $id = intval($_GET['id']);
        switch($task){
            case 'edit':
                wpabstracts_editEmail($id);
                break;
            case 'testemail':
                wpabstracts_sendTestEmail($id);
                break;
            default :
                wpabstracts_showEmails();
                break;
        }
    }else{
        wpabstracts_showEmails();
    }
}

function wpabstracts_editEmail($id) {
    global $wpdb;

    if($_POST){
        $template_name = sanitize_text_field($_POST["template_name"]);
        $from_name = sanitize_text_field($_POST["from_name"]);
        $from_email = sanitize_text_field($_POST["from_email"]);
        $email_subject = sanitize_text_field($_POST["email_subject"]);
        $email_body = wp_kses_post($_POST["email_body"]);
        $wpdb->show_errors();
        $data = array(
            'name' => $template_name, 'subject' => $email_subject, 'message' => $email_body,
            'from_name' => $from_name, 'from_email' => $from_email);
        $where = array( 'ID' => $id);
        $wpdb->update($wpdb->prefix."wpabstracts_emailtemplates", $data, $where);

        wpabstracts_redirect('?page=wpabstracts&tab=emails');

    }else{
        wpabstracts_getEditView('EmailTemplate', $id, 'backend');
    }
}

function wpabstracts_sendTestEmail($tid) {
  $user_id = get_current_user_id();
  $aid = 76; //Test abstract in DB
  $success = false;
  if($user_id !== 0) {
    $emailer = new WPAbstracts_Emailer($aid, $user_id, $tid);
    $success = $emailer->send();

  }
  if($success) {
    wpabstracts_redirect('?page=wpabstracts&tab=emails&test=success');
  }
  else {
    wpabstracts_redirect('?page=wpabstracts&tab=emails&test=failure');
  }
}
function wpabstracts_showEmails(){ ?>
        <form id="showReviews" method="get">
            <input type="hidden" name="page" value="wpabstracts" />
            <input type="hidden" name="tab" value="emails" />
            <?php
            $showEmails = new WPAbstracts_EmailsTemplates();
            $showEmails->prepare_items();
            $showEmails->display(); ?>
            </form>
    <?php
}

function test_notice_success() {
  test_notice(true);
}
function test_notice_failure() {
  test_notice(false);
}

function test_notice($success) {
  if($success) {
    ?>
    <div class="notice notice-success is-dismissible">
        <p><?php _e( 'A test email was sent to your email', 'wpabstracts' ); ?></p>
    </div>
    <?php
  }
  else {
    ?>
    <div class="notice notice-error is-dismissible">
        <p><?php _e( 'A test email could not be sent to your email', 'wpabstracts' ); ?></p>
    </div>
    <?php
  }
}
