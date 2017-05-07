<?php
defined('ABSPATH') or die("ERROR: You do not have permission to access this page");
include_once(WPABSTRACTS_PLUGIN_DIR . 'wpabstracts_abstracts.php');
include_once(WPABSTRACTS_PLUGIN_DIR . 'wpabstracts_reviews.php');

$user = wp_get_current_user();

if(!is_user_logged_in()) {
  ?>

  <?php
  if(isset($_GET['login']) && (strpos($_GET['login'],'failed') !== false || strpos($_GET['login'],'empty') !== false) ) {
    ?> <p style='color:red'> <?php
    _e('Wrong username or password', 'wpabstracts');

    ?> </p> <?php
  }
  ?>

  <?php
  if(isset($_GET['login']) && strpos($_GET['login'],'reset') !== false ) {
    ?> <p style='color:blue'> <?php
    _e('Password reset requested. You should receive an email shortly', 'wpabstracts');

    ?> </p> <?php
  }
  ?>

  <p><?php _e('Please login for your conference participation', 'wpabstracts') ?></p>
  <?php
  wp_login_form();
  ?>
  <a href="<?php echo wp_lostpassword_url( get_permalink().'?login=reset' ); ?>" title="<?php _e('Lost password?', 'wpabstracts') ?>"><?php _e('Lost password?', 'wpabstracts') ?></a>
  <?php
}
else if($user->roles[0] == 'administrator'){
    _e("You're an Administrator. Please use the WordPress admin area to manage abstracts.", 'wpabstracts');
    return;
} else {
    $id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
    $userID = get_current_user_id();
    $task = isset($_GET["task"]) ? sanitize_text_field($_GET["task"]) : 'submit';
    switch($user->roles[0]){
        case 'abstract-reviewer':
            wpabstracts_dashboard_header($user);
            if(isset($_GET["id"])){
                $id = intval($_GET["id"]);
            }
            //if($task == "submit"){
                //wpabstracts_addAbstract(); //Disabled for now
            //}
            if ($task == "editabstract"){
                wpabstracts_editAbstract($id);
            }
            else if ($task == "review"){
                wpabstracts_addReview($id);
            }
            else if ($task == "editreview"){
                wpabstracts_editReview($id);
            }
            else if ($task == "delete"){
                wpabstracts_deleteReview($id, true);
                wpabstracts_show_reviewer_dashboard($user);
            }else{
                wpabstracts_show_reviewer_dashboard($user);
            }
            break;
        default:
            if( $task == "submit" ){
                wpabstracts_addAbstract($event_id);
            }
            else if($task == "done" ) {
                echo _e('Thanks for your submission');
            }
            /*
            else if ($task =="edit" ){
                wpabstracts_editAbstract($_GET["id"]);
            }
            else if ( $task =="delete" ){
                wpabstracts_deleteAbstract(intval($_GET['id']), true );
                wpabstracts_show_author_dashboard($user);
            }else{
                wpabstracts_show_author_dashboard($user);
            }
            */
            break;
        }
  }
/*
}else{ print_r(wp_get_current_user());?>
      <p><?php _e('Please login for your conference participation', 'wpabstracts') ?></p>
    <?php
        wp_login_form();
        if(get_option('users_can_register')) { ?>
        <p><?php _e('Need an account?', 'wpabstracts'); ?> <a href="<?php echo wp_registration_url(); ?>"><?php _e('Create an Account', 'wpabstracts'); ?></a></p>
    <?php
    }
}
*/
function wpabstracts_dashboard_header($user) { ?>
    <nav class="wpabstracts navbar navbar-default">
        <div class="wpabstracts container-fluid">
            <div class="wpabstracts navbar-header">
              <button type="button" class="wpabstracts navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="wpabstracts sr-only">Toggle navigation</span>
                <span class="wpabstracts icon-bar"></span>
                <span class="wpabstracts icon-bar"></span>
                <span class="wpabstracts icon-bar"></span>
              </button>
            </div>
            <div class="wpabstracts collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <?php if ($user->roles[0] != 'abstract-reviewer') { ?>
                    <ul class="wpabstracts nav navbar-nav">
                      <li>
                          <a href="?task=submit"><span class="wpabstracts glyphicon glyphicon-plus" aria-hidden="true"></span> <?php _e('New Abstract','wpabstracts');?></a>
                      </li>
                    </ul>
                <?php } else if (get_option('wpabstracts_reviewer_submit') == "Yes"){ ?>
                <ul class="wpabstracts nav navbar-nav">
                      <li>
                          <a href="?task=submit"><span class="wpabstracts glyphicon glyphicon-plus" aria-hidden="true"></span> <?php _e('New Abstract','wpabstracts');?></a>
                      </li>
                    </ul>
                <?php } ?>
              <ul class="wpabstracts nav navbar-nav navbar-right">
                <li>
                    <a href="<?php echo wp_logout_url(home_url()); ?>">
                        <span class="wpabstracts glyphicon glyphicon-off" aria-hidden="true"></span> <?php _e('Logout','wpabstracts');?>
                    </a>
                </li>
              </ul>
            </div>
        </div>
    </nav>
    <?php
}

