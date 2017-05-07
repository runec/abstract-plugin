<?php
defined('ABSPATH') or die("ERROR: You do not have permission to access this page");


/*
 * Returns abstract data from db
 * @field the where clause in the DB call
 * @value the value of the where clause
 * @format the format of the return data ..eg. ARRAY_A etc
 * @wpdb WP DB object
 */
function wpabstracts_getAbstracts($field, $value, $format){
    global $wpdb;
    $wpdb->show_errors();
    $ret_value = null;
    switch ($field){
        case 'user_id':
            $sql = $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."wpabstracts_abstracts
                                   WHERE submit_by = %d", $value);
            $ret_value = $wpdb->get_results($sql);
        break;
        case 'abstract_id':
            $sql = $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."wpabstracts_abstracts
                                   WHERE abstract_id = %d", $value, $format);
            $ret_value = $wpdb->get_results($sql);
        break;
        case 'all':
            $sql = $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."wpabstracts_abstracts");
            $ret_value = $wpdb->get_results($sql);
            break;

    }
    return $ret_value;

}
/*
 * Returns review data from db
 * @field the where clause in the DB call
 * @value the value of the where clause
 * @wpdb WP DB object
 */
function wpabstracts_getReviews($field, $value){
    global $wpdb;
    $ret_value = null;
    switch ($field){
        case 'user_id':
            $sql = $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."wpabstracts_reviews
                                   WHERE user_id = %d", $value);
            $ret_value = $wpdb->get_results($sql);
        break;
        case 'abstract_id':
            $sql = $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."wpabstracts_reviews
                                   WHERE abstract_id = %d", $value, ARRAY_A);
            $ret_value = $wpdb->get_results($sql);
        break;
        case 'review_id':
            $sql = $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."wpabstracts_abstracts Where submit_by = %d", $value);
            $ret_value = $wpdb->get_results($sql);
        break;
    }
    return $ret_value;

}
/*
 * Returns attachments data from db
 * @field the where clause in the DB call
 * @value the value of the where clause
 * @wpdb WP DB object
 */
