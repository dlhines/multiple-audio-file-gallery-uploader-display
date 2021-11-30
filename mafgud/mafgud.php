<?php

class MAFGUD {

  public function __construct(){
    //

    // Meta Box,  Styles, and Scripts
    add_action( 'add_meta_boxes' , array ( $this, 'MAFGUD_meta_box'));
    add_action( 'admin_enqueue_scripts' , array ( $this, 'MAFGUD_scripts_styles') );
    add_action( 'wp_enqueue_scripts' , array ( $this, 'MAFGUD_scripts_styles_render') );

    // Ajax
    add_action( 'wp_ajax_mafgud_save_audio' , array( $this, 'MAFGUD_save_audio' ) );
    add_action( 'wp_ajax_mafgud_delete_audio' , array( $this, 'MAFGUD_delete_audio' ) );
    add_action( 'wp_ajax_mafgud_sort_audio' , array( $this, 'MAFGUD_sort_audio' ) );
    add_shortcode( 'mafgud_display' , array( $this, 'MAFGUD_shortcode') );
    //

  }

  public function MAFGUD_scripts_styles() {
    //

    // Media assets
    wp_enqueue_media();

    // Script Assets
	wp_enqueue_style('jquery-ui', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css', array(), '0.0.0', 'all');
    wp_enqueue_script( 'jquery-ui', '//code.jquery.com/ui/1.12.1/jquery-ui.js', array('jquery'), null, true );

    // Direct Plugin CSS / JS
    wp_enqueue_style('multiple-audio-file-gallery-uploader-display', MAFGUD_PLUGIN_DIR . 'mafgud/css/multiple-audio-file-gallery-uploader-display.css', array(), '0.0.0', 'all');
    wp_enqueue_script( 'MAFGUD', MAFGUD_PLUGIN_DIR . 'mafgud/js/multiple-audio-file-gallery-uploader-display.js', array('jquery'), null, true );

    // Fontawesome
    wp_enqueue_script('font-awesome', 'https://kit.fontawesome.com/556f7ce196.js', array(), '0.0.0', 'true');
    wp_script_add_data( 'font-awesome', array( 'crossorigin' ) , array( 'anonymous' ) );

    // Localization
    wp_localize_script( 'MAFGUD', 'mafgud_save_audio', array( 'ajax_url' => admin_url('admin-ajax.php'), 'ajax_nonce' => wp_create_nonce('MAFGUD')) );
    wp_localize_script( 'MAFGUD', 'mafgud_delete_audio', array( 'ajax_url' => admin_url('admin-ajax.php')) );
    wp_localize_script( 'MAFGUD', 'mafgud_sort_audio', array( 'ajax_url' => admin_url('admin-ajax.php')) );
    //
  }

  public function MAFGUD_scripts_styles_render() {
    //
    wp_enqueue_style('multiple-audio-file-gallery-uploader-display-render', MAFGUD_PLUGIN_DIR . 'css/multiple-audio-file-gallery-uploader-display-render.css', array(), '0.0.0', 'all');
    //
  }

  /*
  * Display Metabox
  *
  */
  public function MAFGUD_meta_box() {

    $pt_s = get_option('MAFGUD_post_types');

    if(!empty($pt_s)) {
        $display = explode(',', get_option('MAFGUD_post_types'));

        add_meta_box(
          'multiple-audio-file-gallery-uploader-display', // id
          'Multiple Audio File Gallery Uploader', // title
          array ( $this, 'MAFGUD_cb'), // callback
          $display, // content-type
          'normal', // display
          'default' // priority
        );
      }

  }

  /*
  * Metabox Callback
  *
  */
  public function MAFGUD_cb( $post ){

    // Call to admin_display_audio to grab all thumbnails
    $audio = $this->admin_display_audio($post->ID);

    wp_nonce_field('mafgud_post_audio_set_meta_box_nonce', 'mafgud_post_audio_set_meta_box_nonce');
    ?>
    <div id="audio-uploader-display">
      <div id="instructions" class="clearfix">
        <p class="instructions_header">MAFGUD Instructions (Show)</p>
        <section class="clearfix">
          <?php require "mafgud-instructions.php" ; ?>
        </section>
      </div>
      <p>Copy/Paste into Content Window: <b style="font-size: 1.2rem;">[mafgud_display id="<?php echo $post->ID; ?>" title=""]</b></p>
      <div id="gallery_load"><?php if($audio) : echo $audio; endif; ?></div>
      <input type="button" id="select_audio" class="" value="Select Audio File(s)" data-id="<?php echo $post->ID ?>">
      <input type="button" id="delete_audio" class="" value="Delete Audio File(s)" data-id="<?php echo $post->ID ?>"/>
    </div>
    <?php
    //
  }

  /*
  * Display audio
  *
  */
  private function admin_display_audio($postID) {

    $datacall = get_post_meta($postID, 'mafgud_post_audio_set');

    $_list_audio = '<ul id="audio_sortable">'; // Start list
    $produce_list_items = "";

    // Check to see if the value returned in $datacall_to_explode is an empty array
    // if not cycle through the array and assign id and source to audio.
    if (!empty($datacall[0])) :
      $audios = explode(',', $datacall[0]);
      foreach ($audios as $audio) :
        $file = get_attached_file($audio);
        //Cycle through ids to create thumbnail audio
        $produce_list_items .= '<li class="ui-state-default"><span id="' . $audio . '"><i class="fas fa-record-vinyl"></i> '. get_the_title($audio) . '</span></li>';
      endforeach;
    endif;
    if ($produce_list_items) {
      $_list_audio .= $produce_list_items;
      $_list_audio .= "</ul>";
    } else {
      $_list_audio = "";
    }

    return $_list_audio;
  }

  /*
  * Audio Save Function
  *
  */
  public function MAFGUD_save_audio() {

    //WP Ajax nonce check
    check_ajax_referer( 'MAFGUD', 'security' );

    $audio_ids = $_POST['audio_ids']; // Grab incoming ids
    $postID = $_POST['postID']; // Grab Post ID
    $mafgud_post_audio_set = get_post_meta($postID, 'mafgud_post_audio_set'); // Grab existing ids in 'mafgud_post_audio_set' postmeta key
    $return_data_array = [];  // Return array shell

    // Check the user's permissions.
    if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_page', $postID ) ) {
            return;
        }
    }
    else {
        if ( ! current_user_can( 'edit_post', $postID ) ) {
            return;
        }
    }

