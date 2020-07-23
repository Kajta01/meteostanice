<?php

if (!defined ('ABSPATH')) exit;

require_once AD_INSERTER_PLUGIN_DIR.'constants.php';

abstract class ai_BaseCodeBlock {
  var $wp_options;
  var $fallback;
  var $client_side_list_detection;
  var $w3tc_code;
  var $w3tc_code2;
  var $before_w3tc_code2;
  var $needs_class;
  var $code_version;
  var $version_name;
  var $additional_code_before; // For server-side dynamic PHP code
  var $additional_code_after;  // For server-side dynamic PHP code
  var $counters;
  var $client_side_cookie_check;
  var $demo_debugging;

  var $label;

  function __construct () {

    $this->number = 0;

    $this->wp_options = array ();
    $this->fallback = 0;
    $this->client_side_list_detection = false;
    $this->w3tc_code = '';
    $this->w3tc_code2 = '';
    $this->before_w3tc_code2 = '';
    $this->needs_class = false;
    $this->code_version = 0;
    $this->version_name = '';
    $this->additional_code_before = '';
    $this->additional_code_after = '';
    $this->counters = '';
    $this->client_side_cookie_check = false;
    $this->demo_debugging = false;

    $this->labels = new ai_block_labels ();

    $this->wp_options [AI_OPTION_CODE]                = AD_EMPTY_DATA;
    $this->wp_options [AI_OPTION_PROCESS_PHP]         = AI_DISABLED;
    $this->wp_options [AI_OPTION_ENABLE_MANUAL]       = AI_DISABLED;
    $this->wp_options [AI_OPTION_ENABLE_AMP]          = AI_DISABLED;
    $this->wp_options [AI_OPTION_ENABLE_404]          = AI_DISABLED;
    $this->wp_options [AI_OPTION_DETECT_SERVER_SIDE]  = AI_DISABLED;
    $this->wp_options [AI_OPTION_DISPLAY_FOR_DEVICES] = DEFAULT_DISPLAY_FOR_DEVICES;
  }

  public function load_options ($block) {
    global $ai_db_options;

    if (isset ($ai_db_options [$block])) $options = $ai_db_options [$block]; else $options = array ();

    // Convert old options
    if (empty ($options) && !isset ($ai_db_options [AI_OPTION_GLOBAL]['VERSION'])) {

      if     ($block == "h") $options = ai_get_option (str_replace ("#", "Header", AD_ADx_OPTIONS));
      elseif ($block == "f") $options = ai_get_option (str_replace ("#", "Footer", AD_ADx_OPTIONS));
      else                   $options = ai_get_option (str_replace ("#", $block, AD_ADx_OPTIONS));

      if (is_array ($options)) {

        $old_name = "ad" . $block . "_data";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_CODE] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "ad" . $block . "_enable_manual";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_ENABLE_MANUAL] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "ad" . $block . "_process_php";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_PROCESS_PHP] = $options [$old_name];
          unset ($options [$old_name]);
        }

        $old_name = "adH_data";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_CODE] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "adH_enable";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_ENABLE_MANUAL] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "adH_process_php";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_PROCESS_PHP] = $options [$old_name];
          unset ($options [$old_name]);
        }

        $old_name = "adF_data";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_CODE] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "adF_enable";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_ENABLE_MANUAL] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "adF_process_php";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_PROCESS_PHP] = $options [$old_name];
          unset ($options [$old_name]);
        }

        $old_name = "ad" . $block . "_name";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_BLOCK_NAME] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "ad" . $block . "_displayType";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_AUTOMATIC_INSERTION] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "ad" . $block . "_paragraphNumber";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_PARAGRAPH_NUMBER] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "ad" . $block . "_minimum_paragraphs";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_MIN_PARAGRAPHS] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "ad" . $block . "_minimum_words";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_MIN_WORDS] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "ad" . $block . "_excerptNumber";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_EXCERPT_NUMBER] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "ad" . $block . "_directionType";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_DIRECTION_TYPE] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "ad" . $block . "_floatType";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_ALIGNMENT_TYPE] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "ad" . $block . "_general_tag";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_GENERAL_TAG] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "ad" . $block . "_after_day";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_AFTER_DAYS] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "ad" . $block . "_block_user";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_DOMAIN_LIST] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "ad" . $block . "_domain_list_type";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_DOMAIN_LIST_TYPE] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "ad" . $block . "_block_cat";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_CATEGORY_LIST] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "ad" . $block . "_block_cat_type";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_CATEGORY_LIST_TYPE] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "ad" . $block . "_block_tag";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_TAG_LIST] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "ad" . $block . "_block_tag_type";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_TAG_LIST_TYPE] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "ad" . $block . "_widget_settings_home";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_DISPLAY_ON_HOMEPAGE] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "ad" . $block . "_widget_settings_page";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_DISPLAY_ON_PAGES] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "ad" . $block . "_widget_settings_post";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_DISPLAY_ON_POSTS] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "ad" . $block . "_widget_settings_category";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_DISPLAY_ON_CATEGORY_PAGES] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "ad" . $block . "_widget_settings_search";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_DISPLAY_ON_SEARCH_PAGES] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "ad" . $block . "_widget_settings_archive";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_DISPLAY_ON_ARCHIVE_PAGES] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "ad" . $block . "_enabled_on_which_pages";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_ENABLED_ON_WHICH_PAGES] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "ad" . $block . "_enabled_on_which_posts";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_ENABLED_ON_WHICH_POSTS] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "ad" . $block . "_enable_php_call";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_ENABLE_PHP_CALL] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "ad" . $block . "_paragraph_text";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_PARAGRAPH_TEXT] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "ad" . $block . "_custom_css";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_CUSTOM_CSS] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "ad" . $block . "_display_for_users";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_DISPLAY_FOR_USERS] = $options [$old_name];
          unset ($options [$old_name]);
        }
        $old_name = "ad" . $block . "_display_for_devices";
        if (isset ($options [$old_name])) {
          $options [AI_OPTION_DISPLAY_FOR_DEVICES] = $options [$old_name];
          unset ($options [$old_name]);
        }
      }
    }

    if (!empty ($options)) $this->wp_options = array_merge ($this->wp_options, $options);
    unset ($this->wp_options ['']);
  }

  public function get_ad_name(){
     $name = isset ($this->wp_options [AI_OPTION_BLOCK_NAME]) ? $this->wp_options [AI_OPTION_BLOCK_NAME] : "";
     return $name;
  }

  public function get_ad_data(){
    $ad_data = isset ($this->wp_options [AI_OPTION_CODE]) ? $this->wp_options [AI_OPTION_CODE] : '';
    return $ad_data;
  }

  public function get_enable_manual (){
    $enable_manual = isset ($this->wp_options [AI_OPTION_ENABLE_MANUAL]) ? $this->wp_options [AI_OPTION_ENABLE_MANUAL] : AI_DISABLED;
    if ($enable_manual == '') $enable_manual = AI_DISABLED;
    return $enable_manual;
  }

  public function get_enable_amp ($return_saved_value = false){
    $enable_amp = isset ($this->wp_options [AI_OPTION_ENABLE_AMP]) ? $this->wp_options [AI_OPTION_ENABLE_AMP] : AI_DISABLED;

    if ($return_saved_value) return $enable_amp;

    // Fix for AMP code blocks with url white-list */amp
    $urls = $this->get_ad_url_list();
    $url_type = $this->get_ad_url_list_type();
    if ($url_type == AI_WHITE_LIST && strpos ($urls, '/amp') !== false) {
      $enable_amp = true;
    }
    // Fix for code blocks using PHP function is_amp_endpoint
    elseif ($this->get_process_php() && strpos ($this->get_ad_data (), 'is_amp_endpoint') !== false) {
      $enable_amp = true;
    }

    return $enable_amp;
  }

  public function get_process_php (){
    $process_php = isset ($this->wp_options [AI_OPTION_PROCESS_PHP]) ? $this->wp_options [AI_OPTION_PROCESS_PHP] : AI_DISABLED;
    if ($process_php == '') $process_php = AI_DISABLED;
    return $process_php;
  }

  public function get_enable_404 (){
    $enable_404 = isset ($this->wp_options [AI_OPTION_ENABLE_404]) ? $this->wp_options [AI_OPTION_ENABLE_404] : AI_DISABLED;
    if ($enable_404 == '') $enable_404 = AI_DISABLED;
    return $enable_404;
  }

  public function get_detection_server_side(){
    // Check old settings for all devices
    if (isset ($this->wp_options [AI_OPTION_DISPLAY_FOR_DEVICES])) {
      $display_for_devices = $this->wp_options [AI_OPTION_DISPLAY_FOR_DEVICES];
    } else $display_for_devices = '';

    if ($display_for_devices === AD_DISPLAY_ALL_DEVICES) $option = AI_DISABLED; else

      $option = isset ($this->wp_options [AI_OPTION_DETECT_SERVER_SIDE]) ? $this->wp_options [AI_OPTION_DETECT_SERVER_SIDE] : AI_DISABLED;

    return $option;
  }

  function check_server_side_detection () {
    global $ai_last_check;

    if ($this->get_detection_server_side ()) {
      $display_for_devices = $this->get_display_for_devices ();

      $ai_last_check = AI_CHECK_DESKTOP_DEVICES;
      if ($display_for_devices == AI_INSERT_FOR_DESKTOP_DEVICES && !AI_DESKTOP) return false;
      $ai_last_check = AI_CHECK_MOBILE_DEVICES;
      if ($display_for_devices == AI_INSERT_FOR_MOBILE_DEVICES && !AI_MOBILE) return false;
      $ai_last_check = AI_CHECK_TABLET_DEVICES;
      if ($display_for_devices == AI_INSERT_FOR_TABLET_DEVICES && !AI_TABLET) return false;
      $ai_last_check = AI_CHECK_PHONE_DEVICES;
      if ($display_for_devices == AI_INSERT_FOR_PHONE_DEVICES && !AI_PHONE) return false;
      $ai_last_check = AI_CHECK_DESKTOP_TABLET_DEVICES;
      if ($display_for_devices == AI_INSERT_FOR_DESKTOP_TABLET_DEVICES && !(AI_DESKTOP || AI_TABLET)) return false;
      $ai_last_check = AI_CHECK_DESKTOP_PHONE_DEVICES;
      if ($display_for_devices == AI_INSERT_FOR_DESKTOP_PHONE_DEVICES && !(AI_DESKTOP || AI_PHONE)) return false;
    }
    return true;
  }

  public function get_display_for_devices () {
    $option = isset ($this->wp_options [AI_OPTION_DISPLAY_FOR_DEVICES]) ? $this->wp_options [AI_OPTION_DISPLAY_FOR_DEVICES] : DEFAULT_DISPLAY_FOR_DEVICES;
    //                                convert old option
    if ($option == '' || $option == AD_DISPLAY_ALL_DEVICES) $option = DEFAULT_DISPLAY_FOR_DEVICES;

    elseif ($option == AD_DISPLAY_DESKTOP_DEVICES)         $option = AI_INSERT_FOR_DESKTOP_DEVICES;
    elseif ($option == AD_DISPLAY_MOBILE_DEVICES)          $option = AI_INSERT_FOR_MOBILE_DEVICES;
    elseif ($option == AD_DISPLAY_TABLET_DEVICES)          $option = AI_INSERT_FOR_TABLET_DEVICES;
    elseif ($option == AD_DISPLAY_PHONE_DEVICES)           $option = AI_INSERT_FOR_PHONE_DEVICES;
    elseif ($option == AD_DISPLAY_DESKTOP_TABLET_DEVICES)  $option = AI_INSERT_FOR_DESKTOP_TABLET_DEVICES;
    elseif ($option == AD_DISPLAY_DESKTOP_PHONE_DEVICES)   $option = AI_INSERT_FOR_DESKTOP_PHONE_DEVICES;

    return $option;
  }

  public function get_display_for_devices_text ($translate = true) {
    switch ($this->get_display_for_devices ()) {
      case AI_INSERT_FOR_DESKTOP_DEVICES:
        if (!$translate) return AI_TEXT_ENG_DESKTOP_DEVICES;
        return AI_TEXT_DESKTOP_DEVICES;
        break;
      case AI_INSERT_FOR_MOBILE_DEVICES:
        if (!$translate) return AI_TEXT_ENG_MOBILE_DEVICES;
        return AI_TEXT_MOBILE_DEVICES;
        break;
      case AI_INSERT_FOR_TABLET_DEVICES:
        if (!$translate) return AI_TEXT_ENG_TABLET_DEVICES;
        return AI_TEXT_TABLET_DEVICES;
        break;
      case AI_INSERT_FOR_PHONE_DEVICES:
        if (!$translate) return AI_TEXT_ENG_PHONE_DEVICES;
        return AI_TEXT_PHONE_DEVICES;
        break;
      case AI_INSERT_FOR_DESKTOP_TABLET_DEVICES:
        if (!$translate) return AI_TEXT_ENG_DESKTOP_TABLET_DEVICES;
        return AI_TEXT_DESKTOP_TABLET_DEVICES;
        break;
      case AI_INSERT_FOR_DESKTOP_PHONE_DEVICES:
        if (!$translate) return AI_TEXT_ENG_DESKTOP_PHONE_DEVICES;
        return AI_TEXT_DESKTOP_PHONE_DEVICES;
        break;
      default:
        return '';
        break;
    }
  }

  public function get_debug_disable_insertion (){
    global $ai_wp_data;

    if (defined ('AI_DEBUGGING_DEMO') && !$this->demo_debugging) {
      return false;
    }

    return ($ai_wp_data [AI_WP_DEBUGGING] & AI_DEBUG_NO_INSERTION) != 0;
  }

  public function clear_code_cache (){
    unset ($this->wp_options ['GENERATED_CODE']);
  }

  public function empty_code () {
    global $ai_last_check;

    $ai_last_check = AI_CHECK_CODE;
    $empty = $this->ai_getCode () == '';

    if ($this->get_automatic_insertion () == AI_AUTOMATIC_INSERTION_INSIDE_HTML_ELEMENT &&
        ($this->get_inside_element () == AI_HTML_REPLACE_CONTENT ||
         $this->get_inside_element () == AI_HTML_REPLACE_ELEMENT)) {
      return false;
    }

    return $empty;
  }

  public function ai_getCode (){
    global $block_object, $ai_total_php_time, $ai_wp_data;

    if ($this->fallback != 0) return $block_object [$this->fallback]->ai_getCode ();

    $obj = $this;
    $code = $obj->get_ad_data();

    if ($obj->get_process_php () && !get_disable_php_processing () && (!is_multisite() || is_main_site () || multisite_php_processing ())) {
      $global_name = 'GENERATED_CODE';
      if (isset ($obj->wp_options [$global_name])) return $obj->wp_options [$global_name];

      $start_time = microtime (true);

      $php_error = "";
      ob_start ();

      try {
        eval ("?>". $code . "<?php ");
      } catch (Exception $e) {
                                // translators: %s: Ad Inserter
          $php_error = sprintf (__("PHP error in %s block", 'ad-inserter') . AD_INSERTER_NAME) . ' ' . ($obj->number == 0 ? '' : $obj->number . " - ") . $obj->get_ad_name() . "<br />\n" .  $e->getMessage();
      }

      $processed_code = ob_get_clean ();

      if (strpos ($processed_code, __FILE__) || $php_error != "") {

        if (preg_match ("%(.+) in ".__FILE__."%", strip_tags($processed_code), $error_message))
                           // translators: %s: Ad Inserter
          $code = sprintf (__("PHP error in %s block", 'ad-inserter'), AD_INSERTER_NAME) . ' '. ($obj->number == 0 ? '' : $obj->number . " - ") . $obj->get_ad_name() . "<br />\n" . $error_message [1];
        elseif (preg_match ("%(.+) in ".__FILE__."%", $php_error, $error_message))
                           // translators: %s: Ad Inserter
          $code = sprintf (__("PHP error in %s block", 'ad-inserter'), AD_INSERTER_NAME) . ' ' .($obj->number == 0 ? '' : $obj->number . " - ") . $obj->get_ad_name() . "<br />\n" . $error_message [1];

        else $code = $processed_code;
      } else $code = $processed_code;

      // Cache generated code
      $obj->wp_options [$global_name] = $code;

      $ai_total_php_time += microtime (true) - $start_time;
    }

    return $code;
  }
}

abstract class ai_CodeBlock extends ai_BaseCodeBlock {

  var $number;

  function __construct () {

    parent::__construct();

    $this->wp_options [AI_OPTION_BLOCK_NAME]                 = '';
    $this->wp_options [AI_OPTION_DISABLE_INSERTION]          = AI_DISABLED;
    $this->wp_options [AI_OPTION_SHOW_LABEL]                 = AI_DISABLED;
    $this->wp_options [AI_OPTION_LAZY_LOADING]               = AI_DISABLED;
    $this->wp_options [AI_OPTION_IFRAME]                     = AI_DISABLED;
    $this->wp_options [AI_OPTION_IFRAME_WIDTH]               = DEFAULT_IFRAME_WIDTH;
    $this->wp_options [AI_OPTION_IFRAME_HEIGHT]              = DEFAULT_IFRAME_HEIGHT;
    $this->wp_options [AI_OPTION_TRACKING]                   = AI_DISABLED;
    $this->wp_options [AI_OPTION_AUTOMATIC_INSERTION]        = AI_AUTOMATIC_INSERTION_DISABLED;
    $this->wp_options [AI_OPTION_HTML_SELECTOR]              = AD_EMPTY_DATA;
    $this->wp_options [AI_OPTION_SERVER_SIDE_INSERTION]      = DEFAULT_SERVER_SIDE_INSERTION;
    $this->wp_options [AI_OPTION_HTML_ELEMENT_INSERTION]     = DEFAULT_HTML_ELEMENT_INSERTION;
    $this->wp_options [AI_OPTION_INSIDE_ELEMENT]             = DEFAULT_INSIDE_ELEMENT;
    $this->wp_options [AI_OPTION_PARAGRAPH_NUMBER]           = AD_ONE;
    $this->wp_options [AI_OPTION_MIN_PARAGRAPHS]             = AD_EMPTY_DATA;
    $this->wp_options [AI_OPTION_MIN_WORDS_ABOVE]            = AD_EMPTY_DATA;
    $this->wp_options [AI_OPTION_MIN_WORDS]                  = AD_EMPTY_DATA;
    $this->wp_options [AI_OPTION_MAX_WORDS]                  = AD_EMPTY_DATA;
    $this->wp_options [AI_OPTION_MIN_PARAGRAPH_WORDS]        = AD_EMPTY_DATA;
    $this->wp_options [AI_OPTION_MAX_PARAGRAPH_WORDS]        = AD_EMPTY_DATA;
    $this->wp_options [AI_OPTION_COUNT_INSIDE_BLOCKQUOTE]    = AI_DISABLED;
    $this->wp_options [AI_OPTION_PARAGRAPH_TAGS]             = DEFAULT_PARAGRAPH_TAGS;
    $this->wp_options [AI_OPTION_AVOID_PARAGRAPHS_ABOVE]     = AD_EMPTY_DATA;
    $this->wp_options [AI_OPTION_AVOID_PARAGRAPHS_BELOW]     = AD_EMPTY_DATA;
    $this->wp_options [AI_OPTION_AVOID_TEXT_ABOVE]           = AD_EMPTY_DATA;
    $this->wp_options [AI_OPTION_AVOID_TEXT_BELOW]           = AD_EMPTY_DATA;
    $this->wp_options [AI_OPTION_AVOID_ACTION]               = DEFAULT_AVOID_ACTION;
    $this->wp_options [AI_OPTION_AVOID_TRY_LIMIT]            = AD_ONE;
    $this->wp_options [AI_OPTION_AVOID_DIRECTION]            = DEFAULT_AVOID_DIRECTION;
    $this->wp_options [AI_OPTION_EXCERPT_NUMBER]             = AD_EMPTY_DATA;
    $this->wp_options [AI_OPTION_FILTER_TYPE]                = AI_FILTER_AUTO;
    $this->wp_options [AI_OPTION_INVERTED_FILTER]            = AI_DISABLED;
    $this->wp_options [AI_OPTION_DIRECTION_TYPE]             = DEFAULT_DIRECTION_TYPE;
    $this->wp_options [AI_OPTION_ALIGNMENT_TYPE]             = AI_ALIGNMENT_DEFAULT;

    if (defined ('AI_STICKY_SETTINGS') && AI_STICKY_SETTINGS) {
      $this->wp_options [AI_OPTION_HORIZONTAL_POSITION]        = DEFAULT_HORIZONTAL_POSITION;
      $this->wp_options [AI_OPTION_VERTICAL_POSITION]          = DEFAULT_VERTICAL_POSITION;
      $this->wp_options [AI_OPTION_HORIZONTAL_MARGIN]          = DEFAULT_HORIZONTAL_MARGIN;
      $this->wp_options [AI_OPTION_VERTICAL_MARGIN]            = DEFAULT_VERTICAL_MARGIN;
      $this->wp_options [AI_OPTION_ANIMATION]                  = DEFAULT_ANIMATION;
      $this->wp_options [AI_OPTION_ANIMATION_TRIGGER]          = DEFAULT_ANIMATION_TRIGGER;
      $this->wp_options [AI_OPTION_ANIMATION_TRIGGER_VALUE]    = DEFAULT_ANIMATION_TRIGGER_VALUE;
      $this->wp_options [AI_OPTION_ANIMATION_TRIGGER_OFFSET]   = DEFAULT_ANIMATION_TRIGGER_OFFSET;
      $this->wp_options [AI_OPTION_ANIMATION_TRIGGER_DELAY]    = DEFAULT_ANIMATION_TRIGGER_DELAY;
      $this->wp_options [AI_OPTION_ANIMATION_TRIGGER_ONCE]     = DEFAULT_ANIMATION_TRIGGER_ONCE;
    }
    $this->wp_options [AI_OPTION_GENERAL_TAG]                = DEFAULT_GENERAL_TAG;
    $this->wp_options [AI_OPTION_SCHEDULING]                 = AI_SCHEDULING_OFF;
    $this->wp_options [AI_OPTION_AFTER_DAYS]                 = AD_EMPTY_DATA;
    $this->wp_options [AI_OPTION_START_DATE]                 = AD_EMPTY_DATA;
    $this->wp_options [AI_OPTION_END_DATE]                   = AD_EMPTY_DATA;
    $this->wp_options [AI_OPTION_FALLBACK]                   = AD_EMPTY_DATA;
    $this->wp_options [AI_OPTION_ADB_BLOCK_ACTION]           = DEFAULT_ADB_BLOCK_ACTION;
    $this->wp_options [AI_OPTION_ADB_BLOCK_REPLACEMENT]      = AD_EMPTY_DATA;
    $this->wp_options [AI_OPTION_MAXIMUM_INSERTIONS]         = AD_EMPTY_DATA;
    $this->wp_options [AI_OPTION_ID_LIST]                    = AD_EMPTY_DATA;
    $this->wp_options [AI_OPTION_ID_LIST_TYPE]               = AI_BLACK_LIST;
    $this->wp_options [AI_OPTION_URL_LIST]                   = AD_EMPTY_DATA;
    $this->wp_options [AI_OPTION_URL_LIST_TYPE]              = AI_BLACK_LIST;
    $this->wp_options [AI_OPTION_URL_PARAMETER_LIST]         = AD_EMPTY_DATA;
    $this->wp_options [AI_OPTION_URL_PARAMETER_LIST_TYPE]    = AI_BLACK_LIST;
    $this->wp_options [AI_OPTION_DOMAIN_LIST]                = AD_EMPTY_DATA;
    $this->wp_options [AI_OPTION_DOMAIN_LIST_TYPE]           = AI_BLACK_LIST;
    $this->wp_options [AI_OPTION_IP_ADDRESS_LIST]            = AD_EMPTY_DATA;
    $this->wp_options [AI_OPTION_IP_ADDRESS_LIST_TYPE]       = AI_BLACK_LIST;
    $this->wp_options [AI_OPTION_COUNTRY_LIST]               = AD_EMPTY_DATA;
    $this->wp_options [AI_OPTION_COUNTRY_LIST_TYPE]          = AI_BLACK_LIST;
    $this->wp_options [AI_OPTION_CATEGORY_LIST]              = AD_EMPTY_DATA;
    $this->wp_options [AI_OPTION_CATEGORY_LIST_TYPE]         = AI_BLACK_LIST;
    $this->wp_options [AI_OPTION_TAG_LIST]                   = AD_EMPTY_DATA;
    $this->wp_options [AI_OPTION_TAG_LIST_TYPE]              = AI_BLACK_LIST;
    $this->wp_options [AI_OPTION_TAXONOMY_LIST]              = AD_EMPTY_DATA;
    $this->wp_options [AI_OPTION_TAXONOMY_LIST_TYPE]         = AI_BLACK_LIST;
    $this->wp_options [AI_OPTION_DISPLAY_ON_POSTS]           = AI_ENABLED;
    $this->wp_options [AI_OPTION_DISPLAY_ON_PAGES]           = AI_DISABLED;
    $this->wp_options [AI_OPTION_DISPLAY_ON_HOMEPAGE]        = AI_DISABLED;
    $this->wp_options [AI_OPTION_DISPLAY_ON_CATEGORY_PAGES]  = AI_DISABLED;
    $this->wp_options [AI_OPTION_DISPLAY_ON_SEARCH_PAGES]    = AI_DISABLED;
    $this->wp_options [AI_OPTION_DISPLAY_ON_ARCHIVE_PAGES]   = AI_DISABLED;
    $this->wp_options [AI_OPTION_ENABLE_AJAX]                = AI_ENABLED;
    $this->wp_options [AI_OPTION_DISABLE_CACHING]            = AI_DISABLED;
    $this->wp_options [AI_OPTION_MAX_PAGE_BLOCKS_ENABLED]    = AI_DISABLED;
    $this->wp_options [AI_OPTION_ONLY_IN_THE_LOOP]           = AI_DISABLED;
    $this->wp_options [AI_OPTION_ENABLE_FEED]                = AI_DISABLED;
    $this->wp_options [AI_OPTION_ENABLED_ON_WHICH_PAGES]     = AI_NO_INDIVIDUAL_EXCEPTIONS;
    $this->wp_options [AI_OPTION_ENABLED_ON_WHICH_POSTS]     = AI_NO_INDIVIDUAL_EXCEPTIONS;
    $this->wp_options [AI_OPTION_ENABLE_PHP_CALL]            = AI_DISABLED;
    $this->wp_options [AI_OPTION_ENABLE_WIDGET]              = AI_ENABLED;
    $this->wp_options [AI_OPTION_PARAGRAPH_TEXT]             = AD_EMPTY_DATA;
    $this->wp_options [AI_OPTION_PARAGRAPH_TEXT_TYPE]        = DEFAULT_PARAGRAPH_TEXT_TYPE;
    $this->wp_options [AI_OPTION_CUSTOM_CSS]                 = AD_EMPTY_DATA;
    $this->wp_options [AI_OPTION_DISPLAY_FOR_USERS]          = DEFAULT_DISPLAY_FOR_USERS;
    $this->wp_options [AI_OPTION_DETECT_CLIENT_SIDE]         = AI_DISABLED;
    $this->wp_options [AI_OPTION_CLIENT_SIDE_ACTION]         = DEFAULT_CLIENT_SIDE_ACTION;
    $this->wp_options [AI_OPTION_CLOSE_BUTTON]               = DEFAULT_CLOSE_BUTTON;
    for ($viewport = 1; $viewport <= AD_INSERTER_VIEWPORTS; $viewport ++) {
      $this->wp_options [AI_OPTION_DETECT_VIEWPORT . '_' . $viewport] = AI_DISABLED;
    }
  }

  public function get_disable_insertion (){
    $disable_insertion = isset ($this->wp_options [AI_OPTION_DISABLE_INSERTION]) ? $this->wp_options [AI_OPTION_DISABLE_INSERTION] : AI_DISABLED;
    return $disable_insertion;
  }

  public function get_show_label (){
    $show_label = isset ($this->wp_options [AI_OPTION_SHOW_LABEL]) ? $this->wp_options [AI_OPTION_SHOW_LABEL] : AI_DISABLED;
    if ($show_label == '') $show_label = AI_DISABLED;
    return $show_label;
  }

  public function get_lazy_loading (){
    $lazy_loading = isset ($this->wp_options [AI_OPTION_LAZY_LOADING]) ? $this->wp_options [AI_OPTION_LAZY_LOADING] : AI_DISABLED;
    if ($lazy_loading == '') $lazy_loading = AI_DISABLED;
    return $lazy_loading;
  }

  public function get_iframe (){
    $option = isset ($this->wp_options [AI_OPTION_IFRAME]) ? $this->wp_options [AI_OPTION_IFRAME] : AI_DISABLED;
    if ($option == '') $option = AI_DISABLED;
    return $option;
  }

  public function get_iframe_width (){
    $option = isset ($this->wp_options [AI_OPTION_IFRAME_WIDTH]) ? $this->wp_options [AI_OPTION_IFRAME_WIDTH] : DEFAULT_IFRAME_WIDTH;
    return $option;
  }

  public function get_iframe_height (){
    $option = isset ($this->wp_options [AI_OPTION_IFRAME_HEIGHT]) ? $this->wp_options [AI_OPTION_IFRAME_HEIGHT] : DEFAULT_IFRAME_HEIGHT;
    return $option;
  }

  public function get_automatic_insertion (){
    global $ai_db_options;

    $option = isset ($this->wp_options [AI_OPTION_AUTOMATIC_INSERTION]) ? $this->wp_options [AI_OPTION_AUTOMATIC_INSERTION] : AI_AUTOMATIC_INSERTION_DISABLED;

    if     ($option == '')                          $option = AI_AUTOMATIC_INSERTION_DISABLED;
    elseif ($option == AD_SELECT_MANUAL)            $option = AI_AUTOMATIC_INSERTION_DISABLED;
    elseif ($option == AD_SELECT_BEFORE_TITLE)      $option = AI_AUTOMATIC_INSERTION_BEFORE_POST;
    elseif ($option == AD_SELECT_WIDGET)            $option = AI_AUTOMATIC_INSERTION_DISABLED;

    if     ($option == AD_SELECT_NONE)              $option = AI_AUTOMATIC_INSERTION_DISABLED;
    elseif ($option == AD_SELECT_BEFORE_POST)       $option = AI_AUTOMATIC_INSERTION_BEFORE_POST;
    elseif ($option == AD_SELECT_AFTER_POST)        $option = AI_AUTOMATIC_INSERTION_AFTER_POST;
    elseif ($option == AD_SELECT_BEFORE_PARAGRAPH)  $option = AI_AUTOMATIC_INSERTION_BEFORE_PARAGRAPH;
    elseif ($option == AD_SELECT_AFTER_PARAGRAPH)   $option = AI_AUTOMATIC_INSERTION_AFTER_PARAGRAPH;
    elseif ($option == AD_SELECT_BEFORE_CONTENT)    $option = AI_AUTOMATIC_INSERTION_BEFORE_CONTENT;
    elseif ($option == AD_SELECT_AFTER_CONTENT)     $option = AI_AUTOMATIC_INSERTION_AFTER_CONTENT;
    elseif ($option == AD_SELECT_BEFORE_EXCERPT)    $option = AI_AUTOMATIC_INSERTION_BEFORE_EXCERPT;
    elseif ($option == AD_SELECT_AFTER_EXCERPT)     $option = AI_AUTOMATIC_INSERTION_AFTER_EXCERPT;
    elseif ($option == AD_SELECT_BETWEEN_POSTS)     $option = AI_AUTOMATIC_INSERTION_BETWEEN_POSTS;

    return $option;
  }

  public function get_automatic_insertion_text ($server_side_insertion = false, $translate = true){

    if ($server_side_insertion)
      $automatic_insertion = $this->get_server_side_insertion (); else
        $automatic_insertion = $this->get_automatic_insertion();

    if ($automatic_insertion == null) $automatic_insertion = $this->get_automatic_insertion();
    switch ($automatic_insertion) {
      case AI_AUTOMATIC_INSERTION_DISABLED:
        if (!$translate) return AI_TEXT_ENG_DISABLED;
        return AI_TEXT_DISABLED;
        break;
      case AI_AUTOMATIC_INSERTION_BEFORE_POST:
        if (!$translate) return AI_TEXT_ENG_BEFORE_POST;
        return AI_TEXT_BEFORE_POST;
        break;
      case AI_AUTOMATIC_INSERTION_AFTER_POST:
        if (!$translate) return AI_TEXT_ENG_AFTER_POST;
        return AI_TEXT_AFTER_POST;
        break;
      case AI_AUTOMATIC_INSERTION_BEFORE_CONTENT:
        if (!$translate) return AI_TEXT_ENG_BEFORE_CONTENT;
        return AI_TEXT_BEFORE_CONTENT;
        break;
      case AI_AUTOMATIC_INSERTION_AFTER_CONTENT:
        if (!$translate) return AI_TEXT_ENG_AFTER_CONTENT;
        return AI_TEXT_AFTER_CONTENT;
        break;
      case AI_AUTOMATIC_INSERTION_BEFORE_PARAGRAPH:
        if (!$translate) return AI_TEXT_ENG_BEFORE_PARAGRAPH;
        return AI_TEXT_BEFORE_PARAGRAPH;
        break;
      case AI_AUTOMATIC_INSERTION_AFTER_PARAGRAPH:
        if (!$translate) return AI_TEXT_ENG_AFTER_PARAGRAPH;
        return AI_TEXT_AFTER_PARAGRAPH;
        break;
      case AI_AUTOMATIC_INSERTION_BEFORE_EXCERPT:
        if (!$translate) return AI_TEXT_ENG_BEFORE_EXCERPT;
        return AI_TEXT_BEFORE_EXCERPT;
        break;
      case AI_AUTOMATIC_INSERTION_AFTER_EXCERPT:
        if (!$translate) return AI_TEXT_ENG_AFTER_EXCERPT;
        return AI_TEXT_AFTER_EXCERPT;
        break;
      case AI_AUTOMATIC_INSERTION_BETWEEN_POSTS:
        if (!$translate) return AI_TEXT_ENG_BETWEEN_POSTS;
        return AI_TEXT_BETWEEN_POSTS;
        break;
      case AI_AUTOMATIC_INSERTION_BEFORE_COMMENTS:
        if (!$translate) return AI_TEXT_ENG_BEFORE_COMMENTS;
        return AI_TEXT_BEFORE_COMMENTS;
        break;
      case AI_AUTOMATIC_INSERTION_BETWEEN_COMMENTS:
        if (!$translate) return AI_TEXT_ENG_BETWEEN_COMMENTS;
        return AI_TEXT_BETWEEN_COMMENTS;
        break;
      case AI_AUTOMATIC_INSERTION_AFTER_COMMENTS:
        if (!$translate) return AI_TEXT_ENG_AFTER_COMMENTS;
        return AI_TEXT_AFTER_COMMENTS;
        break;
      case AI_AUTOMATIC_INSERTION_FOOTER:
        if (!$translate) return AI_TEXT_ENG_FOOTER;
        return AI_TEXT_FOOTER;
        break;
      case AI_AUTOMATIC_INSERTION_ABOVE_HEADER:
        if (!$translate) return AI_TEXT_ENG_ABOVE_HEADER;
        return AI_TEXT_ABOVE_HEADER;
        break;
      case AI_AUTOMATIC_INSERTION_BEFORE_HTML_ELEMENT:
        if (!$translate) return AI_TEXT_ENG_BEFORE_HTML_ELEMENT;
        return AI_TEXT_BEFORE_HTML_ELEMENT;
        break;
      case AI_AUTOMATIC_INSERTION_INSIDE_HTML_ELEMENT:
        if (!$translate) return AI_TEXT_ENG_INSIDE_HTML_ELEMENT;
        return AI_TEXT_INSIDE_HTML_ELEMENT;
        break;
      case AI_AUTOMATIC_INSERTION_AFTER_HTML_ELEMENT:
        if (!$translate) return AI_TEXT_ENG_AFTER_HTML_ELEMENT;
        return AI_TEXT_AFTER_HTML_ELEMENT;
        break;
      default:
        if ($automatic_insertion >= AI_AUTOMATIC_INSERTION_CUSTOM_HOOK && $automatic_insertion < AI_AUTOMATIC_INSERTION_CUSTOM_HOOK + AD_INSERTER_HOOKS) {
          $hook_index = $automatic_insertion - AI_AUTOMATIC_INSERTION_CUSTOM_HOOK;
          return get_hook_name ($hook_index + 1);
        }

        return '';
        break;
    }
  }

