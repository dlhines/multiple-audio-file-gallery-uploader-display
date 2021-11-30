<?php

class MAFGUD_administration {

  public function __construct() {
    // Add Administration Page
    add_action( 'admin_menu' , array ( $this , 'plugin_admin_add_menu'));
    add_action( 'wp_ajax_mafgud_set_post_types', array( $this, 'MAFGUD_administration_set_post_types' ) );
  }

  public function plugin_admin_add_menu() {
    // Administration Page creation
    $hook = add_menu_page(
      'Multiple Audio File Gallery Uploader Display',
      'Multiple Audio File Gallery Uploader Display',
      'manage_options',
      'multiple-audio-file-gallery-uploader-display-administration',
      array( $this , 'MAFGUD_administration_main'), '');

    add_action( 'load-' . $hook , array( $this, 'MAFGUD_administration_assets' ) );
  }

  public function MAFGUD_administration_main() {
      require_once ( MAFGUD_PLUGIN_FOLDER . '/administration/templates/main.php' );
  }

  public function MAFGUD_administration_assets() {
    wp_enqueue_style( 'multiple-audio-file-gallery-uploader-display-administration', MAFGUD_PLUGIN_DIR . 'administration/css/multiple-audio-file-gallery-uploader-display-administration.css', array(), '0.0.0', 'all');
    wp_enqueue_script( 'multiple-audio-file-gallery-uploader-display-administration', MAFGUD_PLUGIN_DIR . 'administration/js/multiple-audio-file-gallery-uploader-display-administration.js', array('jquery'), null, true);
    wp_localize_script( 'multiple-audio-file-gallery-uploader-display-administration', 'mafgud_set_post_types',
      array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'ajax_nonce' => wp_create_nonce('MAFGUD')
      )
    );
  }

  public function MAFGUD_administration_set_post_types() {
    check_ajax_referer( 'MAFGUD', 'security' );
    $post_types = $_POST['post_types'];
    $post_types = implode(',', $post_types);

    $update = update_option('MAFGUD_post_types', $post_types);

    if($update = 1) {
      if(!empty($_POST['post_types'])) :
        echo "\nYou have successfully updated the Content Types\non which MAFGUD will be attached.";
      else :
        echo "You have cleared all Post Types.\n\nYou are no longer using MAFGUD.";
      endif;
    } else {
      echo "Error: Updating Content Types not Succesfull. Contact Administrator.\n";
    };

    wp_die();
  }

}

$mafgud_admin = new MAFGUD_administration();

?>