function wpabstracts_getAttachments($field, $value){
    global $wpdb;
    $ret_val = null;
    switch ($field){
        case 'abstracts_id':
            $attachments = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."wpabstracts_attachments WHERE abstracts_id=".$value);
            $ret_val = $attachments;
            break;

    }
    return $ret_val;

}
/*
* Returns events data from db
* @field the where clause in the DB call
* @value the value of the where clause
* @format the format of the return data ..eg. ARRAY_A etc
* @wpdb WP DB object
*/
function wpabstracts_getEvents($field, $value, $format){
   global $wpdb;
   $ret_val = null;
   switch ($field){
       case 'event_id':
           $event = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."wpabstracts_events WHERE event_id = $value", $format);
           $ret_val = $event;
           break;
       case 'all':
           $events = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."wpabstracts_events");
           $ret_val = $events;
           break;
   }
   return $ret_val;
}
/*
* Updates abstract data in db
* @id the abstract id
* @data the POST data to be updated
* @wpdb WP DB object
* @action is the DB action (insert, update or delete)
*/
function wpabstracts_manageAbstracts($id, $data, $action){
    global $wpdb;
    if($data){
        $abs_title = sanitize_text_field($data["abs_title"]);
        $abs_text = wp_kses_post($data["abstext"]);
        $resume_text = wp_kses_post($data["resumetext"]);
        $target_group = wp_kses_post($data["targetgroup"]);
        $comments = wp_kses_post($data["comments"]);
        $manager_comments = wp_kses_post($data['manager-comments']);
        $abs_event = intval($data["abs_event"]);
        $abs_topic = sanitize_text_field($data["abs_topic"]);
        $abs_presenter = sanitize_text_field($data["abs_presenter"]);
        $abs_presenter_company = sanitize_text_field($data["abs_presenter_company"]);
        $abs_presenter_email = strtolower(sanitize_email($data["abs_presenter_email"]));
        $abs_presenter_preference = sanitize_text_field($data["abs_presenter_preference"]);
        $abs_presenter_phone = sanitize_text_field($data["abs_presenter_phone"]);
        $abs_presenter_linkedin = sanitize_text_field($data["abs_presenter_linkedin"]);
        $abs_presenter_perspektiv = $data["abs_presenter_perspektiv"] == 'on' ? 1 : 0;
        if(sizeof($data["abs_author"])>1) {
            foreach($data["abs_author"] as $key=>$author) {
                $author = sanitize_text_field($data["abs_author"][$key]);
                $abs_authors[] = $author;
            }
        $abs_authors = implode(' | ',$abs_authors);
        } else {
            $author = sanitize_text_field($data["abs_author"][0]);
            $abs_authors = $author;
        }
        if(sizeof($data["abs_author_email"])>1) {
            foreach($data["abs_author_email"] as $key=>$author_email) {
                $author_email = sanitize_email($data["abs_author_email"][$key]);
                $abs_authors_email[] = $author_email;
            }
        $abs_authors_email = implode(' | ',$abs_authors_email);
        } else {
            $author_email = sanitize_email($data["abs_author_email"][0]);
            $abs_authors_email = $author_email;
        }
        if(sizeof($data["abs_author_affiliation"])>1) {
            foreach($data["abs_author_affiliation"] as $key=>$author_affiliation) {
                $author_affiliation = sanitize_text_field($data["abs_author_affiliation"][$key]);
                $abs_authors_affiliation[] = $author_affiliation;
            }
        $abs_authors_affiliation = implode(' | ',$abs_authors_affiliation);
        } else {
            $author_affiliation = sanitize_text_field($data["abs_author_affiliation"][0]);
            $abs_authors_affiliation = $author_affiliation;
        }
    }
    switch($action){
        case 'insert':
            $wpdb->show_errors();
            $user_ID = get_current_user_id();
            $date_time = current_time( 'mysql' );
            $wpdb->insert($wpdb->prefix.'wpabstracts_abstracts',
                    array(
                       'title' => $abs_title, 'text' => $abs_text, 'resume' => $resume_text, 'target_group' => $target_group, 'presenter_comments' => $comments, 'event'=>$abs_event,
                       'topic' => $data['abs_topic'], 'status' => "Pending", 'author' => $abs_authors, 'author_email' => $abs_authors_email,
                'author_affiliation' => $abs_authors_affiliation, 'presenter' => $abs_presenter, 'presenter_company' => $abs_presenter_company, 'presenter_email' =>$abs_presenter_email,
                       'presenter_preference' =>$abs_presenter_preference, 'presenter_phone' =>$abs_presenter_phone,
                       'presenter_linkedin' =>$abs_presenter_linkedin, 'presenter_perspektiv' => $abs_presenter_perspektiv, 'submit_by' =>$user_ID, 'submit_date' =>$date_time,
                       'manager_comments' => $manager_comments
                        ));
            $abstract_id = $wpdb->insert_id;
            return $abstract_id;
        case 'update':
            $wpdb->show_errors();
            $original_abstract = wpabstracts_getAbstracts('abstract_id',$id,ARRAY_A);

            $date_time = current_time( 'mysql' );
            $sql = "UPDATE ".$wpdb->prefix."wpabstracts_abstracts
                        SET title = '$abs_title', text = '$abs_text', resume = '$resume_text', target_group = '$target_group', event = '$abs_event', topic = '$abs_topic',
                        author = '$abs_authors', author_email = '$abs_authors_email', author_affiliation = '$abs_authors_affiliation', presenter = '$abs_presenter',
                        presenter_company = '$abs_presenter_company', presenter_email = '$abs_presenter_email', presenter_preference = '$abs_presenter_preference', presenter_phone ='$abs_presenter_phone',
                        presenter_linkedin = '$abs_presenter_linkedin', last_edit_date = '$date_time', manager_comments = '$manager_comments'";
            if($original_abstract[0]->last_edit_date == NULL) {
              $sql = $sql.", unedited_text ='".$original_abstract[0]->text."'
              , unedited_resume ='".$original_abstract[0]->resume."', unedited_target_group ='".$original_abstract[0]->target_group."'";
            }
            $sql = $sql." WHERE abstract_id = '$id'";
            $wpdb->query($sql);
            // user was changed
            if(isset($data['abs_user']) && $data['abs_user']){
                $abs_user = intval($data['abs_user']);
                $wpdb->query("UPDATE ".$wpdb->prefix."wpabstracts_abstracts
                        SET submit_by = '$abs_user'
                        WHERE abstract_id = '$id'");
            }

            if(isset($data['abs_remove_attachments'])){
                $attachmentsIDs = (array) $data["abs_remove_attachments"];
                foreach($attachmentsIDs AS $attachID){
                    $wpdb->query("delete FROM ".$wpdb->prefix."wpabstracts_attachments WHERE attachment_id=".intval($attachID));
                }
            }
        break;
        case 'delete':
            $wpdb->show_errors();
            $abstractID = intval($id);
            $profile_image = $wpdb->get_var("SELECT profile_image FROM ".$wpdb->prefix."wpabstracts_abstracts WHERE abstract_id=".$abstractID);
            if($profile_image != '') {
              unlink(WPABSTRACTS_PROFILE_IMAGE_DIR.$profile_image);
            }

            $wpdb->query("delete FROM ".$wpdb->prefix."wpabstracts_abstracts WHERE abstract_id=".$abstractID);
            $wpdb->query("delete FROM ".$wpdb->prefix."wpabstracts_reviews WHERE abstract_id=".$abstractID);
            $wpdb->query("delete FROM ".$wpdb->prefix."wpabstracts_attachments WHERE abstracts_id=".$abstractID);
        break;
    }

}

/*
 * Saves profile image as a file and add filename to DB
 * @abstract_id ID of the abstract that the image belongs to
 * @data The dataURL of the image (from POST data)
 */
function wpabstracts_manageProfileImage($abstract_id, $data) {
  global $wpdb;

  list($type, $data) = explode(';', $data);
  list(, $data)      = explode(',', $data);
  $data = base64_decode($data);

  $image_filename = $abstract_id.'.png';
  file_put_contents(WPABSTRACTS_PROFILE_IMAGE_DIR.$image_filename, $data);

  $wpdb->show_errors();
  $sql = "UPDATE ".$wpdb->prefix."wpabstracts_abstracts SET profile_image = '$image_filename' WHERE abstract_id = '$abstract_id'";
  $wpdb->query($sql);
}

 /*
 * Updates attachments data in db
 * @id the attachment id
 * @files the _FILES data to be updated
 * @wpdb WP DB object
 * @action is the DB action (insert, update or delete)
 */
function wpabstracts_manageAttachments($id, $files, $action){
    global $wpdb;
     if($files) {
        foreach($files['attachments']['error'] as $key=>$error) {
            if($error==0) {
                    $fileName = $files['attachments']['name'][$key];
                    $tmpName  = $files['attachments']['tmp_name'][$key];
                    $fileSize = $files['attachments']['size'][$key];
                    $fileType = $files['attachments']['type'][$key];
                    $fileExtension = explode('.',$fileName);
                    $fileExt = strtolower($fileExtension[count($fileExtension)-1]);
                    $approvedExtensions = explode(',',get_option('wpabstracts_permitted_attachments'));
                    // checks for approved extension and file size
                    if(in_array($fileExt, $approvedExtensions) and $fileSize<=get_option('wpabstracts_max_attach_size')) {
                            $fp = fopen($tmpName, 'r');
                            $fileContent = rawurlencode(fread($fp, $fileSize));
                            fclose($fp);
                            switch ($action){
                                case 'insert':
                                    $wpdb->show_errors();
                                    $wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."wpabstracts_attachments (abstracts_id,filecontent,filename,filetype,filesize)
                                                                 VALUES (%d,%s,%s,%s,%s)",$id,$fileContent,$fileName,$fileType,$fileSize));
                                    break;
                            }
                    } else{
                        // return error TODO
                    }
            }
        }
    }
}

/*
 * Renders html template
 */
function wpabstracts_getAddView( $type, $id, $focus) {
    global $wpdb;
    $templatePath = WPABSTRACTS_PLUGIN_DIR . $focus . '/wpabstracts_add' . $type . '.php';
    $html = null;
    switch($type){
        case 'Abstract' :
            $events = wpabstracts_getEvents('all', 0, NULL);
            ob_start();
            include ( $templatePath);
            $html = ob_get_contents();
            ob_end_clean();
            break;
        case 'Review':
            $abstract = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."wpabstracts_abstracts WHERE abstract_id = $id", ARRAY_A);
            $attachments = wpabstracts_getAttachments('abstracts_id', $id);
            $event = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."wpabstracts_events where event_id=".$abstract['event']);
            ob_start();
            include ( $templatePath);
            $html = ob_get_contents();
            ob_end_clean();
            break;
        case 'Event' :
            ob_start();
            include ( $templatePath);
            $html = ob_get_contents();
            ob_end_clean();
            break;
        case 'User' :
            ob_start();
            include ( $templatePath);
            $html = ob_get_contents();
            ob_end_clean();
            break;
    }
    echo $html;
}