/*
 * Displays author dashboard
 */
function wpabstracts_show_author_dashboard($user) {

    $abstracts = array(); ?>
       <div class="wpabstracts table-responsive">
           <div class="wpabstracts panel panel-default">
            <div class="wpabstracts panel-heading">
                <h6 class="wpabstracts panel-title"><?php _e('My Abstracts', 'wpabstracts'); ?></h6>
            </div>

            <div class="wpabstracts panel-body">
        <table class="wpabstracts table table-hover">
          <thead>
             <tr>
                <th style="width: 5%;"><?php _e('ID','wpabstracts');?></th>
                <th style="width: 20%;"><?php _e('Title','wpabstracts');?></th>
                <th style="width: 15%;"><?php _e('Status','wpabstracts');?></th>
                <th style="width: 15%;"><?php _e('Type','wpabstracts');?></th>
                <th style="width: 20%;"><?php _e('Submitted','wpabstracts');?></th>
                <th style="width: 15%;"><?php _e('Action','wpabstracts');?></th>
            </tr>
        </thead>
        <?php
            if (count($abstracts) == '0') { ?>
            <?php _e("You have NOT submitted any abstracts.", 'wpabstracts') ?>
        <?php
        }
        else{
            foreach($abstracts as $abstract){ ?>
                <tbody>
                <tr>
                    <td><?php echo $abstract->abstract_id; ?></td>
                    <td><?php echo $abstract->title; ?></td>
                    <td><?php _e($abstract->status, 'wpabstracts'); ?></td>
                    <td><?php echo $abstract->presenter_preference; ?></td>
                    <td><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($abstract->submit_date)); ?></td>
                <?php
                    if ($abstract->status == "Pending") { ?>
                        <td><a href="?task=edit&id=<?php echo $abstract->abstract_id; ?>"><?php _e('Edit','wpabstracts');?></a>
                            | <a href='javascript:wpabstracts_delete_abstract(<?php echo $abstract->abstract_id; ?>)'><?php _e('Delete','wpabstracts');?></a></td>
                <?php
                    }else{ ?>
                        <td><?php _e('Closed','wpabstracts');?></td>
                <?php } ?>
                </tr>
                </tbody>
            <?php
            }
    }
    ?>
        </table>
    </div>
    </div>


    <?php
}

/*
 * shows reviewer dashboard
 */