  public function get_alignment_type (){
    $option = isset ($this->wp_options [AI_OPTION_ALIGNMENT_TYPE]) ? $this->wp_options [AI_OPTION_ALIGNMENT_TYPE] : AI_ALIGNMENT_DEFAULT;

    if ($option == '') $option = AI_ALIGNMENT_DEFAULT;

    if     ($option == AD_ALIGNMENT_NONE)              $option = AI_ALIGNMENT_DEFAULT;
    elseif ($option == AD_ALIGNMENT_LEFT)              $option = AI_ALIGNMENT_LEFT;
    elseif ($option == AD_ALIGNMENT_RIGHT)             $option = AI_ALIGNMENT_RIGHT;
    elseif ($option == AD_ALIGNMENT_CENTER)            $option = AI_ALIGNMENT_CENTER;
    elseif ($option == AD_ALIGNMENT_FLOAT_LEFT)        $option = AI_ALIGNMENT_FLOAT_LEFT;
    elseif ($option == AD_ALIGNMENT_FLOAT_RIGHT)       $option = AI_ALIGNMENT_FLOAT_RIGHT;
    elseif ($option == AD_ALIGNMENT_NO_WRAPPING)       $option = AI_ALIGNMENT_NO_WRAPPING;
    elseif ($option == AD_ALIGNMENT_CUSTOM_CSS)        $option = AI_ALIGNMENT_CUSTOM_CSS;

    if (defined ('AI_STICKY_SETTINGS') && AI_STICKY_SETTINGS) {
          if ($option == AI_ALIGNMENT_STICKY_LEFT)     $option = AI_ALIGNMENT_STICKY;
      elseif ($option == AI_ALIGNMENT_STICKY_RIGHT)    $option = AI_ALIGNMENT_STICKY;
      elseif ($option == AI_ALIGNMENT_STICKY_TOP)      $option = AI_ALIGNMENT_STICKY;
      elseif ($option == AI_ALIGNMENT_STICKY_BOTTOM)   $option = AI_ALIGNMENT_STICKY;
    }

    return $option;
  }

  public function get_alignment_type_text ($translate = true){
    switch ($this->get_alignment_type ()) {
      case AI_ALIGNMENT_DEFAULT:
        if (!$translate) return AI_TEXT_ENG_DEFAULT;
        return AI_TEXT_DEFAULT;
        break;
      case AI_ALIGNMENT_LEFT:
        if (!$translate) return AI_TEXT_ENG_LEFT;
        return AI_TEXT_LEFT;
        break;
      case AI_ALIGNMENT_RIGHT:
        if (!$translate) return AI_TEXT_ENG_RIGHT;
        return AI_TEXT_RIGHT;
        break;
      case AI_ALIGNMENT_CENTER:
        if (!$translate) return AI_TEXT_ENG_CENTER;
        return AI_TEXT_CENTER;
        break;
      case AI_ALIGNMENT_FLOAT_LEFT:
        if (!$translate) return AI_TEXT_ENG_FLOAT_LEFT;
        return AI_TEXT_FLOAT_LEFT;
        break;
      case AI_ALIGNMENT_FLOAT_RIGHT:
        if (!$translate) return AI_TEXT_ENG_FLOAT_RIGHT;
        return AI_TEXT_FLOAT_RIGHT;
        break;
      case AI_ALIGNMENT_STICKY_LEFT:
        if (!$translate) return AI_TEXT_ENG_STICKY_LEFT;
        return AI_TEXT_STICKY_LEFT;
        break;
      case AI_ALIGNMENT_STICKY_RIGHT:
        if (!$translate) return AI_TEXT_ENG_STICKY_RIGHT;
        return AI_TEXT_STICKY_RIGHT;
        break;
      case AI_ALIGNMENT_STICKY_TOP:
        if (!$translate) return AI_TEXT_ENG_STICKY_TOP;
        return AI_TEXT_STICKY_TOP;
        break;
      case AI_ALIGNMENT_STICKY_BOTTOM:
        if (!$translate) return AI_TEXT_ENG_STICKY_BOTTOM;
        return AI_TEXT_STICKY_BOTTOM;
        break;
      case AI_ALIGNMENT_STICKY:
        if (!$translate) return AI_TEXT_ENG_STICKY;
        return AI_TEXT_STICKY;
        break;
      case AI_ALIGNMENT_NO_WRAPPING:
        if (!$translate) return AI_TEXT_ENG_NO_WRAPPING;
        return AI_TEXT_NO_WRAPPING;
        break;
      case AI_ALIGNMENT_CUSTOM_CSS:
        if (!$translate) return AI_TEXT_ENG_CUSTOM_CSS;
        return AI_TEXT_CUSTOM_CSS;
        break;
      default:
        return '';
        break;
    }
  }

  public function sticky_style ($horizontal_position, $vertical_position, $horizontal_margin = null, $vertical_margin = null) {
    $style = "";

    if ($horizontal_margin === null)  $horizontal_margin = trim ($this->get_horizontal_margin ());
    if ($vertical_margin === null)    $vertical_margin   = trim ($this->get_vertical_margin ());

    $animation = $this->get_animation () != AI_ANIMATION_NONE;

    $main_content_fixed_width = is_numeric (get_main_content_element ());
    if ($main_content_fixed_width) {
      $main_content_shift = (int) (get_main_content_element () / 2);
    }

    switch ($vertical_position) {
      case AI_STICK_TO_THE_TOP:
        switch ($horizontal_position) {
          case AI_STICK_HORIZONTAL_CENTER:
            $style = AI_ALIGNMENT_CSS_STICK_TO_THE_TOP;
            break;
          default:
            $style = AI_ALIGNMENT_CSS_STICK_TO_THE_TOP_OFFSET;
            break;
        }
        if ($vertical_margin != '') {
          $style = ai_change_css ($style, 'top', $vertical_margin . 'px');
        }
        break;
      case AI_STICK_VERTICAL_CENTER:
        if ($animation) $style .= AI_ALIGNMENT_CSS_CENTER_VERTICAL_H_ANIM; else
          switch ($horizontal_position) {
            case AI_STICK_HORIZONTAL_CENTER:
              $style = AI_ALIGNMENT_CSS_CENTER_VERTICAL_H_ANIM;
              break;
            default:
              $style = AI_ALIGNMENT_CSS_CENTER_VERTICAL;
              break;
          }
        break;
      case AI_SCROLL_WITH_THE_CONTENT:
        $style = AI_ALIGNMENT_CSS_SCROLL_WITH_THE_CONTENT;
        if ($vertical_margin != '') {
          $style = ai_change_css ($style, 'top', $vertical_margin . 'px');
        }
        break;
      case AI_STICK_TO_THE_BOTTOM:
        switch ($horizontal_position) {
          case AI_STICK_HORIZONTAL_CENTER:
            $style = AI_ALIGNMENT_CSS_STICK_TO_THE_BOTTOM;
            break;
          default:
            $style = AI_ALIGNMENT_CSS_STICK_TO_THE_BOTTOM_OFFSET;
            break;
        }
        if ($vertical_margin != '') {
          $style = ai_change_css ($style, 'bottom', $vertical_margin . 'px');
        }
        break;
    }

    switch ($horizontal_position) {
      case AI_STICK_TO_THE_LEFT:
        $style .= AI_ALIGNMENT_CSS_STICK_TO_THE_LEFT;
        if ($horizontal_margin != '') {
          $style = ai_change_css ($style, 'left', $horizontal_margin . 'px');
        }
        break;
      case AI_STICK_TO_THE_CONTENT_LEFT:
        $style .= AI_ALIGNMENT_CSS_STICK_TO_THE_CONTENT_LEFT;
        if ($horizontal_margin != '') {
          $style = ai_change_css ($style, 'margin-right', $horizontal_margin . 'px');
        }
        if ($main_content_fixed_width) {
          $style = $style . ai_change_css (AI_ALIGNMENT_CSS_STICK_TO_THE_CONTENT_LEFT_W, 'right', 'calc(50% + ' . $main_content_shift . 'px)');
        }
        break;
      case AI_STICK_HORIZONTAL_CENTER:
        if ($animation) $style .= AI_ALIGNMENT_CSS_STICK_CENTER_HORIZONTAL_ANIM; else
          switch ($vertical_position) {
            case AI_STICK_VERTICAL_CENTER:
              $style .= AI_ALIGNMENT_CSS_STICK_CENTER_HORIZONTAL_V;
              break;
            default:
              $style .= AI_ALIGNMENT_CSS_STICK_CENTER_HORIZONTAL;
              break;
          }
        break;
      case AI_STICK_TO_THE_CONTENT_RIGHT:
        $style .= AI_ALIGNMENT_CSS_STICK_TO_THE_CONTENT_RIGHT;
        if ($horizontal_margin != '') {
          $style = ai_change_css ($style, 'margin-left', $horizontal_margin . 'px');
        }
        if ($main_content_fixed_width) {
          $style = $style . ai_change_css (AI_ALIGNMENT_CSS_STICK_TO_THE_CONTENT_RIGHT_W, 'left', 'calc(50% + ' . $main_content_shift . 'px)');
        }
        break;
      case AI_STICK_TO_THE_RIGHT:
        switch ($vertical_position) {
          case AI_SCROLL_WITH_THE_CONTENT:
            $style .= AI_ALIGNMENT_CSS_STICK_TO_THE_RIGHT_SCROLL;
            if ($horizontal_margin != '') {
              $style = ai_change_css ($style, 'margin-left', $horizontal_margin . 'px');
            }
            break;
          default:
            $style .= AI_ALIGNMENT_CSS_STICK_TO_THE_RIGHT;
            if ($horizontal_margin != '') {
              $style = ai_change_css ($style, 'right', $horizontal_margin . 'px');
            }
            break;
        }
        break;
    }

    return $style;
  }

  public function stick_to_the_content_class () {
    $classes = array ();

    $alignment_type = $this->get_alignment_type ();
    $custom_css = $this->get_custom_css ();
    $horizontal_position = $this->get_horizontal_position ();
    $vertical_position = $this->get_vertical_position ();

    $main_content_fixed_width = is_numeric (get_main_content_element ());

    switch ($alignment_type) {
      case AI_ALIGNMENT_STICKY:
        if (!$main_content_fixed_width)
          switch ($horizontal_position) {
            case AI_STICK_TO_THE_CONTENT_LEFT:
              $classes []= 'ai-sticky-left';
              break;
            case AI_STICK_TO_THE_CONTENT_RIGHT:
              $classes []= 'ai-sticky-right';
              break;
          }
        switch ($vertical_position) {
          case AI_SCROLL_WITH_THE_CONTENT:
            $classes []= 'ai-sticky-scroll';
            break;
        }
        break;
      case AI_ALIGNMENT_CUSTOM_CSS:
        $clean_custom_css_code = str_replace (' ', '', $custom_css);
        if (!$main_content_fixed_width &&
            strpos ($clean_custom_css_code, 'position:fixed') !== false &&
            strpos ($clean_custom_css_code, 'z-index:') !== false &&
            strpos ($clean_custom_css_code, 'display:none') !== false) {
              if (strpos ($clean_custom_css_code, ';left:auto') !== false) $classes []= 'ai-sticky-left'; // ; to avoid margin-left:auto
          elseif (strpos ($clean_custom_css_code, 'right:auto') !== false) $classes []= 'ai-sticky-right';

          if (strpos ($clean_custom_css_code, 'margin-bottom:auto') !== false) $classes []= 'ai-sticky-scroll';
        }
        break;
    }

    return implode (' ', $classes);
  }

  public function is_sticky () {
    $custom_sticky_css = false;
    if ($this->get_alignment_type () == AI_ALIGNMENT_CUSTOM_CSS) {
      $clean_custom_css_code = str_replace (' ', '', $this->get_custom_css ());
      if (strpos ($clean_custom_css_code, 'position:fixed') !== false && strpos ($clean_custom_css_code, 'z-index:') !== false) $custom_sticky_css = true;
    }

    return ($custom_sticky_css || $this->get_alignment_type () == AI_ALIGNMENT_STICKY);
  }

  public function sticky_parameters (&$classes, $preview = false) {
    global $ai_wp_data;

    $sticky_parameters = '';

//    $custom_sticky_css = false;
//    if ($this->get_alignment_type () == AI_ALIGNMENT_CUSTOM_CSS) {
//      $clean_custom_css_code = str_replace (' ', '', $this->get_custom_css ());
//      if (strpos ($clean_custom_css_code, 'position:fixed') !== false && strpos ($clean_custom_css_code, 'z-index:') !== false) $custom_sticky_css = true;
//    }

//    if ($this->get_alignment_type () == AI_ALIGNMENT_STICKY || $custom_sticky_css) {
    if ($this->is_sticky ()) {

      $stick_to_the_content_class = $this->stick_to_the_content_class ();

      if ($stick_to_the_content_class != '') {
        $classes [] = 'ai-sticky-content';
        $classes [] = $stick_to_the_content_class;
      }

      $horizontal_position = $this->get_horizontal_position ();
      $vertical_position = $this->get_vertical_position ();
      $animation = $this->get_animation ();

      $direction = '';

      switch ($horizontal_position) {
        case AI_STICK_TO_THE_LEFT:
        case AI_STICK_TO_THE_CONTENT_LEFT:
          $direction = 'right';
          break;
        case AI_STICK_HORIZONTAL_CENTER:
          $classes [] = 'ai-center-h';
          switch ($vertical_position) {
            case AI_STICK_TO_THE_TOP:
            case AI_SCROLL_WITH_THE_CONTENT:
              $direction = 'down';
              break;
            case AI_STICK_VERTICAL_CENTER:
              $direction = 'left';
              switch ($animation) {
                case AI_ANIMATION_SLIDE:
                case AI_ANIMATION_SLIDE_FADE:
                  $animation = AI_ANIMATION_FADE;
                  break;
                case AI_ANIMATION_ZOOM_IN:
                case AI_ANIMATION_ZOOM_OUT:
                  $direction = 'up';
                  break;
              }
              break;
            case AI_STICK_TO_THE_BOTTOM:
              $direction = 'up';
              break;
          }
          break;
        case AI_STICK_TO_THE_CONTENT_RIGHT:
        case AI_STICK_TO_THE_RIGHT:
          $direction = 'left';
          break;
      }

      if ($vertical_position == AI_STICK_VERTICAL_CENTER) $classes [] = 'ai-center-v';

      switch ($horizontal_position) {
        case AI_STICK_TO_THE_LEFT:
          if ($animation == AI_ANIMATION_TURN) $direction = 'left';
          break;
        case AI_STICK_TO_THE_RIGHT:
          if ($animation == AI_ANIMATION_TURN) $direction = 'right';
          break;
        case AI_STICK_TO_THE_CONTENT_LEFT:
        case AI_STICK_TO_THE_CONTENT_RIGHT:
          if ($animation == AI_ANIMATION_SLIDE) $animation = AI_ANIMATION_SLIDE_FADE;
          break;
      }

      switch ($animation) {
        case AI_ANIMATION_FADE:
          $sticky_parameters .= ' data-aos="fade"';
          break;
        case AI_ANIMATION_SLIDE:
          $sticky_parameters .= ' data-aos="slide-'.$direction.'"';
          break;
        case AI_ANIMATION_SLIDE_FADE:
          $sticky_parameters .= ' data-aos="fade-'.$direction.'"';
          break;
        case AI_ANIMATION_TURN:
          $classes [] = 'ai-sticky-turn';
          $sticky_parameters .= ' data-aos="flip-'.$direction.'"';
          break;
        case AI_ANIMATION_FLIP:
          if ($direction == 'right') $direction = 'left';
          elseif ($direction == 'left') $direction = 'right';
          $sticky_parameters .= ' data-aos="flip-'.$direction.'"';
          break;
        case AI_ANIMATION_ZOOM_IN:
          $sticky_parameters .= ' data-aos="zoom-in-'.$direction.'"';
          break;
        case AI_ANIMATION_ZOOM_OUT:
          $sticky_parameters .= ' data-aos="zoom-out-'.$direction.'"';
          break;
      }

      if (!$preview) {
        switch ($this->get_animation_trigger ()) {
          case AI_TRIGGER_PAGE_SCROLLED_PC:
            $pc = $this->get_animation_trigger_value ();
            if (!is_numeric ($pc)) $pc = 0;
            $pc = intval ($pc);
            if ($pc < 0) $pc = 0;
            if ($pc > 100) $pc = 100;
            $pc = number_format ($pc / 100, 2);
            if (!isset ($ai_wp_data [AI_TRIGGER_ELEMENTS])) $ai_wp_data [AI_TRIGGER_ELEMENTS] = array ();
            $ai_wp_data [AI_TRIGGER_ELEMENTS][$this->number] = $pc;
            $sticky_parameters .= ' data-aos-anchor="#ai-position-'.$this->number.'" data-aos-anchor-placement="top-top"';
            break;
          case AI_TRIGGER_PAGE_SCROLLED_PX:
            $px = $this->get_animation_trigger_value ();
            if (!is_numeric ($px)) $px = 0;
            $px = intval ($px);
            if ($px < 0) $px = 0;
            if (!isset ($ai_wp_data [AI_TRIGGER_ELEMENTS])) $ai_wp_data [AI_TRIGGER_ELEMENTS] = array ();
            $ai_wp_data [AI_TRIGGER_ELEMENTS][$this->number] = $px;
            $sticky_parameters .= ' data-aos-anchor="#ai-position-'.$this->number.'" data-aos-anchor-placement="top-top"';
            break;
          case AI_TRIGGER_ELEMENT_VISIBLE:
            $sticky_parameters .= ' data-aos-anchor="'.$this->get_animation_trigger_value ().'"';
            break;
        }

        $offset = $this->get_animation_trigger_offset ();
        if (is_numeric ($offset)) {
          $offset = intval ($offset);
          if ($offset < -1000) $offset = - 1000;
          elseif ($offset > 1000) $offset = 1000;

          $sticky_parameters .= ' data-aos-offset="'.$offset.'"';
        }

        $delay = $this->get_animation_trigger_delay ();
        if (is_numeric ($delay) && $delay > 0) {
          $delay = intval ($delay);

          $sticky_parameters .= ' data-aos-delay="'.$delay.'"';
        }

        if ($this->get_animation_trigger_once ()) {
          $sticky_parameters .= ' data-aos-once="true"';
        }
      }
    }

    return $sticky_parameters;
  }

  public function alignment_style ($alignment_type, $all_styles = false, $full_sticky_style = true) {

    $style = "";
    switch ($alignment_type) {
      case AI_ALIGNMENT_DEFAULT:
        $style = AI_ALIGNMENT_CSS_DEFAULT;
        break;
      case AI_ALIGNMENT_LEFT:
        $style = AI_ALIGNMENT_CSS_LEFT;
        break;
      case AI_ALIGNMENT_RIGHT:
        $style = AI_ALIGNMENT_CSS_RIGHT;
        break;
      case AI_ALIGNMENT_CENTER:
        $style = AI_ALIGNMENT_CSS_CENTER;
        break;
      case AI_ALIGNMENT_FLOAT_LEFT:
        $style = AI_ALIGNMENT_CSS_FLOAT_LEFT;
        break;
      case AI_ALIGNMENT_FLOAT_RIGHT:
        $style = AI_ALIGNMENT_CSS_FLOAT_RIGHT;
        break;
      case AI_ALIGNMENT_STICKY_LEFT:
        $style = AI_ALIGNMENT_CSS_STICKY_LEFT;
        break;
      case AI_ALIGNMENT_STICKY_RIGHT:
        $style = AI_ALIGNMENT_CSS_STICKY_RIGHT;
        break;
      case AI_ALIGNMENT_STICKY_TOP:
        $style = AI_ALIGNMENT_CSS_STICKY_TOP;
        break;
      case AI_ALIGNMENT_STICKY_BOTTOM:
        $style = AI_ALIGNMENT_CSS_STICKY_BOTTOM;
        break;
      case AI_ALIGNMENT_STICKY:
        $style = AI_ALIGNMENT_CSS_STICKY;
        if ($full_sticky_style) {
          $style .= $this->sticky_style ($this->get_horizontal_position (), $this->get_vertical_position ());
        }
        break;
      case AI_ALIGNMENT_CUSTOM_CSS:
        $style = $this->get_custom_css ();
        break;
      default:
        $style = '';
        break;
    }

    if (!$all_styles && strpos ($style, "||") !== false) {
      $styles = explode ("||", $style);
      if (isset ($styles [0])) {
        $style = trim ($styles [0]);
      }
    }

    return $style;
  }

  public function get_horizontal_position (){
    $option = - 1;

    if (isset ($this->wp_options [AI_OPTION_ALIGNMENT_TYPE])) {
      switch ($this->wp_options [AI_OPTION_ALIGNMENT_TYPE]) {
        case AI_ALIGNMENT_STICKY_LEFT:
          $option = AI_STICK_TO_THE_LEFT;
          break;
        case AI_ALIGNMENT_STICKY_RIGHT:
          $option = AI_STICK_TO_THE_RIGHT;
          break;
        case AI_ALIGNMENT_STICKY_TOP:
          $option = AI_STICK_HORIZONTAL_CENTER;
          break;
        case AI_ALIGNMENT_STICKY_BOTTOM:
          $option = AI_STICK_HORIZONTAL_CENTER;
          break;
      }
    }

    if ($option == - 1) {
      $option = isset ($this->wp_options [AI_OPTION_HORIZONTAL_POSITION]) ? $this->wp_options [AI_OPTION_HORIZONTAL_POSITION] : DEFAULT_HORIZONTAL_POSITION;
    }

    return $option;
  }

  public function get_vertical_position (){
    $option = - 1;

    if (isset ($this->wp_options [AI_OPTION_ALIGNMENT_TYPE])) {
      switch ($this->wp_options [AI_OPTION_ALIGNMENT_TYPE]) {
        case AI_ALIGNMENT_STICKY_LEFT:
          $option = AI_STICK_TO_THE_TOP;
          break;
        case AI_ALIGNMENT_STICKY_RIGHT:
          $option = AI_STICK_TO_THE_TOP;
          break;
        case AI_ALIGNMENT_STICKY_TOP:
          $option = AI_STICK_TO_THE_TOP;
          break;
        case AI_ALIGNMENT_STICKY_BOTTOM:
          $option = AI_STICK_TO_THE_BOTTOM;
          break;
      }
    }

    if ($option == - 1) {
      $option = isset ($this->wp_options [AI_OPTION_VERTICAL_POSITION]) ? $this->wp_options [AI_OPTION_VERTICAL_POSITION] : DEFAULT_VERTICAL_POSITION;
    }

    return $option;
  }

  public function get_tracking ($saved_value = false){
    $tracking = AI_DISABLED;
    if (function_exists ('get_global_tracking')) {
      if (get_global_tracking () || $saved_value) {
        $tracking = isset ($this->wp_options [AI_OPTION_TRACKING]) ? $this->wp_options [AI_OPTION_TRACKING] : AI_DISABLED;
      }
    }
    return $tracking;
  }

  public function get_alignment_style (){
    return $this->alignment_style ($this->get_alignment_type());
  }

  public function get_html_selector ($decode = false){
    $option = isset ($this->wp_options [AI_OPTION_HTML_SELECTOR]) ? $this->wp_options [AI_OPTION_HTML_SELECTOR] : "";
    if ($decode) $option = html_entity_decode ($option);
    return $option;
  }

  public function get_server_side_insertion (){
    $option = isset ($this->wp_options [AI_OPTION_SERVER_SIDE_INSERTION]) ? $this->wp_options [AI_OPTION_SERVER_SIDE_INSERTION] : DEFAULT_SERVER_SIDE_INSERTION;
    return $option;
  }

  public function get_html_element_insertion () {
    $option = isset ($this->wp_options [AI_OPTION_HTML_ELEMENT_INSERTION]) ? $this->wp_options [AI_OPTION_HTML_ELEMENT_INSERTION] : DEFAULT_HTML_ELEMENT_INSERTION;
    return $option;
  }

  public function get_html_element_insertion_text ($translate = true) {
    switch ($this->get_html_element_insertion ()) {
      case AI_HTML_INSERTION_CLIENT_SIDE:
        if (!$translate) return AI_TEXT_ENG_CLIENT_SIDE;
        return AI_TEXT_CLIENT_SIDE;
        break;
      case AI_HTML_INSERTION_CLIENT_SIDE_DOM_READY:
        if (!$translate) return AI_TEXT_ENG_CLIENT_SIDE_DOM_READY;
        return AI_TEXT_CLIENT_SIDE_DOM_READY;
        break;
      case AI_HTML_INSERTION_SEREVR_SIDE:
        if (!$translate) return AI_TEXT_ENG_SERVER_SIDE;
        return AI_TEXT_SERVER_SIDE;
        break;
      default:
        return '';
        break;
    }
  }

  public function get_inside_element (){
    $option = isset ($this->wp_options [AI_OPTION_INSIDE_ELEMENT]) ? $this->wp_options [AI_OPTION_INSIDE_ELEMENT] : DEFAULT_INSIDE_ELEMENT;
    return $option;
  }

  public function get_paragraph_number(){
    $option = isset ($this->wp_options [AI_OPTION_PARAGRAPH_NUMBER]) ? $this->wp_options [AI_OPTION_PARAGRAPH_NUMBER] : "";
    return $option;
  }

  public function get_paragraph_number_minimum(){
    $option = isset ($this->wp_options [AI_OPTION_MIN_PARAGRAPHS]) ? $this->wp_options [AI_OPTION_MIN_PARAGRAPHS] : "";
    if ($option == '0') $option = '';
    return $option;
  }

  public function get_minimum_words_above (){
    $option = isset ($this->wp_options [AI_OPTION_MIN_WORDS_ABOVE]) ? $this->wp_options [AI_OPTION_MIN_WORDS_ABOVE] : "";
    return $option;
  }

  public function get_minimum_words(){
    $option = isset ($this->wp_options [AI_OPTION_MIN_WORDS]) ? $this->wp_options [AI_OPTION_MIN_WORDS] : "";
    if ($option == '0') $option = '';
    return $option;
  }

  public function get_maximum_words(){
    $option = isset ($this->wp_options [AI_OPTION_MAX_WORDS]) ? $this->wp_options [AI_OPTION_MAX_WORDS] : "";
    return $option;
  }

  public function get_paragraph_tags(){
     $option = isset ($this->wp_options [AI_OPTION_PARAGRAPH_TAGS]) ? $this->wp_options [AI_OPTION_PARAGRAPH_TAGS] : DEFAULT_PARAGRAPH_TAGS;
     return str_replace (array ('<', '>'), '', $option);
  }

  public function get_minimum_paragraph_words(){
    $option = isset ($this->wp_options [AI_OPTION_MIN_PARAGRAPH_WORDS]) ? $this->wp_options [AI_OPTION_MIN_PARAGRAPH_WORDS] : "";
    if ($option == '0') $option = '';
    return $option;
   }

  public function get_maximum_paragraph_words(){
    $option = isset ($this->wp_options [AI_OPTION_MAX_PARAGRAPH_WORDS]) ? $this->wp_options [AI_OPTION_MAX_PARAGRAPH_WORDS] : "";
    return $option;
   }

  public function get_count_inside_blockquote(){
    $option = isset ($this->wp_options [AI_OPTION_COUNT_INSIDE_BLOCKQUOTE]) ? $this->wp_options [AI_OPTION_COUNT_INSIDE_BLOCKQUOTE] : "";
    if ($option == '') $option = AI_DISABLED;
    return $option;
   }

  public function get_avoid_paragraphs_above(){
    $option = isset ($this->wp_options [AI_OPTION_AVOID_PARAGRAPHS_ABOVE]) ? $this->wp_options [AI_OPTION_AVOID_PARAGRAPHS_ABOVE] : "";
    return $option;
   }

  public function get_avoid_paragraphs_below(){
    $option = isset ($this->wp_options [AI_OPTION_AVOID_PARAGRAPHS_BELOW]) ? $this->wp_options [AI_OPTION_AVOID_PARAGRAPHS_BELOW] : "";
    return $option;
   }

  public function get_avoid_text_above(){
    $option = isset ($this->wp_options [AI_OPTION_AVOID_TEXT_ABOVE]) ? $this->wp_options [AI_OPTION_AVOID_TEXT_ABOVE] : "";
    return $option;
   }

  public function get_avoid_text_below(){
    $option = isset ($this->wp_options [AI_OPTION_AVOID_TEXT_BELOW]) ? $this->wp_options [AI_OPTION_AVOID_TEXT_BELOW] : "";
    return $option;
   }

  public function get_avoid_action(){
    $option = isset ($this->wp_options [AI_OPTION_AVOID_ACTION]) ? $this->wp_options [AI_OPTION_AVOID_ACTION] : DEFAULT_AVOID_ACTION;
    if ($option == '') $option = DEFAULT_AVOID_ACTION;

    elseif ($option == AD_DO_NOT_INSERT)             $option = AI_DO_NOT_INSERT;
    elseif ($option == AD_TRY_TO_SHIFT_POSITION)     $option = AI_TRY_TO_SHIFT_POSITION;

    return $option;
  }

  public function get_avoid_action_text ($translate = true) {
    switch ($this->get_avoid_action()) {
      case AI_DO_NOT_INSERT:
        if (!$translate) return AI_TEXT_ENG_DO_NOT_INSERT;
        return AI_TEXT_DO_NOT_INSERT;
        break;
      case AI_TRY_TO_SHIFT_POSITION:
        if (!$translate) return AI_TEXT_ENG_TRY_TO_SHIFT_POSITION;
        return AI_TEXT_TRY_TO_SHIFT_POSITION;
        break;
      default:
        return '';
        break;
    }
  }

  public function get_avoid_try_limit(){
    $option = isset ($this->wp_options [AI_OPTION_AVOID_TRY_LIMIT]) ? $this->wp_options [AI_OPTION_AVOID_TRY_LIMIT] : "";
    if ($option == '') $option = AD_ZERO;
    return $option;
   }

  public function get_avoid_direction(){
    $option = isset ($this->wp_options [AI_OPTION_AVOID_DIRECTION]) ? $this->wp_options [AI_OPTION_AVOID_DIRECTION] : DEFAULT_AVOID_DIRECTION;
    if ($option == '') $option = DEFAULT_AVOID_DIRECTION;

    elseif ($option == AD_ABOVE)                  $option = AI_ABOVE;
    elseif ($option == AD_BELOW)                  $option = AI_BELOW;
    elseif ($option == AD_ABOVE_AND_THEN_BELOW)   $option = AI_ABOVE_AND_THEN_BELOW;
    elseif ($option == AD_BELOW_AND_THEN_ABOVE)   $option = AI_BELOW_AND_THEN_ABOVE;

    return $option;
  }

  public function get_avoid_direction_text ($translate = true){
    switch ($this->get_avoid_direction()) {
      case AI_ABOVE:
        if (!$translate) return AI_TEXT_ENG_ABOVE;
        return AI_TEXT_ABOVE;
        break;
      case AI_BELOW:
        if (!$translate) return AI_TEXT_ENG_BELOW;
        return AI_TEXT_BELOW;
        break;
      case AI_ABOVE_AND_THEN_BELOW:
        if (!$translate) return AI_TEXT_ENG_ABOVE_AND_THEN_BELOW;
        return AI_TEXT_ABOVE_AND_THEN_BELOW;
        break;
      case AI_BELOW_AND_THEN_ABOVE:
        if (!$translate) return AI_TEXT_ENG_BELOW_AND_THEN_ABOVE;
        return AI_TEXT_BELOW_AND_THEN_ABOVE;
        break;
      default:
        return '';
        break;
    }
  }

  public function get_call_filter(){
    $option = isset ($this->wp_options [AI_OPTION_EXCERPT_NUMBER]) ? $this->wp_options [AI_OPTION_EXCERPT_NUMBER] : "";
    if ($option == '0') $option = '';
    return $option;
  }

  public function get_filter_type(){
    $option = isset ($this->wp_options [AI_OPTION_FILTER_TYPE]) ? $this->wp_options [AI_OPTION_FILTER_TYPE] : AI_FILTER_AUTO;

    if ($option == '')                                          $option = AI_FILTER_AUTO;

    elseif ($option == AI_OPTION_FILTER_AUTO)                   $option = AI_FILTER_AUTO;
    elseif ($option == AI_OPTION_FILTER_PHP_FUNCTION_CALLS)     $option = AI_FILTER_PHP_FUNCTION_CALLS;
    elseif ($option == AI_OPTION_FILTER_CONTENT_PROCESSING)     $option = AI_FILTER_CONTENT_PROCESSING;
    elseif ($option == AI_OPTION_FILTER_EXCERPT_PROCESSING)     $option = AI_FILTER_EXCERPT_PROCESSING;
    elseif ($option == AI_OPTION_FILTER_BEFORE_POST_PROCESSING) $option = AI_FILTER_BEFORE_POST_PROCESSING;
    elseif ($option == AI_OPTION_FILTER_AFTER_POST_PROCESSING)  $option = AI_FILTER_AFTER_POST_PROCESSING;
    elseif ($option == AI_OPTION_FILTER_WIDGET_DRAWING)         $option = AI_FILTER_WIDGET_DRAWING;
    elseif ($option == AI_OPTION_FILTER_SUBPAGES)               $option = AI_FILTER_SUBPAGES;
    elseif ($option == AI_OPTION_FILTER_POSTS)                  $option = AI_FILTER_POSTS;
    elseif ($option == AI_OPTION_FILTER_COMMENTS)               $option = AI_FILTER_COMMENTS;

    return $option;
  }

