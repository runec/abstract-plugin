<?php
defined('ABSPATH') or die("ERROR: You do not have permission to access this page");
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WPAbstract_Abstracts_Table extends WP_List_Table {

    private $current_filter_event_id ='';
    private $all_items;

    function __construct(){
        global $status, $page;

        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'abstract',     //singular name of the listed records
            'plural'    => 'abstracts',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );

    }

    function column_title($item){
        global $wpdb;
        $reviews_count = $wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."wpabstracts_reviews WHERE abstract_id = $item->abstract_id");
        $actions = array(
            'accept' => '<a href="?page=wpabstracts&tab=abstracts&task=approve&id='.$item->abstract_id.'">'.__('Approve','wpabstracts').'</a>',
            'reject' => '<a href="?page=wpabstracts&tab=abstracts&task=reject&id=' . $item->abstract_id . '">' . __('Reject', 'wpabstracts') . '</a>',
            'pending' => '<a href="?page=wpabstracts&tab=abstracts&task=pending&id=' . $item->abstract_id . '">' . __('Pending', 'wpabstracts') . '</a>',
            'edit' => '<a href="?page=wpabstracts&tab=abstracts&task=edit&id=' . $item->abstract_id . '">' . __('Edit', 'wpabstracts') . '</a>',
            'assign' => '<a href="#assign" onclick="assignReviewer(' . $item->abstract_id . ');">' . __('Assign', 'wpabstracts') . '</a>',
            'reviews' => '<a href="?page=wpabstracts&tab=reviews&task=view&id=' . $item->abstract_id . '";>' . __('Reviews', 'wpabstracts') . ' (' . $reviews_count . ')</a>',
            'pdf' => '<a href="?page=wpabstracts&tab=abstracts&task=download&downloadID=' . $item->abstract_id . '" target="_blank">' . __('Export PDF', 'wpabstracts') . '</a>',
            'delete' => '<a href="javascript:confirm_abstract_delete(' . $item->abstract_id . ')">' . __('Delete', 'wpabstracts') . '</a>',
        );
        return sprintf('%1$s <span style="color:silver">[ID:%2$s]</span> %3$s',$item->title, $item->abstract_id, $this->row_actions($actions));
    }

    function column_cb($item) {
        return sprintf('<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $item->abstract_id);
    }

    function column_topic($item){
        global $wpdb;
        $event = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."wpabstracts_events WHERE event_id = $item->event");
        echo $event->name . " / " .$item->topic;
    }

    function column_reviewers($item){
        $reviewers = array();
        $reviewers[] = get_userdata($item->reviewer_id1);
        $reviewers[] = get_userdata($item->reviewer_id2);
        $reviewers[] = get_userdata($item->reviewer_id3);
        $reviewers[] = get_userdata($item->reviewer_id4);
        $reviewers[] = get_userdata($item->reviewer_id5);
        $reviewers[] = get_userdata($item->reviewer_id6);
        $reviewers[] = get_userdata($item->reviewer_id7);
        $reviewers[] = get_userdata($item->reviewer_id8);

        $reviewer_list = null;
        foreach($reviewers AS $reviewer){
            if($reviewer){
               $reviewer_list .= "<span class=\"reviewerList\">". $reviewer->display_name . "</span>";
            }
        }
        $current_reviewers = (empty($reviewer_list)) ? __("Not Assigned",'wpabstracts') : $reviewer_list;
        echo $current_reviewers;
    }

    function column_rating($item) {
      $reviewer_ids = array_filter(array($item->reviewer_id1, $item->reviewer_id2,$item->reviewer_id3,$item->reviewer_id4,$item->reviewer_id5,$item->reviewer_id6,$item->reviewer_id7,$item->reviewer_id8));
      $count_reviews = count($reviewer_ids);
      //var_dump($test);
      //return;
      //$count_reviews = count(array_filter(array($item->reviewer_id1, $item->reviewer_id2,$item->reviewer_id3), function ($id) {
      //  return !is_null($id) && $id != '0';
      //}));
      $reviews = wpabstracts_getReviews('abstract_id', $item->abstract_id);
      $count_approved = 0;
      foreach($reviews as $review) {
        if($review->status == 'Approved' && in_array($review->user_id, $reviewer_ids)) {
          $count_approved++;
        }
      }
      echo($count_approved . '/' . $count_reviews);
    }

     function column_default( $item, $column_name ) {
        switch ($column_name) {
            case 'presenter': echo $item->$column_name; break;
            case 'status': _e($item->$column_name, 'wpabstracts'); break;
            case 'session': echo(is_null($item->$column_name) || $item->$column_name === "" ? __("No session", 'wpabstracts') : $item->$column_name); break;
            case 'priority': echo $item->$column_name; break;
            case 'submit_date': echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($item->$column_name)); break;
            case 'last_edit_date': echo(is_null($item->$column_name) ? __('No edits','wpabstracts') : date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($item->$column_name))); break;
        }
    }

    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',
            'title' => __('Title', 'wpabstracts'),
            'topic' => __('Event / Topic', 'wpabstracts'),
            'session' => __('Session', 'wpabstracts'),
            'priority' => __('Priority', 'wpabstracts'),
            'presenter' => __('Presenter', 'wpabstracts'),
            'rating' => __('Rating', 'wpabstracts'),
            'status' => __('Status', 'wpabstracts'),
            'reviewers' => __('Reviewers', 'wpabstracts'),
            'submit_date' => __('Submitted', 'wpabstracts'),
            'last_edit_date' => __('Last editted', 'wpabstracts')
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'title'     => array('title',false),     //true means it's already sorted
            'rid'    => array('rid',false),
            'presenter'    => array('presenter',false),
            'topic'    => array('topic',false),
            'session' => array('session', false),
            'priority' => array('priority', false),
            'status'    => array('status',false),
            'submit_date'    => array('submit_date',false),
        );
        return $sortable_columns;
    }

    function get_bulk_actions() {
        $actions = array(
            'accept' => __('Approve', 'wpabstracts'),
            'reject' => __('Reject', 'wpabstracts'),
            'delete' => __('Delete', 'wpabstracts'),
            'assign' => __('Assign reviewers', 'wpabstracts'),
            'multiPDFdownload' => __('Download as PDF', 'wpabstracts')
        );
        return $actions;
    }

    function process_bulk_action() {
        global $wpdb;
        if ( 'accept'=== $this->current_action() ) {
            foreach($_GET['abstract'] as $abstract_id) {
                if(current_user_can(WPABSTRACTS_ACCESS_LEVEL)){
                    wpabstracts_changeStatus(intval($abstract_id), "Approved");
                }
            }
        }
        if ( 'reject'=== $this->current_action() ) {
            foreach($_GET['abstract'] as $abstract_id) {
                if(current_user_can(WPABSTRACTS_ACCESS_LEVEL)){
                    wpabstracts_changeStatus(intval($abstract_id),"Rejected");
                }
            }
        }
        if ( 'delete'=== $this->current_action() ) {
            foreach($_GET['abstract'] as $abstract_id) {
                if(current_user_can(WPABSTRACTS_ACCESS_LEVEL)){
                    wpabstracts_deleteAbstract(intval($abstract_id), false);
                }
            }
        }
        if ( 'assign' === $this->current_action() ) {
          //See display table nav
        }
        if( 'multiPDFdownload' === $this->current_action() ) {
            $ids = array_map('intval', $_GET['abstract']);
            wpabstracts_downloadAbstracts($ids);
        }

    }

    function prepare_items() {
        global $wpdb, $_wp_column_headers;

	$screen = get_current_screen();
        $table_name = $wpdb->prefix."wpabstracts_abstracts";
        $query = "SELECT abstract_id, title, event, topic, session, presenter, status, author_email, reviewer_id1, reviewer_id2, reviewer_id3, reviewer_id4, reviewer_id5, reviewer_id6, reviewer_id7, reviewer_id8, submit_date, last_edit_date FROM  . $table_name";
        $orderby = !empty($_GET["orderby"]) ?esc_sql($_GET["orderby"]) : 'abstract_id';
        $order = !empty($_GET["order"]) ?esc_sql($_GET["order"]) : 'desc';
        if(!empty($orderby) & !empty($order)){ $query.=' ORDER BY '.$orderby.' '.$order; }
        $totalitems = $wpdb->query($query); //return the total number of affected rows
        //How many to display per page?
        $perpage = 30;
        //Which page is this?
        $paged = !empty($_GET["paged"]) ? intval($_GET["paged"]) : '';
        //Page Number
        if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }
        //How many pages do we have in total?
        $totalpages = ceil($totalitems/$perpage);
        //adjust the query to take pagination into account
        if(!empty($paged) && !empty($perpage)){
            $offset=($paged-1)*$perpage;
            $query.=' LIMIT '.(int)$offset.','.(int)$perpage;
        }

        /* -- Register the pagination -- */
        $this->set_pagination_args( array(
            "total_items" => $totalitems,
            "total_pages" => $totalpages,
            "per_page" => $perpage,
        ) );
        /* -- Register the pagination -- */
        $this->process_bulk_action();

        /* -- Fetch the items -- */
        $this->all_items = $wpdb->get_results($query);

        /* -- Register the Columns -- */
        $columns = $this->get_columns();
        $_wp_column_headers[$screen->id]=$columns;
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
    }

    function wpabstracts_events_dropdown_filter(){
            $events = wpabstracts_getEvents('all','',NULL);
            ?>
            <select name="ABSTRACTS_EVENT_FILTER" onChange="wpabstracts_getTopics(this.value)">
            <option value='all'><?php _e('All events', 'wpabstracts'); ?></option>
            <?php
                $this->current_filter_event_id = isset($_GET['ABSTRACTS_EVENT_FILTER'])? $_GET['ABSTRACTS_EVENT_FILTER']:'';
                foreach ($events as $key => $row) {
                    printf
                        (
                            '<option value="%s"%s>%s</option>',
                            $row->event_id,
                            $row->event_id == $this->current_filter_event_id? ' selected="selected"':'',
                            $row->name
                        );
                      }
            ?>
            </select>
            <?php
        //}
    }

    function wpabstracts_topics_dropdown_filter() {
      ?>
      <select  id="abs_topic" name="ABSTRACTS_TOPIC_FILTER">
      <?php /*
      if(!isset($_GET['ABSTRACTS_EVENT_FILTER']) || $_GET['ABSTRACTS_EVENT_FILTER'] == '') {
        ?>
        <option value=""><?php _e('Choose an event first', 'wpabstracts'); ?></option>
        <?php
      } else {
        */ ?>
        <option value='all'><?php _e('All topics', 'wpabstracts'); ?></option>
        <?php
        $event_id = $_GET['ABSTRACTS_EVENT_FILTER'];
        $event = wpabstracts_getEvents('event_id', $event_id, 'ARRAY_A');
        $topics = explode(', ', $event['topics']);
        if($event) {
          foreach ($topics as $topic) {
            printf
                (
                    '<option value="%s"%s>%s</option>',
                    $topic,
                    $topic == $_GET['ABSTRACTS_TOPIC_FILTER']? ' selected="selected"':'',
                    $topic
                );
          }
        }

      //}
      ?>
      </select>
      <?php
    }

    protected function extra_tablenav($which) {
      if($which == 'top') {
        ?>
        <?php
        $this->wpabstracts_events_dropdown_filter();
        $this->wpabstracts_topics_dropdown_filter();
        ?>

        <input type='submit' class='button action' name='filter' onClick="jQuery('#bulk-action-selector-').val(-1);" value='<?php _e('Filter', 'wpabstracts') ?>'></input>
        <input type='button' class='button action' name='export' onClick='excel_export_abstracts();' value='<?php _e('Export to Excel', 'wpabstracts') ?>'></input>
        <?php
      }
    }

    function apply_dropdown_filters() {
      $this->items = $this->all_items;

      //Check if we have an event filter
      if($this->current_filter_event_id != '' && $this->current_filter_event_id != 'all') {
        $this->items  = array_filter($this->items, function ($abstract) {
          return $abstract->event == $this->current_filter_event_id;
        });
        if($_GET['ABSTRACTS_TOPIC_FILTER'] != '' && $_GET['ABSTRACTS_TOPIC_FILTER'] != 'all') {
          $this->items = array_filter($this->items, function ($abstract) {
            return trim($abstract->topic) == trim($_GET['ABSTRACTS_TOPIC_FILTER']);
          });
        }
      }
    }

    public function display_rows_or_placeholder() {
      $this->apply_dropdown_filters();
      parent::display_rows_or_placeholder();
    }

    /**
     * Generates the table navigation above or bellow the table and removes the
     * _wp_http_referrer and _wpnonce because it generates a error about URL too large
     *
     * @param string $which
     * @return void
     */
    function display_tablenav( $which )
    {
      if($which == 'top') {
        ?>
        <div class="tablenav <?php echo esc_attr( $which ); ?>">

            <div class="alignleft actions">
                <?php
                  $this->bulk_actions();
                 ?>
            </div>
            <?php
            $this->extra_tablenav( $which );
            $this->pagination( $which );
            ?>
            <div class='scrpt'>
            <script type='text/javascript'>
              jQuery(document).ready(function() {
                jQuery('form#showsAbstracts').submit(function(event) {
                  if(jQuery("form#showsAbstracts select[name='action']").val() == 'assign') {
                    event.preventDefault();
                    var abstract_ids = [];
                    //Get list of abstract ids in form
                    jQuery("input[name='abstract[]'][type='checkbox']").each(function() {
                      if(this.checked) {
                        abstract_ids.push(this.value);
                      }
                    });
                    //call assignreveierws with list input
                    multi_assign_reviewers(abstract_ids);
                    return false;
                  }
                  else if(jQuery("form#showsAbstracts select[name='action']").val() == 'set_session') {
                    event.preventDefault();
                    var abstract_ids = [];
                    //Get list of abstract ids in form
                    jQuery("input[name='abstract[]'][type='checkbox']").each(function() {
                      if(this.checked) {
                        abstract_ids.push(this.value);
                      }
                    });
                    //call assignreveierws with list input
                    multi_set_session(abstract_ids);
                    return false;
                  }
                  else {
                    return true;
                  }
                });
              });
            </script>
          </div>
            <br class="clear" />
        </div>
        <?php
      }
    }
} // END ABSTRACTS TABLE CLASS

