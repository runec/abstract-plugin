<?php
defined('ABSPATH') or die("ERROR: You do not have permission to access this page");

if(!class_exists('WPAbstract_Abstracts_Table')){
    require_once( WPABSTRACTS_PLUGIN_DIR . 'inc/wpabstracts_classes.php' );
}

if(is_admin() && isset($_GET['tab']) && ($_GET["tab"]=="reviews")){
    if(isset($_GET['task'])){
        $task = sanitize_text_field($_GET['task']);
        $id = intval($_GET['id']);
        switch($task){
            case 'new':
                wpabstracts_addReview($id);
                break;
            case 'edit':
                wpabstracts_editReview($id);
                break;
            case 'view':
                wpabstracts_viewReviews($id);
                break;
            case 'delete':
                wpabstracts_deleteReview($id);
            default :
                wpabstracts_showReviews();
                break;
        }
    }else{
        wpabstracts_showReviews();
    }
}

function wpabstracts_addReview($abstract_id) {
    global $wpdb;
    if($_POST){
         if(is_super_admin()){
                $redirect = '?page=wpabstracts&tab=reviews';
            }
            else{
                $redirect = '?dashboard';
            }
       $abstract_id = intval($abstract_id);
       $event_id = wpabstracts_getAbstracts('abstract_id', $abstract_id, ARRAY_A)[0]->event;
       $user_id = get_current_user_id();
       $abs_status = $_POST['abs_status'];
       $abs_relevance = $_POST['abs_relevance'];
       $abs_quality = $_POST['abs_quality'];
       $abs_comments = wp_kses_post($_POST['abs_comments']);
       $abs_recommendation = $_POST['abs_recommendation'];
       $review_time = current_time( 'mysql' );
       $wpdb->show_errors();
       $wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."wpabstracts_reviews (abstract_id, event_id, user_id, status, relevance, quality, comments, recommendation, review_date) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s)",
                                    $abstract_id, $event_id, $user_id, $abs_status, $abs_relevance, $abs_quality, $abs_comments, $abs_recommendation, $review_time));
       wpabstracts_redirect($redirect);
    }
    else{
        $focus = is_admin() ? 'backend' : 'frontend';
        wpabstracts_getAddView('Review', $abstract_id, $focus);
    }
 }

function wpabstracts_editReview($id) {
    global $wpdb;
    if($_POST){
        if(is_super_admin()){
            $redirect = '?page=wpabstracts&tab=reviews';
        }
        else{
            $redirect = '?dashboard';
        }
        $abs_status = $_POST['abs_status'];
        $abs_relevance = $_POST['abs_relevance'];
        $abs_quality = $_POST['abs_quality'];
        $abs_comments = $_POST['abs_comments'];
        $abs_recommendation = $_POST['abs_recommendation'];
        $review_date = current_time( 'mysql' );
        $wpdb->show_errors();
        $wpdb->query("UPDATE ".$wpdb->prefix."wpabstracts_reviews
                      SET status = '$abs_status', relevance = '$abs_relevance', quality = '$abs_quality', comments = '$abs_comments', recommendation = '$abs_recommendation', review_date = '$review_date'
                      WHERE review_id = $id");

        wpabstracts_redirect($redirect);
    }
    else{
        $focus = is_admin() ? 'backend' : 'frontend';
        wpabstracts_getEditView("Review", $id, $focus);
    }
 }

function wpabstracts_deleteReview($id) {
    global $wpdb;
    $wpdb->show_errors();
    $wpdb->query("delete from ".$wpdb->prefix."wpabstracts_reviews where review_id=".$id);
    wpabstracts_showMessage("Review $id successfully deleted");
}

function wpabstracts_viewReviews($id){ ?>
    <h3>
        <?php _e('Reviews for Abstract ID', 'wpabstracts'); ?>: <?php echo $id; ?>   <a href="?page=wpabstracts&tab=reviews&task=new&id=<?php echo $id; ?>" class="button-primary" /><?php _e('Add New', 'wpabstracts'); ?></a>
        <a href="?page=wpabstracts&tab=reviews" class="button-primary" /><?php _e('All Reviews', 'wpabstracts'); ?></a>
                </h3>
        <form id="viewReviews" method="get">
            <input type="hidden" name="page" value="wpabstracts" />
            <input type="hidden" name="tab" value="reviews" />
            <?php
            $viewReviews = new WPAbstract_singleItem_Reviews_Table($id);
            $viewReviews->prepare_items();
            $viewReviews->display(); ?>
            </form>
    <?php
}

function wpabstracts_showReviews(){ ?>
        <form id="showReviews" method="get">
            <input type="hidden" name="page" value="wpabstracts" />
            <input type="hidden" name="tab" value="reviews" />
            <?php
            $showReviews = new WPAbstract_Reviews_Table();
            $showReviews ->prepare_items();
            $showReviews ->display(); ?>
            </form>
    <?php
}
