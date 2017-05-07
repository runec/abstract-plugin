<?php
/*
Plugin Name: WP Abstracts Pro
Plugin URI: http://www.wpabstracts.com
Description: Use WP Abstracts Pro to allow and manage Abstracts and Manuscripts submissions on your site. </br>Manage authors, reviews, events and more
Version: 1.1.2
Author: Kevon Adonis
Author URI: http://www.kevonadonis.com
*/
defined('ABSPATH') or die("ERROR: You do not have permission to access this page");
define('WPABSTRACTS_ACCESS_LEVEL', 'manage_options');
define('WPABSTRACTS_PLUGIN_DIR', dirname(__FILE__).'/' );
define('WPABSTRACTS_VERSION', "1.1.2");
define('WPABSTRACTS_PROFILE_IMAGE_DIR', wp_upload_dir()['basedir'] . '/wpabstracts/');
define('WPABSTRACTS_PROFILE_IMAGE_URL', wp_upload_dir()['baseurl'] . '/wpabstracts/');


add_action('init', 'wpabstracts_init');
add_action('admin_menu', 'wpabstracts_register_menu');
add_action('admin_init', 'wpabstracts_disable_dashboard');
add_action('wp_ajax_getreviewers', 'wpabstracts_getreviewers_ajax');
add_action('wp_ajax_managereviews', 'wpabstracts_checkreviews_ajax');
add_action('wp_ajax_loadtopics', 'wpabstracts_loadtopics_ajax');
add_action('wp_ajax_loadsessions', 'wpabstracts_loadsessions_ajax');
add_action('wp_ajax_getmultireviewers', 'wpabstracts_getmultireviewers_ajax');
add_action('wp_ajax_multisetsession', 'wpabstracts_multisetsession_ajax');
add_filter('show_admin_bar', 'wpabstracts_disable_adminbar');
add_filter('plugin_row_meta', 'wpabstracts_plugin_links', 10, 2);
//add_filter('pre_set_site_transient_update_plugins', 'wpabsracts_insert_transient'); //Auto updates
add_filter('plugins_api', 'wpabstracts_api_call', 10, 3);
add_action('plugins_loaded', 'wpabstracts_db_check');
add_shortcode('wpabstracts', 'wpabstracts_dashboard_shortcode');
add_shortcode('wpabstracts_author', 'wpabstracts_author_shortcode');
add_shortcode('wpabstracts_reviewer', 'wpabstracts_reviewer_shortcode');
add_shortcode('wpabstracts_list_abstracts', 'wpabstracts_list_abstracts_shortcode');
register_activation_hook(__FILE__,'wpabstracts_db_check');
register_activation_hook(__FILE__,'wpabstracts_install');
register_deactivation_hook(__FILE__, 'wpabstracts_deactivation');
add_action( 'wp_login_failed', 'login_failed' );
add_filter( 'authenticate', 'verify_username_password', 1, 3);


//Remove wp editor buttons
//function my_format_TinyMCE( $in ) {
//    $in['toolbar1'] = '';
//    $in['toolbar2'] = '';
//    $in['toolbar'] = false;
//    return $in;
//}
//add_filter( 'tiny_mce_before_init', 'my_format_TinyMCE' );

/*
 * only load scripts and style on plugin page
 */
global $pagenow;
if ('admin.php' == $pagenow && isset($_GET['page']) && ($_GET['page'] == 'wpabstracts')){
    add_action('admin_head', 'wpabstracts_admin_js');
    add_action('admin_head', 'wpabstracts_admin_css');
    add_action('admin_init', 'wpabstracts_editor_admin_init');
}

function login_failed() {
	$referrer = $_SERVER['HTTP_REFERER'];  // where did the post submission come from?
	// if there's a valid referrer, and it's not the default log-in screen
	if ( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') && !strstr($referrer, 'lukmigind') ) {
	 	wp_redirect( $referrer . '?login=failed' );  // let's append some information (login=failed) to the URL for the theme to use
		exit;
	}
}

function verify_username_password( $user, $username, $password ) {
	$referrer = $_SERVER['HTTP_REFERER'];  // where did the post submission come from?
  if( $username == "" || $password == "" ) {
		if ( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') && !strstr($referrer, 'lostpassword') && !strstr($referrer, 'lukmigind')) {
			wp_redirect( $referrer . '?login=empty' );  // let's append some information (login=failed) to the URL for the theme to use
    	exit;
		}
  }
}