class WPAbstract_Reviews_Table extends WP_List_Table {

    private $all_items;

    function __construct(){
        global $status, $page;

        //Set parent defaults
        parent::__construct( array(
            'singular' => 'review', //singular name of the listed records
            'plural'    => 'reviews',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );

    }

    function column_abstract_id($item){
        global $wpdb;
        $abstract = $wpdb->get_row("SELECT title FROM ".$wpdb->prefix."wpabstracts_abstracts WHERE abstract_id = $item->abstract_id", ARRAY_A);
        $actions = array(
            'edit' => '<a href="?page=wpabstracts&tab=reviews&task=edit&id=' . $item->review_id . '">' . __('Edit', 'wpabstracts') . '</a>',
            'delete' => '<a href="javascript:confirm_review_delete(' . $item->review_id . ')">' . __('Delete', 'wpabstracts') . '</a>',
        );
        return sprintf('%1$s<span style="color:silver"> [%2$s]</span>%3$s', $abstract['title'], $item->abstract_id, $this->row_actions($actions));
    }
    function column_comments($item){
        $user_info = get_userdata($item->user_id);
             if($user_info){
                 $reviewer = $user_info->display_name;
             }else{
                 $reviewer = __("User deleted", 'wpabstracts');
        }
        $lastUpdated = date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($item->review_date));
        return sprintf(__('%1$s<br><span style="color:silver">Reviewed by: %2$s | %3$s</span>', 'wpabstracts'), $item->comments, $reviewer, $lastUpdated);
    }