  public function get_filter_type_text ($translate = true){
    switch ($this->get_filter_type()) {
      case AI_FILTER_AUTO:
        if (!$translate) return AI_TEXT_ENG_AUTO_COUNTER;
        return AI_TEXT_AUTO_COUNTER;
        break;
      case AI_FILTER_PHP_FUNCTION_CALLS:
        if (!$translate) return AI_TEXT_ENG_PHP_FUNCTION_CALLS_COUNTER;
        return AI_TEXT_PHP_FUNCTION_CALLS_COUNTER;
        break;
      case AI_FILTER_CONTENT_PROCESSING:
        if (!$translate) return AI_TEXT_ENG_CONTENT_PROCESSING_COUNTER;
        return AI_TEXT_CONTENT_PROCESSING_COUNTER;
        break;
      case AI_FILTER_EXCERPT_PROCESSING:
        if (!$translate) return AI_TEXT_ENG_EXCERPT_PROCESSING_COUNTER;
        return AI_TEXT_EXCERPT_PROCESSING_COUNTER;
        break;
      case AI_FILTER_BEFORE_POST_PROCESSING:
        if (!$translate) return AI_TEXT_ENG_BEFORE_POST_PROCESSING_COUNTER;
        return AI_TEXT_BEFORE_POST_PROCESSING_COUNTER;
        break;
      case AI_FILTER_AFTER_POST_PROCESSING:
        if (!$translate) return AI_TEXT_ENG_AFTER_POST_PROCESSING_COUNTER;
        return AI_TEXT_AFTER_POST_PROCESSING_COUNTER;
        break;
      case AI_FILTER_WIDGET_DRAWING:
        if (!$translate) return AI_TEXT_ENG_WIDGET_DRAWING_COUNTER;
        return AI_TEXT_WIDGET_DRAWING_COUNTER;
        break;
      case AI_FILTER_SUBPAGES:
        if (!$translate) return AI_TEXT_ENG_SUBPAGES_COUNTER;
        return AI_TEXT_SUBPAGES_COUNTER;
        break;
      case AI_FILTER_POSTS:
        if (!$translate) return AI_TEXT_ENG_POSTS_COUNTER;
        return AI_TEXT_POSTS_COUNTER;
        break;
      case AI_FILTER_PARAGRAPHS:
        if (!$translate) return AI_TEXT_ENG_PARAGRAPHS_COUNTER;
        return AI_TEXT_PARAGRAPHS_COUNTER;
        break;
      case AI_FILTER_COMMENTS:
        if (!$translate) return AI_TEXT_ENG_COMMENTS_COUNTER;
        return AI_TEXT_COMMENTS_COUNTER;
        break;
      default:
        return '';
        break;
    }
  }

  public function get_inverted_filter (){
    $inverted_filter = isset ($this->wp_options [AI_OPTION_INVERTED_FILTER]) ? $this->wp_options [AI_OPTION_INVERTED_FILTER] : AI_DISABLED;
    if ($inverted_filter == '') $inverted_filter = AI_DISABLED;
    return $inverted_filter;
  }

  public function get_direction_type () {
    $option = isset ($this->wp_options [AI_OPTION_DIRECTION_TYPE]) ? $this->wp_options [AI_OPTION_DIRECTION_TYPE] : DEFAULT_DIRECTION_TYPE;
    if ($option == '') $option = DEFAULT_DIRECTION_TYPE;

    elseif ($option == AD_DIRECTION_FROM_TOP)       $option = AI_DIRECTION_FROM_TOP;
    elseif ($option == AD_DIRECTION_FROM_BOTTOM)    $option = AI_DIRECTION_FROM_BOTTOM;

    return $option;
   }

  public function get_direction_type_text ($translate = true){
    switch ($this->get_direction_type ()) {
      case AI_DIRECTION_FROM_TOP:
        if (!$translate) return AI_TEXT_ENG_DIRECTION_FROM_TOP;
        return AI_TEXT_DIRECTION_FROM_TOP;
        break;
      case AI_DIRECTION_FROM_BOTTOM:
        if (!$translate) return AI_TEXT_ENG_DIRECTION_FROM_BOTTOM;
        return AI_TEXT_DIRECTION_FROM_BOTTOM;
        break;
      default:
        return '';
        break;
    }
  }

  public function get_display_settings_post(){
    $option = isset ($this->wp_options [AI_OPTION_DISPLAY_ON_POSTS]) ? $this->wp_options [AI_OPTION_DISPLAY_ON_POSTS] : "";
    if ($option == '') $option = AI_ENABLED;
    return $option;
  }

  public function get_display_settings_page(){
    $option = isset ($this->wp_options [AI_OPTION_DISPLAY_ON_PAGES]) ? $this->wp_options [AI_OPTION_DISPLAY_ON_PAGES] : "";
    if ($option == '') $option = AI_DISABLED;
    return $option;
  }

  public function get_display_settings_home(){
    global $ai_db_options;

    $option = isset ($this->wp_options [AI_OPTION_DISPLAY_ON_HOMEPAGE]) ? $this->wp_options [AI_OPTION_DISPLAY_ON_HOMEPAGE] : "";
    if ($option == '') $option = AI_DISABLED;

    if (isset ($ai_db_options [AI_OPTION_GLOBAL]['VERSION']) && $ai_db_options [AI_OPTION_GLOBAL]['VERSION'] < '010605') {
      if (isset ($this->wp_options [AI_OPTION_AUTOMATIC_INSERTION])) {
        $automatic_insertion = $this->wp_options [AI_OPTION_AUTOMATIC_INSERTION];
      } else $automatic_insertion = '';

      if ($automatic_insertion == AI_AUTOMATIC_INSERTION_BEFORE_PARAGRAPH ||
          $automatic_insertion == AI_AUTOMATIC_INSERTION_AFTER_PARAGRAPH ||
          $automatic_insertion == AI_AUTOMATIC_INSERTION_BEFORE_CONTENT ||
          $automatic_insertion == AI_AUTOMATIC_INSERTION_AFTER_CONTENT)
        $option = AI_DISABLED;
    }

    return $option;
  }

  public function get_display_settings_category(){
    global $ai_db_options;

    $option = isset ($this->wp_options [AI_OPTION_DISPLAY_ON_CATEGORY_PAGES]) ? $this->wp_options [AI_OPTION_DISPLAY_ON_CATEGORY_PAGES] : "";
    if ($option == '') $option = AI_DISABLED;

    if (isset ($ai_db_options [AI_OPTION_GLOBAL]['VERSION']) && $ai_db_options [AI_OPTION_GLOBAL]['VERSION'] < '010605') {
      if (isset ($this->wp_options [AI_OPTION_AUTOMATIC_INSERTION])) {
        $automatic_insertion = $this->wp_options [AI_OPTION_AUTOMATIC_INSERTION];
      } else $automatic_insertion = '';

      if ($automatic_insertion == AI_AUTOMATIC_INSERTION_BEFORE_PARAGRAPH ||
          $automatic_insertion == AI_AUTOMATIC_INSERTION_AFTER_PARAGRAPH ||
          $automatic_insertion == AI_AUTOMATIC_INSERTION_BEFORE_CONTENT ||
          $automatic_insertion == AI_AUTOMATIC_INSERTION_AFTER_CONTENT)
        $option = AI_DISABLED;
    }

    return $option;
  }

  public function get_display_settings_search(){
    global $ai_db_options;

    $option = isset ($this->wp_options [AI_OPTION_DISPLAY_ON_SEARCH_PAGES]) ? $this->wp_options [AI_OPTION_DISPLAY_ON_SEARCH_PAGES] : "";
    if ($option == '') $option = AI_DISABLED;

    if (isset ($ai_db_options [AI_OPTION_GLOBAL]['VERSION']) && $ai_db_options [AI_OPTION_GLOBAL]['VERSION'] < '010605') {
      if (isset ($this->wp_options [AI_OPTION_AUTOMATIC_INSERTION])) {
        $automatic_insertion = $this->wp_options [AI_OPTION_AUTOMATIC_INSERTION];
      } else $automatic_insertion = '';

      if ($automatic_insertion == AI_AUTOMATIC_INSERTION_BEFORE_PARAGRAPH ||
          $automatic_insertion == AI_AUTOMATIC_INSERTION_AFTER_PARAGRAPH ||
          $automatic_insertion == AI_AUTOMATIC_INSERTION_BEFORE_CONTENT ||
          $automatic_insertion == AI_AUTOMATIC_INSERTION_AFTER_CONTENT)
        $option = AI_DISABLED;
    }

    return $option;
  }

  public function get_display_settings_archive(){
    global $ai_db_options;

    $option = isset ($this->wp_options [AI_OPTION_DISPLAY_ON_ARCHIVE_PAGES]) ? $this->wp_options [AI_OPTION_DISPLAY_ON_ARCHIVE_PAGES] : "";
    if ($option == '') $option = AI_DISABLED;

    if (isset ($ai_db_options [AI_OPTION_GLOBAL]['VERSION']) && $ai_db_options [AI_OPTION_GLOBAL]['VERSION'] < '010605') {
      if (isset ($this->wp_options [AI_OPTION_AUTOMATIC_INSERTION])) {
        $automatic_insertion = $this->wp_options [AI_OPTION_AUTOMATIC_INSERTION];
      } else $automatic_insertion = '';

      if ($automatic_insertion == AI_AUTOMATIC_INSERTION_BEFORE_PARAGRAPH ||
          $automatic_insertion == AI_AUTOMATIC_INSERTION_AFTER_PARAGRAPH ||
          $automatic_insertion == AI_AUTOMATIC_INSERTION_BEFORE_CONTENT ||
          $automatic_insertion == AI_AUTOMATIC_INSERTION_AFTER_CONTENT)
        $option = AI_DISABLED;
    }

    return $option;
  }

  public function get_enable_feed (){
    $enable_feed = isset ($this->wp_options [AI_OPTION_ENABLE_FEED]) ? $this->wp_options [AI_OPTION_ENABLE_FEED] : "";
    if ($enable_feed == '') $enable_feed = AI_DISABLED;
    return $enable_feed;
  }

  public function get_enable_ajax (){
    $enable_ajax = isset ($this->wp_options [AI_OPTION_ENABLE_AJAX]) ? $this->wp_options [AI_OPTION_ENABLE_AJAX] : "";
    if ($enable_ajax == '') $enable_ajax = AI_ENABLED;
    return $enable_ajax;
  }

  public function get_disable_caching (){
    $option = isset ($this->wp_options [AI_OPTION_DISABLE_CACHING]) ? $this->wp_options [AI_OPTION_DISABLE_CACHING] : AI_DISABLED;
    return $option;
  }

  public function get_max_page_blocks_enabled (){
    $option = isset ($this->wp_options [AI_OPTION_MAX_PAGE_BLOCKS_ENABLED]) ? $this->wp_options [AI_OPTION_MAX_PAGE_BLOCKS_ENABLED] : AI_DISABLED;
    return $option;
  }

  public function get_only_in_the_loop (){
    $option = isset ($this->wp_options [AI_OPTION_ONLY_IN_THE_LOOP]) ? $this->wp_options [AI_OPTION_ONLY_IN_THE_LOOP] : AI_DISABLED;
    return $option;
  }

   // Used for shortcodes
   public function get_enable_manual (){
     $option = isset ($this->wp_options [AI_OPTION_ENABLE_MANUAL]) ? $this->wp_options [AI_OPTION_ENABLE_MANUAL] : AI_DISABLED;

     if ($option == '') $option = AI_DISABLED;

     return $option;
   }

   public function get_enable_widget (){
     global $ai_db_options;

     $enable_widget = isset ($this->wp_options [AI_OPTION_ENABLE_WIDGET]) ? $this->wp_options [AI_OPTION_ENABLE_WIDGET] : "";
     if ($enable_widget == '') $enable_widget = AI_ENABLED;

     return $enable_widget;
   }

   public function get_enable_php_call (){
     $option = isset ($this->wp_options [AI_OPTION_ENABLE_PHP_CALL]) ? $this->wp_options [AI_OPTION_ENABLE_PHP_CALL] : "";
     if ($option == '') $option = AI_DISABLED;
     return $option;
   }

   public function get_paragraph_text (){
     $paragraph_text = isset ($this->wp_options [AI_OPTION_PARAGRAPH_TEXT]) ? $this->wp_options [AI_OPTION_PARAGRAPH_TEXT] : "";
     return $paragraph_text;
   }

  public function get_paragraph_text_type () {
    $option = isset ($this->wp_options [AI_OPTION_PARAGRAPH_TEXT_TYPE]) ? $this->wp_options [AI_OPTION_PARAGRAPH_TEXT_TYPE] : DEFAULT_PARAGRAPH_TEXT_TYPE;
    if ($option == '') $option = DEFAULT_PARAGRAPH_TEXT_TYPE;

    elseif ($option == AD_CONTAIN)          $option = AI_CONTAIN;
    elseif ($option == AD_DO_NOT_CONTAIN)   $option = AI_DO_NOT_CONTAIN;

    return $option;
  }

  public function get_paragraph_text_type_text ($translate = true){
    switch ($this->get_paragraph_text_type ()) {
      case AI_CONTAIN:
        if (!$translate) return AI_TEXT_ENG_CONTAIN;
        return AI_TEXT_CONTAIN;
        break;
      case AI_DO_NOT_CONTAIN:
        if (!$translate) return AI_TEXT_ENG_DO_NOT_CONTAIN;
        return AI_TEXT_DO_NOT_CONTAIN;
        break;
      default:
        return '';
        break;
    }
  }

   public function get_custom_css (){
      global $ai_db_options;

      $option = isset ($this->wp_options [AI_OPTION_CUSTOM_CSS]) ? $this->wp_options [AI_OPTION_CUSTOM_CSS] : "";

      // Fix for old bug
      if (isset ($ai_db_options [AI_OPTION_GLOBAL]['VERSION']) && $ai_db_options [AI_OPTION_GLOBAL]['VERSION'] < '010605' && strpos ($option, "Undefined index")) $option = "";

      return $option;
   }

  public function get_display_for_users (){
    $option = isset ($this->wp_options [AI_OPTION_DISPLAY_FOR_USERS]) ? $this->wp_options [AI_OPTION_DISPLAY_FOR_USERS] : DEFAULT_DISPLAY_FOR_USERS;
    if ($option == '') $option = DEFAULT_DISPLAY_FOR_USERS;

    elseif ($option == 'all')                           $option = AI_DISPLAY_ALL_USERS;
    elseif ($option == 'logged in')                     $option = AI_DISPLAY_LOGGED_IN_USERS;
    elseif ($option == 'not logged in')                 $option = AI_DISPLAY_NOT_LOGGED_IN_USERS;

    elseif ($option == AD_DISPLAY_ALL_USERS)            $option = AI_DISPLAY_ALL_USERS;
    elseif ($option == AD_DISPLAY_LOGGED_IN_USERS)      $option = AI_DISPLAY_LOGGED_IN_USERS;
    elseif ($option == AD_DISPLAY_NOT_LOGGED_IN_USERS)  $option = AI_DISPLAY_NOT_LOGGED_IN_USERS;
    elseif ($option == AD_DISPLAY_ADMINISTRATORS)       $option = AI_DISPLAY_ADMINISTRATORS;

    return $option;
  }

  public function get_display_for_users_text ($translate = true){
    switch ($this->get_display_for_users ()) {
      case AI_DISPLAY_ALL_USERS:
        if (!$translate) return AI_TEXT_ENG_DISPLAY_ALL_USERS;
        return AI_TEXT_DISPLAY_ALL_USERS;
        break;
      case AI_DISPLAY_LOGGED_IN_USERS:
        if (!$translate) return AI_TEXT_ENG_DISPLAY_LOGGED_IN_USERS;
        return AI_TEXT_DISPLAY_LOGGED_IN_USERS;
        break;
      case AI_DISPLAY_NOT_LOGGED_IN_USERS:
        if (!$translate) return AI_TEXT_ENG_DISPLAY_NOT_LOGGED_IN_USERS;
        return AI_TEXT_DISPLAY_NOT_LOGGED_IN_USERS;
        break;
      case AI_DISPLAY_ADMINISTRATORS:
        if (!$translate) return AI_TEXT_ENG_DISPLAY_ADMINISTRATORS;
        return AI_TEXT_DISPLAY_ADMINISTRATORS;
        break;
      default:
        return '';
        break;
    }
  }

   public function get_detection_client_side(){
     global $ai_db_options;

     $option = isset ($this->wp_options [AI_OPTION_DETECT_CLIENT_SIDE]) ? $this->wp_options [AI_OPTION_DETECT_CLIENT_SIDE] : AI_DISABLED;

      if (isset ($ai_db_options [AI_OPTION_GLOBAL]['VERSION']) && $ai_db_options [AI_OPTION_GLOBAL]['VERSION'] < '010605') {
        if (isset ($this->wp_options [AI_OPTION_DISPLAY_FOR_DEVICES])) {
         $display_for_devices = $this->wp_options [AI_OPTION_DISPLAY_FOR_DEVICES];
        } else $display_for_devices = '';

        if ($display_for_devices == AD_DISPLAY_ALL_DEVICES) $option = AI_DISABLED;
      }

     return $option;
   }

   public function get_client_side_action (){
     global $ai_db_options;

     $option = isset ($this->wp_options [AI_OPTION_CLIENT_SIDE_ACTION]) ? $this->wp_options [AI_OPTION_CLIENT_SIDE_ACTION] : DEFAULT_CLIENT_SIDE_ACTION;

     return $option;
   }

  public function get_detection_viewport ($viewport){
    global $ai_db_options;

    $option_name = AI_OPTION_DETECT_VIEWPORT . '_' . $viewport;
    $option = isset ($this->wp_options [$option_name]) ? $this->wp_options [$option_name] : AI_DISABLED;

    if (isset ($ai_db_options [AI_OPTION_GLOBAL]['VERSION']) && $ai_db_options [AI_OPTION_GLOBAL]['VERSION'] < '010605' && $this->get_detection_client_side()) {
      if (isset ($this->wp_options [AI_OPTION_DISPLAY_FOR_DEVICES])) {
       $display_for_devices = $this->wp_options [AI_OPTION_DISPLAY_FOR_DEVICES];
      } else $display_for_devices = '';

      if ($display_for_devices == AD_DISPLAY_DESKTOP_DEVICES ||
          $display_for_devices == AD_DISPLAY_DESKTOP_TABLET_DEVICES ||
          $display_for_devices == AD_DISPLAY_DESKTOP_PHONE_DEVICES) {
           switch ($viewport) {
             case 1:
               $option = AI_ENABLED;
               break;
             default:
               $option = AI_DISABLED;
           }
      }
      elseif ($display_for_devices == AD_DISPLAY_TABLET_DEVICES ||
              $display_for_devices == AD_DISPLAY_MOBILE_DEVICES ||
              $display_for_devices == AD_DISPLAY_DESKTOP_TABLET_DEVICES) {
           switch ($viewport) {
             case 2:
               $option = AI_ENABLED;
               break;
             default:
               $option = AI_DISABLED;
           }
      }
      elseif ($display_for_devices == AD_DISPLAY_PHONE_DEVICES ||
              $display_for_devices == AD_DISPLAY_MOBILE_DEVICES ||
              $display_for_devices == AD_DISPLAY_DESKTOP_PHONE_DEVICES) {
           switch ($viewport) {
             case 3:
               $option = AI_ENABLED;
               break;
             default:
               $option = AI_DISABLED;
           }
      }
      elseif ($display_for_devices == AD_DISPLAY_ALL_DEVICES) $option = AI_DISABLED;
    }

    return $option;
  }

  public function ai_get_counters (&$title){
    global $ai_wp_data, $ad_inserter_globals;

    $predefined_counters_text = $this->counters;
    if ($predefined_counters_text != '') {
      $this->counters = '';
      return $predefined_counters_text;
    }

    $counters = '';
    $title = __('Counters', 'ad-inserter') . ':';

    if (isset ($ad_inserter_globals [AI_CONTENT_COUNTER_NAME]) && ($ai_wp_data [AI_CONTEXT] == AI_CONTEXT_CONTENT || $ai_wp_data [AI_CONTEXT] == AI_CONTEXT_SHORTCODE)) {
      $counters .= ' C='.$ad_inserter_globals [AI_CONTENT_COUNTER_NAME];
      $title .= ' C= ' . __('Content', 'ad-inserter') . ', ';
    }

    if (isset ($ad_inserter_globals [AI_EXCERPT_COUNTER_NAME]) && $ai_wp_data [AI_CONTEXT] == AI_CONTEXT_EXCERPT) {
      $counters .= ' X='.$ad_inserter_globals [AI_EXCERPT_COUNTER_NAME];
      $title .= ' X = ' . __('Excerpt', 'ad-inserter') . ', ';
    }

    if (isset ($ad_inserter_globals [AI_LOOP_BEFORE_COUNTER_NAME]) && $ai_wp_data [AI_CONTEXT] == AI_CONTEXT_BEFORE_POST) {
      $counters .= ' B='.$ad_inserter_globals [AI_LOOP_BEFORE_COUNTER_NAME];
      $title .= ' B = ' . __('Before post', 'ad-inserter') . ', ';
    }

    if (isset ($ad_inserter_globals [AI_LOOP_AFTER_COUNTER_NAME]) && $ai_wp_data [AI_CONTEXT] == AI_CONTEXT_AFTER_POST) {
      $counters .= ' A='.$ad_inserter_globals [AI_LOOP_AFTER_COUNTER_NAME];
      $title .= ' A = ' . __('After post', 'ad-inserter') . ', ';
    }

    if (isset ($ad_inserter_globals [AI_WIDGET_COUNTER_NAME . $this->number]) && $ai_wp_data [AI_CONTEXT] == AI_CONTEXT_WIDGET) {
      $counters .= ' W='.$ad_inserter_globals [AI_WIDGET_COUNTER_NAME . $this->number];
      $title .= ' W = ' . __('Widget', 'ad-inserter') . ', ';
    }

    if (isset ($ad_inserter_globals [AI_PHP_FUNCTION_CALL_COUNTER_NAME . $this->number])) {
      $counters .= ' P='.$ad_inserter_globals [AI_PHP_FUNCTION_CALL_COUNTER_NAME . $this->number];
      $title .= ' P = ' . __('PHP function call', 'ad-inserter') . ', ';
    }

    if (isset ($ad_inserter_globals [AI_BLOCK_COUNTER_NAME . $this->number])) {
      $counters .= ' N='.$ad_inserter_globals [AI_BLOCK_COUNTER_NAME . $this->number];
      $title .= ' N = ' . __('Block', 'ad-inserter');
    }

    return $counters;
  }

  public function ai_getAdLabel () {
    $label_enabled = $this->get_show_label ();

    if (!$label_enabled) return '';

    $ad_label = get_ad_label (true);
    if (strpos ($ad_label, '<') === false && strpos ($ad_label, '>') === false) {
      $ad_label = '<div class="' . get_block_class_name (true) . '-label">' . $ad_label . '</div>';
    }
    return $ad_label .= "\n";
  }

  public function ai_generateDebugLabel ($class = '', $title = '') {
    global $ai_wp_data;

    $right_title = '';
    $fallback_block_name = '';
    if ($class != '') {
      $this->labels->class = $class;
    }

    if ($ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_AJAX) {
      $this->labels->class = 'ai-debug-ajax';
      $title = __('AJAX REQUEST', 'ad-inserter');
    }

    if ($this->fallback != 0) {
      $this->labels->class = 'ai-debug-fallback';
      $fallback_block = $block_object [$this->fallback];
      $fallback_block_name = ' &nbsp;&#8678;&nbsp; '. $this->fallback . ' &nbsp; ' . $fallback_block->get_ad_name ();
    }

    $counters = $this->ai_get_counters ($right_title);
    $version_name = $this->version_name == '' ? '' : ' - ' . $this->version_name;
    $block_name = $this->number . ' &nbsp; ' . $this->get_ad_name () . '<kbd data-separator=" - " class="ai-option-name">' . $version_name . '</kbd>' . $fallback_block_name;

    if ($ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_AJAX) {
      $ajax_bar = $this->labels->bar ($_SERVER ['REQUEST_URI'], __('Ajax request url', 'ad-inserter'), '', __('IN THE LOOP', 'ad-inserter') . ': ' . (in_the_loop() ? __('YES', 'ad-inserter') : __('NO', 'ad-inserter')), 'in_the_loop ()');
    } else $ajax_bar = '';

    $this->additional_code_before =
      $this->labels->block_start () .
      $this->labels->bar ($block_name, '', '<kbd class="ai-debug-name ai-main">' . $title . '</kbd>', $counters, $right_title) .
      $ajax_bar .
      $this->additional_code_before;

    $this->additional_code_after .= $this->labels->block_end ();
  }