function my_media_lib_uploader_enqueue() {
  wp_enqueue_media();
  wp_register_script( 'mediaLibUpload.js', plugins_url( 'js/mediaLibUpload.js' , __FILE__ ), array('jquery') );
  wp_enqueue_script( 'mediaLibUpload.js' );
}
add_action('admin_enqueue_scripts', 'my_media_lib_uploader_enqueue');

/*
 * Allows front-end ajaxs calls if settings to disable admin access is enabled
 */
if(isset($_REQUEST['action']) && $_REQUEST['action']=='loadtopics'):
        do_action( 'wp_ajax_' . $_REQUEST['action'] );
endif;

// remove wp version param from any enqueued scripts
function vc_remove_wp_ver_css_js( $src ) {
    if ( strpos( $src, 'ver=' . get_bloginfo( 'version' ) ) )
        $src = remove_query_arg( 'ver', $src );
    return $src . "?final10";
}
add_filter( 'style_loader_src', 'vc_remove_wp_ver_css_js', 9999 );
add_filter( 'script_loader_src', 'vc_remove_wp_ver_css_js', 9999 );

/**
 * Load language
 */
function wpabstracts_init() {
    load_plugin_textdomain('wpabstracts', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    include( WPABSTRACTS_PLUGIN_DIR . 'inc/wpabstracts_functions.php' );
    include( WPABSTRACTS_PLUGIN_DIR . 'inc/wpabstracts_downloads.php' );
    //include( WPABSTRACTS_PLUGIN_DIR . 'inc/wpabstracts_filters.php' );

}

/*
 * Adds menu to admin panel
 */
function wpabstracts_register_menu(){
    add_menu_page( 'Abstracts & Manuscripts Manager', __('WP Abstracts Pro', 'wpabstracts'), 'manage_options', 'wpabstracts', 'wpabstracts_dashboard', plugins_url( 'images/icon.png', __FILE__), 99 );
    //add_submenu_page( 'wpabstracts',  __('Abstracts & Manuscripts Summary', 'wpabstracts'), __('Summary', 'wpabstracts'),'manage_options', 'admin.php?page=wpabstracts&tab=summary' );
    add_submenu_page( 'wpabstracts', __('Manage Submissions', 'wpabstracts'), __('Abstracts', 'wpabstracts'), 'manage_options', 'admin.php?page=wpabstracts&tab=abstracts');
    add_submenu_page( 'wpabstracts', __('Events / Conference', 'wpabstracts'), __('Events', 'wpabstracts'), 'manage_options', 'admin.php?page=wpabstracts&tab=events');
    //add_submenu_page( 'wpabstracts', __('Users', 'wpabstracts'), __('Users', 'wpabstracts'), 'manage_options', 'admin.php?page=wpabstracts&tab=users');
    //add_submenu_page( 'wpabstracts', __('Reports', 'wpabstracts'), __('Reports', 'wpabstracts'), 'manage_options', 'admin.php?page=wpabstracts&tab=reports' );
    add_submenu_page( 'wpabstracts', __('Settings', 'wpabstracts'), __('Settings', 'wpabstracts'), 'manage_options', 'admin.php?page=wpabstracts&tab=settings' );
    add_submenu_page( 'wpabstracts', __('Emails', 'wpabstracts'), __('Emails', 'wpabstracts'), 'manage_options', 'admin.php?page=wpabstracts&tab=emails' );
    add_submenu_page( 'wpabstracts', __('Help', 'wpabstracts'), __('Help', 'wpabstracts'), 'manage_options', 'admin.php?page=wpabstracts&tab=help' );
    remove_submenu_page('wpabstracts','wpabstracts');
}
/*
 * adds author combo dashboard [wpabstracts]
 */
function wpabstracts_dashboard_shortcode($atts) {
    global $wpdb;
    wpabstracts_frontend_css(); //load css only on dashboard pages
    wpabstracts_frontend_js();  //load js only on dashboard pages
    $args = array('event_id' => 0); // shortcode args with defaults
    $a = shortcode_atts( $args, $atts);
    $event_id = intval($a['event_id']);
    $event = $wpdb->get_var("SELECT COUNT(*) FROM " . $wpdb->prefix."wpabstracts_events WHERE event_id = ".$event_id);
    if(!$event_id || !$event){
        _e('<h3 style="color:red;">Please enter a valid event ID</h3>', 'wpabstracts');
        _e('<p>The shortcode in this page should be similar to [wpabstracts event_id=EventID]</p>', 'wpabstracts');
        return;
    }
    ob_start();
    include('frontend/wpabstracts_dashboard.php');
    $html = ob_get_contents();
    ob_end_clean();
    return $html;
}
/*
 * adds author shortcode [wpabstracts_author]
 */
function wpabstracts_author_shortcode($atts) {
    global $wpdb;
    wpabstracts_frontend_css(); //load css only on dashboard pages
    wpabstracts_frontend_js();  //load js only on dashboard pages
    $args = array('event_id' => 0); // shortcode args with defaults
    $a = shortcode_atts( $args, $atts);
    $event_id = intval($a['event_id']);
    $event = $wpdb->get_var("SELECT COUNT(*) FROM " . $wpdb->prefix."wpabstracts_events WHERE event_id = ".$event_id);
    if(!$event_id || !$event){
        _e('<h3 style="color:red;">Please enter a valid event ID</h3>', 'wpabstracts');
        _e('<p>The shortcode in this page should be similar to [wpabstracts event_id=EventID]</p>', 'wpabstracts');
        return;
    }
    ob_start();
    include_once(WPABSTRACTS_PLUGIN_DIR . 'wpabstracts_abstracts.php');
    wpabstracts_addAbstract($event_id);
    $html = ob_get_contents();
    ob_end_clean();
    return $html;
}
/*
 * adds reviewer shortcode [wpabstracts_reviewer]
 */
function wpabstracts_reviewer_shortcode($atts) {
    global $wpdb;
    wpabstracts_frontend_css(); //load css only on dashboard pages
    wpabstracts_frontend_js();  //load js only on dashboard pages
    $args = array('event_id' => 0); // shortcode args with defaults
    $a = shortcode_atts( $args, $atts);
    $event_id = intval($a['event_id']);
    $event = $wpdb->get_var("SELECT COUNT(*) FROM " . $wpdb->prefix."wpabstracts_events WHERE event_id = ".$event_id);
    if(!$event_id || !$event){
        _e('<h3 style="color:red;">Please enter a valid event ID</h3>', 'wpabstracts');
        _e('<p>The shortcode in this page should be similar to [wpabstracts event_id=EventID]</p>', 'wpabstracts');
        return;
    }
    ob_start();
    include('frontend/wpabstracts_dashboard.php');
    $html = ob_get_contents();
    ob_end_clean();
    return $html;
}

function wpabstracts_list_abstracts_shortcode($atts) {
  global $wpdb;
  wpabstracts_frontend_css();
  wpabstracts_frontend_js();
  $args = array('event_id' => 0); // shortcode args with defaults
  $a = shortcode_atts( $args, $atts);
  $event_id = intval($a['event_id']);
  $event = $wpdb->get_var("SELECT COUNT(*) FROM " . $wpdb->prefix."wpabstracts_events WHERE event_id = ".$event_id);
  if(!$event_id || !$event){
      _e('<h3 style="color:red;">Please enter a valid event ID</h3>', 'wpabstracts');
      _e('<p>The shortcode in this page should be similar to [wpabstracts_list_abstracts event_id=EventID]</p>', 'wpabstracts');
      return;
  }
  ob_start();
  include('frontend/wpabstracts_listAbstracts.php');
  $html = ob_get_contents();
  ob_end_clean();
  return $html;

}
/*
 * Allows user to block authors from accessing WordPress admin dashboard
 */
function wpabstracts_disable_dashboard() {
    if(get_option('wpabstracts_frontend_dashboard') == 'No') {
       if (!current_user_can(WPABSTRACTS_ACCESS_LEVEL) && $_SERVER['DOING_AJAX'] != '/wp-admin/admin-ajax.php'){
	wp_redirect(home_url()); exit;
       }
    }
}

/*
 * Disables the admin bar from front end users after login
 */
function wpabstracts_disable_adminbar() {
    if(get_option('wpabstracts_show_adminbar') == 'Yes'){
	return true;
    }
    return false;
}
/*
 * Adds links under plugin in wordpress plugin manager
 */
function wpabstracts_plugin_links($links, $file) {

    if ($file == plugin_basename(__FILE__)) {
        $links[] = '<a href="http://www.wpabstracts.com/contact/requests/" target="_blank">' . __('Request Customization', 'wpabstracts') . '</a>';
        $links[] = '<a href="http://www.wpabstracts.com/support" target="_blank">' . __('Support', 'wpabstracts') . '</a>';
    }
    return $links;
}
/*
 * Displays the tabs and manages tabs to be displayed
 */
function wpabstracts_dashboard() {
    wpabstracts_admin_header();	//load header
    global $pagenow;
    if ( isset ( $_GET['tab'] ) ) {
        wpabstracts_admin_tabs($_GET['tab']);
    }else {
        wpabstracts_admin_tabs('abstracts');
    }
    if ( $pagenow == 'admin.php' && $_GET['page'] == 'wpabstracts' ){
	if ( isset ( $_GET['tab'] ) ) {
	    $tab = $_GET['tab'];
	}
	else {
            $tab = 'absacts';
        }
        switch ( $tab ){
            case 'summary' :
                include 'wpabstracts_summary.php';
                break;
            case 'abstracts' :
                include 'wpabstracts_abstracts.php';
                break;
            case 'events' :
                include 'wpabstracts_events.php';
                break;
            case 'reviews' :
                include 'wpabstracts_reviews.php';
                break;
            case 'attachments' :
                include 'wpabstracts_attachments.php';
                break;
            case 'users' :
                include 'wpabstracts_users.php';
                break;
             case 'reports' :
                include 'wpabstracts_reports.php';
                break;
	    case 'settings' :
                include 'wpabstracts_settings.php';
                break;
            case 'emails' :
                include 'wpabstracts_emails.php';
                break;
            case 'help' :
                include 'wpabstracts_help.php';
                break;
            default:
                //include 'wpabstracts_summary.php';
                include 'wpabstracts_abstracts.php';
	}
    }

}

function wpabstracts_admin_tabs( $current = 'summary' ) {
      //$tabs = array('summary' => __('Summary', 'wpabstracts'), 'abstracts' => __('Abstracts', 'wpabstracts'), 'events' => __('Events', 'wpabstracts'), 'users' => __('Users', 'wpabstracts'), 'reports' => __('Reports','wpabstracts'), 'settings' => __('Settings', 'wpabstracts'), 'emails' => __('Emails', 'wpabstracts'), 'help' => __('Help', 'wpabstracts'));
      $tabs = array(
        //'summary' => __('Summary', 'wpabstracts'),
        'abstracts' => __('Abstracts', 'wpabstracts'),
        'events' => __('Events', 'wpabstracts'),
        //'users' => __('Users', 'wpabstracts'),
        //'reports' => __('Reports','wpabstracts'),
        'settings' => __('Settings', 'wpabstracts'),
        'emails' => __('Emails', 'wpabstracts'),
        'help' => __('Help', 'wpabstracts')
      );
    echo '<div id="icon-themes" class="icon32"><br></div>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach( $tabs as $tab => $name ){
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' href='?page=wpabstracts&tab=$tab'>$name</a>";
    }
    echo '</h2>';
}
/*
 * Creates DB tables and set default plugin options
 */
