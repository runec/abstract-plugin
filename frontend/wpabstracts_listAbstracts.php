<input id="sortPriority" name="sortPriority" value="false" type="hidden">
<?php
defined('ABSPATH') or die("ERROR: You do not have permission to access this page");
//include_once(WPABSTRACTS_PLUGIN_DIR . 'inc/wpabstracts_functions.php');

if(isset($_GET['aid']) && $_GET['aid'] !== "") {
  displayAbstract($event_id, $_GET['aid']);
}
else {
  displayAbstractList2($event_id);
}

//function for getting all approved abstracts for an event

/*
 * Display a single abstract with info
 */
function displayAbstract($event_id, $abstract_id) {
  $abstract = wpabstracts_getAbstracts('abstract_id', $abstract_id, ARRAY_A);

  if($abstract && $abstract[0] && $abstract[0]->status == 'Approved') {
    $abstract = $abstract[0];

    $profile_image = getURLforProfileImage($abstract->profile_image);

    ?>
        <h1><?php echo(esc_attr($abstract->title));?></h1>
        <div class="et_pb_column et_pb_column_2_3  et_pb_column_0">
          <h2  class="single-abstract-header"><?php _e('Session','wpabstracts'); ?> </h2><p><?php echo $abstract->session; ?></p>
          <h2 class="single-abstract-header"><?php _e('Resume', 'wpabstracts'); ?> </h2>
                      <?php $resume = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $abstract->resume);
                            echo(wpautop($resume));
                        ?>
          <h2 class="single-abstract-header"><?php _e('Target Group', 'wpabstracts'); ?> </h2>
                                    <?php $tg = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $abstract->target_group);
                                          echo(wpautop($tg));
                                      ?>
        <p></p>
<h2 class="single-abstract-header"><?php _e('Abstract', 'wpabstracts'); ?> </h2>
          <div class="et_pb_text et_pb_module et_pb_bg_layout_light et_pb_text_align_left  et_pb_text_0">
            <?php $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $abstract->text);
                  echo(wpautop($html));
              ?>
          </div>
        </div>
        <div class="et_pb_column et_pb_column_1_3  et_pb_column_1">
          <div class="et_pb_module et-waypoint et_pb_image et_pb_animation_off et_pb_image_0 et_always_center_on_mobile et-animated">
            <div style="width:133px; height:200px;">
              <img src="<?php echo $profile_image;?>" alt='<?php echo $abstract->title;?>' width='133px' height='200px' />
            </div>
          </div>
          <div class="et_pb_text et_pb_module et_pb_bg_layout_light et_pb_text_align_left  et_pb_text_1 author-info">
            <?php
            $linkedinRegexp="/(ftp|http|https):\/\/?((www|\w\w)\.)?linkedin.com(\w+:{0,1}\w*@)?(\S+)(:([0-9])+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/";
              if(preg_match($linkedinRegexp, $abstract->presenter_linkedin)) {
                ?>
                <div class='author-name'><a href='<?php echo(esc_url($abstract->presenter_linkedin)); ?>' target='_blank'> <?php echo(esc_attr($abstract->presenter)); ?></a></div>

                <?php
              } else {
                ?>
                <div class='author-name'> <?php echo(esc_attr($abstract->presenter)); ?></div>
                <?php
              }
             ?>
              <div class='author-company'><?php echo(esc_attr($abstract->presenter_company)); ?></div>
        </div>
      </div>
    </div>
    <?php
  }
  else {
    _e("Unknown abstract id", 'wpabstracts');
  }
}

function shortenCategory($category) {
  if(strlen($category) > 33) {
    $category = substr($category, 0, 30) . "...";
  }
  return $category;
}