    function column_cb($item) {
        return sprintf('<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $item->review_id);
    }

     function column_default( $item, $column_name ) {
        switch( $column_name ) {
          case 'abstract_id': echo $item->$column_name; break;
          case 'status': _e($item->$column_name, 'wpabstracts');
                break;
            case 'relevance': _e($item->$column_name, 'wpabstracts');
                break;
            case 'quality': _e($item->$column_name, 'wpabstracts');
                break;
            case 'recommendation': _e($item->$column_name, 'wpabstracts');
                break;
            case 'event_id': _e(wpabstracts_getEvents('event_id', $item->$column_name, ARRAY_A)['name'], 'wpabstracts');
                break;
        }
    }

    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',
            'abstract_id' => __('Abstract', 'wpabstracts'),
            'event_id' => __('Event', 'wpabstracts'),
            'comments' => __('Comments', 'wpabstracts'),
            'relevance' => __('Relevance', 'wpabstracts'),
            'quality' => __('Quality', 'wpabstracts'),
            'status' => __('Suggested', 'wpabstracts'),
            'recommendation' => __('Type', 'wpabstracts'),
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'abstract_id'    => array('abstract_id',true),
            'status'    => array('status',false),
            'relevance'    => array('relevance',false),
            'quality'    => array('quality',false),
            'recommendaton'    => array('recommendaton',false)
        );
        return $sortable_columns;
    }

    function get_bulk_actions() {
        $actions = array(
            'delete' => __('Delete', 'wpabstracts')
        );
        return $actions;
    }

    function process_bulk_action() {

        if ( 'delete'=== $this->current_action() ) {
            foreach($_GET['review'] as $review) {
                wpabstracts_deleteReview($review, false);
            }
        }
    }

    function prepare_items() {
        global $wpdb, $_wp_column_headers;
	$screen = get_current_screen();
        $table_name = $wpdb->prefix."wpabstracts_reviews";
        $query = "SELECT review_id, abstract_id, event_id, comments, user_id, status, relevance, quality, recommendation, review_date  FROM  . $table_name";
        $orderby = !empty($_GET["orderby"]) ?esc_sql($_GET["orderby"]) : 'review_id';
        $order = !empty($_GET["order"]) ?esc_sql($_GET["order"]) : 'desc';
        if(!empty($orderby) & !empty($order)){ $query.=' ORDER BY '.$orderby.' '.$order; }
        $totalitems = $wpdb->query($query); //return the total number of affected rows
        //How many to display per page?
        $perpage = 15;
        //Which page is this?
        $paged = !empty($_GET["paged"]) ?esc_sql($_GET["paged"]) : '';
        //Page Number
        if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }
        //How many pages do we have in total?
        $totalpages = ceil($totalitems/$perpage);
        //adjust the query to take pagination into account
        if(!empty($paged) && !empty($perpage)){
            $offset=($paged-1)*$perpage;
            $query.=' LIMIT '.(int)$offset.','.(int)$perpage;
        }

        /* -- Register the pagination -- */
        $this->set_pagination_args( array(
            "total_items" => $totalitems,
            "total_pages" => $totalpages,
            "per_page" => $perpage,
        ) );
        /* -- Register the pagination -- */
        $this->process_bulk_action();

        /* -- Fetch the items -- */
        $this->all_items = $wpdb->get_results($query);

        /* -- Register the Columns -- */
        $columns = $this->get_columns();
        $_wp_column_headers[$screen->id]=$columns;
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);



    } // end prepare items

    function wpabstracts_events_dropdown_filter(){
            $events = wpabstracts_getEvents('all','',NULL);
            ?>
            <select name="REVIEWS_EVENT_FILTER" onChange="wpabstracts_getTopics(this.value)">
            <option value=""><?php _e('All events', 'wpabstracts'); ?></option>
            <?php
                foreach ($events as $key => $row) {
                    printf
                        (
                            '<option value="%s"%s>%s</option>',
                            $row->event_id,
                            $row->event_id == $_GET['REVIEWS_EVENT_FILTER']? ' selected="selected"':'',
                            $row->name
                        );
                      }
            ?>
            </select>
            <?php
        //}
    }

    protected function extra_tablenav($which) {
      if($which == 'top') {
        $this->wpabstracts_events_dropdown_filter();
        printf("<input type='submit' class='button action' value='" .  __('Filter') . "'></input>");
      }
    }

    function apply_dropdown_filters() {
      $this->items = $this->all_items;

      //Check if we have an event filter
      if($_GET['REVIEWS_EVENT_FILTER'] != '') {
        $this->items  = array_filter($this->items, function ($review) {
          return $review->event_id == $_GET['REVIEWS_EVENT_FILTER'];
        });
      }
    }

    public function display_rows_or_placeholder() {
      $this->apply_dropdown_filters();
      parent::display_rows_or_placeholder();
    }

    /**
     * Generates the table navigation above or bellow the table and removes the
     * _wp_http_referrer and _wpnonce because it generates a error about URL too large
     *
     * @param string $which
     * @return void
     */
    function display_tablenav( $which )
    {
        ?>
        <div class="tablenav <?php echo esc_attr( $which ); ?>">

            <div class="alignleft actions">
                <?php $this->bulk_actions(); ?>
            </div>
            <?php
            $this->extra_tablenav( $which );
            $this->pagination( $which );
            ?>
            <br class="clear" />
        </div>
        <?php
    }

} // end REVIEW Table class