function wpabstracts_getEditView($type, $id, $focus){
    global $wpdb;
    $id = intval($id); // can never be too safe
    $html = null;
    $templatePath = WPABSTRACTS_PLUGIN_DIR . $focus . '/wpabstracts_edit' . $type . '.php';
    switch($type){
        case 'Abstract':
            $abstract = wpabstracts_getAbstracts('abstract_id', $id, 'ARRAY_A');
            $event = wpabstracts_getEvents('event_id', $abstract[0]->event, 'ARRAY_A');
            $events = wpabstracts_getEvents('all', 0, NULL);
            $topics = explode(',',$event['topics']);
            $sessions = explode(',',$event['sessions']);
            $attachments = wpabstracts_getAttachments('abstracts_id', $abstract[0]->abstract_id);
            ob_start();
            include ( $templatePath);
            $html = ob_get_contents();
            ob_end_clean();
            break;
        case 'Review':
            $review = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."wpabstracts_reviews Where review_id = ".$id, ARRAY_A);
            $abstract = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."wpabstracts_abstracts WHERE abstract_id = ".$review['abstract_id'], ARRAY_A);
            $event = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."wpabstracts_events where event_id=".$abstract['event']);
            ob_start();
            include ( $templatePath);
            $html = ob_get_contents();
            ob_end_clean();
            break;
        case 'Event':
            $abs_event = wpabstracts_getEvents('event_id', $id, 'ARRAY_A');
            $topics = explode(", ", $abs_event['topics']);
            $sessions =  explode(", ", $abs_event['sessions']);
            ob_start();
            include ( $templatePath);
            $html = ob_get_contents();
            ob_end_clean();
            break;
        case 'EmailTemplate':
            $template = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."wpabstracts_emailtemplates WHERE ID = ".$id);
            ob_start();
            include ( $templatePath);
            $html = ob_get_contents();
            ob_end_clean();
            break;
    }
    echo $html;
}

