/*
 * Allows abstracts to be converted to PDF and downloaded
 */

function wpabstracts_downloadAbstract($abstractID) {
    global $wpdb;
    $abstract = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "wpabstracts_abstracts
                                            WHERE abstract_id = $abstractID");
    $event = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "wpabstracts_events
                                            WHERE event_id = " . $abstract[0]->event);

    include("mpdf/mpdf.php");
    $mpdf = new mPDF();
    $styleUrl = plugins_url('../css/pdf.css', __FILE__);
    $stylesheet = file_get_contents($styleUrl);
    $mpdf->WriteHTML($stylesheet, 1);

    $header = __("Abstract ID", 'wpabstracts') . ": " . $abstract[0]->abstract_id . " for " . get_option(blogname) . " (" . __("Auto-Generated", 'wpabstracts') . " " . date_i18n(get_option('date_format') . ' ' . get_option('time_format'), date("d-m-y g:i a")) . ")";
    $mpdf->SetHeader($header);
    $filename = $abstract[0]->title . " (ID_" . $abstract[0]->abstract_id . ").pdf";
    $mpdf->SetTitle($filename);
    $mpdf->SetAuthor($abstract[0]->author);
    $footer = "Copyright " . date("Y") . " " . get_option(blogname) . " powered by WPAbstracts Pro";
    $mpdf->SetFooter($footer);
    ob_start();
    ?>
        <html>
        <body>
            <div id="wrap">
                <div id="header">
                    <h1><?php echo $abstract[0]->title; ?><span id="titleAuthor"> <?php _e('by','wpabstracts'); ?> <?php echo $abstract[0]->author; ?></span></h1>
                </div>
                <div id="headerbar"><?php _e('Abstract ID', 'wpabstracts'); ?>: <?php echo $abstract[0]->abstract_id; ?></div>

                        <div id="main">
                          <h4><?php _e('Target Group', 'wpabstracts'); ?></h4>
                          <p><?php echo wpautop(stripslashes($abstract[0]->target_group)); ?></p>
                          <h4><?php _e('Resume', 'wpabstracts'); ?></h4>
                          <p><?php echo wpautop(stripslashes($abstract[0]->resume)); ?></p>
                        <h4><?php _e('Abstract Details', 'wpabstracts'); ?></h4>
                        <p><?php echo wpautop(stripslashes($abstract[0]->text)); ?></p>
                    </div>
                    <div id="sidebar">
                        <h4><?php _e('Event Information', 'wpabstracts'); ?></h4>
                        <p><?php _e('Event', 'wpabstracts'); ?>: <?php echo $event->name; ?></p>
                        <p><?php _e('Topic', 'wpabstracts'); ?>: <?php echo $abstract[0]->topic; ?></p>

                            <h4><?php _e('Presenter Information', 'wpabstracts'); ?></h4>
                        <p><?php _e('Presenter', 'wpabstracts'); ?>: <?php echo $abstract[0]->presenter; ?></p>
                        <p><?php _e('Email', 'wpabstracts'); ?>: <?php echo $abstract[0]->presenter_email; ?></p>
                        <p><?php _e('Phone', 'wpabstracts'); ?>: <?php echo $abstract[0]->presenter_phone; ?></p>
                        <p><?php _e('LinkedIn', 'wpabstracts'); ?>: <?php echo $abstract[0]->presenter_linkedin; ?></p>
                        <p><?php _e('Preference', 'wpabstracts'); ?>: <?php echo $abstract[0]->presenter_preference; ?></p>

                        </div>
                </div>
            </body>
        </html>

        <?php
        $html = ob_get_contents();
        ob_end_clean();
    $mpdf->WriteHTML($html, 2);
    $mpdf->Output($filename, "I");
    exit(0);
}

function wpabstracts_downloadAbstracts($abstractIDs) {
  global $wpdb;
  $abstracts_table = $wpdb->prefix."wpabstracts_abstracts";
  $events_table = $wpdb->prefix."wpabstracts_events";


  $sql = "SELECT a.*,b.name FROM $abstracts_table a, $events_table b WHERE a.abstract_id IN (".implode(',', $abstractIDs).") AND a.event = b.event_id";
  $abstracts = $wpdb->get_results($sql);
  include("mpdf/mpdf.php");
  $mpdf = new mPDF();
  $styleUrl = plugins_url('../css/pdf.css', __FILE__);
  $stylesheet = file_get_contents($styleUrl);
  $mpdf->WriteHTML($stylesheet, 1);
  $header = get_option(blogname) . " (" . __("Auto-Generated", 'wpabstracts') . " " . date_i18n(get_option('date_format') . ' ' . get_option('time_format'), date("d-m-y g:i a")) . ")";
  $mpdf->SetHeader($header);
  $filename = 'abstracts.pdf';
  $mpdf->SetTitle($filename);
  $footer = "Copyright " . date("Y") . " " . get_option(blogname) . " powered by WPAbstracts Pro";
  $mpdf->SetFooter($footer);
  ob_start();
  ?>
      <html>
      <body>
        <?php
          foreach($abstracts as $abstract) {
        ?>
          <div id="wrap">
              <div id="header">
                  <h1><?php echo $abstract->title; ?><span id="titleAuthor"> <?php _e('by','wpabstracts'); ?> <?php echo $abstract->presenter; ?></span></h1>
              </div>
              <div id="headerbar"><?php _e('Abstract ID', 'wpabstracts'); ?>: <?php echo $abstract->abstract_id; ?></div>

                    <div id="main">
                      <div>
                        <h4><?php _e('Target Group', 'wpabstracts'); ?></h4>
                        <p><?php echo wpautop(stripslashes($abstract->target_group)); ?></p>
                      </div>
                      <div>
                        <h4><?php _e('Resume', 'wpabstracts'); ?></h4>
                        <p><?php echo wpautop(stripslashes($abstract->resume)); ?></p>
                      </div>
                      <div>
                        <h4><?php _e('Abstract Details', 'wpabstracts'); ?></h4>
                        <p><?php echo wpautop(stripslashes($abstract->text)); ?></p>
                      </div>
                  </div>
                  <div id="sidebar">
                      <h4><?php _e('Event Information', 'wpabstracts'); ?></h4>
                      <p><?php _e('Event', 'wpabstracts'); ?>: <?php echo $abstract->name; ?></p>
                      <p><?php _e('Topic', 'wpabstracts'); ?>: <?php echo $abstract->topic; ?></p>

                          <h4><?php _e('Presenter Information', 'wpabstracts'); ?></h4>
                      <p><?php _e('Presenter', 'wpabstracts'); ?>: <?php echo $abstract->presenter; ?></p>
                      <p><?php _e('Email', 'wpabstracts'); ?>: <?php echo $abstract->presenter_email; ?></p>
                      <p><?php _e('Phone', 'wpabstracts'); ?>: <?php echo $abstract->presenter_phone; ?></p>
                      <p><?php _e('LinkedIn', 'wpabstracts'); ?>: <?php echo $abstract->presenter_linkedin; ?></p>

                      </div>
              </div>
              <pagebreak />
              <?php //$mpdf->AddPage();
              } ?>
          </body>
      </html>

      <?php
      $html = ob_get_contents();
      ob_end_clean();
  $mpdf->WriteHTML($html, 2);
  $mpdf->Output($filename, "I");
  exit(0);
}