  public function ai_getProcessedCode ($hide_debug_label = false, $force_close_button = false) {
    global $ai_wp_data, $ad_inserter_globals, $block_object;

    $code = $this->ai_getCode ();

    // Clear the codes for cases when the code block is called more than once
    $this->additional_code_before = '';
    $this->additional_code_after = '';
    $this->w3tc_code = '';

    // Code for ad label, close button
    $additional_code = '';

    $additional_code .= $this->ai_getAdLabel ();

    $alignment_type = $this->get_alignment_type ();
    if ($force_close_button || ($this->get_close_button () != AI_CLOSE_NONE && !$ai_wp_data [AI_WP_AMP_PAGE] && $alignment_type != AI_ALIGNMENT_NO_WRAPPING)) {
      switch ($this->get_close_button ()) {
        case AI_CLOSE_TOP_RIGHT:
          $button_class = 'ai-close-button';
          break;
        case AI_CLOSE_TOP_LEFT:
          $button_class = 'ai-close-button ai-close-left';
          break;
        case AI_CLOSE_BOTTOM_RIGHT:
          $button_class = 'ai-close-button ai-close-bottom';
          break;
        case AI_CLOSE_BOTTOM_LEFT:
          $button_class = 'ai-close-button ai-close-bottom ai-close-left';
          break;
        default:
          $button_class = 'ai-close-button';
          break;
      }

      $additional_code .= "<span class='$button_class'></span>\n";
    }

    unset ($ai_wp_data [AI_SHORTCODES]['rotate']);
    unset ($ai_wp_data [AI_SHORTCODES]['count']);

    $processed_code = $this->replace_ai_tags (do_shortcode ($code));

    if (strpos ($processed_code, AD_COUNT_SEPARATOR) !== false) {
      $ads = explode (AD_COUNT_SEPARATOR, $processed_code);

      if (isset ($ad_inserter_globals [AI_BLOCK_COUNTER_NAME . $this->number])) {
        $counter_for_filter = $ad_inserter_globals [AI_BLOCK_COUNTER_NAME . $this->number];

        if ($counter_for_filter != 0 && $counter_for_filter <= count ($ads)) {
          if (isset ($ai_wp_data [AI_SHORTCODES]['count'][$counter_for_filter - 1]['count'])) {
            if (strtolower ($ai_wp_data [AI_SHORTCODES]['count'][$counter_for_filter - 1]['count']) == 'shuffle') {
              $ai_wp_data [AI_COUNT][$this->number] = $ads;
              shuffle ($ai_wp_data [AI_COUNT][$this->number]);
            }
          }

          if (isset ($ai_wp_data [AI_COUNT][$this->number])) {
            $ads = $ai_wp_data [AI_COUNT][$this->number];
          }

          $processed_code = $ads [$counter_for_filter - 1];
        } else $processed_code = '';
      } else $processed_code = $ads [rand (0, count ($ads) - 1)];
    }

    $dynamic_blocks = get_dynamic_blocks ();
    if ($ai_wp_data [AI_FORCE_SERVERSIDE_CODE] || ($dynamic_blocks == AI_DYNAMIC_BLOCKS_SERVER_SIDE_W3TC && defined ('AI_NO_W3TC'))) $dynamic_blocks = AI_DYNAMIC_BLOCKS_SERVER_SIDE;

    if (strpos ($processed_code, AD_ROTATE_SEPARATOR) !== false) {
      $ads = explode (AD_ROTATE_SEPARATOR, $processed_code);

      if (!isset ($ai_wp_data [AI_SHORTCODES]['rotate'])) {
        // using old separator |rotate|
        $ai_wp_data [AI_SHORTCODES]['rotate'] = array ();
        foreach ($ads as $ad) {
          $ai_wp_data [AI_SHORTCODES]['rotate'] []= array ();
        }
      }

      if (trim ($ads [0]) == '') {
        unset ($ads [0]);
        $ads = array_values ($ads);
      } else array_unshift ($ai_wp_data [AI_SHORTCODES]['rotate'],  array ('name' => ''));

      $shares = false;
      $times = false;
      $version_names = array ();
      $version_shares = array ();
      $version_times = array ();
      $version_insert = array ();

      foreach ($ai_wp_data [AI_SHORTCODES]['rotate'] as $index => $option) {
        $version_names []= isset ($option ['name']) && trim ($option ['name']) != '' ? $option ['name'] : chr (ord ('A') + $index);

        $option_share = isset ($option ['share']) && is_numeric ($option ['share']);
        if ($option_share) $shares = true;
        $version_shares []= $option_share ? intval ($option ['share']) : - 1;

        $option_time = isset ($option ['time']) && is_numeric ($option ['time']);
        if ($option_time) $times = true;
        $version_times []= $option_time ? intval ($option ['time']) : - 1;

        $version_insert []= isset ($option ['insert']);
      }

      if ($shares) {
        $total_share = 0;
        $no_share = 0;

        foreach ($version_shares as $index => $share) {
          if ($share < 0) $no_share ++; else $total_share += $share;
        }

        if ($total_share > 100 || $no_share == 0) {
          $scale = $total_share / 100;
        } else $scale = 1;

        foreach ($version_shares as $index => $share) {
          // Disable options with share 0
          if ($share == 0) $version_shares [$index] = - 1; else
            if ($share < 0) $version_shares [$index] = (100 - $total_share / $scale) / $no_share; else
              $version_shares [$index] = $share / $scale;
        }

        $thresholds = array ();
        $total_share = 0;
        foreach ($version_shares as $index => $share) {
          if ($share >= 0) {
            $total_share += $share;
            $thresholds [] = round ($total_share);
          } else $thresholds [] = - 1;
        }
      }

      if ($times) {
        if ($dynamic_blocks != AI_DYNAMIC_BLOCKS_CLIENT_SIDE_SHOW && $dynamic_blocks != AI_DYNAMIC_BLOCKS_CLIENT_SIDE_INSERT) $dynamic_blocks = AI_DYNAMIC_BLOCKS_CLIENT_SIDE_SHOW;
        $ai_wp_data [AI_CLIENT_SIDE_ROTATION] = true;
      }

      $temp_dynamic_blocks = $dynamic_blocks;
      if ($ai_wp_data [AI_FORCE_SERVERSIDE_CODE] || ($temp_dynamic_blocks == AI_DYNAMIC_BLOCKS_CLIENT_SIDE_SHOW || $temp_dynamic_blocks == AI_DYNAMIC_BLOCKS_CLIENT_SIDE_INSERT) && $ai_wp_data [AI_WP_AMP_PAGE]) $temp_dynamic_blocks = AI_DYNAMIC_BLOCKS_SERVER_SIDE;

      switch ($temp_dynamic_blocks) {
        case AI_DYNAMIC_BLOCKS_SERVER_SIDE:
          if ($shares) {
            $random_threshold = mt_rand (0, 100);
            foreach ($thresholds as $index => $threshold) {
              $this->code_version = $index + 1;
              if ($random_threshold <= $threshold) break;
            }
          } else $this->code_version = mt_rand (1, count ($ads));

          $processed_code = $additional_code . trim ($ads [$this->code_version - 1]);
          $this->version_name = $version_names [$this->code_version - 1];
          break;

        case AI_DYNAMIC_BLOCKS_CLIENT_SIDE_SHOW:
        case AI_DYNAMIC_BLOCKS_CLIENT_SIDE_INSERT:
          $this->code_version = '""';

          $rotation_data = '';
          if ($shares) {
            $rotation_data = " data-shares='".base64_encode (json_encode ($thresholds))."'";
          }

          // Additional class to identify rotation code block in case of timed rotation
          $rotation_class = '';
          if ($times) {
            $rotation_class = ' ai-'.$this->number;
            $rotation_data .= " data-info='".base64_encode ('['.$this->number.','.count ($ads).']')."'";
          }

          $processed_code = '';
          if ($times && !isset ($ai_wp_data [AI_CLIENT_SIDE_CSS])) {
            $processed_code = "\n<style type='text/css'>\n" . ai_get_client_side_styles () . "</style>";
          }

          if (defined ('AI_NORMAL_HEADER_STYLES') && AI_NORMAL_HEADER_STYLES && !get_inline_styles ()) {
            $processed_code .= "\n<div class='ai-rotate{$rotation_class}'".$rotation_data.">\n" . $additional_code;
          } else
          $processed_code .= "\n<div class='ai-rotate{$rotation_class}'".$rotation_data." style='position: relative;'>\n" . $additional_code;

          foreach ($ads as $index => $ad) {

            // If AMP separator is present use only code for normal pages
            if (strpos ($ad, AD_AMP_SEPARATOR) !== false) {
              $codes = explode (AD_AMP_SEPARATOR, $ad);
              $ad = trim ($codes [0]);
            }

            $version_name_data = " data-name='".base64_encode ($version_names [$index])."'";
            $version_time_data = $version_times [$index] >= 0 ? " data-time='".base64_encode ($version_times [$index])."'" : '';

            switch ($temp_dynamic_blocks) {
              case AI_DYNAMIC_BLOCKS_CLIENT_SIDE_SHOW:
                switch ($index) {
                  case 0:
                    if (defined ('AI_NORMAL_HEADER_STYLES') && AI_NORMAL_HEADER_STYLES && !get_inline_styles ()) {
                      $processed_code .= "<div class='ai-rotate-option ai-rotate-hidden'".$version_name_data.$version_time_data.">\n".trim ($ad, "\n\r")."</div>\n";
                    } else
                    $processed_code .= "<div class='ai-rotate-option' style='visibility: hidden;'".$version_name_data.$version_time_data.">\n".trim ($ad, "\n\r")."</div>\n";
                    break;
                  default:
                    if (defined ('AI_NORMAL_HEADER_STYLES') && AI_NORMAL_HEADER_STYLES && !get_inline_styles ()) {
                      $processed_code .= "<div class='ai-rotate-option ai-rotate-hidden ai-rotate-hidden-2'".$version_name_data.$version_time_data.">\n".trim ($ad, "\n\r")."</div>\n";
                    } else
                    $processed_code .= "<div class='ai-rotate-option' style='visibility: hidden; position: absolute; top: 0; left: 0; width: 100%; height: 100%;'".$version_name_data.$version_time_data.">\n".trim ($ad, "\n\r")."</div>\n";
                    break;
                }
                break;
              case AI_DYNAMIC_BLOCKS_CLIENT_SIDE_INSERT:
                $version_code_data = " data-code='".base64_encode ($ad)."'";
                $processed_code .= "<div class='ai-rotate-option'".$version_name_data.$version_time_data.$version_code_data.">\n</div>\n";
                break;
            }

          }
          $processed_code .= "</div>\n";
          break;

        case AI_DYNAMIC_BLOCKS_SERVER_SIDE_W3TC:
          if ($shares) {
            $ad_index_code = ' $ai_random_threshold = mt_rand (0, 100); $ai_thresholds = unserialize (\''.
                               serialize ($thresholds).'\'); foreach ($ai_thresholds as $ai_option_index => $ai_threshold) {$ai_index = $ai_option_index + 1; if ($ai_random_threshold <= $ai_threshold) break;}';
          } else $ad_index_code = ' $ai_index = mt_rand (1, count ($ai_code));';

          $this->w3tc_code = '$ai_code = unserialize (base64_decode (\''.base64_encode (serialize ($ads)).'\'));'.$ad_index_code.' $ai_code = base64_decode (\''.base64_encode ($additional_code).'\') . $ai_code [$ai_index - 1]; $ai_enabled = true;';

          $processed_code = '<!-- mfunc '.W3TC_DYNAMIC_SECURITY.' -->';
          $processed_code .= $this->w3tc_code.' echo $ai_code;';
          $processed_code .= '<!-- /mfunc '.W3TC_DYNAMIC_SECURITY.' -->';
          break;
      }
    } else $processed_code = $additional_code . $processed_code;

    $temp_dynamic_blocks = $dynamic_blocks;
        if ($ai_wp_data [AI_FORCE_SERVERSIDE_CODE])                                                 $temp_dynamic_blocks = AI_DYNAMIC_BLOCKS_SERVER_SIDE;
    elseif ($temp_dynamic_blocks == AI_DYNAMIC_BLOCKS_SERVER_SIDE_W3TC && $this->w3tc_code == '')   $temp_dynamic_blocks = AI_DYNAMIC_BLOCKS_SERVER_SIDE;
    elseif ($temp_dynamic_blocks == AI_DYNAMIC_BLOCKS_CLIENT_SIDE_SHOW)                             $temp_dynamic_blocks = AI_DYNAMIC_BLOCKS_SERVER_SIDE;
    elseif ($temp_dynamic_blocks == AI_DYNAMIC_BLOCKS_CLIENT_SIDE_INSERT)                           $temp_dynamic_blocks = AI_DYNAMIC_BLOCKS_SERVER_SIDE;

    switch ($temp_dynamic_blocks) {
      case AI_DYNAMIC_BLOCKS_SERVER_SIDE:
        if (strpos ($processed_code, AD_AMP_SEPARATOR) !== false) {
          $codes = explode (AD_AMP_SEPARATOR, $processed_code);
          $code_index = $ai_wp_data [AI_WP_AMP_PAGE] ? 1 : 0;
          $this->labels->class = $code_index ? 'ai-debug-amp' : 'ai-debug-default';
          if ($code_index == 1) {
            ai_log ('BLOCK ' . $this->number . ' AMP CODE USED');
          }
          $processed_code = trim ($codes [$code_index]);
        } else {
            // AMP page but No AMP separator - don't insert code unless enabled
            if ($ai_wp_data [AI_WP_AMP_PAGE]) {
              if (!$this->get_enable_amp ()) {
                $processed_code = '';
                $hide_debug_label = true;
                ai_log ('BLOCK ' . $this->number . ' NOT ENABLED FOR AMP PAGES - EMPTY CODE');
              }
            }
          }
        break;
      case AI_DYNAMIC_BLOCKS_SERVER_SIDE_W3TC:
        $this->w3tc_code .= '$ai_amp_separator = \'' . AD_AMP_SEPARATOR . '\'; $ai_amp_page = ' . ($ai_wp_data [AI_WP_AMP_PAGE] ? 'true' : 'false') . '; $ai_amp_enabled = ' . $this->get_enable_amp () . ';';

        // Fix to prevent converting && into &amp;&amp; on AMP pages
//        $this->w3tc_code .= 'if (strpos ($ai_code, $ai_amp_separator) !== false) {$codes = explode ($ai_amp_separator, $ai_code); $ai_code = trim ($codes [$ai_amp_page ? 1 : 0]); } else {if ($ai_amp_page && !$ai_amp_enabled) $ai_code = \'\';} $ai_enabled = true;';
        $this->w3tc_code .= 'if (strpos ($ai_code, $ai_amp_separator) !== false) {$codes = explode ($ai_amp_separator, $ai_code); $ai_code = trim ($codes [$ai_amp_page ? 1 : 0]); } else {if ($ai_amp_page + !$ai_amp_enabled == 2) $ai_code = \'\';} $ai_enabled = true;';

        // Lazy loading code for W3TC cases with ROTATE separator
        if ($this->get_lazy_loading () && !$ai_wp_data [AI_WP_AMP_PAGE]) {
          $this->w3tc_code .= $this->w3tc_code.'$ai_lazy_code = base64_encode ($ai_code); $ai_wrapper_class = \''.base64_encode (get_block_class_name ()).'\'; $ai_code = \'<div class="ai-lazy" data-code="\'.$ai_lazy_code.\'" data-class="\'.$ai_wrapper_class.\'"></div>\'."\n";';
        }

        $processed_code = '<!-- mfunc '.W3TC_DYNAMIC_SECURITY.' -->';
        $processed_code .= $this->w3tc_code.' echo $ai_code;';
        $processed_code .= '<!-- /mfunc '.W3TC_DYNAMIC_SECURITY.' -->';
        break;
    }

    // Lazy loading code for all cases except W3TC with ROTATE separator
    if ($this->get_lazy_loading () && !$ai_wp_data [AI_WP_AMP_PAGE] && $this->w3tc_code == '') {
      $lazy_code = base64_encode ($processed_code);
      $wrapper_class = base64_encode (get_block_class_name ());
      $processed_code = '<div class="ai-lazy" data-code="'.$lazy_code.'" data-class="'.$wrapper_class.'"></div>'."\n";
    }

    $temp_dynamic_blocks = $dynamic_blocks;
    if ($ai_wp_data [AI_FORCE_SERVERSIDE_CODE] || ($temp_dynamic_blocks == AI_DYNAMIC_BLOCKS_CLIENT_SIDE_SHOW || $temp_dynamic_blocks == AI_DYNAMIC_BLOCKS_CLIENT_SIDE_INSERT) && $ai_wp_data [AI_WP_AMP_PAGE]) $temp_dynamic_blocks = AI_DYNAMIC_BLOCKS_SERVER_SIDE;

    if ($temp_dynamic_blocks != AI_DYNAMIC_BLOCKS_SERVER_SIDE) {
      $url_parameters_raw = trim (str_replace (' ', '', $this->get_url_parameter_list ()));
      $url_parameters = base64_encode ($url_parameters_raw);
      $url_parameter_list_type = $this->get_url_parameter_list_type ();

      $referers_raw = trim (str_replace (' ', '', strtolower ($this->get_ad_domain_list ())));
      $referers = base64_encode ($referers_raw);
      $referer_list_type = $this->get_ad_domain_list_type ();

      if ($this->client_side_cookie_check && ($url_parameters != '' || $url_parameter_list_type == AI_WHITE_LIST) || $referers != '' || $referer_list_type == AI_WHITE_LIST) {
        switch ($dynamic_blocks) {
          case AI_DYNAMIC_BLOCKS_CLIENT_SIDE_SHOW:
          case AI_DYNAMIC_BLOCKS_CLIENT_SIDE_INSERT:
            if ($url_parameter_list_type == AI_BLACK_LIST) $url_parameter_list_type = 'B'; else $url_parameter_list_type = 'W';
            if ($referer_list_type       == AI_BLACK_LIST) $referer_list_type       = 'B'; else $referer_list_type       = 'W';

            if ($this->client_side_cookie_check && ($url_parameters != '' || $url_parameter_list_type == 'W')) $url_parameter_attributes  = "parameter-list='$url_parameters' parameter-list-type='$url_parameter_list_type'"; else $url_parameter_attributes = '';
            if (                                    $referers       != '' || $referer_list_type       == 'W')  $referer_attributes        = "referer-list='$referers' referer-list-type='$referer_list_type'";                 else $referer_attributes       = '';

            $this->client_side_list_detection = true;

            switch ($dynamic_blocks) {
              case AI_DYNAMIC_BLOCKS_CLIENT_SIDE_SHOW:
                if (defined ('AI_NORMAL_HEADER_STYLES') && AI_NORMAL_HEADER_STYLES && !get_inline_styles ()) {
                  $processed_code = "\n<div class='ai-dynamic ai-list-data' $referer_attributes $url_parameter_attributes>$processed_code</div>\n";
                } else $processed_code = "\n<div class='ai-dynamic ai-list-data' $referer_attributes $url_parameter_attributes style='visibility: hidden; position: absolute; width: 100%; height: 100%; z-index: -9999;'>$processed_code</div>\n";
                break;
              case AI_DYNAMIC_BLOCKS_CLIENT_SIDE_INSERT:
                $code_data = "data-code='".base64_encode ($processed_code)."'";

                $processed_code = "\n<div class='ai-dynamic ai-list-data' $referer_attributes $url_parameter_attributes $code_data></div>\n";
                break;
            }

            if (($ai_wp_data [AI_WP_DEBUGGING] & AI_DEBUG_BLOCKS) != 0) {
                                                                                                                                                           // translators: %s: list parameters and type
              if ($this->client_side_cookie_check && ($url_parameters != '' || $url_parameter_list_type == 'W')) $url_parameter_attributes_dbg  = sprintf (__ ("parameters='%s' type='%s'", 'ad-inserter'), $url_parameters_raw, $url_parameter_list_type); else $url_parameter_attributes_dbg = '';
                                                                                                                                                           // translators: %s: list parameters and type
              if (                                    $referers       != '' || $referer_list_type       == 'W')  $referer_attributes_dbg        = sprintf (__ ("referers='%s' type='%s'", 'ad-inserter'), $referers_raw, $referer_list_type); else $referer_attributes_dbg       = '';

              $debug_list = new ai_block_labels ('ai-debug-lists');
              $processed_code = $debug_list->bar ($url_parameter_attributes_dbg . ' ' . $referer_attributes_dbg, 'B = ' . AI_TEXT_BLACK_LIST .', W = ' . AI_TEXT_WHITE_LIST, '<kbd class="ai-debug-name ai-list-status"></kbd>', '<kbd class="ai-debug-name ai-list-info"></kbd>') . $processed_code;
            }

            break;
          case AI_DYNAMIC_BLOCKS_SERVER_SIDE_W3TC:
            if ($this->w3tc_code == '') $this->w3tc_code = '$ai_code = base64_decode (\''.base64_encode ($processed_code).'\'); $ai_index = 0; $ai_enabled = true;';

            if ($referers != '') {
              $this->w3tc_code .= ' if ($ai_enabled) $ai_enabled = check_check_referer_list (base64_decode (\''.$referers.'\'), '.($referer_list_type == AI_WHITE_LIST ? 'true':'false').');';
            } elseif ($referer_list_type == AI_WHITE_LIST) $this->w3tc_code .= ' $ai_enabled = false;';

            if ($this->client_side_cookie_check) {
              if ($url_parameters != '') {
                $this->w3tc_code .= ' if ($ai_enabled) $ai_enabled = check_cookie_list (base64_decode (\''.$url_parameters.'\'), '.($url_parameter_list_type == AI_WHITE_LIST ? 'true':'false').');';
              } elseif ($url_parameter_list_type == AI_WHITE_LIST) $this->w3tc_code .= ' $ai_enabled = false;';
            }

            $processed_code = '<!-- mfunc '.W3TC_DYNAMIC_SECURITY.' -->';
            $processed_code .= $this->w3tc_code.' if ($ai_enabled) echo $ai_code;';
            $processed_code .= '<!-- /mfunc '.W3TC_DYNAMIC_SECURITY.' -->';
            break;
        }
      }

      $countries = trim (str_replace (' ', '', strtoupper ($this->get_ad_country_list (true))));
      $country_list_type = $this->get_ad_country_list_type ();

      $ip_addresses = trim (str_replace (' ', '', strtolower ($this->get_ad_ip_address_list ())));
      $ip_address_list_type = $this->get_ad_ip_address_list_type ();

      if ($countries != '' || $country_list_type == AI_WHITE_LIST || $ip_addresses != '' || $ip_address_list_type == AI_WHITE_LIST) {
        switch ($dynamic_blocks) {
          case AI_DYNAMIC_BLOCKS_CLIENT_SIDE_SHOW:
          case AI_DYNAMIC_BLOCKS_CLIENT_SIDE_INSERT:
            if ($country_list_type    == AI_BLACK_LIST) $country_list_type    = 'B'; else $country_list_type = 'W';
            if ($ip_address_list_type == AI_BLACK_LIST) $ip_address_list_type = 'B'; else $ip_address_list_type = 'W';

            if ($countries    != '' || $country_list_type    == 'W')  $country_attributes     = "countries='$countries' country-list='$country_list_type'";             else $country_attributes    = '';
            if ($ip_addresses != '' || $ip_address_list_type == 'W')  $ip_address_attributes  = "ip-addresses='$ip_addresses' ip-address-list='$ip_address_list_type'"; else $ip_address_attributes = '';

            $this->client_side_list_detection = true;

            switch ($dynamic_blocks) {
              case AI_DYNAMIC_BLOCKS_CLIENT_SIDE_SHOW:
                if (defined ('AI_NORMAL_HEADER_STYLES') && AI_NORMAL_HEADER_STYLES && !get_inline_styles ()) {
                  $processed_code = "\n<div class='ai-dynamic ai-ip-data' $ip_address_attributes $country_attributes>$processed_code</div>\n";
                } else $processed_code = "\n<div class='ai-dynamic ai-ip-data' $ip_address_attributes $country_attributes style='visibility: hidden; position: absolute; width: 100%; height: 100%; z-index: -9999;'>$processed_code</div>\n";
                break;
              case AI_DYNAMIC_BLOCKS_CLIENT_SIDE_INSERT:
                $code_data = "data-code='".base64_encode ($processed_code)."'";

                $processed_code = "\n<div class='ai-dynamic ai-ip-data' $ip_address_attributes $country_attributes $code_data></div>\n";
                break;
            }

            if (($ai_wp_data [AI_WP_DEBUGGING] & AI_DEBUG_BLOCKS) != 0) {

                                                                                                               // translators: %s: list parameters and type
              if ($countries    != '' || $country_list_type    == 'W')  $country_attributes_dbg     = sprintf (__ ("countries='%s' type='%s'", 'ad-inserter'), $countries, $country_list_type);          else $country_attributes_dbg    = '';
                                                                                                               // translators: %s: list parameters and type
              if ($ip_addresses != '' || $ip_address_list_type == 'W')  $ip_address_attributes_dbg  = sprintf (__ ("ip addresses='%s' type='%s'", 'ad-inserter'), $ip_addresses, $ip_address_list_type); else $ip_address_attributes_dbg = '';

              $debug_ip = new ai_block_labels ('ai-debug-lists');
              $processed_code = $debug_ip->bar ($country_attributes_dbg . ' ' . $ip_address_attributes_dbg, 'B = ' . AI_TEXT_BLACK_LIST .', W = ' . AI_TEXT_WHITE_LIST, '<kbd class="ai-debug-name ai-ip-status"></kbd>', '<kbd class="ai-debug-name ai-ip-country"></kbd>') . $processed_code;
            }

            break;
          case AI_DYNAMIC_BLOCKS_SERVER_SIDE_W3TC:
            if ($this->w3tc_code == '') $this->w3tc_code = '$ai_code = base64_decode (\''.base64_encode ($processed_code).'\'); $ai_index = 0; $ai_enabled = true;';

            $this->w3tc_code .= ' require_once \''.AD_INSERTER_PLUGIN_DIR.'includes/geo/Ip2Country.php\';';

            if ($ip_addresses != '') {
              $this->w3tc_code .= ' if ($ai_enabled) $ai_enabled = check_ip_address_list (base64_decode (\''.base64_encode ($ip_addresses).'\'), '.($ip_address_list_type == AI_WHITE_LIST ? 'true':'false').');';
            } elseif ($ip_address_list_type == AI_WHITE_LIST) $this->w3tc_code .= ' $ai_enabled = false;';

            if ($countries != '') {
              $this->w3tc_code .= ' if ($ai_enabled) $ai_enabled = check_country_list (base64_decode (\''.base64_encode ($countries).'\'), '.($country_list_type == AI_WHITE_LIST ? 'true':'false').');';
            } elseif ($country_list_type == AI_WHITE_LIST) $this->w3tc_code .= ' $ai_enabled = false;';

            $processed_code = '<!-- mfunc '.W3TC_DYNAMIC_SECURITY.' -->';
            $processed_code .= $this->w3tc_code.' if ($ai_enabled) echo $ai_code;';
            $processed_code .= '<!-- /mfunc '.W3TC_DYNAMIC_SECURITY.' -->';
            break;
        }
      }
    }

    if (defined ('AI_DEBUGGING_DEMO') && !$this->demo_debugging) {
      $hide_debug_label = true;
    }

    if (($ai_wp_data [AI_WP_DEBUGGING] & AI_DEBUG_BLOCKS) != 0 /*&& !$hide_debug_label*/) {
      $processed_code =  "<div class='ai-code'>\n" . $processed_code ."\n</div>\n";
    }

    if (function_exists ('ai_adb_block_actions')) ai_adb_block_actions ($this, $hide_debug_label);

    if (($ai_wp_data [AI_WP_DEBUGGING] & AI_DEBUG_BLOCKS) != 0 && !$hide_debug_label) {
      $this->ai_generateDebugLabel ();

//      $title = '';
//      $fallback_block_name = '';
//      $ajax = '';

//      if ($ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_AJAX) {
//        $this->labels->class = 'ai-debug-ajax';
//        $ajax = __('AJAX REQUEST', 'ad-inserter');
//      }

//      if ($this->fallback != 0) {
//        $this->labels->class = 'ai-debug-fallback';
//        $fallback_block = $block_object [$this->fallback];
//        $fallback_block_name = ' &nbsp;&#8678;&nbsp; '. $this->fallback . ' &nbsp; ' . $fallback_block->get_ad_name ();
//      }

//      $counters = $this->ai_get_counters ($title);
//      $version_name = $this->version_name == '' ? '' : ' - ' . $this->version_name;
//      $block_name = $this->number . ' &nbsp; ' . $this->get_ad_name () . '<kbd data-separator=" - " class="ai-option-name">' . $version_name . '</kbd>' . $fallback_block_name;

//      if ($ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_AJAX) {
//        $ajax_bar = $this->labels->bar ($_SERVER ['REQUEST_URI'], __('Ajax request url', 'ad-inserter'), '', __('IN THE LOOP', 'ad-inserter') . ': ' . (in_the_loop() ? __('YES', 'ad-inserter') : __('NO', 'ad-inserter')), 'in_the_loop ()');
//      } else $ajax_bar = '';

//      $this->additional_code_before =
//        $this->labels->block_start () .
//        $this->labels->bar ($block_name, '', '<kbd class="ai-debug-name ai-main">' . $ajax . '</kbd>', $counters, $title) .
//        $ajax_bar .
//        $this->additional_code_before;

//      $this->additional_code_after .= $this->labels->block_end ();
    }

    return $this->additional_code_before . $processed_code . $this->additional_code_after;
  }

  public function get_code_for_insertion ($include_viewport_classes = true, $hidden_widgets = false, $code_only = false, $code_for_iframe = false) {
    global $ai_wp_data, $block_object;

    if ($this->get_disable_caching ()) $ai_wp_data [AI_DISABLE_CACHING] = true;

    $block_class_name = get_block_class_name ($this->needs_class);

    $block_class              = get_block_class ();
    $block_number_class       = get_block_number_class ();

    $alignment_class = $code_for_iframe ? '' : $this->get_alignment_class ();
    $alignment_style = $code_for_iframe ? '' : $this->get_alignment_style ();

    if ($this->get_client_side_action () == AI_CLIENT_SIDE_ACTION_INSERT) $include_viewport_classes = false;
    $viewport_classes = $include_viewport_classes ? trim ($this->get_viewport_classes ()) : "";

    if ($block_class_name != '' && ($block_class || $block_number_class) || $alignment_class != '' || $viewport_classes != '') {
      $classes = array ();
      if ($block_class_name != '' && ($block_class || $this->needs_class)) $classes []= $block_class_name;
      if ($alignment_class) $classes []= $alignment_class;
      if ($block_class_name != '' && ($block_number_class || $this->needs_class)) $classes []= $block_class_name . "-" . $this->number;
      if ($viewport_classes) $classes []= $viewport_classes;
    } else $classes = array ();

    $sticky_parameters = '';

    if (!$ai_wp_data [AI_WP_AMP_PAGE] && !$code_for_iframe) {
      $sticky_parameters = $this->sticky_parameters ($classes);
    }

    if ($this->get_iframe () && !$code_for_iframe) {
      // Clear the codes for cases when the code block is called more than once
      $this->additional_code_before = '';
      $this->additional_code_after = '';
      $this->w3tc_code = '';

      foreach ($classes as $index => $class_name) {
        if (trim ($class_name) == '') unset ($classes [$index]);
      }
      if (count ($classes) != 0) {
        $class = ' class="' . trim (implode (' ', $classes)) . '"';
      } else $class = '';

      $alignment_style = $this->get_alignment_style ();

      if (($ai_wp_data [AI_WP_DEBUGGING] & AI_DEBUG_BLOCKS) != 0) {
        $this->ai_generateDebugLabel ('ai-debug-iframe', 'IFRAME');

        $this->additional_code_before = '<div' . $class . ' style="' . $alignment_style. '"' . $sticky_parameters .'>' . $this->additional_code_before;
        $this->additional_code_after .= '</div>';

        $iframe = '<iframe style="width: ' . $this->get_iframe_width () . 'px; height: ' . $this->get_iframe_height () . 'px;" src="' . get_home_url (null, 'wp-admin/admin-ajax.php?action=ai_ajax&block=') . $this->number . '" frameborder="0" scrolling="no"></iframe>';
      } else  {
          // No alignment classes to be able to use iframe code anywhere
          if ($this->is_sticky ()) {
            $this->additional_code_before = '<div' . $class . ' style="' . $alignment_style . '"' . $sticky_parameters .'>' . $this->additional_code_before;
            $this->additional_code_after .= '</div>';

            $iframe = '<iframe style="width: ' . $this->get_iframe_width () . 'px; height: ' . $this->get_iframe_height () . 'px;" src="' . get_home_url (null, 'wp-admin/admin-ajax.php?action=ai_ajax&block=') . $this->number . '" frameborder="0" scrolling="no"></iframe>';
          } else {
              $iframe = '<iframe style="width: ' . $this->get_iframe_width () . 'px; height: ' . $this->get_iframe_height () . 'px; ' . $alignment_style . '"' . ' src="' . get_home_url (null, 'wp-admin/admin-ajax.php?action=ai_ajax&block=') . $this->number . '" frameborder="0" scrolling="no"></iframe>';
            }
        }

      return $this->additional_code_before . $iframe . $this->additional_code_after;
    }

    if ($this->get_alignment_type() == AI_ALIGNMENT_NO_WRAPPING || $code_only) return $this->ai_getProcessedCode ($code_for_iframe);

//    $alignment_class = $code_for_iframe ? '' : $this->get_alignment_class ();
//    $alignment_style = $code_for_iframe ? '' : $this->get_alignment_style ();

    $hidden_viewports = '';
    if (($ai_wp_data [AI_WP_DEBUGGING] & AI_DEBUG_BLOCKS) != 0 && $this->get_detection_client_side() && $this->get_client_side_action () == AI_CLIENT_SIDE_ACTION_SHOW) {

      $processed_code = $this->ai_getProcessedCode (true);
      $title = '';
      $counters = $this->ai_get_counters ($title);

      $visible_viewports = '';
      for ($viewport = 1; $viewport <= AD_INSERTER_VIEWPORTS; $viewport ++) {
        $viewport_name = get_viewport_name ($viewport);
        if ($viewport_name != '') {
          $version_name = $this->version_name == '' ? '' : ' - ' . $this->version_name;
          $viewport_class_name = 'ai-viewport-' . $viewport;

          if ($this->get_detection_viewport ($viewport)) {
            $visible_viewports .=
              '<section class="' . $viewport_class_name .'">' .
              $this->labels->bar (
                $this->number . ' ' . $this->get_ad_name () . '<kbd data-separator=" - " class="ai-option-name">' . $version_name . '</kbd>', '',
                $viewport_name . ' <kbd class="ai-debug-name ai-main"></kbd>',
                $counters, $title) .
             '</section>';
          } else {
              if (!$ai_wp_data [AI_WP_AMP_PAGE]) {
                if (defined ('AI_NORMAL_HEADER_STYLES') && AI_NORMAL_HEADER_STYLES && !get_inline_styles ()) {
                  $hidden_wrapper_start = '<section class="' . $viewport_class_name .' ai-debug-block ai-debug-viewport-invisible '.$alignment_class.'">';
                } else {
                    $hidden_wrapper_start = '<section class="' . $viewport_class_name .' ai-debug-block ai-debug-viewport-invisible" style="' . $alignment_style . '">';
                  }

                $hidden_viewports .=
                  $hidden_wrapper_start .
                  $this->labels->bar_hidden_viewport (
                    $this->number . ' ' . $this->get_ad_name () . '<kbd data-separator=" - " class="ai-option-name">' . $version_name . '</kbd>', '',
                    $viewport_name . ' <kbd class="ai-debug-name ai-main"></kbd>',
                    $counters, $title) .
                    $this->labels->message (($hidden_widgets ? __('WIDGET', 'ad-inserter') : __('BLOCK', 'ad-inserter')).' '._x('INSERTED BUT NOT VISIBLE', 'block or widget', 'ad-inserter')) .
                  '</section>';
              }
            }
        }
      }

      $viewport_header_before = "<div class='ai-debug-block " . $this->labels->class . "'>".$visible_viewports;
      $viewport_header_after  = '</div>';

      $this->additional_code_before  = $viewport_header_before . $this->additional_code_before;
      $this->additional_code_after  .= $viewport_header_after;

      $code = $viewport_header_before . $processed_code . $viewport_header_after;

    } else $code = $this->ai_getProcessedCode ($code_for_iframe);

    // Prevent empty wrapping div on AMP pages
    if ($ai_wp_data [AI_WP_AMP_PAGE] && $code == '') return '';

//    $block_class_name = get_block_class_name ($this->needs_class);

//    $block_class              = get_block_class ();
//    $block_number_class       = get_block_number_class ();

//    if ($this->get_client_side_action () == AI_CLIENT_SIDE_ACTION_INSERT) $include_viewport_classes = false;
//    $viewport_classes = $include_viewport_classes ? trim ($this->get_viewport_classes ()) : "";

//    print_r ($classes);

//    if ($block_class_name != '' && ($block_class || $block_number_class) || $alignment_class != '' || $viewport_classes != '') {
//      $classes = array ();
//      if ($block_class_name != '' && ($block_class || $this->needs_class)) $classes []= $block_class_name;
//      if ($alignment_class) $classes []= $alignment_class;
//      if ($block_class_name != '' && ($block_number_class || $this->needs_class)) $classes []= $block_class_name . "-" . $this->number;
//      if ($viewport_classes) $classes []= $viewport_classes;
//    } else $classes = array ();

//    print_r ($classes);

    if ($hidden_widgets) return $hidden_viewports; else {
      if ($this->client_side_list_detection && !$ai_wp_data [AI_WP_AMP_PAGE]) {
        if ($this->is_sticky ()) {
          $additional_block_style = 'visibility: hidden; ';
        } else {
            $additional_block_style = 'visibility: hidden; position: absolute; ';
            $classes [] = 'ai-remove-position';
          }

        // Needed to locate wrapping div
        $classes [] = 'ai-list-block';
       } else {
           $additional_block_style = '';
         }

//      $sticky_parameters = '';

//      if (!$ai_wp_data [AI_WP_AMP_PAGE]) {
//        $sticky_parameters = $this->sticky_parameters ($classes);
//      }

      if ($this->get_close_button () && !$ai_wp_data [AI_WP_AMP_PAGE]) {
        $classes [] = 'ai-close';
      }

      foreach ($classes as $index => $class_name) {
        if (trim ($class_name) == '') unset ($classes [$index]);
      }
      if (count ($classes) != 0) {
        $class = " class='" . trim (implode (' ', $classes)) . "'";
      } else $class = "";

      $tracking_code_pre  = '';
      $tracking_code_data = '';
      $tracking_code_post = '';
      $tracking_code      = '';

      if ($this->fallback != 0) {
        if ($block_object [$this->fallback]->get_tracking ()) {
          $tracking_code_pre = " data-ai='";
          $tracking_code_data = "[{$this->fallback},{$this->code_version},\"{$block_object [$this->fallback]->get_ad_name ()}\",\"{$this->version_name}\"]";
          $tracking_code_post = "'";

          $tracking_code = $tracking_code_pre . base64_encode ($tracking_code_data) . $tracking_code_post;
        }
      } else {
          if ($this->get_tracking ()) {
            $tracking_code_pre = " data-ai='";
            $tracking_code_data = "[{$this->number},{$this->code_version},\"{$this->get_ad_name ()}\",\"{$this->version_name}\"]";
            $tracking_code_post = "'";

            $tracking_code = $tracking_code_pre . base64_encode ($tracking_code_data) . $tracking_code_post;
          }
        }

      if ($this->w3tc_code != '' && get_dynamic_blocks () == AI_DYNAMIC_BLOCKS_SERVER_SIDE_W3TC && !defined ('AI_NO_W3TC')) {
        if ($this->get_tracking ()) $tracking_code_data = '[#AI_DATA#]';

        if ($ai_wp_data [AI_WP_AMP_PAGE] || ($alignment_class != '' && defined ('AI_NORMAL_HEADER_STYLES') && AI_NORMAL_HEADER_STYLES && !get_inline_styles ())) {
          $wrapper_before = $hidden_viewports . "<div" . $class . $tracking_code_pre . $tracking_code_data . $tracking_code_post . $sticky_parameters . ">\n";
        } else {
            $wrapper_before = $hidden_viewports . "<div" . $class . $tracking_code_pre . $tracking_code_data . $tracking_code_post . $sticky_parameters . " style='" . $additional_block_style . $alignment_style . "'>\n";
          }


//          TO TEST
//        $wrapper_before = $hidden_viewports . "<div" . $class . $tracking_code_pre . $tracking_code_data . $tracking_code_post .

//        if ($ai_wp_data [AI_WP_AMP_PAGE] || ($alignment_class != '' && defined ('AI_NORMAL_HEADER_STYLES') && AI_NORMAL_HEADER_STYLES && !get_inline_styles ())) {
//          $wrapper_before .= " style='" . $additional_block_style . $alignment_style;

//        $wrapper_before .=  ">\n";



        $wrapper_after  = "</div>\n";

        $wrapper_before .= $this->additional_code_before;
        $wrapper_after = $this->additional_code_after . $wrapper_after;

        $this->w3tc_code .= ' $ai_code = str_replace (\'[#AI_DATA#]\', base64_encode ("[' . $this->number . ',$ai_index]"), base64_decode (\''.base64_encode ($wrapper_before).'\')) . $ai_code . base64_decode (\''.base64_encode ($wrapper_after).'\');';

        if ($this->w3tc_code2 != '' ) {
          $this->w3tc_code = $this->w3tc_code2 . ' $ai_code2 = $ai_enabled ? $ai_code : "";' . $this->w3tc_code . ' $ai_code = str_replace ("[#AI_CODE2#]", $ai_code2, $ai_code);';
        }

        $code = '<!-- mfunc '.W3TC_DYNAMIC_SECURITY.' -->';
        $code .= $this->w3tc_code .' if ($ai_enabled) echo $ai_code;';
        $code .= '<!-- /mfunc '.W3TC_DYNAMIC_SECURITY.' -->';
      } else {

          if ($ai_wp_data [AI_WP_AMP_PAGE] || ($alignment_class != '' && defined ('AI_NORMAL_HEADER_STYLES') && AI_NORMAL_HEADER_STYLES && !get_inline_styles ())) {
            $wrapper_before = $hidden_viewports . "<div" . $class . $tracking_code . $sticky_parameters . ">\n";
          } else {
              $wrapper_before = $hidden_viewports . "<div" . $class . $tracking_code . $sticky_parameters . " style='" . $additional_block_style . $alignment_style . "'>\n";
            }

//          TO TEST
//          $wrapper_before = $hidden_viewports . "<div" . $class . $tracking_code;
//          if ($ai_wp_data [AI_WP_AMP_PAGE] || ($alignment_class != '' && defined ('AI_NORMAL_HEADER_STYLES') && AI_NORMAL_HEADER_STYLES && !get_inline_styles ()))
//            $wrapper_before .= $hidden_viewports . "<div" . $class . $tracking_code . " style='" . $additional_block_style . $alignment_style . "'>\n";
//          $wrapper_before .= "'>\n";

          $wrapper_after  = "</div>\n";

          if ($this->w3tc_code2 != '' ) {
            $this->before_w3tc_code2 = $wrapper_before . $code . $wrapper_after;

            $code2 = '<!-- mfunc '.W3TC_DYNAMIC_SECURITY.' -->';
            $code2 .= $this->w3tc_code2 .' if ($ai_enabled) echo $ai_code;';
            $code2 .= '<!-- /mfunc '.W3TC_DYNAMIC_SECURITY.' -->';

            $code = str_replace ("[#AI_CODE2#]", $code2, $code);
          }

          $code = $wrapper_before . $code . $wrapper_after;
        }
    }

    return $code;
  }