function wpabstracts_install() {
   global $wpdb;
   require_once(ABSPATH.'wp-admin/includes/upgrade.php');


   // reviews table
   $table_name = $wpdb->prefix."wpabstracts_reviews";

   $sql = "CREATE TABLE ".$table_name." (
		  review_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		  abstract_id int(11),
      event_id int(11),
		  user_id int(11),
		  status varchar(25),
		  relevance varchar(25),
		  quality varchar(25),
		  comments text,
                  recommendation varchar(25),
                  review_date datetime,
		  PRIMARY KEY (review_id)

	  );";

      dbDelta($sql);

    $table_name = $wpdb->prefix."wpabstracts_abstracts";

    $sql = "CREATE TABLE ".$table_name." (
		  abstract_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		  title text,
		  text longtext,
      resume mediumtext,
      target_group mediumtext,
      presenter_comments mediumtext,
      manager_comments mediumtext,
		  event int(11),
                  topic text,
                  session text,
                  status varchar(25),
                  priority smallint NOT NULL DEFAULT 20,
		  author text,
		  author_email text,
                  author_affiliation text,
		  presenter varchar(255),
      presenter_company varchar(255),
		  presenter_email varchar(255),
      presenter_phone varchar(50),
      presenter_linkedin varchar(255),
      presenter_perspektiv boolean,
		  presenter_preference varchar(255),
                  reviewer_id1 int(11),
                  reviewer_id2 int(11),
                  reviewer_id3 int(11),
                  reviewer_id4 int(11),
                  reviewer_id5 int(11),
                  reviewer_id6 int(11),
                  reviewer_id7 int(11),
                  reviewer_id8 int(11),
                  reviewer_id9 int(11),
                  reviewer_id10 int(11),
                  reviewer_id11 int(11),
                  reviewer_id12 int(11),
                  reviewer_id13 int(11),
                  reviewer_id14 int(11),
                  reviewer_id15 int(11),
      profile_image varchar(255),
		  submit_by int(11),
		  submit_date datetime,
      last_edit_date datetime,
      unedited_text longtext,
      unedited_resume mediumtext,
      unedited_target_group mediumtext,
		  PRIMARY KEY (abstract_id)
	  );";

    $updateEmail = "UPDATE " . $table_name . " SET author_email = REPLACE(author_email, ',', ' | ')";
    $updateAffiliation = "UPDATE " . $table_name . " SET author_affiliation = REPLACE(author_affiliation, ',', ' | ')";
    dbDelta($updateEmail);
    dbDelta($updateAffiliation);
    dbDelta($sql);

      // Events Table
   $table_name = $wpdb->prefix."wpabstracts_events";
   //$wpdb->query("drop table ".$table_name);
   $sql = "CREATE TABLE ".$table_name." (
		  event_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		  name varchar(255),
		  description longtext,
		  address longtext,
		  host varchar(255),
                  topics text,
                  sessions text,
		  start_date date,
		  end_date date,
                  deadline date,
		  PRIMARY KEY  (event_id)
	  );";

      dbDelta($sql);

    $table_name = $wpdb->prefix."wpabstracts_attachments";
    //$wpdb->query("drop table ".$table_name);
    $sql = "CREATE TABLE ".$table_name." (
	  	attachment_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		abstracts_id int(11),
		filecontent longblob,
		filename varchar(255),
		filetype varchar(255),
		filesize varchar(255),
		PRIMARY KEY  (attachment_id)
	  );";
      dbDelta($sql);
    $table_name = $wpdb->prefix."wpabstracts_emailtemplates";

    $sql = "CREATE TABLE ".$table_name." (
	  	ID int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		name varchar(255),
                subject varchar(255),
		message text,
                from_name varchar(255),
                from_email varchar(255),
                receiver varchar(255),
		PRIMARY KEY (ID)
	  );";
      dbDelta($sql);

      // settings tab
        add_option("wpabstracts_version", WPABSTRACTS_VERSION);
	add_option("wpabstracts_chars_count", 250);
  add_option("wpabstracts_resume_count", 300);
  add_option("wpabstracts_targetgroup_count", 200);
        add_option("wpabstracts_upload_limit", 3);
	add_option("wpabstracts_max_attach_size", 2048000);
        add_option('wpabstracts_author_instructions', "Enter description here.");
        add_option("wpabstracts_presenter_preference", "Poster,Panel,Roundtable,Projector");
	add_option("wpabstracts_email_admin", Yes);
	add_option("wpabstracts_email_author", No);
        add_option("wpabstracts_email_reviewer", Yes);
	add_option("wpabstracts_frontend_dashboard", Yes);
        add_option("wpabstracts_reviewer_submit", Yes);
        add_option("wpabstracts_reviewer_edit", Yes);
        add_option("wpabstracts_blind_review", No);
	add_option("wpabstracts_show_adminbar", Yes);
	add_option("wpabstracts_permitted_attachments", 'pdf,doc,xls,docx,xlsx,txt,rtf');
	add_option("wpabstracts_change_ownership", Yes);
        add_option("wpabstracts_status_notification", 1);
        add_option("wpabstracts_show_attachments", 1);

        //load email templates data if its the first time
        $sql = "SELECT COUNT(*) FROM ". $wpdb->prefix."wpabstracts_emailtemplates";
        if($wpdb->get_var($sql) < 1){
            wpabstracts_sampleEmailTemplates();
        }
}