function wpabstracts_redirect($tab){ ?>
    <script type="text/javascript">
        window.location = '<?php echo $tab; ?>';
    </script>
<?php
}

function wpabstracts_loadUserGuide(){
    $templatePath = WPABSTRACTS_PLUGIN_DIR . 'wpabstracts_userguide.php';
    ob_start();
    include ( $templatePath);
    $html = ob_get_contents();
    ob_end_clean();
    return $html;
}

function wpabstracts_showMessage($message){
    if(is_admin()){
        echo "<div id='message' class='updated fade'><strong>$message</strong></div>";
    }else{
        echo "<div class='wpabstracts alert alert-info' role='alert'><strong>$message<strong></div>";
    }
}

function wpabstracts_is_event_active($event_id){
    $event = wpabstracts_getEvents('event_id', $event_id, "ARRAY_A");
    $current_date = date_i18n(get_option('date_format'), strtotime(current_time( 'mysql' )));
    $deadline = strtotime($event['deadline']) - strtotime($current_date);
    //var_dump($deadline);
    //var_dump($event['deadline']);
    return $deadline > 0;
}

function wpabstracts_includeImageButtonScripts() {
  $incpath = WPABSTRACTS_PLUGIN_DIR.'inc/cropper-master/';
  echo $incpath;
  function image_buttton_js_and_css() {
    echo "LOL";
    //wp_enqueue_style($incpath.'assets/css/bootstrap.min.css');
    wp_enqueue_style('cropper-css', $incpath.'cropper.min.css');
    //wp_enqueue_style($incpath.'examples/crop-avatar/css/main.css');
    //wp_enqueue_script($incpath.'assets/js/bootstrap.min.js');
    wp_enqueue_script('cropper-js', $incpath.'cropper.min.js');
    //wp_enqueue_script($incpath.'examples/crop-avatar/js/main.js');
    //wp_enqueue_script($incpath.'assets/js/jquery.min.js');
  }
  add_action('wp_enqueue_scripts', 'image_buttton_js_and_css');
}

