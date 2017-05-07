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

  if($abstract && $abstract[0]){ //&& $abstract[0]->status == 'Approved') {
    $abstract = $abstract[0];

    $image_dir = plugin_dir_path( __FILE__ ) . '../profile-images'. '/' . $abstract->profile_image;

    if(file_exists($image_dir) && filesize($image_dir) > 5 * 1024) {//If larger than 5KB
      $profile_image = get_site_url().'/wp-content/plugins/wpabstracts_pro/profile-images/'.$abstract->profile_image;
    } else {
      $profile_image = get_site_url().'/wp-content/plugins/wpabstracts_pro/profile-images/profile-placeholder.jpg';
    }
    ?>
        <h1><?php echo(esc_attr($abstract->title));?></h1>
        <div class="et_pb_column et_pb_column_2_3  et_pb_column_0">
          <div class="et_pb_text et_pb_module et_pb_bg_layout_light et_pb_text_align_left  et_pb_text_0">
            <?php $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $abstract->text);
                  echo(wpautop($html));
              ?>
          </div>
        </div>
        <div class="et_pb_column et_pb_column_1_3  et_pb_column_1">
          <div class="et_pb_module et-waypoint et_pb_image et_pb_animation_off et_pb_image_0 et_always_center_on_mobile et-animated">
            <img src="<?php echo $profile_image ?>" width='200px' height='300px'>
          </div>
          <div class="et_pb_text et_pb_module et_pb_bg_layout_light et_pb_text_align_left  et_pb_text_1 author-info">
            <div class='author-name'><a href='<?php echo(esc_url($abstract->presenter_linkedin)); ?>' target='_blank'> <?php echo(esc_attr($abstract->presenter)); ?></a></div>
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

function displayAbstractList($event_id) {
  $event = wpabstracts_getEvents('event_id',$event_id,ARRAY_A);

  $abstracts = getAbstractsForEvent($event_id);

  ?>
  <div class="et_pb_section  et_pb_section_0 et_section_regular">
    <div class="et_pb_row et_pb_row_0">
      <div class="et_pb_column et_pb_column_4_4  et_pb_column_0">
        <div class="et_pb_text et_pb_module et_pb_bg_layout_light et_pb_text_align_left  et_pb_text_0">
          <h1><?php printf(__("Events for %s", 'wpabstracts'), $event['title']);?></h1>
        </div>
        <div class="et_pb_text et_pb_module et_pb_bg_layout_light et_pb_text_align_left  et_pb_text_1">
          <div class="abstract-list-container">
            <ul class="abstract-listing">
              <?php
              //For each abstract, output an <li> element. It will contain a few divs with specified widths, and be a link to the specified abstract. Add some classes and do some css
              foreach($abstracts as $abstract) {
                ?>
                <li class="abstract-list-item">
                  <a href="<?php echo $_SERVER["REQUEST_URI"]; ?>?aid=<?php echo $abstract->id;?>">

                  </a>
                </li>
                <?php
              }
               ?>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

<?php
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
  $abstractsNoPriority = getAbstractsForEventNoPriority($event_id);
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
				  <div class="et_pb_portfolio_filters clearfix">
            <ul class="clearfix">
              <li class="et_pb_portfolio_filter et_pb_portfolio_filter_all" style="width:33%;">
                <a href="#" class="active" data-category-slug="all" style="padding-bottom:0px;padding-top:0px;">
                  <span style="padding-bottom:10px;padding-top:10px;width:100%;text-align:center;display:inline-block;height:40px;">Alle</span></a>
              </li>
              <?php
                foreach($sessions as $session) {
                  ?>
                  <li class="et_pb_portfolio_filter" style="width:33%;">
                    <a href="#" style="padding-bottom:0px;padding-top:0px;" data-category-slug="<?php echo dataslug($session);?>">
                      <span style="padding-bottom:10px;padding-top:10px;width:100%;text-align:center;display:inline-block;height:40px;"><?php echo $session;?></span>
                    </a>
                  </li>
                  <?php
                }
              ?>
            </ul>
          </div><!-- .et_pb_portfolio_filters -->
				  <div class="et_pb_portfolio_items_wrapper ">
					  <div id="abstract-frontend-listing" class="et_pb_portfolio_items">
              <?php
              foreach($abstracts as $abstract) {
                $image_dir = plugin_dir_path( __FILE__ ) . '../profile-images'. '/' . $abstract->profile_image;

                if(file_exists($image_dir) && filesize($image_dir) > 5 * 1024) {//If larger than 5KB
                  $profile_image = get_site_url().'/wp-content/plugins/wpabstracts_pro/profile-images/'.$abstract->profile_image;
                } else {
                  $profile_image = get_site_url().'/wp-content/plugins/wpabstracts_pro/profile-images/profile-placeholder.jpg';
                }
                ?>
                <div data-abstract-title="<?php echo titleslug($abstract->title); ?>" data-abstract-priority="<?php echo $abstract->priority; ?>" class="abstract-frontend-listing-item et_pb_portfolio_item et_pb_grid_item project type-project status-publish has-post-thumbnail hentry project_category_<?php echo dataslug($abstract->session);?>">
                  <a href="<?php echo $_SERVER["REQUEST_URI"]; ?>?aid=<?php echo $abstract->abstract_id;?>">
                    <span class="et_portfolio_image">
                      <img src="<?php echo $profile_image;?>" alt='<?php echo $abstract->title;?>' width='200px' height='300px' />
                      <span class="et_overlay"></span>
                    </span>
                  </a>
                  <h2 style="font-size:75%;"><a href="<?php echo $_SERVER["REQUEST_URI"]; ?>?aid=<?php echo $abstract->abstract_id;?>"><?php echo $abstract->title; ?></a></h2>
                  <p style="margin-top:10px; padding-bottom: 0px; font-size:75%;line-height:1;" class="post-meta"><a href="#" rel="tag"><?php echo $abstract->presenter?></a></p>
                  <p style="margin-top:0px; font-size:75%;line-height:1;" class="post-meta"><a href="#" rel="tag"><?php echo $abstract->presenter_company;?></a></p>
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

function getAbstractsForEventNoPriority($event_id) {
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

function titleslug($title) {
  $string = strtolower($string);
  $string = preg_replace("/[^a-z0-9]/", "", $string);
}
?>
