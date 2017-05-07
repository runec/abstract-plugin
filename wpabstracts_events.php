<?php
defined('ABSPATH') or die("ERROR: You do not have permission to access this page");
if(!class_exists('WPAbstract_Abstracts_Table')){
    require_once( WPABSTRACTS_PLUGIN_DIR . 'inc/wpabstracts_classes.php' );
}
    if($_GET["tab"]=="events") {
        if(isset($_GET["task"])){
            $task = $_GET["task"];
            switch($task){
                case 'new':
                    wpabstracts_addEvent();
                    break;
                case 'edit':
                    wpabstracts_editEvent($_GET["id"]);
                    break;
                case 'delete':
                    wpabstracts_deleteEvent($_GET['id']);
                default :
                    wpabstracts_showEvents();
                    break;
            }
        }else{
            wpabstracts_showEvents();
        }
    }
    else{
        echo "You do not have permission to view this page";
    }

function wpabstracts_addEvent() {
    global $wpdb;

    $tab = "?page=wpabstracts&tab=events";
        if ($_POST) {
            $abs_event_name = sanitize_text_field($_POST["abs_event_name"]);
            $abs_event_desc = sanitize_text_field($_POST["abs_event_desc"]);
            $abs_event_address = sanitize_text_field($_POST["abs_event_address"]);
            $abs_event_host = sanitize_text_field($_POST["abs_event_host"]);
            $abs_event_start = sanitize_text_field($_POST["abs_event_start"]);
            $abs_event_end = sanitize_text_field($_POST["abs_event_end"]);
            $abs_event_deadline = sanitize_text_field($_POST["abs_event_deadline"]);
            // get and sanitize topics
            if(sizeof($_POST["topics"])>1) {
                foreach($_POST["topics"] as $key=>$topic) {
                    $topic = sanitize_text_field($_POST["topics"][$key]);
                    $topics[] = $topic;
                }
            $event_topics = implode(', ',$topics);
            } else {
                $topic = sanitize_text_field($_POST["topics"][0]);
                $event_topics = $topic;
            }
            // get and sanitize sessions
            if(sizeof($_POST["sessions"])>1) {
                foreach($_POST["sessions"] as $key=>$session) {
                    $session = sanitize_text_field($_POST["sessions"][$key]);
                    $sessions[] = $session;
                }
            $event_sessions = implode(', ',$sessions);
            } else {
                $session = sanitize_text_field($_POST["sessions"][0]);
                $event_sessions = $session;
            }
            $wpdb->show_errors();
            $wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."wpabstracts_events (name, description, address, host, topics, sessions, start_date, end_date, deadline)
                                        VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s)",$abs_event_name,$abs_event_desc,$abs_event_address,$abs_event_host,$event_topics,$event_sessions, $abs_event_start,$abs_event_end,$abs_event_deadline));

            wpabstracts_redirect($tab);
        }
        else {
            wpabstracts_getAddView('Event', null, 'backend');
        }
}

function wpabstracts_editEvent($id){
    global $wpdb;
    $tab = "?page=wpabstracts&tab=events";
        if ($_POST) {
            $abs_event_name = sanitize_text_field($_POST["abs_event_name"]);
            $abs_event_desc = sanitize_text_field($_POST["abs_event_desc"]);
            $abs_event_address = sanitize_text_field($_POST["abs_event_address"]);
            $abs_event_host = sanitize_text_field($_POST["abs_event_host"]);
            $abs_event_start = sanitize_text_field($_POST["abs_event_start"]);
            $abs_event_end = sanitize_text_field($_POST["abs_event_end"]);
            $abs_event_deadline = sanitize_text_field($_POST["abs_event_deadline"]);
            // get and sanitize topics
            if(sizeof($_POST["topics"])>1) {
                foreach($_POST["topics"] as $key=>$topic) {
                    $topic = sanitize_text_field($_POST["topics"][$key]);
                    $topics[] = $topic;
                }
            $event_topics = implode(', ',$topics);
            } else {
                $topic = sanitize_text_field($_POST["topics"][0]);
                $event_topics = $topic;
            }

            // get and sanitize sessions
            if(sizeof($_POST["sessions"])>1) {
                foreach($_POST["sessions"] as $key=>$session) {
                    $session = sanitize_text_field($_POST["sessions"][$key]);
                    $sessions[] = $session;
                }
            $event_sessions = implode(', ',$sessions);
            } else {
                $session = sanitize_text_field($_POST["sessions"][0]);
                $event_sessions = $session;
            }

            $wpdb->show_errors();
            $wpdb->query("UPDATE ".$wpdb->prefix."wpabstracts_events
                        SET name = '$abs_event_name', description = '$abs_event_desc', address = '$abs_event_address', "
                    . "host = '$abs_event_host', topics = '$event_topics', sessions = '$event_sessions', start_date = '$abs_event_start', "
                    . "end_date = '$abs_event_end', deadline = '$abs_event_deadline' "
                    . "WHERE event_id = $id");

            wpabstracts_redirect($tab);
        }
        else {
            wpabstracts_getEditView('Event', $id, 'backend');
        }
}

function wpabstracts_showEvents(){ ?>
        <h2><?php _e('Events', 'wpabstracts');?>   <a href="?page=wpabstracts&tab=events&task=new" class="button-primary" /><?php _e('Create event', 'wpabstracts');?></a></h2>
    <form id="showsEvents" method="get">
        <input type="hidden" name="page" value="wpabstracts" />
        <input type="hidden" name="tab" value="events" />
           <?php
                $showEvents = new WPAbstract_Events_Table();
                $showEvents ->prepare_items();
                $showEvents ->display();
            ?>
    </form>


   <?php
}

function wpabstracts_deleteEvent($id){
    global $wpdb;
        $wpdb->show_errors();
        $wpdb->query("delete from ".$wpdb->prefix."wpabstracts_events where event_id=".$id);
        ?>
    <div id="message" class="updated fade"><p><strong><?php _e('Event deleted', 'wpabstracts');?>.</strong></p></div>
        <?php
}

function wpabstracts_loadTopics(){
    global $wpdb;
    if($_POST['event_id']){
        $event_id = intval($_POST['event_id']);
        $event = $wpdb->get_row("SELECT topics FROM ".$wpdb->prefix."wpabstracts_events Where event_id = $event_id");

        $topics = explode(', ',$event->topics);
        ?> <option value='all'><?php _e('All topics', 'wpabstracts'); ?></option> <?php
        if($event) {
          foreach($topics as $topic){ ?>
              <option value="<?php echo esc_attr($topic);?>"><?php echo esc_attr($topic);?></option>;
          <?php }
        }
    }else{
   _e("Error!", 'wpabstracts');
    }
   die();
}
function wpabstracts_loadSessions(){
    global $wpdb;
    if($_POST['event_id']){
        $event_id = intval($_POST['event_id']);
        $event = $wpdb->get_row("SELECT sessions FROM ".$wpdb->prefix."wpabstracts_events Where event_id = $event_id");

        $sessions = explode(', ',$event->sessions);
        ?> <option value='all'><?php _e('All sessions', 'wpabstracts'); ?></option> <?php
        if($event) {
          foreach($sessions as $session){ ?>
              <option value="<?php echo esc_attr($session);?>"><?php echo esc_attr($session);?></option>;
          <?php }
        }
    }else{
   _e("Error!", 'wpabstracts');
    }
   die();
}