  public function get_code_for_serverside_insertion ($include_viewport_classes = true, $hidden_widgets = false, $code_only = false) {
    global $ai_wp_data, $block_object;

    $html_element_insertion     = false;
    $viewports_insertion        = $this->get_detection_client_side() && $this->get_client_side_action () == AI_CLIENT_SIDE_ACTION_INSERT;
    $server_side_html_insertion = $this->get_html_element_insertion () == AI_HTML_INSERTION_SEREVR_SIDE;

    $insertion_name = '';
    switch ($this->get_automatic_insertion()) {
      case AI_AUTOMATIC_INSERTION_BEFORE_HTML_ELEMENT:
        $insertion = 'before';
        $insertion_name = __('BEFORE', 'ad-inserter');
        if ($server_side_html_insertion && !$viewports_insertion) return $this->get_code_for_insertion ($include_viewport_classes, $hidden_widgets, $code_only);
        $html_element_insertion = true;
        break;
      case AI_AUTOMATIC_INSERTION_INSIDE_HTML_ELEMENT:
        switch ($this->get_inside_element ()) {
          case AI_HTML_PREPEND_CONTENT:
            $insertion = 'prepend';
            $insertion_name = __('PREPEND CONTENT', 'ad-inserter');
            break;
          case AI_HTML_APPEND_CONTENT:
            $insertion = 'append';
            $insertion_name = __('APPEND CONTENT', 'ad-inserter');
            break;
          case AI_HTML_REPLACE_CONTENT:
            $insertion = 'replace-content';
            $insertion_name = __('REPLACE CONTENT', 'ad-inserter');
            break;
          case AI_HTML_REPLACE_ELEMENT:
            $insertion = 'replace-element';
            $insertion_name = __('REPLACE ELEMENT', 'ad-inserter');
            break;
          default:
            $insertion = '';
            break;
        }
        if ($server_side_html_insertion && !$viewports_insertion) return $this->get_code_for_insertion ($include_viewport_classes, $hidden_widgets, $code_only);
        $html_element_insertion = true;
        break;
      case AI_AUTOMATIC_INSERTION_AFTER_HTML_ELEMENT:
        $insertion = 'after';
        $insertion_name = __('AFTER', 'ad-inserter');
        if ($server_side_html_insertion && !$viewports_insertion) return $this->get_code_for_insertion ($include_viewport_classes, $hidden_widgets, $code_only);
        $html_element_insertion = true;
        break;
      default:
        $insertion = '';
        if (!$viewports_insertion) return $this->get_code_for_insertion ($include_viewport_classes, $hidden_widgets, $code_only);
        break;
    }

    if ($ai_wp_data [AI_WP_AMP_PAGE] || $ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_FEED || $ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_AJAX) return '';

    if ($viewports_insertion && !$html_element_insertion) {
      $block_code = base64_encode ($this->get_code_for_insertion ($include_viewport_classes, $hidden_widgets, $code_only));
      $selector = $this->get_viewport_names ();
      $viewport_classes = trim ($this->get_viewport_classes ());

      $serverside_insertion_code = "<div class='ai-viewports $viewport_classes' data-code='[#AI_CODE#]' data-block='{$this->number}'></div>\n";
      if (!get_disable_js_code ())
        $serverside_insertion_code .= "<script>var ai_script = document.getElementsByClassName ('ai-viewports'); ai_insert_viewport (ai_script [ai_script.length - 1]);</script>\n";
    }
    elseif ($viewports_insertion && $html_element_insertion) {
      $this->counters = '<span class="ai-selector-counter"></span>';
      $block_code = base64_encode ($this->get_code_for_insertion ($include_viewport_classes, $hidden_widgets, $code_only));
      $selector = $this->get_html_selector (true);
      $viewport_classes = trim ($this->get_viewport_classes ());

      $serverside_insertion_code = "<div class='ai-viewports $viewport_classes' data-insertion='$insertion' data-selector='$selector' data-code='[#AI_CODE#]' data-block='{$this->number}'></div>\n";
      if (!get_disable_js_code () && $this->get_html_element_insertion () == AI_HTML_INSERTION_CLIENT_SIDE)
        // Try to insert it immediately. If the code is server-side inserted before the HTML element, it will be client-side inserted after DOM ready (remaining .ai-viewports)
        $serverside_insertion_code .= "<script>var ai_script = document.getElementsByClassName ('ai-viewports'); ai_insert_viewport (ai_script [ai_script.length - 1]);</script>\n";
    }
    else { // only HTML element insertion
      $this->counters = '<span class="ai-selector-counter"></span>';
      $block_code = base64_encode ($this->get_code_for_insertion ($include_viewport_classes, $hidden_widgets, $code_only));
      $selector = $this->get_html_selector (true);

      $code_before = '';
      $code_after = '';

      if ($this->get_html_element_insertion () == AI_HTML_INSERTION_CLIENT_SIDE_DOM_READY) {

      $code_before = "var ai_insert_{$this->number} = function(){
    ";
      $code_after  = "\n  };
  if (document.readyState === 'complete' || (document.readyState !== 'loading' && !document.documentElement.doScroll)) ai_insert_{$this->number} (); else document.addEventListener ('DOMContentLoaded', ai_insert_{$this->number});";
      }

    if (get_disable_js_code ()) $serverside_insertion_code = ''; else
      $serverside_insertion_code = "<script>
  {$code_before}ai_insert ('$insertion', '$selector', b64d ('[#AI_CODE#]'));{$code_after}
</script>\n";
    }

    if (!get_disable_js_code () && $ai_wp_data [AI_WP_DEBUGGING] & AI_DEBUG_BLOCKS) {
      $title = '';
      $fallback_block_name = '';

      if ($this->fallback != 0) {
        $fallback_block = $block_object [$this->fallback];
        $fallback_block_name = ' &nbsp;&#8678;&nbsp; '. $this->fallback . ' &nbsp; ' . $fallback_block->get_ad_name ();
      }

      $counters = $this->ai_get_counters ($title);

      $version_name = $this->version_name == '' ? '' : ' - ' . $this->version_name;

      $tag = $viewports_insertion ? 'div' : _x('script', 'JavaScript', 'ad-inserter');
      $debug_script = new ai_block_labels ('ai-debug-script');
      $serverside_insertion_code = $debug_script->bar (" $tag " . __('for', 'ad-inserter') . " " . $this->number . ' &nbsp; ' . $this->get_ad_name () . $version_name . ' ' . $fallback_block_name, '', $insertion_name . ' ' . $selector, $counters, $title) . $serverside_insertion_code;
    }

    if ($this->w3tc_code != '' && get_dynamic_blocks () == AI_DYNAMIC_BLOCKS_SERVER_SIDE_W3TC && !defined ('AI_NO_W3TC')) {

      $this->w3tc_code .= ' $ai_code = str_replace ("[#AI_CODE#]", base64_encode ($ai_code), base64_decode ("'. base64_encode ($serverside_insertion_code) . '"));';

      $serverside_insertion_code = '<!-- mfunc '.W3TC_DYNAMIC_SECURITY.' -->';
      $serverside_insertion_code .= $this->w3tc_code.' if ($ai_enabled) echo $ai_code;';
      $serverside_insertion_code .= '<!-- /mfunc '.W3TC_DYNAMIC_SECURITY.' -->';

    } else {
        if ($this->w3tc_code2 != '' && get_dynamic_blocks () == AI_DYNAMIC_BLOCKS_SERVER_SIDE_W3TC && !defined ('AI_NO_W3TC')) {
          $this->w3tc_code2 .= ' $ai_code = str_replace ("[#AI_CODE2#]", $ai_enabled ? $ai_code : "", base64_decode ("'. base64_encode ($this->before_w3tc_code2) . '"));';
          $this->w3tc_code2 .= ' $ai_code = str_replace ("[#AI_CODE#]", base64_encode ($ai_code), base64_decode ("'. base64_encode ($serverside_insertion_code) . '"));';

          $serverside_insertion_code = '<!-- mfunc '.W3TC_DYNAMIC_SECURITY.' -->';
          $serverside_insertion_code .= $this->w3tc_code2 .' echo $ai_code;';
          $serverside_insertion_code .= '<!-- /mfunc '.W3TC_DYNAMIC_SECURITY.' -->';
        } else

        $serverside_insertion_code = str_replace ('[#AI_CODE#]', $block_code, $serverside_insertion_code);
      }

    return $serverside_insertion_code;
  }

  public function get_iframe_page () {
    ob_start ();
    echo '<!DOCTYPE>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
';
 ai_header_noindex ();
 add_head_inline_styles ();
echo '<style type="text/css">
  body {margin: 0; padding: 0;}
</style>
<title>Ad Inserter Pro - Advanced WordPress Ads Management Plugin</title>
</head>
<body>
';
  echo $this->get_code_for_insertion (true, false, false, true);
  add_footer_inline_scripts ();
echo '</body>
</html>';

    $page = ob_get_clean ();
    return $page;
  }

  public function get_close_button (){
     $option = isset ($this->wp_options [AI_OPTION_CLOSE_BUTTON]) ? $this->wp_options [AI_OPTION_CLOSE_BUTTON] : DEFAULT_CLOSE_BUTTON;
     return $option;
  }

  public function get_horizontal_margin (){
     $option = isset ($this->wp_options [AI_OPTION_HORIZONTAL_MARGIN]) ? $this->wp_options [AI_OPTION_HORIZONTAL_MARGIN] : DEFAULT_HORIZONTAL_MARGIN;
     return $option;
  }

  public function get_vertical_margin () {
    $option = isset ($this->wp_options [AI_OPTION_VERTICAL_MARGIN]) ? $this->wp_options [AI_OPTION_VERTICAL_MARGIN] : DEFAULT_VERTICAL_MARGIN;
    return $option;
  }

  public function get_animation () {
    $option = isset ($this->wp_options [AI_OPTION_ANIMATION]) ? $this->wp_options [AI_OPTION_ANIMATION] : DEFAULT_ANIMATION;
    return $option;
  }

  public function get_animation_trigger () {
    $option = isset ($this->wp_options [AI_OPTION_ANIMATION_TRIGGER]) ? $this->wp_options [AI_OPTION_ANIMATION_TRIGGER] : DEFAULT_ANIMATION_TRIGGER;
    return $option;
  }

  public function get_animation_trigger_value () {
    $option = isset ($this->wp_options [AI_OPTION_ANIMATION_TRIGGER_VALUE]) ? $this->wp_options [AI_OPTION_ANIMATION_TRIGGER_VALUE] : DEFAULT_ANIMATION_TRIGGER_VALUE;
    return $option;
  }

  public function get_animation_trigger_offset () {
    $option = isset ($this->wp_options [AI_OPTION_ANIMATION_TRIGGER_OFFSET]) ? $this->wp_options [AI_OPTION_ANIMATION_TRIGGER_OFFSET] : DEFAULT_ANIMATION_TRIGGER_OFFSET;
    return $option;
  }

  public function get_animation_trigger_delay () {
    $option = isset ($this->wp_options [AI_OPTION_ANIMATION_TRIGGER_DELAY]) ? $this->wp_options [AI_OPTION_ANIMATION_TRIGGER_DELAY] : DEFAULT_ANIMATION_TRIGGER_DELAY;
    return $option;
  }

  public function get_animation_trigger_once () {
    $option = isset ($this->wp_options [AI_OPTION_ANIMATION_TRIGGER_ONCE]) ? $this->wp_options [AI_OPTION_ANIMATION_TRIGGER_ONCE] : DEFAULT_ANIMATION_TRIGGER_ONCE;
    return $option;
  }

  public function get_ad_general_tag(){
    $option = isset ($this->wp_options [AI_OPTION_GENERAL_TAG]) ? $this->wp_options [AI_OPTION_GENERAL_TAG] : DEFAULT_GENERAL_TAG;
//    if ($option == '') $option = DEFAULT_GENERAL_TAG;
    return $option;
  }

  public function get_adb_block_action (){
     $option = isset ($this->wp_options [AI_OPTION_ADB_BLOCK_ACTION]) ? $this->wp_options [AI_OPTION_ADB_BLOCK_ACTION] : DEFAULT_ADB_BLOCK_ACTION;
     return $option;
  }

  public function get_adb_block_replacement (){
     $option = isset ($this->wp_options [AI_OPTION_ADB_BLOCK_REPLACEMENT]) ? $this->wp_options [AI_OPTION_ADB_BLOCK_REPLACEMENT] : AD_EMPTY_DATA;
     return $option;
  }

  public function get_scheduling(){
     $option = isset ($this->wp_options [AI_OPTION_SCHEDULING]) ? $this->wp_options [AI_OPTION_SCHEDULING] : "";

     // Convert old option
     if ($option == '' && intval ($this->get_ad_after_day()) != 0) $option = AI_SCHEDULING_DELAY_FOR;

     if ($option == '') $option = AI_SCHEDULING_OFF;

     return $option;
  }

  public function get_ad_after_day(){
     $option = isset ($this->wp_options [AI_OPTION_AFTER_DAYS]) ? $this->wp_options [AI_OPTION_AFTER_DAYS] : "";
//     if ($option == '') $option = AD_ZERO;

     if ($option == '0') $option = '';

     return $option;
  }

  public function get_schedule_start_date(){
    $option = isset ($this->wp_options [AI_OPTION_START_DATE]) ? $this->wp_options [AI_OPTION_START_DATE] : "";
    return $option;
  }

  public function get_schedule_end_date(){
    $option = isset ($this->wp_options [AI_OPTION_END_DATE]) ? $this->wp_options [AI_OPTION_END_DATE] : "";
    return $option;
  }

  public function get_fallback(){
    $option = isset ($this->wp_options [AI_OPTION_FALLBACK]) ? $this->wp_options [AI_OPTION_FALLBACK] : "";
    return $option;
  }

  public function get_maximum_insertions (){
    $option = isset ($this->wp_options [AI_OPTION_MAXIMUM_INSERTIONS]) ? $this->wp_options [AI_OPTION_MAXIMUM_INSERTIONS] : "";
    if ($option == '0') $option = '';
    return $option;
  }

  public function get_id_list(){
    $option = isset ($this->wp_options [AI_OPTION_ID_LIST]) ? $this->wp_options [AI_OPTION_ID_LIST] : "";
    return $option;
  }

  public function get_id_list_type (){
    $option = isset ($this->wp_options [AI_OPTION_ID_LIST_TYPE]) ? $this->wp_options [AI_OPTION_ID_LIST_TYPE] : AI_BLACK_LIST;
    if ($option == '') $option = AI_BLACK_LIST;

    elseif ($option == AD_BLACK_LIST)     $option = AI_BLACK_LIST;
    elseif ($option == AD_WHITE_LIST)     $option = AI_WHITE_LIST;

    return $option;
  }

  public function get_ad_url_list(){
    $option = isset ($this->wp_options [AI_OPTION_URL_LIST]) ? $this->wp_options [AI_OPTION_URL_LIST] : "";
    return $option;
  }

  public function get_ad_url_list_type (){
    $option = isset ($this->wp_options [AI_OPTION_URL_LIST_TYPE]) ? $this->wp_options [AI_OPTION_URL_LIST_TYPE] : AI_BLACK_LIST;
    if ($option == '') $option = AI_BLACK_LIST;

    elseif ($option == AD_BLACK_LIST)     $option = AI_BLACK_LIST;
    elseif ($option == AD_WHITE_LIST)     $option = AI_WHITE_LIST;

    return $option;
  }

  public function get_url_parameter_list(){
    $option = isset ($this->wp_options [AI_OPTION_URL_PARAMETER_LIST]) ? $this->wp_options [AI_OPTION_URL_PARAMETER_LIST] : "";
    return $option;
  }

  public function get_url_parameter_list_type (){
    $option = isset ($this->wp_options [AI_OPTION_URL_PARAMETER_LIST_TYPE]) ? $this->wp_options [AI_OPTION_URL_PARAMETER_LIST_TYPE] : AI_BLACK_LIST;
    if ($option == '') $option = AI_BLACK_LIST;

    elseif ($option == AD_BLACK_LIST)     $option = AI_BLACK_LIST;
    elseif ($option == AD_WHITE_LIST)     $option = AI_WHITE_LIST;

    return $option;
  }

  public function get_ad_domain_list(){
     $option = isset ($this->wp_options [AI_OPTION_DOMAIN_LIST]) ? $this->wp_options [AI_OPTION_DOMAIN_LIST] : "";
     return $option;
  }

  public function get_ad_domain_list_type (){
    $option = isset ($this->wp_options [AI_OPTION_DOMAIN_LIST_TYPE]) ? $this->wp_options [AI_OPTION_DOMAIN_LIST_TYPE] : AI_BLACK_LIST;
    if ($option == '') $option = AI_BLACK_LIST;

    elseif ($option == AD_BLACK_LIST)     $option = AI_BLACK_LIST;
    elseif ($option == AD_WHITE_LIST)     $option = AI_WHITE_LIST;

    return $option;
  }

  public function get_ad_ip_address_list (){
     $option = isset ($this->wp_options [AI_OPTION_IP_ADDRESS_LIST]) ? $this->wp_options [AI_OPTION_IP_ADDRESS_LIST] : "";
     return $option;
  }

  public function get_ad_ip_address_list_type (){
    $option = isset ($this->wp_options [AI_OPTION_IP_ADDRESS_LIST_TYPE]) ? $this->wp_options [AI_OPTION_IP_ADDRESS_LIST_TYPE] : AI_BLACK_LIST;
    if ($option == '') $option = AI_BLACK_LIST;

    elseif ($option == AD_BLACK_LIST)     $option = AI_BLACK_LIST;
    elseif ($option == AD_WHITE_LIST)     $option = AI_WHITE_LIST;

    return $option;
  }

  public function get_ad_country_list ($expand = false){
     $option = isset ($this->wp_options [AI_OPTION_COUNTRY_LIST]) ? $this->wp_options [AI_OPTION_COUNTRY_LIST] : "";
     if ($expand && function_exists ('expanded_country_list')) return expanded_country_list ($option);
     return $option;
  }

  public function get_ad_country_list_type (){
    $option = isset ($this->wp_options [AI_OPTION_COUNTRY_LIST_TYPE]) ? $this->wp_options [AI_OPTION_COUNTRY_LIST_TYPE] : AI_BLACK_LIST;
    if ($option == '') $option = AI_BLACK_LIST;

    elseif ($option == AD_BLACK_LIST)     $option = AI_BLACK_LIST;
    elseif ($option == AD_WHITE_LIST)     $option = AI_WHITE_LIST;

    return $option;
  }

  public function get_ad_name(){
     $option = isset ($this->wp_options [AI_OPTION_BLOCK_NAME]) ? $this->wp_options [AI_OPTION_BLOCK_NAME] : "";
     if ($option == '') $option = DEFAULT_AD_NAME. " " . $this->number;
     return $option;
  }

  public function get_ad_block_cat(){
     $option = isset ($this->wp_options [AI_OPTION_CATEGORY_LIST]) ? $this->wp_options [AI_OPTION_CATEGORY_LIST] : "";
     return $option;
  }

  public function get_ad_block_cat_type(){
    $option = isset ($this->wp_options [AI_OPTION_CATEGORY_LIST_TYPE]) ? $this->wp_options [AI_OPTION_CATEGORY_LIST_TYPE] : AI_BLACK_LIST;

    // Update old data
    if ($option == ''){
      $option = AI_BLACK_LIST;
      $this->wp_options [AI_OPTION_CATEGORY_LIST_TYPE] = AI_BLACK_LIST;
    }

    if ($option == '') $option = AI_BLACK_LIST;

    elseif ($option == AD_BLACK_LIST)     $option = AI_BLACK_LIST;
    elseif ($option == AD_WHITE_LIST)     $option = AI_WHITE_LIST;

    return $option;
  }

  public function get_ad_block_tag(){
     $option = isset ($this->wp_options [AI_OPTION_TAG_LIST]) ? $this->wp_options [AI_OPTION_TAG_LIST] : "";
     return $option;
  }

  public function get_ad_block_tag_type(){
    $option = isset ($this->wp_options [AI_OPTION_TAG_LIST_TYPE]) ? $this->wp_options [AI_OPTION_TAG_LIST_TYPE] : AI_BLACK_LIST;
    if ($option == '') $option = AI_BLACK_LIST;

    elseif ($option == AD_BLACK_LIST)     $option = AI_BLACK_LIST;
    elseif ($option == AD_WHITE_LIST)     $option = AI_WHITE_LIST;

    return $option;
  }

  public function get_ad_block_taxonomy(){
     $option = isset ($this->wp_options [AI_OPTION_TAXONOMY_LIST]) ? $this->wp_options [AI_OPTION_TAXONOMY_LIST] : "";
     return $option;
  }

  public function get_ad_block_taxonomy_type(){
    $option = isset ($this->wp_options [AI_OPTION_TAXONOMY_LIST_TYPE]) ? $this->wp_options [AI_OPTION_TAXONOMY_LIST_TYPE] : AI_BLACK_LIST;
    if ($option == '') $option = AI_BLACK_LIST;

    elseif ($option == AD_BLACK_LIST)     $option = AI_BLACK_LIST;
    elseif ($option == AD_WHITE_LIST)     $option = AI_WHITE_LIST;

    return $option;
  }

  public function get_ad_enabled_on_which_pages (){
    $option = isset ($this->wp_options [AI_OPTION_ENABLED_ON_WHICH_PAGES]) ? $this->wp_options [AI_OPTION_ENABLED_ON_WHICH_PAGES] : AI_NO_INDIVIDUAL_EXCEPTIONS;

    if ($option == '') $option = AI_NO_INDIVIDUAL_EXCEPTIONS;

    elseif ($option == AD_ENABLED_ON_ALL)                       $option = AI_NO_INDIVIDUAL_EXCEPTIONS;
    elseif ($option == AD_ENABLED_ON_ALL_EXCEPT_ON_SELECTED)    $option = AI_INDIVIDUALLY_DISABLED;
    elseif ($option == AD_ENABLED_ONLY_ON_SELECTED)             $option = AI_INDIVIDUALLY_ENABLED;

//    return AI_INDIVIDUALLY_DISABLED;
    return $option;
  }

  public function get_ad_enabled_on_which_pages_text ($translated = true){
    switch ($this->get_ad_enabled_on_which_pages ()) {
      case AI_NO_INDIVIDUAL_EXCEPTIONS:
        if (!$translated) return AI_TEXT_ENG_NO_INDIVIDUAL_EXCEPTIONS;
        return AI_TEXT_PAGES_NO_INDIVIDUAL_EXCEPTIONS;
        break;
      case AI_INDIVIDUALLY_DISABLED:
        if (!$translated) return AI_TEXT_ENG_INDIVIDUALLY_DISABLED;
        return AI_TEXT_PAGES_INDIVIDUALLY_DISABLED;
        break;
      case AI_INDIVIDUALLY_ENABLED:
        if (!$translated) return AI_TEXT_ENG_INDIVIDUALLY_ENABLED;
        return AI_TEXT_PAGES_INDIVIDUALLY_ENABLED;
        break;
      default:
        return '';
        break;
    }
  }

  public function get_ad_enabled_on_which_posts (){
    $option = isset ($this->wp_options [AI_OPTION_ENABLED_ON_WHICH_POSTS]) ? $this->wp_options [AI_OPTION_ENABLED_ON_WHICH_POSTS] : AI_NO_INDIVIDUAL_EXCEPTIONS;

    if ($option == '') $option = AI_NO_INDIVIDUAL_EXCEPTIONS;

    elseif ($option == AD_ENABLED_ON_ALL)                       $option = AI_NO_INDIVIDUAL_EXCEPTIONS;
    elseif ($option == AD_ENABLED_ON_ALL_EXCEPT_ON_SELECTED)    $option = AI_INDIVIDUALLY_DISABLED;
    elseif ($option == AD_ENABLED_ONLY_ON_SELECTED)             $option = AI_INDIVIDUALLY_ENABLED;

//    return AI_INDIVIDUALLY_DISABLED;
    return $option;
  }

  public function get_ad_enabled_on_which_posts_text ($translated = true){
    switch ($this->get_ad_enabled_on_which_posts ()) {
      case AI_NO_INDIVIDUAL_EXCEPTIONS:
        if (!$translated) return AI_TEXT_ENG_NO_INDIVIDUAL_EXCEPTIONS;
        return AI_TEXT_POSTS_NO_INDIVIDUAL_EXCEPTIONS;
        break;
      case AI_INDIVIDUALLY_DISABLED:
        if (!$translated) return AI_TEXT_ENG_INDIVIDUALLY_DISABLED;
        return AI_TEXT_POSTS_INDIVIDUALLY_DISABLED;
        break;
      case AI_INDIVIDUALLY_ENABLED:
        if (!$translated) return AI_TEXT_ENG_INDIVIDUALLY_ENABLED;
        return AI_TEXT_POSTS_INDIVIDUALLY_ENABLED;
        break;
      default:
        return '';
        break;
    }
  }

  public function get_viewport_classes () {
    global $ai_wp_data;

    if ($ai_wp_data [AI_WP_AMP_PAGE]) return '';

    $viewport_classes = "";
    if ($this->get_detection_client_side ()) {
      $all_viewports = true;
      for ($viewport = 1; $viewport <= AD_INSERTER_VIEWPORTS; $viewport ++) {
        $viewport_name = get_viewport_name ($viewport);
        if ($viewport_name != '') {
          if ($this->get_detection_viewport ($viewport)) $viewport_classes .= " ai-viewport-" . $viewport; else $all_viewports = false;
        }
      }
      if ($viewport_classes == "") $viewport_classes = " ai-viewport-0";
        elseif ($all_viewports) $viewport_classes = "";
    }
    return ($viewport_classes);
  }

  public function get_viewport_names () {
    global $ai_wp_data;

    if ($ai_wp_data [AI_WP_AMP_PAGE]) return '';

    $viewport_names = array ();
    if ($this->get_detection_client_side ()) {
      for ($viewport = 1; $viewport <= AD_INSERTER_VIEWPORTS; $viewport ++) {
        $viewport_name = get_viewport_name ($viewport);
        if ($viewport_name != '') {
          if ($this->get_detection_viewport ($viewport)) $viewport_names []= $viewport_name;
        }
      }
    }
    return (implode (', ', $viewport_names));
  }

  public function get_alignment_class ($block_class_name = null){
    global $ai_wp_data;

    if (defined ('AI_AMP_HEADER_STYLES')    && AI_AMP_HEADER_STYLES     &&  $ai_wp_data [AI_WP_AMP_PAGE] ||
        defined ('AI_NORMAL_HEADER_STYLES') && AI_NORMAL_HEADER_STYLES  && !$ai_wp_data [AI_WP_AMP_PAGE] && !get_inline_styles ()) {
      return $this->generate_alignment_class ($block_class_name);
    }

    return '';
  }

  public function generate_alignment_class ($block_class_name = null){

    if ($block_class_name == null) $block_class_name = get_block_class_name (true);
    $block_class_name .= '-';

    switch ($this->get_alignment_type ()) {
      case AI_ALIGNMENT_DEFAULT:
      case AI_ALIGNMENT_LEFT:
      case AI_ALIGNMENT_RIGHT:
      case AI_ALIGNMENT_CENTER:
      case AI_ALIGNMENT_FLOAT_LEFT:
      case AI_ALIGNMENT_FLOAT_RIGHT:
      case AI_ALIGNMENT_STICKY_LEFT:
      case AI_ALIGNMENT_STICKY_RIGHT:
      case AI_ALIGNMENT_STICKY_TOP:
      case AI_ALIGNMENT_STICKY_BOTTOM:
        return $block_class_name . str_replace (' ', '-', strtolower ($this->get_alignment_type_text (false)));
        break;
      case AI_ALIGNMENT_STICKY:
        return $block_class_name . str_replace (' ', '-', strtolower (md5 ($this->alignment_style ($this->get_alignment_type ()))));
        break;
      case AI_ALIGNMENT_CUSTOM_CSS:
        return $block_class_name . str_replace (' ', '-', strtolower (md5 ($this->get_custom_css ())));
        break;
    }

    return '';
  }