function wpabstracts_sampleEmailTemplates(){
    global $wpdb;
    $from_name = get_option('blogname');
    $from_email = get_option('admin_email');

    // submission confirmation template
    $submitConfirmationMsg = 'Hi {DISPLAY_NAME},
                You have successfully submitted your abstract.
                Abstracts Title: {ABSTRACT_TITLE}
                Event: {EVENT_NAME}
                To make changes to your submission or view the status visit {SITE_URL} and sign in to your dashboard.
                Regards,
                WP Abstracts Team
                {SITE_NAME}
                {SITE_URL}';

    $submitConfirmationTemplate = array(
                'name' => "Abstracts Submission Acknowledgement",
                'subject' => "Abstract Submitted Successfully",
                'message'=> $submitConfirmationMsg,
                'from_name' => $from_name,
                'from_email' => $from_email,
                'receiver' => "Authors"
            );
    $wpdb->insert($wpdb->prefix.'wpabstracts_emailtemplates', $submitConfirmationTemplate);

    // reviewer assignment template
    $reviewerAssignmentMsg = 'Hello {DISPLAY_NAME},
                You have been assigned a new abstract for review.
                To review this or other abstracts please sign in at: {SITE_URL}
                Regards,
                WP Abstracts Team
                {SITE_NAME}
                {SITE_URL}';


    $reviewerAssignmentTemplate = array(
                'name' => "Reviewer Assignment",
                'subject' => "New Abstract Assigned",
                'message'=> $reviewerAssignmentMsg,
                'from_name' => $from_name,
                'from_email' => $from_email,
                'receiver' => "Reviewers"
            );

     $wpdb->insert($wpdb->prefix.'wpabstracts_emailtemplates', $reviewerAssignmentTemplate);


    // admin submission notification template
    $adminNotications = 'Hello {DISPLAY_NAME},
                        You have a new abstract for {SITE_NAME}
                        Title: {ABSTRACT_TITLE}
                        Regards,
                        WP Abstracts Team
                        {SITE_NAME}
                        {SITE_URL}';

    $adminEmailTemplate = array(
                'name' => "Abstract Submission Notification",
                'subject' => "New Abstract Submitted",
                'message'=> $adminNotications,
                'from_name' => $from_name,
                'from_email' => $from_email,
                'receiver' => "Administrators"
            );
    $wpdb->insert($wpdb->prefix.'wpabstracts_emailtemplates', $adminEmailTemplate);

     // author acceptance notification template
    $authorApprovalMsg = 'Hello {DISPLAY_NAME},
                        We are happy to announce that your abstract entitled {ABSTRACT_TITLE} was approved.
                        Regards,
                        WP Abstracts Team
                        {SITE_NAME}
                        {SITE_URL}';

    $authorApprovalTemplate = array(
                'name' => "Abstract Approval Notification",
                'subject' => "Abstract Approved",
                'message'=> $authorApprovalMsg,
                'from_name' => $from_name,
                'from_email' => $from_email,
                'receiver' => "Auhtors"
            );
    $wpdb->insert($wpdb->prefix.'wpabstracts_emailtemplates', $authorApprovalTemplate);

     // author rejection notification template
    $authorRejectedMsg = 'Hello {DISPLAY_NAME},
                        We are sorry to inform you that your abstract entitled {ABSTRACT_TITLE} was rejected.
                        Regards,
                        WP Abstracts Team
                        {SITE_NAME}
                        {SITE_URL}';

    $authorRejectedTemplate = array(
                'name' => "Abstract Rejection Notification",
                'subject' => "Abstract Rejected",
                'message'=> $authorRejectedMsg,
                'from_name' => $from_name,
                'from_email' => $from_email,
                'receiver' => "Authors"
            );
    $wpdb->insert($wpdb->prefix.'wpabstracts_emailtemplates', $authorRejectedTemplate);

}
/*
 * Checks DB to see if it has the latest format, if not, then the WPAbstacts tables are updated
 * @since 1.0.1
 */
