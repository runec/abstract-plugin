<?php
defined('ABSPATH') or die("ERROR: You do not have permission to access this page");

if(!class_exists('WPAbstract_Attachments_Table')){
    require_once( WPABSTRACTS_PLUGIN_DIR . 'inc/wpabstracts_classes.php' );
}
if(is_admin() && isset($_GET['tab']) && $_GET["tab"]=="attachments"){
    wpabstracts_showAttachments();
}


/*
 * displays all submitted attachments using WP_TABLE_LIST class
 */
function wpabstracts_showAttachments(){ ?>
    <form id="showAttachments" method="get">
    <input type="hidden" name="page" value="wpabstracts" />
    <input type="hidden" name="tab" value="attachments" />
       <?php
            $showAttachments = new WPAbstract_Attachments_Table();
            $showAttachments->prepare_items();
            $showAttachments->display();
        ?>
</form>
    <?php
}