class WPAbstract_Attachments_Table extends WP_List_Table {

    function __construct(){

        global $status, $page;

        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'attachment',     //singular name of the listed records
            'plural'    => 'attachments',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );

    }

    function column_filename($item){
        $actions = array(
            'download' => '<a href="?page=wpabstracts&task=download&attachmentID=' . $item->attachment_id . '" ">' . __('Download', 'wpabstracts') . '</a>',
        );
        return sprintf('%1$s <span style="color:silver"></span>%2$s',$item->filename, $this->row_actions($actions));
    }

    function column_cb($item) {
        return sprintf('<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $item->attachment_id);
    }

    function column_abstracts_id( $item ) {
        global $wpdb;
        $abstract = $wpdb->get_row("SELECT title FROM ".$wpdb->prefix."wpabstracts_abstracts WHERE abstract_id =" . $item->abstracts_id);
        echo $abstract->title;
    }

    function column_author( $item ) {
        global $wpdb;
        $abstract = $wpdb->get_row("SELECT submit_by FROM ".$wpdb->prefix."wpabstracts_abstracts WHERE abstract_id =" . $item->abstracts_id);
        $user = get_user_by( 'id', $abstract->submit_by );
        echo $user->display_name . " (" . $user->user_login . ")";
    }

    function column_default( $item, $column_name ) {
        switch( $column_name ) {
            case 'filename': echo $item->$column_name; break;
            case 'filetype':
                $filetype = wp_check_filetype($item->filename);
                echo $filetype['ext'];break;
            case 'filesize': echo number_format(($item->filesize/1048576), 2) . " MB"; break;
        }
    }

    function column_download($item) {
        return sprintf('<a href="?page=wpabstracts&task=download&attachmentID=' . $item->attachment_id . '" "><span class="dashicons dashicons-download"></span></a>');
    }

    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',
            'filename' => __('File Name', 'wpabstracts'),
            'abstracts_id' => __('Uploaded to', 'wpabstracts'),
            'author' => __('Author', 'wpabstracts'),
            'filetype' => __('File Type', 'wpabstracts'),
            'filesize' => __('File Size', 'wpabstracts'),
            'download' => __('Download', 'wpabstracts')
        );
        return $columns;
    }

    /**
     *
     * @return array
     */
    function get_sortable_columns() {
        $sortable_columns = array(
            'filename' => array('filename',true),
            'filesize' => array('filesize',false),
            'filetype' => array('filetype',false),
            'abstracts_id' => array('abstracts_id',false)
        );
        return $sortable_columns;
    }

    function process_bulk_action() {
        if ( 'download'=== $this->current_action() ) {
            foreach($_GET['attachment'] as $event) {
                //wpabstracts_deleteEvent($event, false);
            }
        }
    }

    function prepare_items() {
        global $wpdb, $_wp_column_headers;
	$screen = get_current_screen();
        $table_name = $wpdb->prefix."wpabstracts_attachments";
        $query = "SELECT * FROM  . $table_name";
        $orderby = !empty($_GET["orderby"]) ?esc_sql($_GET["orderby"]) : 'attachment_id';
        $order = !empty($_GET["order"]) ?esc_sql($_GET["order"]) : 'desc';
        if(!empty($orderby) & !empty($order)){ $query.=' ORDER BY '.$orderby.' '.$order; }
        $totalitems = $wpdb->query($query); //return the total number of affected rows
        //How many to display per page?
        $perpage = 15;
        //Which page is this?
        $paged = !empty($_GET["paged"]) ?esc_sql($_GET["paged"]) : '';
        //Page Number
        if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }
        //How many pages do we have in total?
        $totalpages = ceil($totalitems/$perpage);
        //adjust the query to take pagination into account
        if(!empty($paged) && !empty($perpage)){
            $offset=($paged-1)*$perpage;
            $query.=' LIMIT '.(int)$offset.','.(int)$perpage;
        }

        /* -- Register the pagination -- */
        $this->set_pagination_args( array(
            "total_items" => $totalitems,
            "total_pages" => $totalpages,
            "per_page" => $perpage,
        ) );
        /* -- Register the pagination -- */
        $this->process_bulk_action();

        /* -- Fetch the items -- */
        $this->items = $wpdb->get_results($query);

        /* -- Register the Columns -- */
        $columns = $this->get_columns();
        $_wp_column_headers[$screen->id]=$columns;
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
    } // end prepare items

} // end Event Table class

