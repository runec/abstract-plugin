<?php defined('ABSPATH') or die("ERROR: You do not have permission to access this page");
global $wpdb;
if(!class_exists('WPABSTRACTS')){
    require_once( WPABSTRACTS_PLUGIN_DIR . 'inc/wpabstracts_functions.php' );
}

$abstracts_submitted_count = $wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."wpabstracts_abstracts");

$abstracts_approved_count = $wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."wpabstracts_abstracts WHERE status = 'Approved'");

$abstracts_pending_count = $wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."wpabstracts_abstracts WHERE status = 'Pending'");

$abstracts_rejected_count = $wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."wpabstracts_abstracts WHERE status = 'Rejected'");

$reviews_submitted_count = $wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."wpabstracts_reviews");

$reviews_excellent_count = $wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."wpabstracts_reviews WHERE relevance = 'Excellent'");

$reviews_good_count = $wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."wpabstracts_reviews WHERE relevance = 'Good'");

$reviews_average_count = $wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."wpabstracts_reviews WHERE relevance = 'Average'");

$reviews_poor_count = $wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."wpabstracts_reviews WHERE relevance = 'Poor'");
?>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
  google.load("visualization", "1", {packages:["corechart"]});
  google.setOnLoadCallback(drawAbstractsReport);
  google.setOnLoadCallback(drawReviewsReport);
  google.setOnLoadCallback(drawEventReport);

  function drawAbstractsReport() {

   var submitted =  <?php echo($abstracts_submitted_count); ?>;
   var approved =  <?php echo($abstracts_approved_count); ?>;
   var pending =  <?php echo($abstracts_pending_count); ?>;
   var rejected =  <?php echo($abstracts_rejected_count); ?>;

    var data = google.visualization.arrayToDataTable([
      ['<?php _e('Status', 'wpabstracts'); ?>', '', { role: 'style' }],
      ['<?php _e('Submitted', 'wpabstracts'); ?>',  submitted, 'color: #CCCCCC'],
      ['<?php _e('Approved', 'wpabstracts'); ?>',  approved, 'color: #CCCCCC'],
      ['<?php _e('Pending', 'wpabstracts'); ?>',  pending, 'color: #CCCCCC'],
      ['<?php _e('Rejected', 'wpabstracts'); ?>',  rejected, 'color: #CCCCCC']
    ]);

    var options = {
      title: '<?php _e('ABSTRACTS BY STATUS', 'wpabstracts'); ?>',
      hAxis: {title: '<?php _e('STATUS', 'wpabstracts'); ?>', titleTextStyle: {color: 'black'}},
      vAxis: {title: '<?php _e('SUBMITTED', 'wpabstracts'); ?>', titleTextStyle: {color: 'black'}, maxValue:'<?php echo($abstracts_submitted_count); ?>', format: '#' } };

    var chart = new google.visualization.ColumnChart(document.getElementById('wpabstracts_status_report'));
    chart.draw(data, options);
  }
   function drawReviewsReport() {

    var excellent =  <?php echo($reviews_excellent_count); ?>;
    var good =  <?php echo($reviews_good_count); ?>;
    var average =  <?php echo($reviews_average_count); ?>;
    var poor =  <?php echo($reviews_poor_count); ?>;

    var data = google.visualization.arrayToDataTable([
      ['<?php _e('Status', 'wpabstracts'); ?>', '', { role: 'style' }],
      ['<?php _e('Excellent', 'wpabstracts'); ?>',  excellent, 'color: #CCCCCC'],
      ['<?php _e('Good', 'wpabstracts'); ?>',  good, 'color: #CCCCCC'],
      ['<?php _e('Average', 'wpabstracts'); ?>',  average, 'color: #CCCCCC'],
      ['<?php _e('Poor', 'wpabstracts'); ?>',  poor, 'color: #CCCCCC']
    ]);

    var options = {
      title: '<?php _e('REVIEWS BY RELEVANCE', 'wpabstracts'); ?>',
      hAxis: {title: '<?php _e('RELEVANCE', 'wpabstracts'); ?>', titleTextStyle: {color: 'black'}},
      vAxis: {title: '<?php _e('SUBMITTED', 'wpabstracts'); ?>', titleTextStyle: {color: 'black'}, maxValue:'<?php echo($reviews_submitted_count); ?>', format:'#'}};

    var chart = new google.visualization.ColumnChart(document.getElementById('wpabstracts_review_report'));
    chart.draw(data, options);
  }