function wpabstracts_db_check(){
        if(!(get_option( "wpabstracts_db_upgraded") == "Y")){
             wpabstracts_upgrade_db();
        }
}

function wpabstracts_upgrade_db(){
    global $wpdb;
    // upgrade abstracts table
    $oldtable_name = $wpdb->prefix."wpabstracts_submissions";
    $newtable_name = $wpdb->prefix."wpabstracts_abstracts";
    $sql = "ALTER TABLE ".$oldtable_name." RENAME TO $newtable_name";
    $wpdb->query($sql);
    $sql = "ALTER TABLE ".$newtable_name." ADD status varchar(25);";
    $wpdb->query($sql);
    $sql = "ALTER TABLE ".$newtable_name." CHANGE id abstract_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT;";
    $wpdb->query($sql);
    $sql = "ALTER TABLE ".$newtable_name." CHANGE event event int(11);";
    $wpdb->query($sql);
    $sql = "ALTER TABLE ".$newtable_name." ADD topic varchar(55);";
    $wpdb->query($sql);
    $sql = "ALTER TABLE ".$newtable_name." CHANGE rid reviewer_id1 int(11)";
    $wpdb->query($sql);

    for($i = 2; $i <= 15; $i++) {
      $sql = "ALTER TABLE ".$newtable_name." ADD reviewer_id".$i." int(11);";
      $wpdb->query($sql);
    }
    $sql = "ALTER TABLE ".$newtable_name." CHANGE submit_date submit_date datetime;";
    $wpdb->query($sql);
    $sql = "ALTER TABLE ".$newtable_name." ADD author_affiliation varchar(255);";
    $wpdb->query($sql);
    // upgrade reviews table
    $reviews_table = $wpdb->prefix."wpabstracts_reviews";
    $sql = "SHOW INDEXES FROM " .$reviews_table;
    $indexes = $wpdb->get_results($sql);
    foreach($indexes as $index){
        if($index->Column_name == "abstract_id" ){
            $sql = "DROP INDEX " . $index->Key_name . " ON " . $reviews_table;
            $wpdb->query($sql);
        }
    }
    // upgrade event table
    $events_table = $wpdb->prefix."wpabstracts_events";
    $sql = "ALTER TABLE ".$events_table." ADD topics text;";
    $wpdb->query($sql);
    $sql = "ALTER TABLE ".$events_table." CHANGE id event_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT;";
    $wpdb->query($sql);
    // upgrade attachments table
    $attachments_table = $wpdb->prefix."wpabstracts_attachments";
    $sql = "ALTER TABLE ".$attachments_table." CHANGE id attachment_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT;";
    $wpdb->query($sql);
    // store option to show DB updated and current version
    update_option('wpabstracts_db_upgraded', 'Y');
    update_option("wpabstracts_version", WPABSTRACTS_VERSION);
}

