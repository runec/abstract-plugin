<?php
defined('ABSPATH') or die("ERROR: You do not have permission to access this page");



if (isset($_GET['attachmentID']) && ($_GET['attachmentID'])) {
    wpabstracts_downloadAttachment();
} else if (isset($_GET['reportType']) && ($_GET['reportID'])) {
    wpabstracts_downloadReport($_GET['reportType'], $_GET['reportID']);
} else if (isset($_GET['downloadID']) && ($_GET['downloadID'])) {
    wpabstracts_downloadAbstract(intval($_GET['downloadID']));
} else if (isset($_GET['action']) && $_GET['action'] == 'multiPDFdownload') {
    $ids = array_map('intval', $_GET['abstract']);
    wpabstracts_downloadAbstracts($ids);
} else if (isset($_GET['eid']) && isset($_GET['topic'])) {
  wpabstracts_exportAbstracts(intval($_GET['eid']),$_GET['topic']);
}

/* Download abstracts */
function wpabstracts_exportAbstracts($event_id, $topic) {
  // We'll be outputting an excel file
  header('Content-type: application/vnd.ms-excel');
  // It will be called:
  header('Content-Disposition: attachment; filename="abstracts.xlsx"');
  global $wpdb;

  //titel, begivenhed, oplægsholder, antal godkend, antal anmeldere, status
  if($event_id == 'all') {
    $sql = "SELECT * FROM ".$wpdb->prefix."wpabstracts_abstracts";
  }
  else if($topic === "" || $topic == "all") {
    $sql = $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."wpabstracts_abstracts
                           WHERE event = %d", $event_id);
  }
  else {
    $sql = $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."wpabstracts_abstracts
                           WHERE event = %d AND TRIM(topic) = %s", $event_id, $topic);
  }
  $abstracts = $wpdb->get_results($sql, ARRAY_A);



 //Include necessary(?) wordpress library files
 $location = get_theme_root().'/../..';
 include ($location . '/wp-config.php');
 include ($location . '/wp-load.php');
 include ($location . '/wp-includes/pluggable.php');
 //PHPExcel library
 require_once get_theme_root().'/geoforum/lib/phpexcel/PHPExcel.php';

 //require_once ($location.'/wp-content/plugins/events-manager/classes/em-event.php');

 $objEx = new PHPExcel();
 $entries = array();

 foreach($abstracts as $abstract) {

   //Get number of approving, rejecting and total reviews
   $reviewer_ids = array();
   for($i = 1; $i <= 15; $i++) {
     $reviewer_ids[] = $abstract['reviewer_id'.$i];
   }
   $reviewer_ids = array_filter($reviewer_ids);

   $reviews = wpabstracts_getReviews('abstract_id', intval($abstract['abstract_id']));
   $count_approved = 0;
   $count_rejected = 0;
   $count_reviews = 0;
   foreach($reviews as $review) {
     if(in_array($review->user_id, $reviewer_ids)) {
       if($review->status == 'Approved') {
         $count_approved++;
       }
       else if($review->status == 'Rejected') {
         $count_rejected++;
       }
      $count_reviews++;
     }
   }
   //Relevance and Quality
   if($count_reviews === 0) {
     $relevance = '-';
     $quality = '-';
   }
   else {
     $relevance = 0;
     $quality = 0;
     foreach($reviews as $review) {
       if(in_array($review->user_id, $reviewer_ids)) {
         //Quality
         switch($review->quality) {
           case 'Excellent':
              $quality += 4;
              break;
          case 'Good':
             $quality += 3;
             break;
         case 'Average':
            $quality += 2;
            break;
          case 'Poor':
             $quality += 1;
             break;
         }

         switch($review->relevance) {
           case 'Excellent':
              $relevance += 4;
              break;
          case 'Good':
             $relevance += 3;
             break;
         case 'Average':
            $relevance += 2;
            break;
          case 'Poor':
             $relevance += 1;
             break;
         }
       }
     }

     //Take avarage
     $quality = $quality / $count_reviews;
     $relevance = $relevance / $count_reviews;
   }


   //Get event info
   $event_result = wpabstracts_getEvents('event_id', $abstract['event'], ARRAY_A);
   if($event_result) {
     $event_name = $event_result['name'];
   } else {
     $event_name = 'Not found';
   }

   $entries[] = array(
     $abstract['abstract_id'],
     $abstract['title'], //Titel
     $event_name,       //Event
     trim($abstract['topic']), //Emne
     $abstract['presenter'], //Oplægsholder
     $abstract['presenter_company'],
     $abstract['presenter_email'],
     $relevance, //Relevans
     $quality, //Kvalitet
     $count_reviews === 0 ? '-' : $count_approved / $count_reviews, //Andel godkendt
     $count_approved, //Antal godkendt
     $count_rejected, //Antal afvist
     $count_reviews,  //Antal anmeldere
     __($abstract['status'],'wpabstracts'), //Tildelt status
     $abstract['presenter_perspektiv'] == 1 ? "Ja" : "Nej",
     count($reviewer_ids)
   );
 }

 //Add column headers
 $sheet = $objEx->setActiveSheetIndex(0);
 $sheet->setCellValue('A1','ID')
       ->setCellValue('B1','Titel')
       ->setCellValue('C1','Event')
       ->setCellValue('D1','Emne')
       ->setCellValue('E1','Oplægsholder')
       ->setCellValue('F1', 'Virksomhed')
       ->setCellValue('G1', 'Email')
       ->setCellValue('H1','Relevans')
       ->setCellValue('I1','Kvalitet')
       ->setCellValue('J1','Andel godkendt')
       ->setCellValue('K1','Antal godkendt')
       ->setCellValue('L1','Antal afvist')
       ->setCellValue('M1','Antal indsendte anmeldelser')
       ->setCellValue('N1','Status')
       ->setCellValue('O1','Artikel til Perspektiv')
       ->setCellValue('P1', 'Antal tildelte anmeldere');
 $objEx->getActiveSheet()->getStyle('A1:P1')->getFont()->setBold(true);

 $row = 2;
 foreach($entries as $entry) {
   for($i = 0; $i < count($entry); $i++) {
     $sheet->setCellValue(chr(ord('A') + $i).$row, $entry[$i]);
   }
   $row++;
 }
 //Resize columns to fit the data
 for($i = 0; $i < count($entries[0]); $i++) {
   $objEx->getActiveSheet()
           ->getColumnDimension(chr(ord('A')+$i))
           ->setAutoSize(true);
 }
 //Excel 2007 (xlsx)
 $objWriter = PHPExcel_IOFactory::createWriter($objEx, 'Excel2007');
 $objWriter->save('php://output');

 //Excel 95 (xls)
 //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
 //$objWriter->save(str_replace('.php', '.xls', __FILE__));
 exit;
}