</script>

<div class="metabox-holder has-right-sidebar">
    <div class="inner-sidebar">
        <div class="postbox">
            <h3><?php _e('Abstracts - Export by status', 'wpabstracts');?></h3>
            <div class="inside">
                <p class="export_reports"> <?php _e('Approved', 'wpabstracts');?> (<?php echo $abstracts_approved_count; ?>)<a href="?page=wpabstracts&tab=reports&task=download&reportType=abstracts&reportID=1"><?php _e('Export CSV', 'wpabstracts');?></a></p>
                <p class="export_reports"> <?php _e('Pending', 'wpabstracts');?> (<?php echo $abstracts_pending_count; ?>)<a href="?page=wpabstracts&tab=reports&task=download&reportType=abstracts&reportID=2"><?php _e('Export CSV', 'wpabstracts');?></a></p>
                <p class="export_reports"> <?php _e('Rejected', 'wpabstracts');?> (<?php echo $abstracts_rejected_count; ?>)<a href="?page=wpabstracts&tab=reports&task=download&reportType=abstracts&reportID=3"><?php _e('Export CSV', 'wpabstracts');?></a></p>
                <p class="export_reports"> <?php _e('All Abstracts', 'wpabstracts');?> (<?php echo $abstracts_submitted_count; ?>)<a href="?page=wpabstracts&tab=reports&task=download&reportType=abstracts&reportID=4"><?php _e('Export CSV', 'wpabstracts');?></a></p>
            </div>
        </div>
        <div class="postbox">
            <h3><span><?php _e('Reviews - Export by relevance', 'wpabstracts');?></span></h3>
            <div class="inside">
                <p class="export_reports"> <?php _e('Excellent', 'wpabstracts');?> (<?php echo $reviews_excellent_count; ?>)<a href="?page=wpabstracts&tab=reports&task=download&reportType=reviews&reportID=1"><?php _e('Export CSV', 'wpabstracts');?></a></p>
                <p class="export_reports"> <?php _e('Good', 'wpabstracts');?> (<?php echo $reviews_good_count; ?>)<a href="?page=wpabstracts&tab=reports&task=download&reportType=reviews&reportID=2"><?php _e('Export CSV', 'wpabstracts');?></a></p>
                <p class="export_reports"> <?php _e('Average', 'wpabstracts');?> (<?php echo $reviews_average_count; ?>)<a href="?page=wpabstracts&tab=reports&task=download&reportType=reviews&reportID=3"><?php _e('Export CSV', 'wpabstracts');?></a></p>
                <p class="export_reports"> <?php _e('Poor', 'wpabstracts');?> (<?php echo $reviews_poor_count; ?>)<a href="?page=wpabstracts&tab=reports&task=download&reportType=reviews&reportID=4"><?php _e('Export CSV', 'wpabstracts');?></a></p>
                <p class="export_reports"> <?php _e('All Reviews', 'wpabstracts');?> (<?php echo $reviews_submitted_count; ?>)<a href="?page=wpabstracts&tab=reports&task=download&reportType=reviews&reportID=5"><?php _e('Export CSV', 'wpabstracts');?></a></p>
            </div>
        </div>
        <div class="postbox">
            <h3><span><?php _e('Help us Improve', 'wpabstracts');?></span></h3>
            <div class="inside">
                <p><?php _e('These report and export features are new to WP Abstracts Pro, please <a href="mailto=support@wpabstracts.com">let me know</a> what other types of reports would be convenient to implement.', 'wpabstracts');?></p>
            </div>
        </div>
    </div>
    <div id="post-body">
        <div id="post-body-content">
            <div class="postarea">
                <div id="wpabstracts_status_report"></div>
                <br />
                <div id="wpabstracts_review_report"></div>
            </div>
        </div>
    </div>
</div>