function wpabstracts_deactivation() {

  delete_option('wpabstracts_version');
}

function wpabstracts_frontend_js() {
	wp_enqueue_script( 'scripts', plugins_url('js/frontend.js', __FILE__), array( 'jquery' ));
        wp_enqueue_script( 'notifyme', plugins_url('js/notifyme.js', __FILE__), array( 'jquery' ));
        wpabstracts_localize();
}

function wpabstracts_admin_js() {
	wp_enqueue_script( 'scripts', plugins_url('js/backend.js', __FILE__), array( 'jquery' ));
        wpabstracts_localize();
}

function wpabstracts_frontend_css(){
    wp_enqueue_style( 'wpabstracts-frontend', plugins_url('css/frontend.css', __FILE__) );
    wp_enqueue_style( 'notifyme', plugins_url('css/notifyme.css', __FILE__) );
}

function wpabstracts_admin_css(){
    global $wp_scripts;
    wp_enqueue_style( 'style', plugins_url('css/admin.css', __FILE__) );
    $ui = $wp_scripts->query('jquery-ui-core');
    $url = "http://code.jquery.com/ui/{$ui->ver}/themes/flick/jquery-ui.css";
    wp_enqueue_style('jquery-ui-core', $url, false, $ui->ver);
}


function wpabstracts_localize(){
    wp_localize_script('scripts', 'front_ajax',
            array('ajaxurl' => admin_url('admin-ajax.php'),
                'name_event' => __('Please enter a name for your event', 'wpabstracts'),
                'authorName' => __('Name', 'wpabstracts'),
                'authorEmail' => __('Email', 'wpabstracts'),
                'confirmdelete' => __('Do you really want to delete this abstract and all its attachments?', 'wpabstracts'),
                'confirmdeleteEvent' => __('Do you really want to delete this event?', 'wpabstracts'),
                'confirmdeleteReview' => __('Do you really want to delete this review?', 'wpabstracts'),
                'confirmdeleteUser' => __('Are you sure you want to delete this user?', 'wpabstracts'),
                'hostedby' => __('hosted by', 'wpabstracts'),
                'assign_reviewer' => __("Assign Reviewer", 'wpabstracts'),
                'set_session' => __('Set session', 'wpabstracts'),
                'review_alert' => __('Review Alert', 'wpabstracts'),
                'fillin' => __('Please fill in all required fields.', 'wpabstracts'),
                'topic' => __('Topic', 'wpabstracts'),
                'session' => __('Session', 'wpabstracts'),
                'affiliation' => __("Affiliation", 'wpabstracts'),
            )
    );
    wp_enqueue_script( 'jquery-ui' );
    wp_enqueue_script( 'jquery-ui-datepicker' );
    wp_enqueue_script( 'jquery-ui-dialog');
}