class WPAbstract_singleItem_Reviews_Table extends WP_List_Table {


    function __construct($id){

        global $status, $page;


        //Set parent defaults
        parent::__construct( array(
            'singular' => __('review', 'wpabstracts'), //singular name of the listed records
            'plural' => __('reviews', 'wpabstracts'), //plural name of the listed records
            'ajax'      => false,        //does this table support ajax?
            'id' => $id
        ) );

    }

    function column_title($item){
        global $wpdb;
        $abstract = $wpdb->get_row("SELECT title FROM ".$wpdb->prefix."wpabstracts_abstracts WHERE abstract_id = $item->abstract_id", ARRAY_A);
        $actions = array(
               'edit' => '<a href="?page=wpabstracts&tab=reviews&task=edit&id=' . $item->review_id . '">' . __('Edit', 'wpabstracts') . '</a>',
            'delete' => '<a href="javascript:confirm_review_delete(' . $item->review_id . ')">' . __('Delete', 'wpabstracts') . '</a>',
        );
        return sprintf('%1$s<span style="color:silver"></span>%2$s',$abstract['title'], $this->row_actions($actions));
    }

    function column_comments($item){
        $user_info = get_userdata($item->user_id);
             if($user_info){
                 $reviewer = $user_info->display_name;
             }else{
                 $reviewer = __("User deleted", 'wpabastracts');
             }
        $lastUpdated = date('M d Y g:i a', strtotime($item->review_date));
        return sprintf(__('%1$s<br><span style="color:silver">Reviewed by: %2$s | %3$s</span>', 'wpabstracts'), $item->comments, $reviewer, $lastUpdated);
    }

