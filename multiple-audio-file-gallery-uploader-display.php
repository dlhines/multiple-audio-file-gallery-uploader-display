<?php
/**
* Plugin Name: Multiple Audio File Uploader Display (MAFGUD)
* Description: Audio file uploader for audio galleries (per post type). Allows update free uploading, sortiing, and deleting in real time. Uses JQuery UI utilities.
* Author: Daniel Hines (dlhines.net)
**/

define('MAFGUD_PLUGIN_FOLDER', dirname(__FILE__) );
define('MAFGUD_PLUGIN_BASE_FILENAME', plugin_basename(__FILE__));

// Grab plugin directory name from the plugin main file name
define('MAFGUD_PLUGIN_DIR', plugins_url() . "/" . str_replace(".php","", substr(MAFGUD_PLUGIN_BASE_FILENAME, strpos(MAFGUD_PLUGIN_BASE_FILENAME, "/") + 1)) . "/");

class MAFGUD_initiate {

    public function __construct() {

      // Include Administration Page
      require ( MAFGUD_PLUGIN_FOLDER . '/administration/mafgud_administration.php' );

      // Include IUD post_type_display and render frontend
      require ( MAFGUD_PLUGIN_FOLDER . '/mafgud/mafgud.php' );

    }

}

$initiate = new MAFGUD_initiate();