/*
 * Downloads attachments from submissions
 */

function wpabstracts_downloadAttachment() {
    global $wpdb;
    $id = intval($_GET['attachmentID']);
    $file = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "wpabstracts_attachments WHERE attachment_id=" . $id);
    $content = rawurldecode($file->filecontent);
    header("Cache-Control: no-cache, must-revalidate");
    header("Content-length: " . $file->filesize);
    header("Content-type: " . $file->filetype);
    header("Content-Disposition: attachment; filename=\"$file->filename\"");
    echo $content;
    exit(0);
}

/*
 * Allows reports to be downloaded
 */

function wpabstracts_downloadReport($reportType, $reportID) {
    global $wpdb;
    $reportData = null;
    if ($reportType == "abstracts") {

      $reviewer_select_string = "";
      for($i = 1; $i<= 15; $i++) {
        $reviewer_select_string .= "reviewer_id".$i.", ";
      }

        $report_header = array(__("Abstract", 'wpabstracts') . "ID", __("Title", 'wpabstracts'), __("Description", 'wpabstracts'), __("Event", 'wpabstracts') . "ID", __("Topic", 'wpabstracts'),
            __("Status", 'wpabstracts'), __("Authors", 'wpabstracts'), __("AuthorsEmail", 'wpabstracts'), __("AuthorAffiliation", 'wpabstracts'), __("Presenter", 'wpabstracts'), __("PresenterEmail", 'wpabstracts'),
            __("Preference", 'wpabstracts'), __("Reviewer", 'wpabstracts') . "ID1", __("Reviewer", 'wpabstracts') . "ID2", __("Reviewer", 'wpabstracts') . "ID3", __("Reviewer", 'wpabstracts') . "ID4",
            __("Reviewer", 'wpabstracts') . "ID5", __("Reviewer", 'wpabstracts') . "ID6", __("Reviewer", 'wpabstracts') . "ID7", __("Reviewer", 'wpabstracts') . "ID8", __("User", 'wpabstracts') . "ID", __("Submitted", 'wpabstracts'));

        if ($reportID == 1) {
            $reportName = __("Approved Abstracts", 'wpabstracts');
            $reportData = $wpdb->get_results("SELECT abstract_id, title, text, event, topic,
                                status, author, author_email, author_affiliation, presenter, presenter_email,
                                presenter_preference, ".$reviewer_select_string." submit_by,
                                submit_date FROM " . $wpdb->prefix . "wpabstracts_abstracts
                                WHERE status = 'Approved'", ARRAY_N);
        } else if ($reportID == 2) {
            $reportName = __("Pending Abstracts", 'wpabstracts');
            $reportData = $wpdb->get_results("SELECT abstract_id, title, text, event, topic,
                                status, author, author_email, author_affiliation, presenter, presenter_email,
                                presenter_preference, ".$reviewer_select_string." submit_by,
                                submit_date FROM " . $wpdb->prefix . "wpabstracts_abstracts
                                WHERE status = 'Pending'", ARRAY_N);
        } else if ($reportID == 3) {
            $reportName = __("Rejected Abstracts", 'wpabstracts');
            $reportData = $wpdb->get_results("SELECT abstract_id, title, text, event, topic,
                                status, author, author_email, author_affiliation, presenter, presenter_email,
                                presenter_preference, ".$reviewer_select_string." submit_by,
                                submit_date FROM " . $wpdb->prefix . "wpabstracts_abstracts
                                WHERE status = 'Rejected'", ARRAY_N);
        } else if ($reportID == 4) {
            $reportName = __("All Abstracts", 'wpabstracts');
            $reportData = $wpdb->get_results("SELECT aabstract_id, title, text, event, topic,
                                status, author, author_email, author_affiliation, presenter, presenter_email,
                                presenter_preference, ".$reviewer_select_string." submit_by,
                                submit_date FROM " . $wpdb->prefix . "wpabstracts_abstracts", ARRAY_N);
        }
    } else if ($reportType == "reviews") {

        $report_header = array(__("ReviewID", 'wpabstracts'), __("AbstractID", 'wpabstracts'), __("UserID", 'wpabstracts'), __("Status", 'wpabstracts'), __("Relevance", 'wpabstracts'),
            __("Quality", 'wpabstracts'), __("Comments", 'wpabstracts'), __("Recommendation", 'wpabstracts'), __("ReviewDate", 'wpabstracts'));

        if ($reportID == 1) {
            $reportName = __("Excellent Reviews", 'wpabstracts');
            $reportData = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "wpabstracts_reviews
                                            WHERE relevance = 'Excellent'", ARRAY_N);
        } else if ($reportID == 2) {
            $reportName = __("Good Reviews", 'wpabstracts');
            $reportData = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "wpabstracts_reviews
                                            WHERE relevance = 'Good'", ARRAY_N);
        } else if ($reportID == 3) {
            $reportName = __("Average Reviews", 'wpabstracts');
            $reportData = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "wpabstracts_reviews
                                            WHERE relevance = 'Average'", ARRAY_N);
        } else if ($reportID == 4) {
            $reportName = __("Poor Reviews", 'wpabstracts');
            $reportData = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "wpabstracts_reviews
                                            WHERE relevance = 'Poor'", ARRAY_N);
        } else if ($reportID == 5) {
            $reportName = __("All Reviews", 'wpabstracts');
            $reportData = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "wpabstracts_reviews", ARRAY_N);
        }
    }

    header("Cache-Control: no-cache, must-revalidate");
    header("Content-Type: text/csv");
    header('Content-Disposition: attachment; filename='.$reportName.".csv");
    ob_start();
    $file_report = fopen('php://output', 'w');
    fputcsv($file_report, array_values($report_header), ",");
    foreach ($reportData AS $data) {
        fputcsv($file_report, array_values(stripslashes_deep($data)), ",");
    }
    fclose($file_report);
    $report = ob_get_contents();
    ob_end_clean();
    echo $report;
    exit(0);
}

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
    $styleUrl = plugins_url('../css/pdf.css?1', __FILE__);
    $stylesheet = file_get_contents($styleUrl);
    $mpdf->WriteHTML($stylesheet, 1);
    $abstract = $abstract[0];

    //$header = __("Abstract ID", 'wpabstracts') . ": " . $abstract[0]->abstract_id . " for " . get_option(blogname) . " (" . __("Auto-Generated", 'wpabstracts') . " " . date_i18n(get_option('date_format') . ' ' . get_option('time_format'), date("d-m-y g:i a")) . ")";
    //$mpdf->SetHeader($header);
    $filename = $abstract->title . " (ID_" . $abstract->abstract_id . ").pdf";
    $mpdf->SetTitle($filename);
    //$mpdf->SetAuthor($abstract[0]->author);
    //$footer = "Copyright " . date("Y") . " " . get_option(blogname) . " powered by WPAbstracts Pro";
    //$mpdf->SetFooter($footer);
    if($abstract->profile_image == null) {
      $profile_image = get_site_url().'/wp-content/plugins/wpabstracts_pro/profile-images/profile-placeholder.jpg';
    }
    else if(preg_match("/^[a-zA-Z0-9]+\.png$/", $abstract->profile_image)) {
      $image_dir = plugin_dir_path( __FILE__ ) . '../profile-images'. '/' . $abstract->profile_image;
      if(file_exists($image_dir) && filesize($image_dir) > 5 * 1024) {//If larger than 5KB
        $profile_image = get_site_url().'/wp-content/plugins/wpabstracts_pro/profile-images/'.$abstract->profile_image;
      } else {
        $profile_image = get_site_url().'/wp-content/plugins/wpabstracts_pro/profile-images/profile-placeholder.jpg';
      }
    }
    else {
      $profile_image = $abstract->profile_image;
    }

    ob_start();
    ?>
        <html>
        <body>

            <div id="wrap">
              <div>
              <div id="abstract-photo">
                <img id="photo" src="<?php echo $profile_image; ?>">
              </div>
              <div id="abstract-id">
                Abstract ID: <?php echo $abstract->abstract_id; ?>
              </div>

              <div id="abstract-title-presenter">
                <h2> <?php echo $abstract->title; ?> </h2>
                <?php echo $abstract->presenter; ?> - <?php echo $abstract->presenter_company; ?>
              </div>
            </div>
              <div id="abstract-resume">
                <h2>Resume</h2>
                <p>
                  <?php echo $abstract->resume ?>
                </p>
              </div>

              <div id="abstract-text">
                <h2>Abstract</h2>
                <p>
                  <?php echo $abstract->text ?>
                </p>
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
  $mpdf->useSubstitutions = false;
  $mpdf->simpleTables = true;
  $mpdf->text_input_as_HTML = true;
  $styleUrl = plugins_url('../css/pdf.css', __FILE__);
  $stylesheet = file_get_contents($styleUrl);
  $mpdf->WriteHTML($stylesheet, 1);
  $mpdf->SetHeader($header);
  $filename = 'abstracts.pdf';
  $mpdf->SetTitle($filename);
  ob_start();
  ?>
      <html>
      <body>
        <?php
          foreach($abstracts as $abstract) {
            $mpdf->AddPage();
            if($abstract->profile_image == null) {
              $profile_image = get_site_url().'/wp-content/plugins/wpabstracts_pro/profile-images/profile-placeholder.jpg';
            }
            else if(preg_match("/^[a-zA-Z0-9]+\.png$/", $abstract->profile_image)) {
              $image_dir = plugin_dir_path( __FILE__ ) . '../profile-images'. '/' . $abstract->profile_image;
              if(file_exists($image_dir) && filesize($image_dir) > 5 * 1024) {//If larger than 5KB
                $profile_image = get_site_url().'/wp-content/plugins/wpabstracts_pro/profile-images/'.$abstract->profile_image;
              } else {
                $profile_image = get_site_url().'/wp-content/plugins/wpabstracts_pro/profile-images/profile-placeholder.jpg';
              }
            }
            else {
              $profile_image = $abstract->profile_image;
            }

        ?>
          <div id="wrap">
            <div id="abstract-photo">
             <img id="photo" src="<?php echo $profile_image; ?>">
            </div>
            <div id="abstract-id">
              Abstract ID: <?php echo $abstract->abstract_id; ?>
            </div>

            <div id="abstract-title-presenter">
              <h2> <?php echo $abstract->title; ?> </h2>
              <?php echo $abstract->presenter; ?> - <?php echo $abstract->presenter_company; ?>
            </div>

            <div id="abstract-resume">
              <h2>Resume</h2>
              <p>
                <?php echo $abstract->resume ?>
              </p>
            </div>
            <div id="abstract-text">
              <h2>Abstract</h2>
              <p>
                <?php echo $abstract->text ?>
              </p>
            </div>
          </div>
              <?php
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
