<?php

class WPAbstracts_Emailer{

    protected $abstracts = [];
    protected $user = null;
    protected $event = null;
    protected $template = null;


    public function __construct($aid, $user_id, $template, $user_data = NULL) {
        global $wpdb;
        $wpdb->hide_errors();

        if($aid){
          if(!is_array($aid)) {
            $aid = array($aid);
          }
          foreach($aid as $abstract_id) {
            $this->abstracts[] = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."wpabstracts_abstracts WHERE abstract_id = $abstract_id");
          }
          $this->event = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."wpabstracts_events WHERE event_id = " . $this->abstracts[0]->event . "");

          if(!is_null($user_id)) {
            $this->user = get_user_by( 'id', $user_id );
          }
          else {
            $this->user = null;
          }
          $this->template = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."wpabstracts_emailtemplates WHERE ID = $template");
        }
    }

    private function apply_shortcodes(){
        $keys = array(
          '{RECEIVER_NAME}',
          '{AUTHOR_NAME}',
          '{AUTHOR_COMPANY}',
          '{AUTHOR_EMAIL}',
          '{AUTHOR_PHONE}',
          '{ABSTRACT_TITLE}',
          '{ABSTRACT_SUBJECT}',
          '{ABSTRACT_TARGET_GROUP}',
          '{ABSTRACT_RESUME}',
          '{ABSTRACT_CONTENT}',
          '{MANAGER_COMMENTS}',
          '{ABSTRACTS_LIST}',
          '{ABSTRACTS_NUMBER}',
          '{EVENT_NAME}',
          '{EVENT_START}',
          '{EVENT_END}',
          '{SITE_NAME}',
          '{SITE_URL}',
          '{ONE_WEEK_LATER}',
          '{TWO_WEEKS_LATER}'
        );

        $one_week_later = date_i18n(get_option('date_format'), (60 * 60 * 24 * 7) + strtotime(current_time( 'mysql' )));
        $two_weeks_later = date_i18n(get_option('date_format'), ((60 * 60 * 24 * 7) * 2) + strtotime(current_time( 'mysql' )));
        $site_name = get_option('blogname');
        $site_url = home_url();

        $abstract_list = "<ul>";
        foreach($this->abstracts as $abstract) {
          $abstract_list .= "<li>" . $abstract->title . "</li>";
        }
        $abstract_list .= "</ul>";

        $values = array(
          is_null($this->user) ? $this->abstract->presenter : $this->user->display_name,
          count($this->abstracts) < 1 ? "" : $this->abstracts[0]->presenter,
          count($this->abstracts) < 1 ? "" : $this->abstracts[0]->presenter_company,
          count($this->abstracts) < 1 ? "" : $this->abstracts[0]->presenter_email,
          count($this->abstracts) < 1 ? "" : $this->abstracts[0]->presenter_phone,
          count($this->abstracts) < 1 ? "" : $this->abstracts[0]->title,
          count($this->abstracts) < 1 ? "" : $this->abstracts[0]->topic,
          count($this->abstracts) < 1 ? "" : $this->abstracts[0]->target_group,
          count($this->abstracts) < 1 ? "" : $this->abstracts[0]->resume,
          count($this->abstracts) < 1 ? "" : $this->abstracts[0]->text,
          count($this->abstracts) < 1 ? "" : $this->abstracts[0]->manager_comments,

          $abstract_list,
          count($this->abstracts),

          $this->event->name,
          $this->event->start_date,
          $this->event->end_date,
          $site_name, $site_url,
          $one_week_later,
          $two_weeks_later
        );


        $subject = str_replace($keys, $values, stripslashes($this->template->subject));
        $message = str_replace($keys, $values, wpautop(stripslashes($this->template->message)));
        return array($subject, $message);
    }

    public function send(){
        $to = null;
        if(! is_null($this->user)) {
          $to = $this->user->user_email;
        } else if (count($this->abstracts) > 0) {
          $to = $this->abstracts[0]->presenter_email;
        }
        $headers = 'From: ' . $this->template->from_name . " <" . $this->template->from_email . "> \r\n";
        list($subject, $message) = $this->apply_shortcodes();
        add_filter( 'wp_mail_content_type', 'wpabstracts_set_html_content_type' );
        $success = wp_mail($to, $subject, $message, $headers);
        remove_filter( 'wp_mail_content_type', 'wpabstracts_set_html_content_type' );
        return $success;
    }

}