    if(count($audio_ids) > 0) : // Truly, just a safe guard to stop empty img tags
      if(count($mafgud_post_audio_set) > 0) : // If audio ids exist in database prepare to prepend database information with new ids
        $join = array_merge($audio_ids, $mafgud_post_audio_set);  // Prepend incoming audio ids to the front of the database ids
        $save_data = implode(",", $join); // Convert array to comma separated string value for data storage
      else :  // If database is empty
        $save_data = implode(",", $audio_ids);  // Convert array to comma separated string value for data storage
      endif;
      $return_data_array["updated"] = update_post_meta( $postID, 'mafgud_post_audio_set', trim($save_data, ",") );  // Update database and return success or failure. Trim removes the trailing comma from the string
      if($return_data_array["updated"] !== false) : // Check for data saved if true grab all audio...
        $return_data_array["audio"] = $this->admin_display_audio($postID);
      endif;
    endif;

    // Return data to javascript
    echo json_encode($return_data_array);

    wp_die();
    //
  }


  /*
  * Audio Deletion Function for front-end display
  *
  */
  public function MAFGUD_sort_audio() {

    $postID = $_POST['postID'];
    $audio_ids = $_POST['audio_ids'];
    $return_data_array = [];  // Return array shell

    if(count($audio_ids) > 0) : // Truly, just a safe guard to stop empty img tags
      foreach ($audio_ids as $key => $value) :
        if(!is_numeric($value)) :
          unset($audio_ids[$key]);
        endif;
      endforeach;
      $save_data = implode("," ,$_POST['audio_ids']);
      $return_data_array["updated"] = update_post_meta( $postID, 'mafgud_post_audio_set', trim($save_data, ",") );  // Update database and return success or failure. Trim removes the trailing comma from the string
      if($return_data_array["updated"] !== false) : // Check for data saved if true grab all audio...
        $return_data_array["audio"] = $this->admin_display_audio($postID);
      endif;
    endif;

    // Return data to javascript
    echo json_encode($return_data_array);

    wp_die();
    //
  }



  /*
  * Audio Deletion Function for front-end display
  *
  */
  public function MAFGUD_delete_audio() {
    //
    $postID = $_POST['postID'];
    $audio_ids = $_POST['audio_ids'];
    $mafgud_post_audio_set = explode(",", get_post_meta($postID, 'mafgud_post_audio_set')[0]);
    $remainder_array = array_diff($mafgud_post_audio_set, $audio_ids);
    $saved_data = implode(",", $remainder_array);

    // Update database and return success or failure. Trim removes the trailing comma from the string
    $return_data_array["updated"] = update_post_meta( $postID, 'mafgud_post_audio_set', $saved_data  );
    $mafgud_post_audio_set = explode(",", get_post_meta($postID, 'mafgud_post_audio_set')[0]);
    // echo $postID . " | " . print_r($audio_ids, true) . " | " . print_r($mafgud_post_audio_set, true) . " | " . print_r($remainder_array, true);
    $return_data_array["audio"] = $this->admin_display_audio($postID);

    echo json_encode($return_data_array);

    wp_die();
    //
  }

  /*
  * Shortcode Function for front-end display
  *
  */
  function MAFGUD_shortcode ($atts, $content = "") {

    /*
    Saved for making columns.
    $halved = array_chunk($books, ceil(count($books)/2));

    Then $halved[0] will contain the first half of the array.
    It will always be 1 element larger in the event that the array contains an odd number of elements.
    Of course, $halved[1] will contain the 2nd half of the array.
    */

    $audio = get_post_meta( $atts['id'], 'mafgud_post_audio_set');

    $atts = shortcode_atts( array(
      'id' => '',
      'title' => '',
  	), $atts, 'mafgud_post_audio_set' );

    if($audio) {
      // print_r($audio[0]);
      $explode = explode(",", $audio[0]);
      $count = count($explode);
      $output = "\n" . '<!--- Multiple Audio File Gallery Uploader (MAFGUD)---!>' . "\n";
      $output .= '<div id="mafgud-render">' . "\n";
      $output .= "\t<h5 class='mafgud-title'>" . $atts['title'] . "</h5>\n";

      foreach($explode as $audio) :
        $title = get_the_title($audio);
        $alt = get_post_meta($audio, '_wp_attachment_audio_alt', TRUE);
        $track = wp_get_attachment_url($audio);
        // Build HTML //
        $output .= "\t<span>\n";
        $output .= "\t\t<h6 class='mafgud-song-title'>" . $title . "</h6>\n";
        $output .= "\t\t" . '<audio controls>' . "\n";
        $output .= "\t\t\t" . '<source src="' . $track . '" type="audio/mpeg" alt="' . $title . '">' . "\n";
        $output .= "\t\t\t" . '<p>Your browser doesn\'t support HTML5 audio. Please check us our on Social Media Accounts.</p>' . "\n";
        $output .= "\t\t" . '</audio>' . "\n";
        $output .= "\t</span>" . "\n";
      endforeach;

      $output .= "</div>\n";
      $output .= '<!--- End (MAFGUD)---!>' . "\n";

      return $output;
    }

  }

}

$iud = new MAFGUD();
?>