function wpabstracts_showImageButton() {
  //Assumes the js and css is already loaded
  ?>
  <div class="container" id="crop-avatar">

    <!-- Current avatar -->
    <div class="avatar-view" title="Change the avatar">
      <img src="http://placehold.it/350x150" alt="Avatar">
    </div>

    <!-- Cropping modal -->
    <div class="modal fade" id="avatar-modal" aria-hidden="true" aria-labelledby="avatar-modal-label" role="dialog" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form class="avatar-form" action="crop.php" enctype="multipart/form-data" method="post">
            <div class="modal-header">
              <button class="close" data-dismiss="modal" type="button">&times;</button>
              <h4 class="modal-title" id="avatar-modal-label">Change Avatar</h4>
            </div>
            <div class="modal-body">
              <div class="avatar-body">

                <!-- Upload image and data -->
                <div class="avatar-upload">
                  <input class="avatar-src" name="avatar_src" type="hidden">
                  <input class="avatar-data" name="avatar_data" type="hidden">
                  <label for="avatarInput">Local upload</label>
                  <input class="avatar-input" id="avatarInput" name="avatar_file" type="file">
                </div>

                <!-- Crop and preview -->
                <div class="row">
                  <div class="col-md-9">
                    <div class="avatar-wrapper"></div>
                  </div>
                  <div class="col-md-3">
                    <div class="avatar-preview preview-lg"></div>
                    <div class="avatar-preview preview-md"></div>
                    <div class="avatar-preview preview-sm"></div>
                  </div>
                </div>

                <div class="row avatar-btns">
                  <div class="col-md-9">
                    <div class="btn-group">
                      <button class="btn btn-primary" data-method="rotate" data-option="-90" type="button" title="Rotate -90 degrees">Rotate Left</button>
                      <button class="btn btn-primary" data-method="rotate" data-option="-15" type="button">-15deg</button>
                      <button class="btn btn-primary" data-method="rotate" data-option="-30" type="button">-30deg</button>
                      <button class="btn btn-primary" data-method="rotate" data-option="-45" type="button">-45deg</button>
                    </div>
                    <div class="btn-group">
                      <button class="btn btn-primary" data-method="rotate" data-option="90" type="button" title="Rotate 90 degrees">Rotate Right</button>
                      <button class="btn btn-primary" data-method="rotate" data-option="15" type="button">15deg</button>
                      <button class="btn btn-primary" data-method="rotate" data-option="30" type="button">30deg</button>
                      <button class="btn btn-primary" data-method="rotate" data-option="45" type="button">45deg</button>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <button class="btn btn-primary btn-block avatar-save" type="submit">Done</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- <div class="modal-footer">
              <button class="btn btn-default" data-dismiss="modal" type="button">Close</button>
            </div> -->
          </form>
        </div>
      </div>
    </div><!-- /.modal -->

    <!-- Loading state -->
    <div class="loading" aria-label="Loading" role="img" tabindex="-1"></div>
  </div>
  <?php
}