    function column_cb($item) {
        return sprintf('<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $item->review_id);
    }

    function column_default( $item, $column_name ) {
        switch( $column_name ) {
          case 'status': _e($item->$column_name, 'wpabstracts');
                break;
            case 'relevance': _e($item->$column_name, 'wpabstracts');
                break;
            case 'quality': _e($item->$column_name, 'wpabstracts');
                break;
            case 'recommendation': _e($item->$column_name, 'wpabstracts');
                break;
        }
    }

    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',
           'title' => __('Abstract', 'wpabstracts'),
            'comments' => __('Comments', 'wpabstracts'),
            'relevance' => __('Relevance', 'wpabstracts'),
            'quality' => __('Quality', 'wpabstracts'),
            'status' => __('Status', 'wpabstracts'),
            'recommendation' => __('Type', 'wpabstracts'),
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'title'     => array('title',false),
            'status'    => array('status',false),
            'relevance'    => array('relevance',false),
            'quality'    => array('quality',false),
            'recommendaton'    => array('recommendaton',false)
        );
        return $sortable_columns;
    }

    function get_bulk_actions() {
        $actions = array(
            'delete' => __('Delete', 'wpabstracts')
        );
        return $actions;
    }

    function process_bulk_action() {
        if ( 'delete'=== $this->current_action() ) {
            foreach($_GET['review'] as $review) {
                wpabstracts_deleteReview($review, false);
            }
        }
    }

    function prepare_items() {
        global $wpdb, $aID, $_wp_column_headers;
        $abstract_id = $this->_args['id'];

	$screen = get_current_screen();
        $table_name = $wpdb->prefix."wpabstracts_reviews";
        $query = "SELECT review_id, abstract_id, comments, user_id, status, relevance, quality, recommendation, review_date  FROM  . $table_name
                    WHERE abstract_id = $abstract_id";
        $orderby = !empty($_GET["orderby"]) ?esc_sql($_GET["orderby"]) : 'review_id';
        $order = !empty($_GET["order"]) ?esc_sql($_GET["order"]) : 'desc';
        if(!empty($orderby) & !empty($order)){ $query.=' ORDER BY '.$orderby.' '.$order; }
        $totalitems = $wpdb->query($query); //return the total number of affected rows
        //How many to display per page?
        $perpage = 15;
        //Which page is this?
        $paged = !empty($_GET["paged"]) ?esc_sql($_GET["paged"]) : '';
        //Page Number
        if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }
        //How many pages do we have in total?
        $totalpages = ceil($totalitems/$perpage);
        //adjust the query to take pagination into account
        if(!empty($paged) && !empty($perpage)){
            $offset=($paged-1)*$perpage;
            $query.=' LIMIT '.(int)$offset.','.(int)$perpage;
        }

        /* -- Register the pagination -- */
        $this->set_pagination_args( array(
            "total_items" => $totalitems,
            "total_pages" => $totalpages,
            "per_page" => $perpage,
        ) );
        /* -- Register the pagination -- */
        $this->process_bulk_action();

        /* -- Fetch the items -- */
        $this->items = $wpdb->get_results($query);

        /* -- Register the Columns -- */
        $columns = $this->get_columns();
        $_wp_column_headers[$screen->id]=$columns;
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);



    } // end prepare items

} // end REVIEW Table class

class WPAbstract_Events_Table extends WP_List_Table {