  public function before_paragraph ($content, $position_preview = false) {
    global $ai_wp_data, $ai_last_check, $special_element_tags;

    $multibyte = $ai_wp_data [AI_MBSTRING_LOADED] && get_paragraph_counting_functions() == AI_MULTIBYTE_PARAGRAPH_COUNTING_FUNCTIONS;

    $paragraph_positions = array ();
    $active_paragraph_positions = array ();

    $dummy = array ();

    $paragraph_tags = trim ($this->get_paragraph_tags());
    if ($paragraph_tags == '') return $content;

    $paragraph_start_strings = explode (",", $paragraph_tags);

    $ai_last_check = AI_CHECK_PARAGRAPH_TAGS;
    if (count ($paragraph_start_strings) == 0) return $content;

    get_paragraph_start_positions ($content, $multibyte, $dummy, $paragraph_start_strings, $paragraph_positions, $active_paragraph_positions);

    // Nothing to do
    $ai_last_check = AI_CHECK_PARAGRAPHS_WITH_TAGS;
    if (array_sum ($active_paragraph_positions) == 0) return $content;

    sort ($paragraph_positions);
    ksort ($active_paragraph_positions);
    $new_active_paragraph_positions = array ();
    foreach ($active_paragraph_positions as $active_paragraph_position) {
      $new_active_paragraph_positions [] = $active_paragraph_position;
    }
    $active_paragraph_positions = $new_active_paragraph_positions;


    if (!$this->get_count_inside_blockquote ()) {

      $special_element_offsets = array ();

      foreach ($special_element_tags as $special_element_tag) {
        preg_match_all ("/<\/?$special_element_tag/i", $content, $special_elements, PREG_OFFSET_CAPTURE);

        $nesting = array ();
        $special_elements = $special_elements [0];
        foreach ($special_elements as $index => $special_element) {
          if (isset ($special_elements [$index + 1][0])) {
            $tag1 = strtolower ($special_element [0]);
            $tag2 = strtolower ($special_elements [$index + 1][0]);

            $start_offset = $special_element [1];
            $nesting_ended = false;

            $tag1_start = $tag1 == "<$special_element_tag";
            $tag2_start = $tag2 == "<$special_element_tag";
            $tag1_end   = $tag1 == "</$special_element_tag";
            $tag2_end   = $tag2 == "</$special_element_tag";

            if ($tag1_start && $tag2_start) {
              array_push ($nesting, $start_offset);
              continue;
            }
            elseif ($tag1_end && $tag2_end) {
              $start_offset = array_pop ($nesting);
              if (count ($nesting) == 0) $nesting_ended = true;
            }

            if (count ($nesting) != 0) continue;

            if (($nesting_ended || $tag1_start) && $tag2_end) {

              if ($multibyte) {
                $special_element_offsets []= array (mb_strlen (substr ($content, 0, $start_offset)) + 1, mb_strlen (substr ($content, 0, $special_elements [$index + 1][1])));
              } else {
                  $special_element_offsets []= array ($start_offset + 1, $special_elements [$index + 1][1]);
                }
            }
          }
        }
      }

      if (count ($special_element_offsets) != 0) {

        $inside_special_element = array ();

        foreach ($special_element_offsets as $special_element_offset) {
          foreach ($paragraph_positions as $paragraph_position) {
            if ($paragraph_position >= $special_element_offset [0] && $paragraph_position <= $special_element_offset [1]) $inside_special_element [] = $paragraph_position;
          }
        }

        foreach ($paragraph_positions as $index => $paragraph_position) {
          if (in_array ($paragraph_position, $inside_special_element)) $active_paragraph_positions [$index] = 0;
        }
      }

      $ai_last_check = AI_CHECK_PARAGRAPHS_AFTER_NO_COUNTING_INSIDE;
      if (array_sum ($active_paragraph_positions) == 0) return $content;
    }


    $paragraph_min_words = intval ($this->get_minimum_paragraph_words());
    $paragraph_max_words = intval ($this->get_maximum_paragraph_words());

    if ($paragraph_min_words != 0 || $paragraph_max_words != 0) {

      if (!isset ($paragraph_end_positions)) {
        $paragraph_end_positions = array ();
        get_paragraph_end_positions ($content, $multibyte, $paragraph_positions, $paragraph_start_strings, $paragraph_end_positions, $dummy);
      }

      foreach ($paragraph_positions as $index => $paragraph_position) {

        if ($active_paragraph_positions [$index] == 0) continue;

        if ($multibyte) {
          $paragraph_code = mb_substr ($content, $paragraph_position, $paragraph_end_positions [$index] - $paragraph_position + 1);
        } else {
            $paragraph_code = substr ($content, $paragraph_position, $paragraph_end_positions [$index] - $paragraph_position + 1);
          }

        if (!$this->check_number_of_words_in_paragraph ($paragraph_code, $paragraph_min_words, $paragraph_max_words)) $active_paragraph_positions [$index] = 0;
      }
    }

    // Nothing to do
    $ai_last_check = AI_CHECK_PARAGRAPHS_AFTER_MIN_MAX_WORDS;
    if (array_sum ($active_paragraph_positions) == 0) return $content;


    $paragraph_texts = explode (",", html_entity_decode ($this->get_paragraph_text()));
    if ($this->get_paragraph_text() != "" && count ($paragraph_texts) != 0) {

      if (!isset ($paragraph_end_positions)) {
        $paragraph_end_positions = array ();
        get_paragraph_end_positions   ($content, $multibyte, $paragraph_positions, $paragraph_start_strings, $paragraph_end_positions, $dummy);
      }

      $paragraph_text_type = $this->get_paragraph_text_type ();

      foreach ($paragraph_positions as $index => $paragraph_position) {

        if ($active_paragraph_positions [$index] == 0) continue;

        if ($multibyte) {
          $paragraph_code = mb_substr ($content, $paragraph_position, $paragraph_end_positions [$index] - $paragraph_position + 1);
        } else {
            $paragraph_code = substr ($content, $paragraph_position, $paragraph_end_positions [$index] - $paragraph_position + 1);
          }

        if ($paragraph_text_type == AI_CONTAIN) {
          $found = true;
          foreach ($paragraph_texts as $paragraph_text) {
            if (trim ($paragraph_text) == '') continue;

            if ($multibyte) {
              if (mb_stripos ($paragraph_code, trim ($paragraph_text)) === false) {
                $found = false;
                break;
              }
            } else {
                if (stripos ($paragraph_code, trim ($paragraph_text)) === false) {
                  $found = false;
                  break;
                }
              }
          }
          if (!$found) $active_paragraph_positions [$index] = 0;
        } elseif ($paragraph_text_type == AI_DO_NOT_CONTAIN) {
            $found = false;
            foreach ($paragraph_texts as $paragraph_text) {
              if (trim ($paragraph_text) == '') continue;

              if ($multibyte) {
                if (mb_stripos ($paragraph_code, trim ($paragraph_text)) !== false) {
                  $found = true;
                  break;
                }
              } else {
                  if (stripos ($paragraph_code, trim ($paragraph_text)) !== false) {
                    $found = true;
                    break;
                  }
                }
            }
            if ($found) $active_paragraph_positions [$index] = 0;
          }
      }
    }

    // Nothing to do
    $ai_last_check = AI_CHECK_PARAGRAPHS_AFTER_TEXT;
    if (array_sum ($active_paragraph_positions) == 0) return $content;

    $position_text = trim ($this->get_paragraph_number());


    // Prepare $paragraph_end_positions
    if ($position_preview || $position_text == '') {
      if (!isset ($paragraph_end_positions)) {
        $paragraph_end_positions = array ();
        get_paragraph_end_positions ($content, $multibyte, $paragraph_positions, $paragraph_start_strings, $paragraph_end_positions, $dummy);
      }

      $filtered_paragraph_end_positions = array ();
      // Use $paragraph_positions for counting as it is checked for consistency
      foreach ($paragraph_positions as $index => $paragraph_position) {
        if ($active_paragraph_positions [$index]) $filtered_paragraph_end_positions [] = $paragraph_end_positions [$index];
      }
      $paragraph_end_positions = $filtered_paragraph_end_positions;

      if ($this->get_direction_type() == AI_DIRECTION_FROM_BOTTOM) {
        $paragraph_end_positions = array_reverse ($paragraph_end_positions);
      }
    }


    $filtered_paragraph_positions = array ();
    foreach ($paragraph_positions as $index => $paragraph_position) {
      if ($active_paragraph_positions [$index]) $filtered_paragraph_positions [] = $paragraph_position;

    }
    $paragraph_positions = $filtered_paragraph_positions;


    if ($this->get_direction_type() == AI_DIRECTION_FROM_BOTTOM) {
      $paragraph_positions = array_reverse ($paragraph_positions);
    }


//  $positions contains indexes in $paragraph_positions
    $positions = array ();

    if (!$position_preview) {
      $position = $position_text;

      if (is_numeric ($position)) {
        if ($position > 0 && $position < 1) {
          $position = intval ($position * (count ($paragraph_positions) - 1) + 0.5);
        }
        elseif ($position > - 1 && $position < 0) {
          $position = intval (count ($paragraph_positions) + $position * (count ($paragraph_positions) - 1) - 0.5);
        }
        elseif ($position <= - 1) {
          $position = count ($paragraph_positions) + $position;
        }
        elseif ($position == 0) {
          $position = mt_rand (0, count ($paragraph_positions) - 1);
        }
        else $position --;

        $positions = array ($position);
      }
      elseif (strpos ($position_text, ',') !== false) {
        $new_positions = array ();
        $positions = explode (',', str_replace (' ', '', $position_text));
        foreach ($positions as $index => $position) {
          if (isset ($position [0]) && $position [0] == '%') {
            unset ($positions [$index]);
            $mod_value = substr ($position, 1);
            if (is_numeric ($mod_value) && $mod_value > 0) {
              foreach ($paragraph_positions as $index => $paragraph_position) {
                if (($index + 1) % $mod_value == 0) $new_positions []= $index;
              }
            }
          }
          elseif ($position > 0 && $position < 1) {
            $positions [$index] = intval ($position * (count ($paragraph_positions) - 1) + 0.5);
          }
          elseif ($position > - 1 && $position < 0) {
            $positions [$index] = intval (count ($paragraph_positions) + $position * (count ($paragraph_positions) - 1) - 0.5);
          }
          elseif ($position <= - 1) {
            $positions [$index] = count ($paragraph_positions) + $position;
          }
          elseif ($position == 0) {
            $positions [$index] = mt_rand (0, count ($paragraph_positions) - 1);
          }
          else $positions [$index] = $position - 1;
        }
        $positions = array_unique (array_merge ($positions, $new_positions));
        sort ($positions);
      }
      elseif (isset ($position_text [0]) && $position_text [0] == '%') {
        $mod_value = substr ($position_text, 1);
        if (is_numeric ($mod_value) && $mod_value > 0) {
          foreach ($paragraph_positions as $index => $paragraph_position) {
            if (($index + 1) % $mod_value == 0) $positions []= $index;
          }
        }
      }
      elseif ($position_text == '') {
        $positions = array ();

        $min_words_above = $this->get_minimum_words_above ();
        if (!empty ($min_words_above)) {
          $words_above = 0;
          foreach ($paragraph_positions as $index => $paragraph_position) {

            if ($this->get_direction_type() != AI_DIRECTION_FROM_BOTTOM) {
              if ($words_above >= $min_words_above) {
                $positions []= $index;
                $words_above = 0;
              }
            }


            if ($multibyte) {
              $paragraph_code = mb_substr ($content, $paragraph_position, $paragraph_end_positions [$index] - $paragraph_position + 1);
            } else {
                $paragraph_code = substr ($content, $paragraph_position, $paragraph_end_positions [$index] - $paragraph_position + 1);
              }

            $words_above += number_of_words ($paragraph_code);

            if ($this->get_direction_type() == AI_DIRECTION_FROM_BOTTOM) {
              if ($words_above >= $min_words_above) {
                $positions []= $index;
                $words_above = 0;
              }
            }

          }
        } else
        foreach ($paragraph_positions as $index => $paragraph_position) {
          $positions []= $index;
        }

        $filter_settings = trim (str_replace (' ', '', $this->get_call_filter()));
        if (!empty ($filter_settings)) {
          if ($this->get_filter_type() == AI_FILTER_PARAGRAPHS) {

            $filter_values = array ();
            if (strpos ($filter_settings, ",") !== false) {
              $filter_values = explode (",", $filter_settings);
            } else $filter_values []= $filter_settings;

            $inverted_filter = $this->get_inverted_filter();
            $filtered_positions = array ();


            foreach ($positions as $index => $position) {
              $insert = false;
              if (in_array ($index + 1, $filter_values)) {
                $insert = true;
              } else {
                  foreach ($filter_values as $filter_value) {
                    $filter_value = trim ($filter_value);
                    if ($filter_value [0] == '%') {
                      $mod_value = substr ($filter_value, 1);
                      if (is_numeric ($mod_value) && $mod_value > 0) {
                        if (($index + 1) % $mod_value == 0) $insert = true;
                        break;
                      }
                    }
                  }
                }
              if ($insert xor $inverted_filter) $filtered_positions []= $position;
            }
            $positions = $filtered_positions;
          }
//          else $positions = array ();
        }

      }
    }

    $debug_processing = ($ai_wp_data [AI_WP_DEBUGGING] & AI_DEBUG_PROCESSING) != 0;

    if (!empty ($positions)) {
      $avoid_paragraphs_above = intval ($this->get_avoid_paragraphs_above());
      $avoid_paragraphs_below = intval ($this->get_avoid_paragraphs_below());

      $avoid_text_above = $this->get_avoid_text_above();
      $avoid_text_below = $this->get_avoid_text_below();
      $avoid_paragraph_texts_above = explode (",", html_entity_decode (trim ($avoid_text_above)));
      $avoid_paragraph_texts_below = explode (",", html_entity_decode (trim ($avoid_text_below)));

      $direction  = $this->get_avoid_direction();
      $max_checks = $this->get_avoid_try_limit();

      $failed_clearance_positions = array ();
      foreach ($positions as $position_index => $position) {

        if (($avoid_paragraphs_above != 0 || $avoid_paragraphs_below != 0) && count ($paragraph_positions) > $position) {

          if ($debug_processing && $this->number != 0) ai_log ('BLOCK ' . $this->number . ' CLEARANCE CHECK POSITION ' . ($position + 1));

          $checks = $max_checks;
          $saved_position = $position;
          do {
            $found_above = false;
            if ($position != 0 && $avoid_paragraphs_above != 0 && $avoid_text_above != "" && is_array ($avoid_paragraph_texts_above) && count ($avoid_paragraph_texts_above) != 0) {
              $paragraph_position_above = $position - $avoid_paragraphs_above;
              if ($paragraph_position_above < 0) $paragraph_position_above = 0;

              if ($multibyte) {
                $paragraph_code = mb_substr ($content, $paragraph_positions [$paragraph_position_above], $paragraph_positions [$position] - $paragraph_positions [$paragraph_position_above]);
              } else {
                  $paragraph_code = substr ($content, $paragraph_positions [$paragraph_position_above], $paragraph_positions [$position] - $paragraph_positions [$paragraph_position_above]);
                }

              foreach ($avoid_paragraph_texts_above as $paragraph_text_above) {
                if (trim ($paragraph_text_above) == '') continue;
                if ($multibyte) {
                  if (mb_stripos ($paragraph_code, trim ($paragraph_text_above)) !== false) {
                    $found_above = true;
                    break;
                  }
                } else {
                    if (stripos ($paragraph_code, trim ($paragraph_text_above)) !== false) {
                      $found_above = true;
                      break;
                    }
                  }
              }
            }

            $found_below = false;
            if ($avoid_paragraphs_below != 0 && $avoid_text_below != "" && is_array ($avoid_paragraph_texts_below) && count ($avoid_paragraph_texts_below) != 0) {
              $paragraph_position_below = $position + $avoid_paragraphs_below;

              if ($multibyte) {
                if ($paragraph_position_below > count ($paragraph_positions) - 1)
                  $content_position_below = mb_strlen ($content); else
                    $content_position_below = $paragraph_positions [$paragraph_position_below];
                $paragraph_code = mb_substr ($content, $paragraph_positions [$position], $content_position_below - $paragraph_positions [$position]);
              } else {
                  if ($paragraph_position_below > count ($paragraph_positions) - 1)
                    $content_position_below = strlen ($content); else
                      $content_position_below = $paragraph_positions [$paragraph_position_below];
                  $paragraph_code = substr ($content, $paragraph_positions [$position], $content_position_below - $paragraph_positions [$position]);
                }

              foreach ($avoid_paragraph_texts_below as $paragraph_text_below) {
                if (trim ($paragraph_text_below) == '') continue;

                if ($multibyte) {
                  if (mb_stripos ($paragraph_code, trim ($paragraph_text_below)) !== false) {
                    $found_below = true;
                    break;
                  }
                } else {
                    if (stripos ($paragraph_code, trim ($paragraph_text_below)) !== false) {
                      $found_below = true;
                      break;
                    }
                  }
              }
            }


//            echo "position: $position = before #", $position + 1, "<br />\n";
//            echo "checks: $checks<br />\n";
//            echo "direction: $direction<br />\n";
//            if ($found_above)
//            echo "found_above<br />\n";
//            if ($found_below)
//            echo "found_below<br />\n";
//            echo "=================<br />\n";


            if ($found_above || $found_below) {

              if ($debug_processing && $this->number != 0) ai_log ('BLOCK ' . $this->number . ' CLEARANCE CHECK POSITION ' . ($position + 1) . ' FAILED');

              $ai_last_check = AI_CHECK_DO_NOT_INSERT;
              if ($this->get_avoid_action() == AI_DO_NOT_INSERT) {
                $failed_clearance_positions [$positions [$position_index]] = $ai_last_check;
                $positions [$position_index] = - 1;
                break;
              }

              switch ($direction) {
                case AI_ABOVE: // Try above
                  $ai_last_check = AI_CHECK_AD_ABOVE;
                  // Already at the top - do not insert
                  if ($position == 0) {
                    $failed_clearance_positions [$positions [$position_index]] = $ai_last_check;
                    $positions [$position_index] = - 1;
                    break 2;
                  }

                  $position --;
                  break;
                case AI_BELOW: // Try below
                  $ai_last_check = AI_CHECK_AD_BELOW;
                  // Already at the bottom - do not insert
                  if ($position >= count ($paragraph_positions) - 1) {
                    $failed_clearance_positions [$positions [$position_index]] = $ai_last_check;
                    $positions [$position_index] = - 1;
                    break 2;
                  }

                  $position ++;
                  break;
                case AI_ABOVE_AND_THEN_BELOW: // Try first above and then below
                  if ($position == 0 || $checks == 0) {
                    // Try below
                    $direction = AI_BELOW;
                    $checks = $max_checks;
                    $position = $saved_position;
                    $ai_last_check = AI_CHECK_AD_BELOW;
                    // Already at the bottom - do not insert
                    if ($position >= count ($paragraph_positions) - 1) {
                      $failed_clearance_positions [$positions [$position_index]] = $ai_last_check;
                      $positions [$position_index] = - 1;
                      break 2;
                    }

                    $position ++;
                  } else $position --;
                  break;
                case AI_BELOW_AND_THEN_ABOVE: // Try first below and then above
                  if ($position >= count ($paragraph_positions) - 1 || $checks == 0) {
                    // Try above
                    $direction = AI_ABOVE;
                    $checks = $max_checks;
                    $position = $saved_position;
                    $ai_last_check = AI_CHECK_AD_ABOVE;
                    // Already at the top - do not insert
                    if ($position == 0) {
                      $failed_clearance_positions [$positions [$position_index]] = $ai_last_check;
                      $positions [$position_index] = - 1;
                      break 2;
                    }

                    $position --;
                  } else $position ++;
                  break;
              }
            } else {
                // Text not found - insert
                $positions [$position_index] = $position;
                break;
              }

            // Try next position
            if ($checks <= 0) {
              // Suitable position not found - do not insert
              $failed_clearance_positions [$positions [$position_index]] = $ai_last_check;
              $positions [$position_index] = - 1;
              break;
            }

            $checks --;
          } while (true);
        }

        // Nothing to do
        $ai_last_check = AI_CHECK_PARAGRAPHS_AFTER_CLEARANCE;
        if (count ($paragraph_positions) == 0) return $content;
      }
    }


    if ($position_preview || !empty ($positions)) {
      $offset = 0;
      if (!empty ($positions)) $ai_last_check = AI_CHECK_PARAGRAPH_NUMBER;

      $debug_processing = ($ai_wp_data [AI_WP_DEBUGGING] & AI_DEBUG_PROCESSING) != 0;

      $real_positions = array ();
      foreach ($positions as $position_index) $real_positions []= $position_index >= 0 ? $position_index + 1 : '*';
      if ($debug_processing && $this->number != 0 && count ($real_positions) != 0) ai_log ('BLOCK ' . $this->number . ' INSERTION POSITIONS: ' . implode (', ', $real_positions));

      $min_paragraphs = intval ($this->get_paragraph_number_minimum());
      $max_page_blocks_enabled = $this->get_max_page_blocks_enabled ();

      foreach ($paragraph_positions as $counter => $paragraph_position) {
        if ($position_preview) {
          if ($multibyte) {
            $paragraph_code = mb_substr ($content, $paragraph_position + $offset, $paragraph_end_positions [$counter] - $paragraph_position + 1);
          } else {
              $paragraph_code = substr ($content, $paragraph_position + $offset, $paragraph_end_positions [$counter] - $paragraph_position + 1);
            }

          $paragraph_words = number_of_words ($paragraph_code);
          $inserted_code = "[[AI_BP".($counter + 1)."=".$paragraph_words."]]";
        }
        elseif (!empty ($positions) && in_array ($counter, $positions) && $this->check_block_counter ()) {

          $inserted = false;

          $ai_last_check = AI_CHECK_PARAGRAPHS_MIN_NUMBER;
          if (count ($paragraph_positions) >= $min_paragraphs) {

            $ai_last_check = AI_CHECK_MAX_PAGE_BLOCKS;
            if (!$max_page_blocks_enabled || $ai_wp_data [AI_PAGE_BLOCKS] < get_max_page_blocks ()) {
              $this->increment_block_counter ();

              // Increment page block counter
              if ($max_page_blocks_enabled) $ai_wp_data [AI_PAGE_BLOCKS] ++;

              $ai_last_check = AI_CHECK_DEBUG_NO_INSERTION;
              if (!$this->get_debug_disable_insertion ()) {
                $inserted_code = $this->get_code_for_serverside_insertion ();
                $ai_last_check = AI_CHECK_INSERTED;
                $this->clear_code_cache ();
                $inserted = true;
              }
            }
          }
          if ($debug_processing) ai_log (ai_log_block_status ($this->number, $ai_last_check));

          if (!$inserted) continue;
        }
        else {
          if ($debug_processing && isset ($failed_clearance_positions [$counter])) ai_log (ai_log_block_status ($this->number, $failed_clearance_positions [$counter]));
          continue;
        }

        if ($multibyte) {
          if ($this->get_direction_type() == AI_DIRECTION_FROM_BOTTOM) {
            $content = mb_substr ($content, 0, $paragraph_position) . $inserted_code . mb_substr ($content, $paragraph_position);
          } else {
              $content = mb_substr ($content, 0, $paragraph_position + $offset) . $inserted_code . mb_substr ($content, $paragraph_position + $offset);
              $offset += mb_strlen ($inserted_code);
            }
        } else {
            if ($this->get_direction_type() == AI_DIRECTION_FROM_BOTTOM) {
              $content = substr_replace ($content, $inserted_code, $paragraph_position, 0);
            } else {
                $content = substr_replace ($content, $inserted_code, $paragraph_position + $offset, 0);
                $offset += strlen ($inserted_code);
              }
          }
      }

      $ai_last_check = AI_CHECK_NONE;  // Already logged on each insertion
      return $content;
    }

    // empty $positions
    $ai_last_check = AI_CHECK_NO_PARAGRAPHS;
    return $content;
  }

  public function after_paragraph ($content, $position_preview = false) {
    global $ai_wp_data, $ai_last_check, $special_element_tags;

    $multibyte = $ai_wp_data [AI_MBSTRING_LOADED] && get_paragraph_counting_functions() == AI_MULTIBYTE_PARAGRAPH_COUNTING_FUNCTIONS;

    $paragraph_positions = array ();
    $active_paragraph_positions = array ();

    $dummy = array ();

    if ($multibyte) {
      $last_content_position = mb_strlen ($content) - 1;
    } else {
        $last_content_position = strlen ($content) - 1;
      }

    $paragraph_tags = trim ($this->get_paragraph_tags());
    if ($paragraph_tags == '') return $content;

    $paragraph_end_strings = explode (",", $paragraph_tags);

    $ai_last_check = AI_CHECK_PARAGRAPH_TAGS;
    if (count ($paragraph_end_strings) == 0) return $content;

    get_paragraph_end_positions ($content, $multibyte, $dummy, $paragraph_end_strings, $paragraph_positions, $active_paragraph_positions);

    // Nothing to do
    $ai_last_check = AI_CHECK_PARAGRAPHS_WITH_TAGS;
    if (array_sum ($active_paragraph_positions) == 0) return $content;


    sort ($paragraph_positions);
    ksort ($active_paragraph_positions);
    $new_active_paragraph_positions = array ();
    foreach ($active_paragraph_positions as $active_paragraph_position) {
      $new_active_paragraph_positions [] = $active_paragraph_position;
    }
    $active_paragraph_positions = $new_active_paragraph_positions;


    if (!$this->get_count_inside_blockquote ()) {

      $special_element_offsets = array ();

      foreach ($special_element_tags as $special_element_tag) {
        preg_match_all ("/<\/?$special_element_tag/i", $content, $special_elements, PREG_OFFSET_CAPTURE);

        $nesting = array ();
        $special_elements = $special_elements [0];
        foreach ($special_elements as $index => $special_element) {
          if (isset ($special_elements [$index + 1][0])) {
            $tag1 = strtolower ($special_element [0]);
            $tag2 = strtolower ($special_elements [$index + 1][0]);

            $start_offset = $special_element [1];
            $nesting_ended = false;

            $tag1_start = $tag1 == "<$special_element_tag";
            $tag2_start = $tag2 == "<$special_element_tag";
            $tag1_end   = $tag1 == "</$special_element_tag";
            $tag2_end   = $tag2 == "</$special_element_tag";

            if ($tag1_start && $tag2_start) {
              array_push ($nesting, $start_offset);
              continue;
            }
            elseif ($tag1_end && $tag2_end) {
              $start_offset = array_pop ($nesting);
              if (count ($nesting) == 0) $nesting_ended = true;
            }

            if (count ($nesting) != 0) continue;

            if (($nesting_ended || $tag1_start) && $tag2_end) {

              if ($multibyte) {
                $special_element_offsets []= array (mb_strlen (substr ($content, 0, $start_offset)), mb_strlen (substr ($content, 0, $special_elements [$index + 1][1])));
              } else {
                  $special_element_offsets []= array ($start_offset, $special_elements [$index + 1][1]);
                }
            }
          }
        }
      }

      if (count ($special_element_offsets) != 0) {

        $inside_special_element = array ();

        foreach ($special_element_offsets as $special_element_offset) {
          foreach ($paragraph_positions as $paragraph_position) {
            if ($paragraph_position >= $special_element_offset [0] && $paragraph_position <= $special_element_offset [1]) $inside_special_element [] = $paragraph_position;
          }
        }

        foreach ($paragraph_positions as $index => $paragraph_position) {
          if (in_array ($paragraph_position, $inside_special_element)) $active_paragraph_positions [$index] = 0;
        }
      }

      $ai_last_check = AI_CHECK_PARAGRAPHS_AFTER_NO_COUNTING_INSIDE;
      if (array_sum ($active_paragraph_positions) == 0) return $content;
    }


    $paragraph_min_words = intval ($this->get_minimum_paragraph_words());
    $paragraph_max_words = intval ($this->get_maximum_paragraph_words());

    if ($paragraph_min_words != 0 || $paragraph_max_words != 0) {

      if (!isset ($paragraph_start_positions)) {
        $paragraph_start_positions = array ();
        get_paragraph_start_positions ($content, $multibyte, $paragraph_positions, $paragraph_end_strings, $paragraph_start_positions, $dummy);
      }

      foreach ($paragraph_positions as $index => $paragraph_position) {

        if ($active_paragraph_positions [$index] == 0) continue;

        if ($multibyte) {
          $paragraph_code = mb_substr ($content, $paragraph_start_positions [$index], $paragraph_position - $paragraph_start_positions [$index] + 1);
        } else {
            $paragraph_code = substr ($content, $paragraph_start_positions [$index], $paragraph_position - $paragraph_start_positions [$index] + 1);
          }

        if (!$this->check_number_of_words_in_paragraph ($paragraph_code, $paragraph_min_words, $paragraph_max_words)) $active_paragraph_positions [$index] = 0;
      }
    }


    // Nothing to do
    $ai_last_check = AI_CHECK_PARAGRAPHS_AFTER_MIN_MAX_WORDS;
    if (array_sum ($active_paragraph_positions) == 0) return $content;


    $paragraph_texts = explode (",", html_entity_decode ($this->get_paragraph_text()));
    if ($this->get_paragraph_text() != "" && count ($paragraph_texts) != 0) {

      if (!isset ($paragraph_start_positions)) {
        $paragraph_start_positions = array ();
        get_paragraph_start_positions ($content, $multibyte, $paragraph_positions, $paragraph_end_strings, $paragraph_start_positions, $dummy);
      }

      $paragraph_text_type = $this->get_paragraph_text_type ();

      foreach ($paragraph_positions as $index => $paragraph_position) {

        if ($active_paragraph_positions [$index] == 0) continue;

        if ($multibyte) {
          $paragraph_code = mb_substr ($content, $paragraph_start_positions [$index], $paragraph_position - $paragraph_start_positions [$index] + 1);
        } else {
            $paragraph_code = substr ($content, $paragraph_start_positions [$index], $paragraph_position - $paragraph_start_positions [$index] + 1);
          }

        if ($paragraph_text_type == AI_CONTAIN) {
          $found = true;
          foreach ($paragraph_texts as $paragraph_text) {
            if (trim ($paragraph_text) == '') continue;

            if ($multibyte) {
              if (mb_stripos ($paragraph_code, trim ($paragraph_text)) === false) {
                $found = false;
                break;
              }
            } else {
                if (stripos ($paragraph_code, trim ($paragraph_text)) === false) {
                  $found = false;
                  break;
                }
              }

          }
          if (!$found) $filtered_paragraph_positions [] = $active_paragraph_positions [$index] = 0;
        } elseif ($paragraph_text_type == AI_DO_NOT_CONTAIN) {
            $found = false;
            foreach ($paragraph_texts as $paragraph_text) {
              if (trim ($paragraph_text) == '') continue;

              if ($multibyte) {
                if (mb_stripos ($paragraph_code, trim ($paragraph_text)) !== false) {
                  $found = true;
                  break;
                }
              } else {
                  if (stripos ($paragraph_code, trim ($paragraph_text)) !== false) {
                    $found = true;
                    break;
                  }
                }

            }
            if ($found) $active_paragraph_positions [$index] = 0;
          }
      }
    }


    // Nothing to do
    $ai_last_check = AI_CHECK_PARAGRAPHS_AFTER_TEXT;
    if (array_sum ($active_paragraph_positions) == 0) return $content;

    $position_text = trim ($this->get_paragraph_number());


    // Prepare $paragraph_start_positions
    if (!$position_preview && $position_text == '') {
      if (!isset ($paragraph_start_positions)) {
        $paragraph_start_positions = array ();
        get_paragraph_start_positions ($content, $multibyte, $paragraph_positions, $paragraph_end_strings, $paragraph_start_positions, $dummy);
      }

      $filtered_paragraph_positions = array ();
      // Use $paragraph_positions for counting as it is checked for consistency
      foreach ($paragraph_positions as $index => $paragraph_position) {
        if ($active_paragraph_positions [$index]) $filtered_paragraph_positions [] = $paragraph_start_positions [$index];
      }
      $paragraph_start_positions = $filtered_paragraph_positions;

      if ($this->get_direction_type() == AI_DIRECTION_FROM_BOTTOM) {
        $paragraph_start_positions = array_reverse ($paragraph_start_positions);
      }
    }


    $filtered_paragraph_positions = array ();
    foreach ($paragraph_positions as $index => $paragraph_position) {
      if ($active_paragraph_positions [$index]) $filtered_paragraph_positions [] = $paragraph_position;
    }
    $paragraph_positions = $filtered_paragraph_positions;


    if ($this->get_direction_type() == AI_DIRECTION_FROM_BOTTOM) {
      $paragraph_positions = array_reverse ($paragraph_positions);
    }


//  $positions contains indexes in $paragraph_positions
    $positions = array ();

    if (!$position_preview) {
      $position = $position_text;

      if (is_numeric ($position)) {
        if ($position > 0 && $position < 1) {
          $position = intval ($position * (count ($paragraph_positions) - 1) + 0.5);
        }
        elseif ($position > - 1 && $position < 0) {
          $position = intval (count ($paragraph_positions) + $position * (count ($paragraph_positions) - 1) - 0.5);
        }
        elseif ($position <= - 1) {
          $position = count ($paragraph_positions) + $position;
        }
        elseif ($position == 0) {
          $position = mt_rand (0, count ($paragraph_positions) - 1);
        }
        else $position --;

        $positions = array ($position);
      }
      elseif (strpos ($position_text, ',') !== false) {
        $new_positions = array ();
        $positions = explode (',', str_replace (' ', '', $position_text));
        foreach ($positions as $index => $position) {
          if (isset ($position [0]) && $position [0] == '%') {
            unset ($positions [$index]);
            $mod_value = substr ($position, 1);
            if (is_numeric ($mod_value) && $mod_value > 0) {
              foreach ($paragraph_positions as $index => $paragraph_position) {
                if (($index + 1) % $mod_value == 0) $new_positions []= $index;
              }
            }
          }
          elseif ($position > 0 && $position < 1) {
            $positions [$index] = intval ($position * (count ($paragraph_positions) - 1) + 0.5);
          }
          elseif ($position > - 1 && $position < 0) {
            $positions [$index] = intval (count ($paragraph_positions) + $position * (count ($paragraph_positions) - 1) - 0.5);
          }
          elseif ($position <= - 1) {
            $positions [$index] = count ($paragraph_positions) + $position;
          }
          elseif ($position == 0) {
            $positions [$index] = mt_rand (0, count ($paragraph_positions) - 1);
          }
          else $positions [$index] = $position - 1;
        }
        $positions = array_unique (array_merge ($positions, $new_positions));
        sort ($positions);
      }
      elseif (isset ($position_text [0]) && $position_text [0] == '%') {
        $mod_value = substr ($position_text, 1);
        if (is_numeric ($mod_value) && $mod_value > 0) {
          foreach ($paragraph_positions as $index => $paragraph_position) {
            if (($index + 1) % $mod_value == 0) $positions []= $index;
          }
        }
      }
      elseif ($position_text == '') {
        $positions = array ();

        $min_words_above = $this->get_minimum_words_above ();
        if (!empty ($min_words_above)) {
          $words_above = 0;
          foreach ($paragraph_positions as $index => $paragraph_position) {

            if ($this->get_direction_type() == AI_DIRECTION_FROM_BOTTOM) {
              if ($words_above >= $min_words_above) {
                $positions []= $index;
                $words_above = 0;
              }
            }

            if ($multibyte) {
              $paragraph_code = mb_substr ($content, $paragraph_start_positions [$index], $paragraph_position - $paragraph_start_positions [$index] + 1);
            } else {
                $paragraph_code = substr ($content, $paragraph_start_positions [$index], $paragraph_position - $paragraph_start_positions [$index] + 1);
              }

            $words_above += number_of_words ($paragraph_code);

            if ($this->get_direction_type() != AI_DIRECTION_FROM_BOTTOM) {
              if ($words_above >= $min_words_above) {
                $positions []= $index;
                $words_above = 0;
              }
            }

          }
        } else
        foreach ($paragraph_positions as $index => $paragraph_position) {
          $positions []= $index;
        }

        $filter_settings = trim (str_replace (' ', '', $this->get_call_filter()));
        if (!empty ($filter_settings)) {
          if ($this->get_filter_type() == AI_FILTER_PARAGRAPHS) {
            $filter_values = array ();
            if (strpos ($filter_settings, ",") !== false) {
              $filter_values = explode (",", $filter_settings);
            } else $filter_values []= $filter_settings;

            $inverted_filter = $this->get_inverted_filter();
            $filtered_positions = array ();

            foreach ($positions as $index => $position) {
              $insert = false;
              if (in_array ($index + 1, $filter_values)) {
                $insert = true;
              } else {
                  foreach ($filter_values as $filter_value) {
                    $filter_value = trim ($filter_value);
                    if ($filter_value [0] == '%') {
                      $mod_value = substr ($filter_value, 1);
                      if (is_numeric ($mod_value) && $mod_value > 0) {
                        if (($index + 1) % $mod_value == 0) $insert = true;
                      }
                    }
                  }
                }
              if ($insert xor $inverted_filter) $filtered_positions []= $position;
            }
            $positions = $filtered_positions;
          }
//          else $positions = array ();
        }
      }
    }

    $debug_processing = ($ai_wp_data [AI_WP_DEBUGGING] & AI_DEBUG_PROCESSING) != 0;

    if (!empty ($positions)) {
      $avoid_paragraphs_above = intval ($this->get_avoid_paragraphs_above());
      $avoid_paragraphs_below = intval ($this->get_avoid_paragraphs_below());

      $avoid_text_above = $this->get_avoid_text_above();
      $avoid_text_below = $this->get_avoid_text_below();
      $avoid_paragraph_texts_above = explode (",", html_entity_decode (trim ($avoid_text_above)));
      $avoid_paragraph_texts_below = explode (",", html_entity_decode (trim ($avoid_text_below)));

      $direction = $this->get_avoid_direction();
      $max_checks = $this->get_avoid_try_limit();

      $failed_clearance_positions = array ();
      foreach ($positions as $position_index => $position) {

        if (($avoid_paragraphs_above != 0 || $avoid_paragraphs_below != 0) && count ($paragraph_positions) > $position) {

          if ($debug_processing && $this->number != 0) ai_log ('BLOCK ' . $this->number . ' CLEARANCE CHECK POSITION ' . ($position + 1));

          $checks = $max_checks;
          $saved_position = $position;
          do {
            $found_above = false;
            if ($avoid_paragraphs_above != 0 && $avoid_text_above != "" && is_array ($avoid_paragraph_texts_above) && count ($avoid_paragraph_texts_above) != 0) {
              $paragraph_position_above = $position - $avoid_paragraphs_above;
              if ($paragraph_position_above <= 0)
                $content_position_above = 0; else
                  $content_position_above = $paragraph_positions [$paragraph_position_above] + 1;

              if ($multibyte) {
                $paragraph_code = mb_substr ($content, $content_position_above, $paragraph_positions [$position] - $content_position_above);
              } else {
                  $paragraph_code = substr ($content, $content_position_above, $paragraph_positions [$position] - $content_position_above);
                }

              foreach ($avoid_paragraph_texts_above as $paragraph_text_above) {
                if (trim ($paragraph_text_above) == '') continue;

                if ($multibyte) {
                  if (mb_stripos ($paragraph_code, trim ($paragraph_text_above)) !== false) {
                    $found_above = true;
                    break;
                  }
                } else {
                    if (stripos ($paragraph_code, trim ($paragraph_text_above)) !== false) {
                      $found_above = true;
                      break;
                    }
                  }

              }
            }

            $found_below = false;
            if ($avoid_paragraphs_below != 0 && $position != count ($paragraph_positions) - 1 && $avoid_text_below != "" && is_array ($avoid_paragraph_texts_below) && count ($avoid_paragraph_texts_below) != 0) {
              $paragraph_position_below = $position + $avoid_paragraphs_below;

              if ($multibyte) {
                if ($paragraph_position_below > count ($paragraph_positions) - 1) $paragraph_position_below = count ($paragraph_positions) - 1;
                  $paragraph_code = mb_substr ($content, $paragraph_positions [$position] + 1, $paragraph_positions [$paragraph_position_below] - $paragraph_positions [$position]);
              } else {
                  if ($paragraph_position_below > count ($paragraph_positions) - 1) $paragraph_position_below = count ($paragraph_positions) - 1;
                    $paragraph_code = substr ($content, $paragraph_positions [$position] + 1, $paragraph_positions [$paragraph_position_below] - $paragraph_positions [$position]);
                }

              foreach ($avoid_paragraph_texts_below as $paragraph_text_below) {
                if (trim ($paragraph_text_below) == '') continue;

                if ($multibyte) {
                  if (mb_stripos ($paragraph_code, trim ($paragraph_text_below)) !== false) {
                    $found_below = true;
                    break;
                  }
                } else {
                    if (stripos ($paragraph_code, trim ($paragraph_text_below)) !== false) {
                      $found_below = true;
                      break;
                    }
                  }

              }
            }


    //        echo "position: $position = after #", $position + 1, "<br />\n";
    //        echo "checks: $checks<br />\n";
    //        echo "direction: $direction<br />\n";
    //        if ($found_above)
    //        echo "found_above<br />\n";
    //        if ($found_below)
    //        echo "found_below<br />\n";
    //        echo "=================<br />\n";


            if ($found_above || $found_below) {

              if ($debug_processing && $this->number != 0) ai_log ('BLOCK ' . $this->number . ' CLEARANCE CHECK POSITION ' . ($position + 1) . ' FAILED');

              $ai_last_check = AI_CHECK_DO_NOT_INSERT;
              if ($this->get_avoid_action() == AI_DO_NOT_INSERT) {
                $failed_clearance_positions [$positions [$position_index]] = $ai_last_check;
                $positions [$position_index] = - 1;
                break;
              }

              switch ($direction) {
                case AI_ABOVE: // Try above
                  $ai_last_check = AI_CHECK_AD_ABOVE;
                  // Already at the top - do not insert
                  if ($position == 0) {
                    $failed_clearance_positions [$positions [$position_index]] = $ai_last_check;
                    $positions [$position_index] = - 1;
                    break 2;
                  }

                  $position --;
                  break;
                case AI_BELOW: // Try below
                  $ai_last_check = AI_CHECK_AD_BELOW;
                  // Already at the bottom - do not insert
                  if ($position >= count ($paragraph_positions) - 1) {
                    $failed_clearance_positions [$positions [$position_index]] = $ai_last_check;
                    $positions [$position_index] = - 1;
                    break 2;
                  }

                  $position ++;
                  break;
                case AI_ABOVE_AND_THEN_BELOW: // Try first above and then below
                  if ($position == 0 || $checks == 0) {
                    // Try below
                    $direction = AI_BELOW;
                    $checks = $max_checks;
                    $position = $saved_position;
                    $ai_last_check = AI_CHECK_AD_BELOW;
                    // Already at the bottom - do not insert
                    if ($position >= count ($paragraph_positions) - 1) {
                      $failed_clearance_positions [$positions [$position_index]] = $ai_last_check;
                      $positions [$position_index] = - 1;
                      break 2;
                    }

                    $position ++;
                  } else $position --;
                  break;
                case AI_BELOW_AND_THEN_ABOVE: // Try first below and then above
                  if ($position >= count ($paragraph_positions) - 1 || $checks == 0) {
                    // Try above
                    $direction = AI_ABOVE;
                    $checks = $max_checks;
                    $position = $saved_position;
                    $ai_last_check = AI_CHECK_AD_ABOVE;
                    // Already at the top - do not insert
                    if ($position == 0) {
                      $failed_clearance_positions [$positions [$position_index]] = $ai_last_check;
                      $positions [$position_index] = - 1;
                      break 2;
                    }

                    $position --;
                  } else $position ++;
                  break;
              }
            } else {
                // Text not found - insert
                $positions [$position_index] = $position;
                break;
              }

            // Try next position
            if ($checks <= 0) {
              // Suitable position not found - do not insert
              $failed_clearance_positions [$positions [$position_index]] = $ai_last_check;
              $positions [$position_index] = - 1;
              break;
            }

            $checks --;
          } while (true);
        }

        // Nothing to do
        $ai_last_check = AI_CHECK_PARAGRAPHS_AFTER_CLEARANCE;
        if (count ($paragraph_positions) == 0) return $content;
      }
    }


    if ($position_preview || !empty ($positions)) {
      $offset = 0;
      if (!empty ($positions)) $ai_last_check = AI_CHECK_PARAGRAPH_NUMBER;

      $real_positions = array ();
      foreach ($positions as $position_index) $real_positions []= $position_index >= 0 ? $position_index + 1 : '*';
      if ($debug_processing && $this->number != 0 && count ($real_positions) != 0) ai_log ('BLOCK ' . $this->number . ' INSERTION POSITIONS: ' . implode (', ', $real_positions));

      $min_paragraphs = intval ($this->get_paragraph_number_minimum());
      $max_page_blocks_enabled = $this->get_max_page_blocks_enabled ();

      foreach ($paragraph_positions as $counter => $paragraph_position) {
        if ($position_preview) $inserted_code = "[[AI_AP".($counter + 1)."]]";
        elseif (!empty ($positions) && in_array ($counter, $positions) && $this->check_block_counter ()) {

          $inserted = false;

          $ai_last_check = AI_CHECK_PARAGRAPHS_MIN_NUMBER;
          if (count ($paragraph_positions) >= $min_paragraphs) {

            $ai_last_check = AI_CHECK_MAX_PAGE_BLOCKS;
            if (!$max_page_blocks_enabled || $ai_wp_data [AI_PAGE_BLOCKS] < get_max_page_blocks ()) {
              $this->increment_block_counter ();

              // Increment page block counter
              if ($max_page_blocks_enabled) $ai_wp_data [AI_PAGE_BLOCKS] ++;

              $ai_last_check = AI_CHECK_DEBUG_NO_INSERTION;
              if (!$this->get_debug_disable_insertion ()) {
                $inserted_code = $this->get_code_for_serverside_insertion ();
                $ai_last_check = AI_CHECK_INSERTED;
                $this->clear_code_cache ();
                $inserted = true;
              }
            }
          }

          if ($debug_processing) ai_log (ai_log_block_status ($this->number, $ai_last_check));

          if (!$inserted) continue;
        }
        else {
          if ($debug_processing && isset ($failed_clearance_positions [$counter])) ai_log (ai_log_block_status ($this->number, $failed_clearance_positions [$counter]));
          continue;
        }

        if ($multibyte) {
          if ($this->get_direction_type() == AI_DIRECTION_FROM_BOTTOM) {
            $content = mb_substr ($content, 0, $paragraph_position + 1) . $inserted_code . mb_substr ($content, $paragraph_position + 1);
          } else {
              $content = mb_substr ($content, 0, $paragraph_position + $offset + 1) . $inserted_code . mb_substr ($content, $paragraph_position + $offset + 1);
              $offset += mb_strlen ($inserted_code);
            }
        } else {
            if ($this->get_direction_type() == AI_DIRECTION_FROM_BOTTOM) {
              $content = substr_replace ($content, $inserted_code, $paragraph_position + 1, 0);
            } else {
                $content = substr_replace ($content, $inserted_code, $paragraph_position + $offset + 1, 0);
                $offset += strlen ($inserted_code);
              }
          }
      }

      $ai_last_check = AI_CHECK_NONE;  // Already logged on each insertion
      return $content;
    }

    // empty $positions
    $ai_last_check = AI_CHECK_NO_PARAGRAPHS;
    return $content;
  }


//  Deprecated
  function manual ($content){

    if (preg_match_all("/{adinserter (.+?)}/", $content, $tags)){

      $block_class_name = get_block_class_name ();
      $viewport_classes = $this->get_viewport_classes ();
      if ($block_class_name != '' || $viewport_classes != '') {
        if ($block_class_name =='') $viewport_classes = trim ($viewport_classes);
        $class = " class='" . ($block_class_name != '' ? $block_class_name . " " . $block_class_name . "-" . $this->number : '') . $viewport_classes ."'";
      } else $class = '';

//      $display_for_devices = $this->get_display_for_devices ();

      foreach ($tags [1] as $tag) {
         $ad_tag = strtolower (trim ($tag));
         $ad_name = strtolower (trim ($this->get_ad_name()));
         if ($ad_tag == $ad_name || $ad_tag == $this->number) {
          if ($this->get_alignment_type() == AI_ALIGNMENT_NO_WRAPPING) $ad_code = $this->ai_getProcessedCode (); else
            $ad_code = "<div" . $class . " style='" . $this->get_alignment_style() . "'>" . $this->ai_getProcessedCode () . "</div>";
          $content = preg_replace ("/{adinserter " . $tag . "}/", $ad_code, $content);
         }
      }
    }

    return $content;
  }

//  Deprecated
  function display_disabled ($content){

    $ad_name = $this->get_ad_name();

    if (preg_match ("/<!-- +Ad +Inserter +Ad +".($this->number)." +Disabled +-->/i", $content)) return true;

    if (preg_match ("/<!-- +disable +adinserter +\* +-->/i", $content)) return true;

    if (preg_match ("/<!-- +disable +adinserter +".($this->number)." +-->/i", $content)) return true;

    if (strpos ($content, "<!-- disable adinserter " . $ad_name . " -->") != false) return true;

    return false;
  }

