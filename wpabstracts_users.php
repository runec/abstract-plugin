<?php
defined('ABSPATH') or die("ERROR: You do not have permission to access this page");

if(!class_exists('WPAbstracts_Users')){
    require_once( WPABSTRACTS_PLUGIN_DIR . 'inc/wpabstracts_classes.php' );
}

if(is_admin() && isset($_GET['tab']) && ($_GET["tab"]=="users")){
    if(isset($_GET['task'])){
        $task = sanitize_text_field($_GET['task']);
        $id = intval($_GET['id']);
        switch($task){
            case 'delete':
                wpabstracts_deleteUser($id);
            default :
                wpabstracts_showUsers();
                break;
        }
    }else{
        wpabstracts_showUsers();
    }
}

function wpabstracts_showUsers(){ ?>
        <form id="showUsers" method="get">
            <input type="hidden" name="page" value="wpabstracts" />
            <input type="hidden" name="tab" value="users" />
            <?php
            $showUsers = new WPAbstracts_Users();
            $showUsers->prepare_items();
            $showUsers->display(); ?>
            </form>
    <?php
}



function wpabstracts_deleteUser($id){
    if(wp_delete_user($id)){
        echo '<div id="message" class="updated fade"><p><strong>Successfully deleted User ID ' . $id . '!</strong></p></div>';
    }
}