    function __construct(){

        global $status, $page;

        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'event',     //singular name of the listed records
            'plural'    => 'events',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );

    }

    function column_name($item){
        $actions = array(
            'edit' => '<a href="?page=wpabstracts&tab=events&task=edit&id=' . $item->event_id . '">' . __('Edit', 'wpabstracts') . '</a>',
            'delete' => '<a href="javascript:confirm_event_delete(' . $item->event_id . ')">' . __('Delete', 'wpabstracts') . '</a>'
        );
        return sprintf('%1$s <span style="color:silver">[ID: %2$s]</span>%3$s',$item->name, $item->event_id, $this->row_actions($actions));
    }

    function column_cb($item) {
        return sprintf('<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $item->event_id);
    }

    function column_count($item){
        global $wpdb;
        $count = $wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."wpabstracts_abstracts WHERE event = ". $item->event_id);
        echo $count;
    }

     function column_default( $item, $column_name ) {
        switch( $column_name ) {
          case 'name': echo $item->$column_name; break;
          case 'address': echo $item->$column_name; break;
          case 'host': echo $item->$column_name; break;
          case 'topics': echo $item->$column_name; break;
          case 'sessions': echo $item->$column_name; break;
          case 'start_date': echo $item->$column_name; break;
          case 'end_date': echo $item->$column_name; break;
          case 'deadline': echo $item->$column_name; break;
        }
    }

    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',
            'name' => __('Event Name', 'wpabstracts'),
            'address' => __('Location', 'wpabstracts'),
            'host' => __('Host', 'wpabstracts'),
            'topics' => __('Topics', 'wpabstracts'),
            'sessions' => __('Sessions', 'wpabstracts'),
            'start_date' => __('Start Date', 'wpabstracts'),
            'end_date' => __('End Date', 'wpabstracts'),
            'deadline' => __('Deadline', 'wpabstracts'),
            'count' => __('Submissions', 'wpabstracts'),
        );
        return $columns;
    }

    /**
     *
     * @return array
     */
    function get_sortable_columns() {
        $sortable_columns = array(
            'event_id'    => array('event_id',true),
            'name'     => array('name',false),
            'host'    => array('host',false),
            'start_date'    => array('start_date',false),
            'end_date'    => array('end_date',false)
        );
        return $sortable_columns;
    }

    /**
     *
     * @return array
     */
    function get_bulk_actions() {
        $actions = array(
            'delete' => __('Delete', 'wpabstracts')
        );
        return $actions;
    }

    function process_bulk_action() {
        if ( 'delete'=== $this->current_action() ) {
            foreach($_GET['edit'] as $event) {
                wpabstracts_deleteEvent($event, false);
            }
        }
    }

    function prepare_items() {
        global $wpdb, $_wp_column_headers;
	$screen = get_current_screen();
        $table_name = $wpdb->prefix."wpabstracts_events";
        $query = "SELECT * FROM  . $table_name";
        $orderby = !empty($_GET["orderby"]) ?esc_sql($_GET["orderby"]) : 'event_id';
        $order = !empty($_GET["order"]) ?esc_sql($_GET["order"]) : 'desc';
        if(!empty($orderby) & !empty($order)){ $query.=' ORDER BY '.$orderby.' '.$order; }
        $totalitems = $wpdb->query($query); //return the total number of affected rows
        //How many to display per page?
        $perpage = 15;
        //Which page is this?
        $paged = !empty($_GET["paged"]) ?esc_sql($_GET["paged"]) : '';
        //Page Number
        if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }
        //How many pages do we have in total?
        $totalpages = ceil($totalitems/$perpage);
        //adjust the query to take pagination into account
        if(!empty($paged) && !empty($perpage)){
            $offset=($paged-1)*$perpage;
            $query.=' LIMIT '.(int)$offset.','.(int)$perpage;
        }

        /* -- Register the pagination -- */
        $this->set_pagination_args( array(
            "total_items" => $totalitems,
            "total_pages" => $totalpages,
            "per_page" => $perpage,
        ) );
        /* -- Register the pagination -- */
        $this->process_bulk_action();

        /* -- Fetch the items -- */
        $this->items = $wpdb->get_results($query);

        /* -- Register the Columns -- */
        $columns = $this->get_columns();
        $_wp_column_headers[$screen->id]=$columns;
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
    } // end prepare items

} // end Event Table class

class WPAbstracts_EmailsTemplates extends WP_List_Table {

    function __construct(){
        global $status, $page;

        //Set parent defaults
        parent::__construct( array(
            'singular' => 'template', //singular name of the listed records
            'plural'    => 'templates',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );

    }

    function column_name($item){
        global $wpdb;
        $actions = array(
            'edit' => '<a href="?page=wpabstracts&tab=emails&task=edit&id=' . $item->ID . '">' . __('Edit', 'wpabstracts') . '</a>',
            'test' => '<a href="?page=wpabstracts&tab=emails&task=testemail&id=' . $item->ID . '">' . __('Send test email', 'wpabstracts') . '</a>',
        );
        return sprintf('%1$s<span style="color:silver"> [%2$s]</span>%3$s', $item->name, $item->ID, $this->row_actions($actions));
    }

    function column_cb($item) {
        return sprintf('<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $item->ID);
    }

    function column_default( $item, $column_name ) {
        switch( $column_name ) {
            case 'id': echo $item->$column_name; break;
            case 'name': _e($item->$column_name, 'wpabstracts');break;
            case 'subject': _e($item->$column_name, 'wpabstracts');break;
            case 'from_name': _e($item->$column_name, 'wpabstracts');break;
            case 'from_email': _e($item->$column_name, 'wpabstracts');break;
            case 'receiver': _e($item->$column_name, 'wpabstracts');break;
        }
    }

    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',
            'name' => __('Email Template', 'wpconference'),
            'subject' => __('Subject', 'wpconference'),
            'from_name' => __('From Name', 'wpconference'),
            'from_email' => __('From Email', 'wpconference'),
            'receiver' => __('Email Group', 'wpconference'),
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'name'    => array('id',true)
        );
        return $sortable_columns;
    }

    function process_bulk_action() {

        if ( 'delete'=== $this->current_action() ) {

        }
    }