  function check_category () {
    global $ai_wp_data;

    $categories = trim (strtolower ($this->get_ad_block_cat()));
    $cat_type = $this->get_ad_block_cat_type();

    $wp_categories = get_the_category ();

    if ($cat_type == AI_BLACK_LIST) {

      if ($categories == AD_EMPTY_DATA) return true;

      $cats_listed = explode (",", $categories);

      foreach ($wp_categories as $wp_category) {

        foreach ($cats_listed as $cat_disabled){

          $cat_disabled = trim ($cat_disabled);

          $wp_category_name = strtolower ($wp_category->cat_name);
          $wp_category_slug = strtolower ($wp_category->slug);

          if ($wp_category_name == $cat_disabled || $wp_category_slug == $cat_disabled) {
            return false;
          } else {
            }
        }
      }
      return true;

    } else {

        if ($categories == AD_EMPTY_DATA) return false;

        $cats_listed = explode (",", $categories);

        foreach ($wp_categories as $wp_category) {

          foreach ($cats_listed as $cat_enabled) {

            $cat_enabled = trim ($cat_enabled);

            $wp_category_name = strtolower ($wp_category->cat_name);
            $wp_category_slug = strtolower ($wp_category->slug);

            if ($wp_category_name == $cat_enabled || $wp_category_slug == $cat_enabled) {
              return true;
            } else {
              }
          }
        }
        return false;
      }
  }

  function check_tag () {

    $tags = $this->get_ad_block_tag();
    $tag_type = $this->get_ad_block_tag_type();

//    $tags = trim (strtolower ($tags));
    $tags = trim ($tags);
    $tags_listed = explode (",", $tags);
    foreach ($tags_listed as $index => $tag_listed) {
      $tags_listed [$index] = trim ($tag_listed);
    }
    $has_any_of_the_given_tags = has_tag ($tags_listed);

    if ($tag_type == AI_BLACK_LIST) {

      if ($tags == AD_EMPTY_DATA) return true;

      if (is_tag()) {
        foreach ($tags_listed as $tag_listed) {
          if (is_tag ($tag_listed)) return false;
        }
        return true;
      }

      return !$has_any_of_the_given_tags;

    } else {

        if ($tags == AD_EMPTY_DATA) return false;

        if (is_tag()) {
          foreach ($tags_listed as $tag_listed) {
            if (is_tag ($tag_listed)) return true;
          }
          return false;
        }

        return $has_any_of_the_given_tags;
      }
  }

  function check_taxonomy () {
    global $ai_wp_data;

    $taxonomies = trim (strtolower ($this->get_ad_block_taxonomy()));
    $taxonomy_type = $this->get_ad_block_taxonomy_type();

    if ($taxonomy_type == AI_BLACK_LIST) {

      if ($taxonomies == AD_EMPTY_DATA) return true;

      $taxonomies_listed = explode (",", $taxonomies);
      $taxonomy_names = get_post_taxonomies ();

      foreach ($taxonomies_listed as $taxonomy_disabled) {
        $taxonomy_disabled = trim ($taxonomy_disabled);

        if (strpos ($taxonomy_disabled, 'user:') === 0) {
          $current_user = wp_get_current_user();
          $terms = explode (':', $taxonomy_disabled);
          if ($terms [1] == $current_user->user_login) return false;
        }
        elseif (strpos ($taxonomy_disabled, 'author:') === 0) {
          if ($ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_POST || $ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_STATIC)
            $current_author = get_the_author_meta ('user_login'); else
              $current_author = '';
          $terms = explode (':', $taxonomy_disabled);
          if ($terms [1] == $current_author) return false;
        }
        elseif (strpos ($taxonomy_disabled, 'user-role:') === 0) {
          $current_user = wp_get_current_user();
          $terms = explode (':', $taxonomy_disabled);
          foreach (wp_get_current_user()->roles as $role) {
            if ($terms [1] == $role) return false;
          }
        }
        elseif (strpos ($taxonomy_disabled, 'post-type:') === 0) {
          $post_type = get_post_type ();
          $terms = explode (':', $taxonomy_disabled);
          if ($terms [1] == $post_type) return false;
        }

        foreach ($taxonomy_names as $taxonomy_name) {
          $terms = get_the_terms (0, $taxonomy_name);
          if (is_array ($terms)) {
            foreach ($terms as $term) {
              $post_term_name = strtolower ($term->name);
              $post_term_slug = strtolower ($term->slug);
              $post_taxonomy  = strtolower ($term->taxonomy);

              if ($post_term_name == $taxonomy_disabled || $post_term_slug == $taxonomy_disabled) return false;

              $post_taxonomy  = strtolower ($term->taxonomy);
              if ($post_taxonomy == $taxonomy_disabled) return false;

              if ($post_taxonomy . ':' . $post_term_slug == $taxonomy_disabled) return false;
            }
          }
        }
      }

      return true;

    } else {

        if ($taxonomies == AD_EMPTY_DATA) return false;

        $taxonomies_listed = explode (",", $taxonomies);
        $taxonomy_names = get_post_taxonomies ();

        foreach ($taxonomies_listed as $taxonomy_enabled) {
          $taxonomy_enabled = trim ($taxonomy_enabled);

          if (strpos ($taxonomy_enabled, 'user:') === 0) {
            $current_user = wp_get_current_user();
            $terms = explode (':', $taxonomy_enabled);
            if ($terms [1] == $current_user->user_login) return true;
          }
          elseif (strpos ($taxonomy_enabled, 'author:') === 0) {
            if ($ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_POST || $ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_STATIC)
              $current_author = get_the_author_meta ('user_login'); else
                $current_author = '';
            $terms = explode (':', $taxonomy_enabled);
            if ($terms [1] == $current_author) return true;
          }
          elseif (strpos ($taxonomy_enabled, 'user-role:') === 0) {
            $current_user = wp_get_current_user();
            $terms = explode (':', $taxonomy_enabled);
            foreach (wp_get_current_user()->roles as $role) {
              if ($terms [1] == $role) return true;
            }
          }
          elseif (strpos ($taxonomy_enabled, 'post-type:') === 0) {
            $post_type = get_post_type ();
            $terms = explode (':', $taxonomy_enabled);
            if ($terms [1] == $post_type) return true;
          }

          foreach ($taxonomy_names as $taxonomy_name) {
            $terms = get_the_terms (0, $taxonomy_name);
            if (is_array ($terms)) {
              foreach ($terms as $term) {
                $post_term_name = strtolower ($term->name);
                $post_term_slug = strtolower ($term->slug);
                $post_taxonomy  = strtolower ($term->taxonomy);

                if ($post_term_name == $taxonomy_enabled || $post_term_slug == $taxonomy_enabled) return true;

                $post_taxonomy  = strtolower ($term->taxonomy);
                if ($post_taxonomy == $taxonomy_enabled) return true;

                if ($post_taxonomy . ':' . $post_term_slug == $taxonomy_enabled) return true;
              }
            }
          }
        }

        return false;
      }
  }

  function check_id () {
    global $ai_wp_data;

    $page_id = get_the_ID();

    $ids = trim ($this->get_id_list());
    $id_type = $this->get_id_list_type();

    if ($id_type == AI_BLACK_LIST) $return = false; else $return = true;

    if ($ids == AD_EMPTY_DATA || $page_id === false) {
      return !$return;
    }

    $ids_listed = explode (",", $ids);
    foreach ($ids_listed as $index => $id_listed) {
      if (trim ($id_listed) == "") unset ($ids_listed [$index]); else
        $ids_listed [$index] = trim ($id_listed);
    }

//    print_r ($ids_listed);
//    echo "<br />\n";
//    echo ' page id: ' . $page_id, "<br />\n";
//    echo ' listed ids: ' . $ids, "\n";
//    echo "<br />\n";

    if (in_array ($page_id, $ids_listed)) return $return;

    return !$return;
  }

  function check_url () {
    global $ai_wp_data;

    $page_url = $ai_wp_data [AI_WP_URL];

    $urls = trim ($this->get_ad_url_list());
    $url_type = $this->get_ad_url_list_type();

    if ($url_type == AI_BLACK_LIST) $return = false; else $return = true;

    if ($urls == AD_EMPTY_DATA) return !$return;

    $list_separator = ',';
    if (strpos ($urls, ' ') !== false && strpos ($urls, ',') === false) $list_separator = ' ';

    $urls_listed = explode ($list_separator, $urls);
    foreach ($urls_listed as $index => $url_listed) {
      if (trim ($url_listed) == "") unset ($urls_listed [$index]); else
        $urls_listed [$index] = trim ($url_listed);
    }

//    print_r ($urls_listed);
//    echo "<br />\n";
//    echo ' page url: ' . $page_url, "<br />\n";
//    echo ' listed urls: ' . $urls, "\n";
//    echo "<br />\n";

    foreach ($urls_listed as $url_listed) {
      if ($url_listed == '*') return $return;

      if ($url_listed [0] == '*') {
        if ($url_listed [strlen ($url_listed) - 1] == '*') {
          $url_listed = substr ($url_listed, 1, strlen ($url_listed) - 2);
          if (strpos ($page_url, $url_listed) !== false) return $return;
        } else {
            $url_listed = substr ($url_listed, 1);
            if (substr ($page_url, - strlen ($url_listed)) == $url_listed) return $return;
          }
      }
      elseif ($url_listed [strlen ($url_listed) - 1] == '*') {
        $url_listed = substr ($url_listed, 0, strlen ($url_listed) - 1);
        if (strpos ($page_url, $url_listed) === 0) return $return;
      }
      elseif ($url_listed == $page_url) return $return;
    }
    return !$return;
  }

  function check_scheduling () {

    switch ($this->get_scheduling()) {
      case AI_SCHEDULING_OFF:
        return true;
        break;

      case AI_SCHEDULING_DELAY_FOR:
        $after_days = trim ($this->get_ad_after_day());
        if ($after_days == '') return true;
        $after_days = intval ($after_days);
        if ($after_days == AD_ZERO) return true;

        $post_date = get_the_date ('U');
        if ($post_date === false) return true;

        return (date ('U', current_time ('timestamp')) >= $post_date + $after_days * 86400);
        break;

      case AI_SCHEDULING_INSERT_ONLY_FOR:
        $after_days = trim ($this->get_ad_after_day());
        if ($after_days == '') return false;
        $after_days = intval ($after_days);
        if ($after_days == AD_ZERO) return false;

        $post_date = get_the_date ('U');
        if ($post_date === false) return false;

        return (date ('U', current_time ('timestamp')) < $post_date + $after_days * 86400);
        break;

      case AI_SCHEDULING_BETWEEN_DATES:
        if (!function_exists ('ai_scheduling_options')) return true;

        $current_time = current_time ('timestamp');
        $start_date   = strtotime ($this->get_schedule_start_date(), $current_time);
        $end_date     = strtotime ($this->get_schedule_end_date(), $current_time);

        $insertion_enabled = $current_time >= $start_date && $current_time < $end_date;

        if (!$insertion_enabled) {
          $fallback = intval ($this->get_fallback());
          if ($fallback != 0 && $fallback <= AD_INSERTER_BLOCKS) {
            $this->fallback = $fallback;
            return true;
          }
        }

        return ($insertion_enabled);
        break;

      case AI_SCHEDULING_OUTSIDE_DATES:
        if (!function_exists ('ai_scheduling_options')) return true;

        $current_time = current_time ('timestamp');
        $start_date   = strtotime ($this->get_schedule_start_date(), $current_time);
        $end_date     = strtotime ($this->get_schedule_end_date(), $current_time);

        $insertion_enabled = $current_time < $start_date || $current_time >= $end_date;

        if (!$insertion_enabled) {
          $fallback = intval ($this->get_fallback());
          if ($fallback != 0 && $fallback <= AD_INSERTER_BLOCKS) {
            $this->fallback = $fallback;
            return true;
          }
        }

        return ($insertion_enabled);
        break;

      case AI_SCHEDULING_PUBLISHED_BETWEEN_DATES:
        if (!function_exists ('ai_scheduling_options')) return true;

        $post_date = get_the_date ('U');
        if ($post_date === false) return false;

        $current_time = current_time ('timestamp');
        $start_date   = strtotime ($this->get_schedule_start_date(), $current_time);
        $end_date     = strtotime ($this->get_schedule_end_date(), $current_time);

        $insertion_enabled = $post_date >= $start_date && $post_date < $end_date;

        if (!$insertion_enabled) {
          $fallback = intval ($this->get_fallback());
          if ($fallback != 0 && $fallback <= AD_INSERTER_BLOCKS) {
            $this->fallback = $fallback;
            return true;
          }
        }

        return ($insertion_enabled);
        break;

      case AI_SCHEDULING_PUBLISHED_OUTSIDE_DATES:
        if (!function_exists ('ai_scheduling_options')) return true;

        $post_date = get_the_date ('U');
        if ($post_date === false) return true;

        $current_time = current_time ('timestamp');
        $start_date   = strtotime ($this->get_schedule_start_date(), $current_time);
        $end_date     = strtotime ($this->get_schedule_end_date(), $current_time);

//        echo "<pre>";
//        echo "current_time ", $current_time, " ", date ("Y-m-d H:i:s", $current_time), "<br />";
//        echo "start_date   ", $start_date, " ", date ("Y-m-d H:i:s", $start_date), "<br />";
//        echo "end_date     ", $end_date, " ", date ("Y-m-d H:i:s", $end_date), "<br />";
//        echo "post_date    ", $post_date, " ", date ("Y-m-d H:i:s", $post_date), "<br />";
//        echo "</pre>";

        $insertion_enabled = $post_date < $start_date || $post_date >= $end_date;

        if (!$insertion_enabled) {
          $fallback = intval ($this->get_fallback());
          if ($fallback != 0 && $fallback <= AD_INSERTER_BLOCKS) {
            $this->fallback = $fallback;
            return true;
          }
        }

        return ($insertion_enabled);
        break;

      default:
        return true;
        break;
    }
  }

  function check_referer () {
    return check_check_referer_list ($this->get_ad_domain_list(), $this->get_ad_domain_list_type() == AI_WHITE_LIST);
  }

  function check_number_of_words (&$content = null, $number_of_words = 0) {
    global $ai_last_check, $ai_wp_data;

    $minimum_words = intval ($this->get_minimum_words());
    $maximum_words = intval ($this->get_maximum_words());

    if ($minimum_words == 0 && $maximum_words == 0) return true;

//    if ($ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_POST || $ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_STATIC) {
    if ($number_of_words == 0) {
      if (!isset ($ai_wp_data [AI_WORD_COUNT])) {
        if ($content === null) {
          $content = '';
          $content_post = get_post ();
          if (isset ($content_post->post_content)) $content = $content_post->post_content;
        }

        $number_of_words = number_of_words ($content);
      } else $number_of_words = $ai_wp_data [AI_WORD_COUNT];
    }
//    } else $number_of_words = 0;

    // Cache word count only on single pages
    if ($ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_POST || $ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_STATIC)
      $ai_wp_data [AI_WORD_COUNT] = $number_of_words;

    $ai_last_check = AI_CHECK_MIN_NUMBER_OF_WORDS;
    if ($number_of_words < $minimum_words) return false;

    if ($maximum_words <= 0) $maximum_words = 1000000;

    $ai_last_check = AI_CHECK_MAX_NUMBER_OF_WORDS;
    if ($number_of_words > $maximum_words) return false;

    return true;
  }

  function check_number_of_words_in_paragraph ($content, $min, $max) {

    $number_of_words = number_of_words ($content);

    if ($max <= 0) $max = 1000000;

    if ($number_of_words < $min || $number_of_words > $max) return false;

    return true;
  }

  function check_page_types_lists_users ($ignore_page_types = false) {
    global $ai_last_check, $ai_wp_data;

    if (!$ignore_page_types) {
      if ($ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_HOMEPAGE){
         $ai_last_check = AI_CHECK_PAGE_TYPE_FRONT_PAGE;
         if (!$this->get_display_settings_home()) return false;
      }
      elseif ($ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_STATIC){
         $ai_last_check = AI_CHECK_PAGE_TYPE_STATIC_PAGE;
         if (!$this->get_display_settings_page()) return false;
      }
      elseif ($ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_POST){
         $ai_last_check = AI_CHECK_PAGE_TYPE_POST;
         if (!$this->get_display_settings_post()) return false;
      }
      elseif ($ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_CATEGORY){
         $ai_last_check = AI_CHECK_PAGE_TYPE_CATEGORY;
         if (!$this->get_display_settings_category()) return false;
      }
      elseif ($ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_SEARCH){
         $ai_last_check = AI_CHECK_PAGE_TYPE_SEARCH;
         if (!$this->get_display_settings_search()) return false;
      }
      elseif ($ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_ARCHIVE){
         $ai_last_check = AI_CHECK_PAGE_TYPE_ARCHIVE;
         if (!$this->get_display_settings_archive()) return false;
      }
      elseif ($ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_FEED){
         $ai_last_check = AI_CHECK_PAGE_TYPE_FEED;
        if (!$this->get_enable_feed()) return false;
      }
      elseif ($ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_404){
         $ai_last_check = AI_CHECK_PAGE_TYPE_404;
        if (!$this->get_enable_404()) return false;
      }
    }

    $ai_last_check = AI_CHECK_CATEGORY;
    if (!$this->check_category ()) return false;

    $ai_last_check = AI_CHECK_TAG;
    if (!$this->check_tag ()) return false;

    $ai_last_check = AI_CHECK_TAXONOMY;
    if (!$this->check_taxonomy ()) return false;

    $ai_last_check = AI_CHECK_ID;
    if (!$this->check_id ()) return false;

    $ai_last_check = AI_CHECK_URL;
    if (!$this->check_url ()) return false;

    $server_side_check = get_dynamic_blocks () == AI_DYNAMIC_BLOCKS_SERVER_SIDE || ((get_dynamic_blocks () == AI_DYNAMIC_BLOCKS_CLIENT_SIDE_SHOW || get_dynamic_blocks () == AI_DYNAMIC_BLOCKS_CLIENT_SIDE_INSERT) && $ai_wp_data [AI_WP_AMP_PAGE]);

    $ai_last_check = AI_CHECK_URL_PARAMETER;
    switch ($server_side_check) {
      case true:
        if (!check_url_parameter_and_cookie_list ($this->get_url_parameter_list(), $this->get_url_parameter_list_type() == AI_WHITE_LIST)) return false;
      default:
        $url_parameter_found = false;
        $url_parameter_list_pass = check_url_parameter_list ($this->get_url_parameter_list(), $this->get_url_parameter_list_type() == AI_WHITE_LIST, $url_parameter_found);

        if ($url_parameter_found && !$url_parameter_list_pass) return false;

        if (!$url_parameter_found) $this->client_side_cookie_check = true;
    }

    if ($server_side_check) {
      $ai_last_check = AI_CHECK_REFERER;
      if (!$this->check_referer ()) return false;
    }

    if (function_exists ('ai_check_lists')) {
      if (!ai_check_lists ($this, $server_side_check)) return false;
    }

    $ai_last_check = AI_CHECK_SCHEDULING;
    if (!$this->check_scheduling ()) return false;

    $display_for_users = $this->get_display_for_users ();

    $ai_last_check = AI_CHECK_LOGGED_IN_USER;
    if ($display_for_users == AI_DISPLAY_LOGGED_IN_USERS && ($ai_wp_data [AI_WP_USER] & AI_USER_LOGGED_IN) != AI_USER_LOGGED_IN) return false;
    $ai_last_check = AI_CHECK_NOT_LOGGED_IN_USER;
    if ($display_for_users == AI_DISPLAY_NOT_LOGGED_IN_USERS && ($ai_wp_data [AI_WP_USER] & AI_USER_LOGGED_IN) == AI_USER_LOGGED_IN) return false;
    $ai_last_check = AI_CHECK_ADMINISTRATOR;
    if ($display_for_users == AI_DISPLAY_ADMINISTRATORS && ($ai_wp_data [AI_WP_USER] & AI_USER_ADMINISTRATOR) != AI_USER_ADMINISTRATOR) return false;

    return true;
  }

  function check_post_page_exceptions ($selected_blocks) {
    global $ai_last_check, $ai_wp_data;

    if ($ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_POST) {
      $enabled_on = $this->get_ad_enabled_on_which_posts ();
      if ($enabled_on == AI_INDIVIDUALLY_DISABLED) {
        $ai_last_check = AI_CHECK_INDIVIDUALLY_DISABLED;
        if (in_array ($this->number, $selected_blocks)) return false;
      }
      elseif ($enabled_on == AI_INDIVIDUALLY_ENABLED) {
        $ai_last_check = AI_CHECK_INDIVIDUALLY_ENABLED;
        if (!in_array ($this->number, $selected_blocks)) return false;
      }
    } elseif ($ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_STATIC) {
      $enabled_on = $this->get_ad_enabled_on_which_pages ();
      if ($enabled_on == AI_INDIVIDUALLY_DISABLED) {
        $ai_last_check = AI_CHECK_INDIVIDUALLY_DISABLED;
        if (in_array ($this->number, $selected_blocks)) return false;
      }
      elseif ($enabled_on == AI_INDIVIDUALLY_ENABLED) {
        $ai_last_check = AI_CHECK_INDIVIDUALLY_ENABLED;
        if (!in_array ($this->number, $selected_blocks)) return false;
      }
    }
    return true;
  }

  function check_filter ($counter_for_filter) {
    global $ai_last_check, $ad_inserter_globals, $page;

    $filter_ok = $this->get_inverted_filter() ? false : true;

    $ai_last_check = AI_CHECK_FILTER;
    $filter_settings = trim (str_replace (' ', '', $this->get_call_filter()));
    if (empty ($filter_settings)) return $filter_ok;

    switch ($this->get_filter_type ()) {
      case AI_FILTER_PHP_FUNCTION_CALLS:
        if (isset ($ad_inserter_globals [AI_PHP_FUNCTION_CALL_COUNTER_NAME . $this->number]))
          $counter_for_filter = $ad_inserter_globals [AI_PHP_FUNCTION_CALL_COUNTER_NAME . $this->number]; else return !$filter_ok;
        break;
      case AI_FILTER_CONTENT_PROCESSING:
        if (isset ($ad_inserter_globals [AI_CONTENT_COUNTER_NAME]))
          $counter_for_filter = $ad_inserter_globals [AI_CONTENT_COUNTER_NAME]; else return !$filter_ok;
        break;
      case AI_FILTER_EXCERPT_PROCESSING:
        if (isset ($ad_inserter_globals [AI_EXCERPT_COUNTER_NAME]))
          $counter_for_filter = $ad_inserter_globals [AI_EXCERPT_COUNTER_NAME]; else return !$filter_ok;
        break;
      case AI_FILTER_BEFORE_POST_PROCESSING:
        if (isset ($ad_inserter_globals [AI_LOOP_BEFORE_COUNTER_NAME]))
          $counter_for_filter = $ad_inserter_globals [AI_LOOP_BEFORE_COUNTER_NAME]; else return !$filter_ok;
        break;
      case AI_FILTER_AFTER_POST_PROCESSING:
        if (isset ($ad_inserter_globals [AI_LOOP_AFTER_COUNTER_NAME]))
          $counter_for_filter = $ad_inserter_globals [AI_LOOP_AFTER_COUNTER_NAME]; else return !$filter_ok;
        break;
      case AI_FILTER_WIDGET_DRAWING:
        if (isset ($ad_inserter_globals [AI_WIDGET_COUNTER_NAME . $this->number]))
          $counter_for_filter = $ad_inserter_globals [AI_WIDGET_COUNTER_NAME . $this->number]; else return !$filter_ok;
        break;
      case AI_FILTER_SUBPAGES:
        if (isset ($page))
          $counter_for_filter = $page; else return !$filter_ok;
        break;
      case AI_FILTER_POSTS:
        if (isset ($ad_inserter_globals [AI_POST_COUNTER_NAME]))
          $counter_for_filter = $ad_inserter_globals [AI_POST_COUNTER_NAME]; else return !$filter_ok;
        break;
      case AI_FILTER_PARAGRAPHS:
          return true;
        break;
      case AI_FILTER_COMMENTS:
        if (isset ($ad_inserter_globals [AI_COMMENT_COUNTER_NAME]))
          $counter_for_filter = $ad_inserter_globals [AI_COMMENT_COUNTER_NAME]; else return !$filter_ok;
        break;
    }

    $filter_values = array ();
    if (strpos ($filter_settings, ",") !== false) {
      $filter_values = explode (",", $filter_settings);
    } else $filter_values []= $filter_settings;

    foreach ($filter_values as $filter_value) {
      $filter_value = trim ($filter_value);
      if ($filter_value [0] == '%') {
        $mod_value = substr ($filter_value, 1);
        if (is_numeric ($mod_value) && $mod_value > 0) {
          if ($counter_for_filter % $mod_value == 0) return $filter_ok;
        }
      }
    }

    return in_array ($counter_for_filter, $filter_values) xor !$filter_ok;
  }

  function check_and_increment_block_counter () {
    global $ad_inserter_globals, $ai_last_check;

    $global_name = AI_BLOCK_COUNTER_NAME . $this->number;
    $max_insertions = intval ($this->get_maximum_insertions ());
    if (!isset ($ad_inserter_globals [$global_name])) {
      $ad_inserter_globals [$global_name] = 0;
    }
    $ai_last_check = AI_CHECK_MAX_INSERTIONS;
    if ($max_insertions != 0 && $ad_inserter_globals [$global_name] >= $max_insertions) return false;
    $ad_inserter_globals [$global_name] ++;

    return true;
  }

  function check_block_counter () {
    global $ad_inserter_globals, $ai_last_check;

    $global_name = AI_BLOCK_COUNTER_NAME . $this->number;
    $max_insertions = intval ($this->get_maximum_insertions ());
    if (!isset ($ad_inserter_globals [$global_name])) {
      $ad_inserter_globals [$global_name] = 0;
    }
    $ai_last_check = AI_CHECK_MAX_INSERTIONS;
    if ($max_insertions != 0 && $ad_inserter_globals [$global_name] >= $max_insertions) return false;
    return true;
  }

  function increment_block_counter () {
    global $ad_inserter_globals;

    if ($this->number == 0) return;

    $global_name = AI_BLOCK_COUNTER_NAME . $this->number;
    if (!isset ($ad_inserter_globals [$global_name])) {
      $ad_inserter_globals [$global_name] = 0;
    }
    $ad_inserter_globals [$global_name] ++;
    return;
  }