function displayAbstractList2($event_id) {

  $event = wpabstracts_getEvents('event_id',$event_id,ARRAY_A);
  $abstracts = getAbstractsForEvent($event_id);
  $sessions = explode(', ', $event['sessions']);
  //echo "<pre>"; var_dump($event); echo "</pre>";
  /*
  <div class="et_pb_section  et_pb_section_0 et_section_regular">
    <div class=" et_pb_row et_pb_row_0">
      <div class="et_pb_column et_pb_column_4_4  et_pb_column_0">
        <div class="et_pb_text et_pb_module et_pb_bg_layout_light et_pb_text_align_left  et_pb_text_0">
        */
  ?>
          <h1><?php printf(__("Abstracts for %s", 'wpabstracts'), $event['name']);?></h1>
			  </div> <!-- .et_pb_text -->

        <div class="et_pb_filterable_portfolio et_pb_filterable_portfolio_grid clearfix et_pb_module et_pb_bg_layout_light  et_pb_filterable_portfolio_0">
				  <div class="et_pb_portfolio_filters clearfix" style="display:none;">
            <ul class="clearfix">
              <li class="et_pb_portfolio_filter et_pb_portfolio_filter_all" style="width:33%;">
                <a href="#" class="active" data-category-slug="all" style="padding-bottom:0px;padding-top:0px;" onClick="sortAbstractsAlphabetically();">
                  <span style="padding-bottom:10px;padding-top:10px;width:100%;text-align:center;display:inline-block;height:40px;font-weight:bold">Alle</span></a>
              </li>
              <?php
                foreach($sessions as $session) {
                  ?>
                  <li class="et_pb_portfolio_filter" style="width:33%;">
                    <a href="#" id="<?php echo dataslug($session);?>" style="padding-bottom:0px;padding-top:0px;" data-category-slug="<?php echo dataslug($session);?>" onClick="sortAbstractsPriorityAndAlphabetically();">
                      <span style="padding-bottom:10px;padding-top:10px;width:100%;text-align:center;display:inline-block;height:40px;"><?php echo $session;?></span>
                    </a>
                  </li>
                  <?php
                }
              ?>
            </ul>
          </div><!-- .et_pb_portfolio_filters -->


          <?php /* See jquery dropdowns instead? */ ?>
          <div class="et_pb_portfolio_filters clearfix">
            <ul class="clearfix">
              <li class="et_pb_portfolio_filter et_pb_portfolio_filter_all" style="width:33%;text-align:center;">
                <a href="#" class="active" data-category-slug="all" style="padding-bottom:0px;padding-top:0px;margin:0px;" onClick="sortAbstractsAlphabetically();">
                  <span style="width:100%;padding-bottom:10px;padding-top:10px;display:inline-block;height:40px;font-weight:bold">Alle</span></a>
              </li>
                  <li class="et_pb_portfolio_filter" style="width:33%;">
                    <select id="abstract-session-list" style="text-align:center;overflow-y:scroll;height:42px;border:1px solid #e2e2e2;width:100%;border-radius:3px 0 0 3px;" onChange="setSession(this.value)">
                      <option value=""><?php _e("Choose session", 'wpabstracts');?></option>
                      <?php
                        foreach($sessions as $session) {
                      ?>
                          <option class="abstract-session-list-item" value="<?php echo dataslug($session);?>">
                            <?php echo $session ?>
                          </option>
                      <?php } ?>
                    </select>

                  </li>
            </ul>
          </div><!-- .et_pb_portfolio_filters -->
        <?php /**/?>


				  <div class="et_pb_portfolio_items_wrapper ">
					  <div id="abstract-frontend-listing" class="et_pb_portfolio_items">
              <?php
              foreach($abstracts as $abstract) {
                $profile_image = getURLforProfileImage($abstract->profile_image);

                ?>
                <div data-abstract-title="<?php echo titleslug($abstract->title); ?>" data-abstract-priority="<?php echo $abstract->priority; ?>" class="abstract-frontend-listing-item et_pb_portfolio_item et_pb_grid_item project type-project status-publish has-post-thumbnail hentry project_category_<?php echo dataslug($abstract->session);?>">
                  <a href="<?php echo $_SERVER["REQUEST_URI"]; ?>?aid=<?php echo $abstract->abstract_id;?>">
                    <span class="et_portfolio_image">
                      <div style="width:133px; height:200px;">
                        <img src="<?php echo $profile_image;?>" alt='<?php echo $abstract->title;?>' width='133px' height='200px' />
                      </div>
                      <span class="et_overlay"></span>
                    </span>
                  </a>
                  <h2 style="font-size:75%;line-height:15px;"><a href="<?php echo $_SERVER["REQUEST_URI"]; ?>?aid=<?php echo $abstract->abstract_id;?>"><?php echo $abstract->title; ?></a></h2>
                  <p style="margin-top:10px; padding-bottom: 0px; font-size:75%;line-height:15px;" class="post-meta"><a href="#" rel="tag"><?php echo $abstract->presenter?></a></p>
                  <p style="margin-top:0px; font-size:75%;line-height:15px;" class="post-meta"><a href="#" rel="tag"><?php echo $abstract->presenter_company;?></a></p>
                </div>
                <?php
              }
             ?>
				    </div><!-- .et_pb_portfolio_item -->
				</div><!-- .et_pb_portfolio_item -->
				</div><!-- .et_pb_portfolio_items -->
				</div>
				<div class="et_pb_portofolio_pagination"></div>
			</div> <!-- .et_pb_filterable_portfolio -->
			</div> <!-- .et_pb_column -->

			</div> <!-- .et_pb_row -->

			</div> <!-- .et_pb_section -->
  <?php
}

function getAbstractsForEvent($event_id) {
  global $wpdb;
  $sql = $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."wpabstracts_abstracts
                         WHERE event = %d AND status='Approved' ORDER BY title", $event_id);
  $abstracts = $wpdb->get_results($sql);
  //var_dump($abstracts);
  return $abstracts;
}

function dataslug($string) {
    //Lower case everything
    $string = strtolower($string);
    //Make alphanumeric (removes all other characters)
    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
    //Clean up multiple dashes or whitespaces
    $string = preg_replace("/[\s-]+/", " ", $string);
    //Convert whitespaces and underscore to dash
    $string = preg_replace("/[\s_]/", "-", $string);
    return $string;
}

function titleslug($string) {
  $string = strtolower($string);
  $string = preg_replace("/[^a-z0-9]/", "", $string);
  return $string;
}

function getProfileImageURL() {
  $upload_dir = wp_upload_dir();
  return $upload_dir['baseurl'].'/wpabstracts/';
}

function getURLforProfileImage($profile_image_file) {
  if($profile_image_file == null || strlen($profile_image_file) === 0) {
    $profile_image = WPABSTRACTS_PROFILE_IMAGE_URL.'profile-placeholder.jpg';
  }
  else {
    if(strpos($profile_image_file, "/") !== false) {
      $profile_image = $profile_image_file;
    }
    else {
      $server_image_dir = WPABSTRACTS_PROFILE_IMAGE_DIR . $profile_image_file;
      if(file_exists($server_image_dir) && filesize($server_image_dir) > 5 * 1024) {//If larger than 5KB
        $profile_image = WPABSTRACTS_PROFILE_IMAGE_URL.$profile_image_file;
      } else {
        $profile_image = WPABSTRACTS_PROFILE_IMAGE_URL.'profile-placeholder.jpg';
      }
    }

  }
  return $profile_image;
}
?>