    function prepare_items() {
        global $wpdb, $_wp_column_headers;
	$screen = get_current_screen();
        $table_name = $wpdb->prefix."wpabstracts_emailtemplates";
        $query = "SELECT * FROM  . $table_name";
        $orderby = !empty($_GET["orderby"]) ?esc_sql($_GET["orderby"]) : 'ID';
        $order = !empty($_GET["order"]) ?esc_sql($_GET["order"]) : 'desc';
        if(!empty($orderby) & !empty($order)){ $query.=' ORDER BY '.$orderby.' '.$order; }
        $totalitems = $wpdb->query($query); //return the total number of affected rows
        $perpage = 15;
        $paged = !empty($_GET["paged"]) ?esc_sql($_GET["paged"]) : '';
        if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }
        $totalpages = ceil($totalitems/$perpage);
        if(!empty($paged) && !empty($perpage)){
            $offset=($paged-1)*$perpage;
            $query.=' LIMIT '.(int)$offset.','.(int)$perpage;
        }
        $this->set_pagination_args( array(
            "total_items" => $totalitems,
            "total_pages" => $totalpages,
            "per_page" => $perpage,
        ) );
        /* -- Register the pagination -- */
        $this->process_bulk_action();

        /* -- Fetch the items -- */
        $this->items = $wpdb->get_results($query);

        /* -- Register the Columns -- */
        $columns = $this->get_columns();
        $_wp_column_headers[$screen->id]=$columns;
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
    } // end prepare items

} // end

class WPAbstracts_Users extends WP_List_Table {

    function __construct(){
        global $status, $page;

        //Set parent defaults
        parent::__construct( array(
            'singular' => 'user', //singular name of the listed records
            'plural'    => 'users',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );

    }

    function column_cb($item) {
        return sprintf('<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $item->ID);
    }

    function column_ID($item){
        $fullname = ($item->display_name != "") ? $item->display_name : $item->user_login;
        $actions = array(
            'edit' => '<a href=' . admin_url( "user-edit.php?user_id=$item->ID" ) . '>' . __('Edit', 'wpabstracts') . '</a>',
            'delete' => '<a href="javascript:wpabstracts_delete_user(' . $item->ID . ')">' . __('Delete', 'wpabstracts') . '</a>',
        );
        return sprintf('%1$s %2$s', $fullname, $this->row_actions($actions));
    }

    function column_default( $item, $column_name ) {
        $user = get_user_by( 'id', $item->ID );
        switch ($user->roles[0]){
            case 'subscriber':
                $user_role = "Author"; break;
            case 'editor':
                $user_role = "Reviewer"; break;
            case 'administrator':
                $user_role = "Site Admin"; break;
            default :
                $user_role = $user->roles[0]; break;
        }
        switch( $column_name ) {
            case 'user_email':
                _e($user->user_email, 'wpabstracts');
                break;
            case 'user_login':
                _e($user->user_login, 'wpabstracts');
                break;
            case 'account':
                _e($user_role, 'wpconference');
                break;
        }
    }

    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',
            'ID' => __('Full Name', 'wpabstracts'),
            'user_login' => __('Username', 'wpabstracts'),
            'user_email' => __('Email', 'wpabstracts'),
            'account' => __('Type', 'wpabstracts')
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'ID'    => array('ID',true),
            'user_login'    => array('user_login',false),
            'user_email'    => array('user_email',false)
        );
        return $sortable_columns;
    }

    function get_bulk_actions() {
        $actions = array(
            'delete' => __('Delete', 'wpabstracts'),
        );
        return $actions;
    }

    function process_bulk_action() {

        if ( 'delete'=== $this->current_action() ) {
            foreach($_GET['user'] as $user) {
                wpconference_deleteUser($user, false);
            }
        }
    }

    function search_box() { ?>
        <p class="search-box"><br>
                <label class="screen-reader-text" for="user_search"><?php _e('Search users', 'wpabstracts');?></label>
                <input type="search" id="user_search" name="s" value="<?php _admin_search_query(); ?>" />
                <?php submit_button( "Search Users", 'button', false, false, array('id' => 'search-submit') ); ?>
        </p>

    <?php

    }

    function prepare_items() {
        global $_wp_column_headers;
	$screen = get_current_screen();

        $orderby = !empty($_GET["orderby"]) ?esc_sql($_GET["orderby"]) : 'ID';
        $order = !empty($_GET["order"]) ?esc_sql($_GET["order"]) : 'desc';
        if(!empty($orderby) & !empty($order)){ $query='orderby='.$orderby.'&order='.$order; }
        $totalitems = count(get_users($query)); //return the total number of affected rows
        $perpage = 30;
        $paged = !empty($_GET["paged"]) ?esc_sql($_GET["paged"]) : '';
        if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }
        $totalpages = ceil($totalitems/$perpage);
        if(!empty($paged) && !empty($perpage)){
            //$offset=($paged-1)*$perpage;
            $query.='&number='.(int)$perpage;
        }

        $this->search_box();

        if(isset($_GET['s'])){
            $query .= "&search=*" . trim(mysql_real_escape_string($_GET['s'])) . "*";
        }

        $this->set_pagination_args( array(
            "total_items" => $totalitems,
            "total_pages" => $totalpages,
            "per_page" => $perpage,
        ) );
        /* -- Register the pagination -- */
        $this->process_bulk_action();

        /* -- Fetch the items -- */
        //echo $query;
        $this->items = get_users($query);
        //print_r($this->items);

        /* -- Register the Columns -- */
        $columns = $this->get_columns();
        $_wp_column_headers[$screen->id]=$columns;
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
    } // end prepare items

} // end