  function replace_ai_tags ($content){
    global $ai_wp_data;

    if (!isset ($ai_wp_data [AI_TAGS])) {
      $general_tag = str_replace ("&amp;", " and ", $this->get_ad_general_tag());
      $title = $general_tag;
      $short_title = $general_tag;
      $category = $general_tag;
      $short_category = $general_tag;
      $tag = $general_tag;
      $smart_tag = $general_tag;
      if ($ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_CATEGORY) {
          $categories = get_the_category();
          if (!empty ($categories)) {
            $first_category = reset ($categories);
            $category = str_replace ("&amp;", "and", $first_category->name);
            if ($category == _x('Uncategorized', 'category name', 'ad-inserter')) $category = $general_tag;
          } else {
              $category = $general_tag;
          }
          if (strpos ($category, ",") !== false) {
            $short_category = trim (substr ($category, 0, strpos ($category, ",")));
          } else $short_category = $category;
          if (strpos ($short_category, "and") !== false) {
            $short_category = trim (substr ($short_category, 0, strpos ($short_category, "and")));
          }

          $title = $category;
          $title = str_replace ("&amp;", "and", $title);
          $short_title = implode (" ", array_slice (explode (" ", $title), 0, 3));
          $tag = $short_title;
          $smart_tag = $short_title;
      } elseif (is_tag ()) {
          $title = single_tag_title('', false);
          $title = str_replace (array ("&amp;", "#"), array ("and", ""), $title);
          $short_title = implode (" ", array_slice (explode (" ", $title), 0, 3));
          $category = $short_title;
          if (strpos ($category, ",") !== false) {
            $short_category = trim (substr ($category, 0, strpos ($category, ",")));
          } else $short_category = $category;
          if (strpos ($short_category, "and") !== false) {
            $short_category = trim (substr ($short_category, 0, strpos ($short_category, "and")));
          }
          $tag = $short_title;
          $smart_tag = $short_title;
      } elseif ($ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_SEARCH) {
          $title = get_search_query();
          $title = str_replace ("&amp;", "and", $title);
          $short_title = implode (" ", array_slice (explode (" ", $title), 0, 3));
          $category = $short_title;
          if (strpos ($category, ",") !== false) {
            $short_category = trim (substr ($category, 0, strpos ($category, ",")));
          } else $short_category = $category;
          if (strpos ($short_category, "and") !== false) {
            $short_category = trim (substr ($short_category, 0, strpos ($short_category, "and")));
          }
          $tag = $short_title;
          $smart_tag = $short_title;
      } elseif ($ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_STATIC || $ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_POST) {
          $title = get_the_title();
          $title = str_replace ("&amp;", "and", $title);

          $short_title = implode (" ", array_slice (explode (" ", $title), 0, 3));

          $categories = get_the_category();
          if (!empty ($categories)) {
            $first_category = reset ($categories);
            $category = str_replace ("&amp;", "and", $first_category->name);
            if ($category == _x('Uncategorized', 'category name', 'ad-inserter')) $category = $general_tag;
          } else {
              $category = $short_title;
          }
          if (strpos ($category, ",") !== false) {
            $short_category = trim (substr ($category, 0, strpos ($category, ",")));
          } else $short_category = $category;
          if (strpos ($short_category, "and") !== false) {
            $short_category = trim (substr ($short_category, 0, strpos ($short_category, "and")));
          }

          $tags = get_the_tags();
          if (!empty ($tags)) {

            $first_tag = reset ($tags);
            $tag = str_replace (array ("&amp;", "#"), array ("and", ""), isset ($first_tag->name) ? $first_tag->name : '');

            $tag_array = array ();
            foreach ($tags as $tag_data) {
              if (isset ($tag_data->name))
                $tag_array [] = explode (" ", $tag_data->name);
            }

            $selected_tag = '';

            if (count ($tag_array [0]) == 2) $selected_tag = $tag_array [0];
            elseif (count ($tag_array) > 1 && count ($tag_array [1]) == 2) $selected_tag = $tag_array [1];
            elseif (count ($tag_array) > 2 && count ($tag_array [2]) == 2) $selected_tag = $tag_array [2];
            elseif (count ($tag_array) > 3 && count ($tag_array [3]) == 2) $selected_tag = $tag_array [3];
            elseif (count ($tag_array) > 4 && count ($tag_array [4]) == 2) $selected_tag = $tag_array [4];


            if ($selected_tag == '' && count ($tag_array) >= 2 && count ($tag_array [0]) == 1 && count ($tag_array [1]) == 1) {

              if (isset ($tag_array [0][0]) && isset ($tag_array [1][0])) {
                if (strpos ($tag_array [0][0], $tag_array [1][0]) !== false) $tag_array = array_slice ($tag_array, 1, count ($tag_array) - 1);
              }

              if (isset ($tag_array [0][0]) && isset ($tag_array [1][0])) {
                if (strpos ($tag_array [1][0], $tag_array [0][0]) !== false) $tag_array = array_slice ($tag_array, 1, count ($tag_array) - 1);
              }

              if (isset ($tag_array [0][0]) && isset ($tag_array [1][0])) {
                if (count ($tag_array) >= 2 && count ($tag_array [0]) == 1 && count ($tag_array [1]) == 1) {
                  $selected_tag = array ($tag_array [0][0], $tag_array [1][0]);
                }
              }
            }

            if ($selected_tag == '') {
              $first_tag = reset ($tags);
              $smart_tag = implode (" ", array_slice (explode (" ", isset ($first_tag->name) ? $first_tag->name : ''), 0, 3));
            } else $smart_tag = implode (" ", $selected_tag);

            $smart_tag = str_replace (array ("&amp;", "#"), array ("and", ""), $smart_tag);

          } else {
              $tag = $category;
              $smart_tag = $category;
          }
      }

      $title = str_replace (array ("'", '"'), array ("&#8217;", "&#8221;"), $title);
      $title = html_entity_decode ($title, ENT_QUOTES, "utf-8");

      $short_title = str_replace (array ("'", '"'), array ("&#8217;", "&#8221;"), $short_title);
      $short_title = html_entity_decode ($short_title, ENT_QUOTES, "utf-8");

      $search_query = "";
      if (isset ($_SERVER['HTTP_REFERER'])) {
        $referrer = $_SERVER['HTTP_REFERER'];
      } else $referrer = '';
      if (preg_match ("/[\.\/](google|yahoo|bing|ask)\.[a-z\.]{2,5}[\/]/i", $referrer, $search_engine)){
         $referrer_query = parse_url ($referrer);
         $referrer_query = isset ($referrer_query ["query"]) ? $referrer_query ["query"] : "";
         parse_str ($referrer_query, $value);
         $search_query = isset ($value ["q"]) ? $value ["q"] : "";
         if ($search_query == "") {
           $search_query = isset ($value ["p"]) ? $value ["p"] : "";
         }
      }
      if ($search_query == "") $search_query = $smart_tag;

      $author = get_the_author_meta ('display_name');
      $author_name = get_the_author_meta ('first_name') . " " . get_the_author_meta ('last_name');
      if ($author_name == '') $author_name = $author;

      $ai_wp_data [AI_TAGS]['TITLE']          = $title;
      $ai_wp_data [AI_TAGS]['SHORT_TITLE']    = $short_title;
      $ai_wp_data [AI_TAGS]['CATEGORY']       = $category;
      $ai_wp_data [AI_TAGS]['SHORT_CATEGORY'] = $short_category;
      $ai_wp_data [AI_TAGS]['TAG']            = $tag;
      $ai_wp_data [AI_TAGS]['SMART_TAG']      = $smart_tag;
      $ai_wp_data [AI_TAGS]['SEARCH_QUERY']   = $search_query;
      $ai_wp_data [AI_TAGS]['AUTHOR']         = $author;
      $ai_wp_data [AI_TAGS]['AUTHOR_NAME']    = $author_name;
    }

    $ad_data = preg_replace ("/{title}/i",          $ai_wp_data [AI_TAGS]['TITLE'],          $content);
    $ad_data = preg_replace ("/{short-title}/i",    $ai_wp_data [AI_TAGS]['SHORT_TITLE'],    $ad_data);
    $ad_data = preg_replace ("/{category}/i",       $ai_wp_data [AI_TAGS]['CATEGORY'],       $ad_data);
    $ad_data = preg_replace ("/{short-category}/i", $ai_wp_data [AI_TAGS]['SHORT_CATEGORY'], $ad_data);
    $ad_data = preg_replace ("/{tag}/i",            $ai_wp_data [AI_TAGS]['TAG'],            $ad_data);
    $ad_data = preg_replace ("/{smart-tag}/i",      $ai_wp_data [AI_TAGS]['SMART_TAG'],      $ad_data);
    $ad_data = preg_replace ("/{search-query}/i",   $ai_wp_data [AI_TAGS]['SEARCH_QUERY'],   $ad_data);
    $ad_data = preg_replace ("/{author}/i",         $ai_wp_data [AI_TAGS]['AUTHOR'],         $ad_data);
    $ad_data = preg_replace ("/{author-name}/i",    $ai_wp_data [AI_TAGS]['AUTHOR_NAME'],    $ad_data);

    $ad_data = preg_replace ("/{short_title}/i",    $ai_wp_data [AI_TAGS]['SHORT_TITLE'],    $ad_data);
    $ad_data = preg_replace ("/{short_category}/i", $ai_wp_data [AI_TAGS]['SHORT_CATEGORY'], $ad_data);
    $ad_data = preg_replace ("/{smart_tag}/i",      $ai_wp_data [AI_TAGS]['SMART_TAG'],      $ad_data);
    $ad_data = preg_replace ("/{search_query}/i",   $ai_wp_data [AI_TAGS]['SEARCH_QUERY'],   $ad_data);
    $ad_data = preg_replace ("/{author_name}/i",    $ai_wp_data [AI_TAGS]['AUTHOR_NAME'],    $ad_data);

    if (function_exists ('ai_tags')) ai_tags ($ad_data);

    return $ad_data;
  }

  function extract_features (){
    global $ai_wp_data;

//  AI_SERVER_SIDE_DETECTION
//  AI_CLIENT_SIDE_DETECTION
//  AI_CLIENT_SIDE_INSERTION
//  AI_STICK_TO_THE_CONTENT
//  AI_TRACKING
//  AI_CLOSE_BUTTONS
//  AI_ANIMATION
//  AI_LAZY_LOADING
//  AI_GEOLOCATION

//    if ($this->get_detection_server_side()) echo "#", $this->number;
//    echo "#", $this->number;
//    return;

    if ($this->get_detection_server_side()) $ai_wp_data [AI_SERVER_SIDE_DETECTION] = true;
    if ($this->get_detection_client_side ()) {
      $ai_wp_data [AI_CLIENT_SIDE_DETECTION] = true;

      if ($this->get_client_side_action () == AI_CLIENT_SIDE_ACTION_INSERT) {
        $ai_wp_data [AI_CLIENT_SIDE_INSERTION] = true;
      }
    }

    if (function_exists ('ai_extract_features_2')) ai_extract_features_2 ($this);

    if ($this->stick_to_the_content_class () != '') $ai_wp_data [AI_STICK_TO_THE_CONTENT] = true;

    switch ($this->get_automatic_insertion()) {
      case AI_AUTOMATIC_INSERTION_BEFORE_HTML_ELEMENT:
      case AI_AUTOMATIC_INSERTION_INSIDE_HTML_ELEMENT:
      case AI_AUTOMATIC_INSERTION_AFTER_HTML_ELEMENT:
        if ($this->get_html_element_insertion () != AI_HTML_INSERTION_SEREVR_SIDE) $ai_wp_data [AI_CLIENT_SIDE_INSERTION] = true;
        break;
    }
  }
}


class ai_Block extends ai_CodeBlock {

    public function __construct ($number) {
      parent::__construct();

      $this->number = $number;
//      $this->wp_options [AI_OPTION_BLOCK_NAME] = DEFAULT_AD_NAME .' ' . $number;
    }
}

class ai_AdH extends ai_BaseCodeBlock {

  public function __construct () {
    parent::__construct();

    $this->wp_options [AI_OPTION_BLOCK_NAME] = 'HEADER';
  }
}

class ai_AdF extends ai_BaseCodeBlock {

  public function __construct () {
    parent::__construct();

    $this->wp_options [AI_OPTION_BLOCK_NAME] = 'FOOTER';
  }
}

class ai_AdA extends ai_BaseCodeBlock {

  public function __construct () {
    parent::__construct();

    $this->wp_options [AI_OPTION_BLOCK_NAME] = 'AD BLOCKING MESSAGE';
  }

  public function get_ad_data (){
    $option = isset ($this->wp_options [AI_OPTION_CODE]) ? $this->wp_options [AI_OPTION_CODE] : AI_DEFAULT_ADB_MESSAGE;

    if ($option == AD_EMPTY_DATA) $option = AI_DEFAULT_ADB_MESSAGE;

    return $option;
  }
}

class ai_Walker_Comment extends Walker_Comment {

    public function comment_callback ($comment, $args, $depth) {
      if (($comment->comment_type == 'pingback' || $comment->comment_type == 'trackback') && $args ['short_ping']) {
        $this->ping ($comment, $depth, $args);
      } elseif ($args['format'] === 'html5') {
        $this->html5_comment ($comment, $depth, $args);
      } else {
        $this->comment ($comment, $depth, $args);
      }
    }

}

class ai_code_generator {

  public function __construct () {
  }

  public function generate ($data){
    $code = '';

    switch ($data ['generate-code']) {
      case AI_CODE_BANNER:
        $code = '';
        if (isset ($data ['image']) && $data ['image'] != '') {
          $code = '<img src="' . $data ['image'] . '">';
        }
        if (isset ($data ['link']) && $data ['link'] != '') {
          $code = '<a href="' . $data ['link'] . '"' .(isset ($data ['target']) ? ' target="' . $data ['target'] . '"' : '') . '>' . $code . '</a>';
        }
        break;
      case AI_CODE_ADSENSE:
        $adsense_size = ($data ['adsense-width']  != '' ? ' width: '. $data ['adsense-width']. 'px;' : '') . ($data ['adsense-height'] != '' ? ' height: '.$data ['adsense-height'].'px;' : '');

        $code = '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>';
        if ($data ['adsense-comment']) $code .= "\n<!-- " . $data ['adsense-comment'] . " -->";

        switch ($data ['adsense-type']) {
          case AI_ADSENSE_STANDARD:

            switch ($data ['adsense-size']) {
              case AI_ADSENSE_SIZE_FIXED:

                // Normal
                $code .= '
<ins class="adsbygoogle"
     style="display: inline-block;'.$adsense_size.'"
     data-ad-client="ca-'.$data ['adsense-publisher-id'].'"
     data-ad-slot="'.$data ['adsense-ad-slot-id'].'"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>';
                break;

              case AI_ADSENSE_SIZE_FIXED_BY_VIEWPORT:

                $code = $this->adsense_size_styles ($data) . $code;

                // Normal
                $code .= '
<ins class="adsbygoogle ' . AI_ADSENSE_BLOCK_CLASS  .$data ['block'].'"
     data-ad-client="ca-'.$data ['adsense-publisher-id'].'"
     data-ad-slot="'.$data ['adsense-ad-slot-id'].'"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>';
                break;

              case AI_ADSENSE_SIZE_RESPONSIVE:

                // Responsive
                $code .= '
<ins class="adsbygoogle"
     style="display: block;"
     data-ad-client="ca-'.$data ['adsense-publisher-id'].'"
     data-ad-slot="'.$data ['adsense-ad-slot-id'].'"
     data-ad-format="auto"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>';
                break;
            }
            break;

          case AI_ADSENSE_LINK:
            switch ($data ['adsense-size']) {
              case AI_ADSENSE_SIZE_FIXED:

                // Normal
                $code .= '
<ins class="adsbygoogle"
     style="display: inline-block;'.$adsense_size.'"
     data-ad-client="ca-'.$data ['adsense-publisher-id'].'"
     data-ad-slot="'.$data ['adsense-ad-slot-id'].'"
     data-ad-format="link"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>';
                break;

              case AI_ADSENSE_SIZE_FIXED_BY_VIEWPORT:

                $code = $this->adsense_size_styles ($data) . $code;

                // Normal
                $code .= '
<ins class="adsbygoogle ' . AI_ADSENSE_BLOCK_CLASS  .$data ['block'].'"
     data-ad-client="ca-'.$data ['adsense-publisher-id'].'"
     data-ad-slot="'.$data ['adsense-ad-slot-id'].'"
     data-ad-format="link"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>';
                break;

              case AI_ADSENSE_SIZE_RESPONSIVE:

                // Responsive
                $code .= '
<ins class="adsbygoogle"
     style="display: block;"
     data-ad-client="ca-'.$data ['adsense-publisher-id'].'"
     data-ad-slot="'.$data ['adsense-ad-slot-id'].'"
     data-ad-format="link"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>';
                break;
            }
            break;

          case AI_ADSENSE_IN_ARTICLE:
            $code .= '
<ins class="adsbygoogle"
     style="display: block; text-align: center;"
     data-ad-client="ca-'.$data ['adsense-publisher-id'].'"
     data-ad-slot="'.$data ['adsense-ad-slot-id'].'"
     data-ad-layout="in-article"
     data-ad-format="fluid"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>';
            break;

          case AI_ADSENSE_IN_FEED:
            $code .= '
<ins class="adsbygoogle"
     style="display: block;"
     data-ad-client="ca-'.$data ['adsense-publisher-id'].'"
     data-ad-slot="'.$data ['adsense-ad-slot-id'].'"
     data-ad-layout="'.$data ['adsense-layout'].'"
     data-ad-layout-key="'.$data ['adsense-layout-key'].'"
     data-ad-format="fluid"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>';
            break;

          case AI_ADSENSE_MATCHED_CONTENT:
            $code .= '
<ins class="adsbygoogle"
     style="display: block;"
     data-ad-client="ca-'.$data ['adsense-publisher-id'].'"
     data-ad-slot="'.$data ['adsense-ad-slot-id'].'"
     data-ad-format="autorelaxed"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>';
            break;
          case AI_ADSENSE_AUTO:
            $code .= '
<script>
   (adsbygoogle = window.adsbygoogle || []).push({
      google_ad_client: "ca-'.$data ['adsense-publisher-id'].'",
      enable_page_level_ads: true
   });
</script>';

            break;
        }

        if ($data ['adsense-amp'] != AI_ADSENSE_AMP_DISABLED) {
          switch ($data ['adsense-type']) {
            case AI_ADSENSE_AUTO:
                  $code .= '

[ADINSERTER AMP]

<amp-auto-ads
  type="adsense"
  data-ad-client="'.$data ['adsense-publisher-id'].'">
</amp-auto-ads>';
              break;
            default:
              switch ($data ['adsense-amp']) {
                case AI_ADSENSE_AMP_ABOVE_THE_FOLD:
                  $code .= '

[ADINSERTER AMP]

<amp-ad
  layout="fixed-height"
  height=100
  type="adsense"
  data-ad-client="ca-'.$data ['adsense-publisher-id'].'"
  data-ad-slot="'.$data ['adsense-ad-slot-id'].'">
</amp-ad>';
                  break;
                case AI_ADSENSE_AMP_BELOW_THE_FOLD:
                  $code .= '

[ADINSERTER AMP]

<amp-ad
  layout="responsive"
  width=300
  height=250
  type="adsense"
  data-ad-client="ca-'.$data ['adsense-publisher-id'].'"
  data-ad-slot="'.$data ['adsense-ad-slot-id'].'">
</amp-ad>';
                  break;
              }
              break;
          }
        }
        break;
    }

    return $code;
  }

  public function adsense_size_styles ($data){
    $code = '<style>
';
    $display_inline = false;
    for ($viewport = AD_INSERTER_VIEWPORTS; $viewport >= 1; $viewport --) {
      $viewport_name  = get_viewport_name ($viewport);
      $viewport_width = get_viewport_width ($viewport);

      $adsense_width  = $data ['adsense-viewports'][$viewport - 1]['width'];
      $adsense_height = $data ['adsense-viewports'][$viewport - 1]['height'];

      if ($viewport_name != '') {
        if ($adsense_width > 0 && $adsense_height > 0) {
          if (!$display_inline) {
            $size_style = 'display: inline-block; ';
            $display_inline = true;
          } else $size_style = '';

          $size_style .= 'width: ' . $adsense_width . 'px; height: ' .$adsense_height . 'px;';
        } else {
            $size_style = 'display: none;';
            $display_inline = false;
          }

        switch ($viewport_width) {
          case 0:
              $code .= '.' . AI_ADSENSE_BLOCK_CLASS . $data ['block']. ' {' . $size_style . '}';
            break;
          default:
              $code .= '@media (min-width: '.$viewport_width.'px) {.' . AI_ADSENSE_BLOCK_CLASS . $data ['block']. ' {' . $size_style . '}}';
            break;
        }

        $code .= ' /* ' . $viewport_name . ($viewport_width == 0 ? ', default' : '') . ' */' . "\n";
      }
    }
    $code .= '</style>
';
    return $code;
  }


  public function import ($code){

    $amp = AI_ADSENSE_AMP_DISABLED;
    if (strpos (do_shortcode ($code), AD_AMP_SEPARATOR) !== false) {
      $amp = AI_ADSENSE_AMP_ABOVE_THE_FOLD;
    }

    if (!class_exists ('DOMDocument')) {
      echo  __('ERROR: class DOMDocument not found. Your webhost needs to install the DOM extension for PHP.', 'ad-inserter');
      wp_die ();
    }

    try {
      $dom = new DOMDocument ();
      libxml_use_internal_errors (true);
      $dom->loadHTML ($code);
      libxml_clear_errors ();
    } catch (Exception $e) {
        echo 'ERROR: ', $e->getMessage();
        wp_die ();
    }

    // AdSense
    if (strpos ($code, 'data-ad-client') !== false) {
      $adsense_code     = $dom->getElementsByTagName ('ins');
      $adsense_code_amp = $dom->getElementsByTagName ('amp-ad');

      if ($adsense_code_amp->length != 0) {
//        $layout = $adsense_code_amp [0]->getAttribute ('layout');               // PHP 5.6.3
        $layout = $adsense_code_amp->item (0)->getAttribute ('layout');
        switch ($layout) {
          case 'fixed-height':
            $amp = AI_ADSENSE_AMP_ABOVE_THE_FOLD;
            break;
          case 'responsive':
            $amp = AI_ADSENSE_AMP_BELOW_THE_FOLD;
            break;
        }

        if ($adsense_code->length == 0) $adsense_code = $adsense_code_amp;
      }

      if ($adsense_code->length != 0) {
        $data = array (
          'type' => AI_CODE_ADSENSE,
          'adsense-publisher-id' => '',
          'adsense-ad-slot-id' => '',
          'adsense-type' => AI_ADSENSE_STANDARD,
          'adsense-size' => AI_ADSENSE_SIZE_FIXED,
          'adsense-width' => '',
          'adsense-height' => '',
          'adsense-layout' => '',
          'adsense-layout-key' => '',
          'adsense-comment' => '',
          'adsense-amp' => $amp,
        );

//        $data ['adsense-publisher-id'] = str_replace ('ca-', '', $adsense_code [0]->getAttribute ('data-ad-client'));
        $data ['adsense-publisher-id'] = str_replace ('ca-', '', $adsense_code->item (0)->getAttribute ('data-ad-client'));
//        $data ['adsense-ad-slot-id']   = $adsense_code [0]->getAttribute ('data-ad-slot');
        $data ['adsense-ad-slot-id']   = $adsense_code->item (0)->getAttribute ('data-ad-slot');

//        $adsense_style = $adsense_code [0]->getAttribute ('style');
        $adsense_style = $adsense_code->item (0)->getAttribute ('style');

        $style_width  = preg_match ("/width\s*:\s*(\d+)px/",  $adsense_style, $width_match);
        if ($style_width) $data ['adsense-width'] = $width_match [1];

        $style_height = preg_match ("/height\s*:\s*(\d+)px/", $adsense_style, $height_match);
        if ($style_height) $data ['adsense-height'] = $height_match [1];

        $display = '';
        $style_display = preg_match ("/display\s*:\s*([a-z\-]+)/", $adsense_style, $display_match);
        if ($style_display) $display = $display_match [1];

        $adsense_class = trim ($adsense_code->item (0)->getAttribute ('class'));
        $adsense_classes = explode (' ', $adsense_class);

        $adsense_size = !$style_width && !$style_height && $display == 'block' ? AI_ADSENSE_SIZE_RESPONSIVE : AI_ADSENSE_SIZE_FIXED;

        if (count ($adsense_classes) == 2 && !$style_width && !$style_height) {
          $adsense_size = AI_ADSENSE_SIZE_FIXED_BY_VIEWPORT;

          $viewport_class = $adsense_classes [1];

          $style  = preg_match ("#<style>(.+?)</style>#s",  $code, $style_match);
          $style_lines = explode ("\n", trim ($style_match [1]));

          $sizes = array ();
          $viewport_widths = array ();
          for ($viewport = 1; $viewport <= AD_INSERTER_VIEWPORTS; $viewport ++) {
            $viewport_name  = get_viewport_name ($viewport);
            $viewport_width = get_viewport_width ($viewport);
            if ($viewport_name != '') {
              $viewport_widths [] = $viewport_width;
              $sizes []= array (0 => '', 1 => '');
            }
          }
          $viewport_widths = array_reverse ($viewport_widths);

          if (count ($style_lines) == count ($sizes)) {
            foreach ($style_lines as $index => $style_line) {
              if (strpos ($style_line, $viewport_class) !== false) {

                $min_width  = preg_match ("/min-width\s*:\s*(\d+)px/",  $style_line, $min_width_match);
                $viewport_width = $min_width ? $min_width_match [1] : '';

                if ($viewport_width == $viewport_widths [$index]) {
                  $styles = explode ($viewport_class, $style_line);
                  $style_line = $styles [1];

                  $style_width  = preg_match ("/width\s*:\s*(\d+)px/",  $style_line, $width_match);
                  $adsense_width = $style_width ? $width_match [1] : '';

                  $style_height  = preg_match ("/height\s*:\s*(\d+)px/",  $style_line, $height_match);
                  $adsense_height = $style_height ? $height_match [1] : '';

                  $sizes [$index] = array (0 => $adsense_width, 1 => $adsense_height);
                }

              } else $sizes [$index] = array ('', '');
            }
            $sizes = array_reverse ($sizes);
          }

          $data ['adsense-sizes'] = $sizes;
        }

        $data ['adsense-size'] = $adsense_size;

        $comment = preg_match ("#<!--(.+?)-->#",  $code, $comment_match);
        if ($comment) $data ['adsense-comment'] = trim ($comment_match [1]);

//        $adsense_ad_format = $adsense_code [0]->getAttribute ('data-ad-format');
        $adsense_ad_format = $adsense_code->item (0)->getAttribute ('data-ad-format');
        switch ($adsense_ad_format) {
          case '':
            break;
          case 'auto':
            break;
          case 'autorelaxed':
            $data ['adsense-type'] = AI_ADSENSE_MATCHED_CONTENT;
            break;
          case 'link':
            $data ['adsense-type'] = AI_ADSENSE_LINK;
            break;
          case 'fluid':
//            $adsense_ad_layout = $adsense_code [0]->getAttribute ('data-ad-layout');
            $adsense_ad_layout = $adsense_code->item (0)->getAttribute ('data-ad-layout');

            switch ($adsense_ad_layout) {
              case 'in-article':
                $data ['adsense-type'] = AI_ADSENSE_IN_ARTICLE;
                break 2;
            }

            $data ['adsense-type']        = AI_ADSENSE_IN_FEED;

            $data ['adsense-layout']      = $adsense_ad_layout;
//            $data ['adsense-layout-key']  = urlencode ($adsense_code [0]->getAttribute ('data-ad-layout-key'));
            $data ['adsense-layout-key']  = urlencode ($adsense_code->item (0)->getAttribute ('data-ad-layout-key'));

            break;
        }


        return $data;
      }
    }

    // Old AdSense / AdSense Auto ads
    if (strpos ($code, 'google_ad_client') !== false) {

      $data = array (
        'type' => AI_CODE_ADSENSE,
        'adsense-publisher-id' => '',
        'adsense-ad-slot-id' => '',
        'adsense-type' => AI_ADSENSE_STANDARD,
        'adsense-size' => AI_ADSENSE_SIZE_FIXED,
        'adsense-width' => '',
        'adsense-height' => '',
        'adsense-layout' => '',
        'adsense-layout-key' => '',
        'adsense-amp' => $amp,
      );

      $comment = preg_match ("#<!--(.+?)-->#",  $code, $comment_match);
      if ($comment) $data ['adsense-comment'] = trim ($comment_match [1]);

      if (preg_match ("/google_ad_client.+[\"\'](.+?)[\"\']/", $code, $match)) {
        $data ['adsense-publisher-id'] = str_replace ('ca-', '', $match [1]);
      }

      if (preg_match ("/google_ad_slot.+[\"\'](.+?)[\"\']/", $code, $match)) {
        $data ['adsense-ad-slot-id'] = $match [1];
      }

      if (preg_match ("/google_ad_width[^\d]+(\d+)/", $code, $match)) {
        $data ['adsense-width'] = $match [1];
      }

      if (preg_match ("/google_ad_height[^\d]+(\d+)/", $code, $match)) {
        $data ['adsense-height'] = $match [1];
      }

      if (preg_match ("/enable_page_level_ads[^\d]+true/", $code, $match)) {
        $data ['adsense-type'] = AI_ADSENSE_AUTO;
      }

      return $data;
    }


    // Banner
    $links  = $dom->getElementsByTagName ('a');
    $images = $dom->getElementsByTagName ('img');

    if ($links->length != 0 || $images->length != 0) {
      $data = array ('type' => AI_CODE_BANNER, 'image' => '', 'link' => '', 'target' => '');

      if ($images->length != 0) {
//        $data ['image']   = $images [0]->getAttribute ('src');
        $data ['image']   = $images->item (0)->getAttribute ('src');
      }

      if ($links->length != 0) {
//        $data ['link']    = $links [0]->getAttribute ('href');
        $data ['link']    = $links->item (0)->getAttribute ('href');
//        $data ['target']  = $links [0]->getAttribute ('target');
        $data ['target']  = $links->item (0)->getAttribute ('target');
      }

      return $data;
    }

    return array ('type' => AI_CODE_UNKNOWN);
  }

  public function import_rotation ($code){
    global $ai_expand_only_rotate, $ai_wp_data;

    $data = array (
      'options' => array (
          array (
            'code' => $code,
            'name' => '',
            'share' => '',
            'time' => '',
          ),
        ),
    );

    $ai_expand_only_rotate = true;
    unset ($ai_wp_data [AI_SHORTCODES]['rotate']);
    $code = do_shortcode ($code);
    $ai_expand_only_rotate = false;

    if (strpos ($code, AD_ROTATE_SEPARATOR) !== false) {
      $options = explode (AD_ROTATE_SEPARATOR, $code);
      $data ['options'] = array ();
      foreach ($options as $index => $option) {
        $option_code = trim ($option, "\n");
        $option_name = isset ($ai_wp_data [AI_SHORTCODES]['rotate'][$index - 1]['name']) ? $ai_wp_data [AI_SHORTCODES]['rotate'][$index - 1]['name'] : '';
        $option_share = isset ($ai_wp_data [AI_SHORTCODES]['rotate'][$index - 1]['share']) && is_numeric ($ai_wp_data [AI_SHORTCODES]['rotate'][$index - 1]['share']) ? intval ($ai_wp_data [AI_SHORTCODES]['rotate'][$index - 1]['share']) : '';
        $option_time  = isset ($ai_wp_data [AI_SHORTCODES]['rotate'][$index - 1]['time']) && is_numeric ($ai_wp_data [AI_SHORTCODES]['rotate'][$index - 1]['time']) ? intval ($ai_wp_data [AI_SHORTCODES]['rotate'][$index - 1]['time']) : '';
        if ($index == 0 && $option_code == '') continue;
        $data ['options'] []= array ('code' => $option_code, 'name' => $option_name, 'share' => $option_share, 'time' => $option_time);
      }
    }

    return $data;
  }

  public function generate_rotation ($rotation_data){

    if (count ($rotation_data) == 1) {
      $rotation_code = trim ($rotation_data [0]['code']);
    } else {
        $rotation_code = '';
        foreach ($rotation_data as $index => $rotation_data_row) {

          $name = trim ($rotation_data_row ['name']);
          $share = trim ($rotation_data_row ['share']);
          $time  = trim ($rotation_data_row ['time']);
          $code = trim ($rotation_data_row ['code'], "\n");

          if ($index != 0 || $name != '' || $share != '' || $time != '') {

            $shortcode = "" ;
            if ($index != 0) $shortcode .= "\n\n";

            $shortcode .= '[ADINSERTER ROTATE';

            if ($name != '') $shortcode .= ' name="'.str_replace ('"', '\'', $name).'"';
            if ($share != '') $shortcode .= ' share="'.str_replace ('"', '\'', $share).'"';
            if ($time  != '') $shortcode .= ' time="'.str_replace ('"', '\'', $time).'"';
            $shortcode .= "]\n\n";
          } else $shortcode = '';

          $rotation_code .= $shortcode . $code;
        }
      }

    return $rotation_code;
  }
}

class ai_block_labels {

  var $class;
  var $text_color;
  var $left_text;
  var $right_text;

  public function __construct ($class = '') {
    $this->class = $class == '' ? 'ai-debug-default' : $class;
    $this->text_color = '';
    $this->left_text = '';
    $this->right_text = '';
  }

  public function block_start () {
    return "<section class='ai-debug-block $this->class'>\n";
  }

  public function block_end () {
    return "</section>\n";
  }

  public function bar ($left_text, $left_title = '', $center_text = '', $right_text = '', $right_title = '') {
    return
      "<section class='ai-debug-bar $this->class'>" .
        $this->invisible_start () .
        $this->bar_text_left ($left_text, $left_title) .
        $this->bar_text_center ($center_text) .
        $this->bar_text_right ($right_text, $right_title) .
        $this->invisible_end () .
      "</section>\n";
  }

  public function bar_hidden_viewport ($left_text, $left_title = '', $center_text = '', $right_text = '', $right_title = '') {
    return
      "<section class='ai-debug-bar ai-debug-viewport-invisible'>" .
        $this->invisible_start () .
        $this->bar_text_left ($left_text, $left_title) .
        $this->bar_text_center ($center_text) .
        $this->bar_text_right ($right_text, $right_title) .
        $this->invisible_end () .
      "</section>\n";
  }

  public function center_bar ($center_text) {
    return
      "<section class='ai-debug-bar $this->class'>" . $this->left_text .
        $this->invisible_start () .
//        "<kbd style='visibility: hidden;'>".$this->left_text."</kbd>" .
        $this->bar_text_center ($center_text) .
//        "<kbd style='visibility: hidden;'>".$this->right_text."</kbd>" .
        $this->invisible_end () .
      "</section>\n";
  }

  public function bar_text_left ($text, $title) {
//    $this->left_text = $text;
    return "<kbd class='ai-debug-text-left' title='$title'>$text</kbd>";
  }

  public function bar_text_center ($text) {
    return "<kbd class='ai-debug-text-center'>&nbsp;$text&nbsp;</kbd>";
  }

  public function bar_text_right ($text, $title) {
//    $this->right_text = $text;
    return "<kbd class='ai-debug-text-right' title='$title'>$text</kbd>";
  }

  public function invisible_start () {
    return '<kbd class="ai-debug-invisible">[AI]</kbd>';
  }

  public function invisible_end () {
    return '<kbd class="ai-debug-invisible">[/AI]</kbd>';
  }

  public function message ($text) {
    return
      "<section class='ai-debug-adb-center'>" .
      $this->invisible_start () .
      $text .
      $this->invisible_end () .
      "</section>\n";
  }

  public function adb_hidden_section_start () {
    return "<section class='ai-adb-show ai-debug-adb-hidden'>";
  }

  public function adb_hidden_section_end () {
    return "</section>\n";
  }

  public function adb_visible_section_start () {
    return "<section class='ai-adb-hide'>";
  }

  public function adb_visible_section_end () {
    return "</section>\n";
  }

}