function wpabstracts_getreviewers_ajax(){
    require_once 'wpabstracts_abstracts.php';
    wpabstracts_getReviewers();
}

function wpabstracts_checkreviews_ajax(){
    require_once 'wpabstracts_reviews.php';
    wpabstracts_checkReviews();
}

function wpabstracts_loadtopics_ajax(){
    require_once 'wpabstracts_events.php';
    wpabstracts_loadTopics();
}

function wpabstracts_loadsessions_ajax(){
    require_once 'wpabstracts_events.php';
    wpabstracts_loadSessions();
}

function wpabstracts_getmultireviewers_ajax(){
    require_once 'wpabstracts_abstracts.php';
    wpabstracts_getMultiReviewers();
}

function wpabstracts_multisetsession_ajax(){
    require_once 'wpabstracts_abstracts.php';
    wpabstracts_multiSetSessionForm();
}

function wpabstracts_editor_admin_init() {
    wp_enqueue_script('post');
    wp_enqueue_script('editor');
    wp_enqueue_script('media-upload');
}

function wpabstracts_admin_header(){ ?>
<div class="wrap">
    <h2>
        <a href="?page=wpabstracts"><img src="<?php echo plugins_url('images/admin_logo.png', __FILE__)?>"></a>
        <span style="vertical-align: top; font-size: 11px; color: #44648A;"><?php echo "Pro v" . WPABSTRACTS_VERSION; ?></span>
    </h2>
    <div class="alignright">
        <?php _e('Need help?','wpabstracts');?> <input class="button-primary" type="button" value="<?php _e('Support','wpabstracts');?>" onclick="window.open('http://www.wpabstracts.com/support')"/>
    </div>
<?php
}