function wpabstracts_show_reviewer_dashboard($user) {
    global $wpdb;
    ?>
    <div class="wpabstracts table-responsive">
        <div class="wpabstracts panel panel-default">
            <div class="wpabstracts panel-heading">
                <h6 class="wpabstracts panel-title"><?php _e('New Abstracts', 'wpabstracts'); ?></h6>
            </div>

            <div class="wpabstracts panel-body">

            <table class="wpabstracts table table-hover">
            <thead>
             <tr>
                     <th style="width: 20%;"><?php _e('Title','wpabstracts');?></th>
                     <?php if(get_option('wpabstracts_blind_review') == 'No'){ ?>
                        <th style="width: 15%;"><?php _e('Author','wpabstracts');?></th>
                     <?php } ?>
                     <th style="width: 20%;"><?php _e('Action', 'wpabstracts'); ?></th>
                 </tr>
            </thead>
         <?php
         $abstracts = array();
                 for($i = 1; $i <= 15; $i++){ // lazy approach, will fix later
                    $sql = $wpdb->prepare("SELECT abstract_id, title, presenter, submit_date
                                            FROM ".$wpdb->prefix."wpabstracts_abstracts AS abstracts
                                            WHERE abstract_id NOT IN
                                                (SELECT abstract_id
                                                 FROM ".$wpdb->prefix."wpabstracts_reviews AS reviews
                                                 WHERE abstracts.abstract_id = reviews.abstract_id
                                                 AND reviews.user_id = $user->ID)
                                            AND reviewer_id".$i. " = %d", $user->ID);
                    $temp = $wpdb->get_results($sql, ARRAY_A);
                    $abstracts = array_merge($abstracts, $temp);
                 }
         if(!($abstracts)){ ?>
            <tbody>
                <tr><td></td>
                    <td><?php _e('You have NO newly assigned abstracts','wpabstracts');?><td>
                    <td></td><td></td><td></td>
                </tr>
            </tbody>
        <?php
        }
        else{
            foreach($abstracts as $abstract){
                $attachments = wpabstracts_getAttachments('abstracts_id', $abstract['abstract_id']); ?>
                <tbody>
                <tr>
                    <td><?php echo $abstract['title']; ?></td>
                    <?php if(get_option('wpabstracts_blind_review') == 'No'){ ?>
                        <td><?php echo $abstract['presenter']; ?></td>
                     <?php } ?>

                    <td>
                        <?php if (get_option('wpabstracts_reviewer_edit') == "Yes") { ?>
                        <a href="?task=editabstract&id=<?php echo $abstract['abstract_id']; ?>"><?php _e('Edit Abstract','wpabstracts');?></a> |
                        <?php } ?>
                        <a href="?task=review&id=<?php echo $abstract['abstract_id']; ?>"><?php _e('Review','wpabstracts');?></a>
                    </td>
                </tr>
           <?php
            }
        }
        ?>
            </tbody>
        </table>
            </div>

        </div>

        <div class="wpabstracts panel panel-default">

            <div class="wpabstracts panel-heading">
                <h6 class="wpabstracts panel-title"><?php _e('Reviews','wpabstracts');?></H6>
            </div>

            <div class="wpabstracts panel-body">

            <table class="wpabstracts table table-hover">
            <thead>
             <tr>
                     <th style="width: 15%;"><?php _e('Title','wpabstracts');?></th>
                     <th style="width: 25%;"><?php _e('Comments','wpabstracts');?></th>
                     <th style="width: 8%;"><?php _e('Status','wpabstracts');?></th>
                     <th style="width: 8%;"><?php _e('Relevance','wpabstracts');?></th>
                     <th style="width: 8%;"><?php _e('Quality','wpabstracts');?></th>
                     <th style="width: 10%;"><?php _e('Reviewed','wpabstracts');?></th>
                     <th style="width: 16%;"><?php _e('Action','wpabstracts');?></th>
                 </tr>
            </thead>
         <?php
         $reviews = wpabstracts_getReviews('user_id', $user->ID);
         if(!($reviews)){ ?>
            <tbody>
                <tr><td></td><td></td>
                <td><?php _e('You have no COMPLETED reviews','wpabstracts');?><td>
                <td></td><td></td><td></td><td></td><td></td>
                </tr>
            </tbody>
        <?php
        }
        else{
            foreach($reviews as $review){
                $abstract_title = $wpdb->get_row("SELECT title FROM ".$wpdb->prefix."wpabstracts_abstracts WHERE abstract_id = $review->abstract_id"); ?>
            <tbody>
            <tr>
                <td><?php echo $abstract_title->title; ?></td>
                <td><?php echo $review->comments; ?></td>
                <td><?php _e($review->status, 'wpabstracts'); ?></td>
                <td><?php _e($review->relevance, 'wpabstracts'); ?></td>
                <td><?php _e($review->quality, 'wpabstracts'); ?></td>
                <td><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($review->review_date)); ?></td>
                <td>
                    <a href="?task=editreview&id=<?php echo $review->review_id; ?>"><?php _e('Edit','wpabstracts');?></a> |
                    <a href="javascript:wpabstracts_delete_review(<?php echo $review->review_id; ?>)"><?php _e('Delete','wpabstracts');?></a>
                </td>
            </tr>
        <?php
            }
        } ?>
            </tbody>
        </table>
    </div>
        </div>
    </div>
  <?php
}