function wpabstracts_set_html_content_type() {
    return 'text/html';
}


add_filter( 'tiny_mce_before_init', 'wpabstracts_editor_init' );
function wpabstracts_editor_init( $initArray ){
    $initArray['setup'] = <<<JS
[function(ed) {
    ed.onKeyUp.add(function(ed, e){
        wpabstracts_updateCharacterCount();
    });
}][0]
JS;
    return $initArray;
}

//Remove tabs from wp_editor
//function my_remove_editor_tabs($settings) {
//  $settings['quicktags'] = false;
//  return $settings;
//}
//add_filter('wp_editor_settings', 'my_remove_editor_tabs');

// auto updates
$api_url = 'http://updates.wpabstracts.com';
$plugin_slug = basename(dirname(__FILE__)); //plugin slug needs to be 'wpabstracts_pro' for auto-updates to work

function wpabsracts_insert_transient($transient) {
    global $api_url, $plugin_slug, $wp_version;

    if (empty($transient->checked)){
            return $transient;
    }

    $args = array(
            'slug' => $plugin_slug,
            'version' => $transient->checked[$plugin_slug .'/wpabstracts.php'],
    );
    $request_string = array(
                    'body' => array(
                            'action' => 'basic_check',
                            'request' => serialize($args),
                            'api-key' => md5(get_bloginfo('url'))
                    ),
                    'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
            );
    $raw_response = wp_remote_post($api_url, $request_string);

    if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200)){
            $response = unserialize($raw_response['body']);
    }

    if (is_object($response) && !empty($response)) {
            $transient->response[$plugin_slug .'/wpabstracts.php'] = $response;
    }
    return $transient;
}



function wpabstracts_api_call($def, $action, $args) {
    global $plugin_slug, $api_url, $wp_version;

    if (isset($args->slug) && ($args->slug == $plugin_slug)){
        $plugin_info = get_site_transient('update_plugins');
        $current_version = $plugin_info->checked[$plugin_slug .'/wpabstracts.php'];
        $args->version = $current_version;
        $request = wp_remote_post($api_url, array('body' => array('action' => 'plugin_information')));
        if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
            return unserialize($request['body']);
        }
        return false;
    }

    return false;
}
