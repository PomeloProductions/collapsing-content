<?php
/*
   Plugin Name: Collapsing Content
   Plugin URI: http://wordpress.org/extend/plugins/rules-regulations/
   Version: 0.7.0
   Author: Axolotl Interactive
   Description: Plugin for adding open and closing content to a site
   Text Domain: collapsing-content
   License: GPLv3
  */

namespace CollapsingContent;

use WordWrap;

function hasWordWrap() {
    if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( 'word-wrap/word-wrap.php' ) ) {
        add_action( 'admin_notices', '\CollapsingContent\showInstallErrorMessage' );

        deactivate_plugins( plugin_basename( __FILE__ ) );

        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }
}
add_action( 'admin_init', '\CollapsingContent\hasWordWrap' );

function showInstallErrorMessage(){
    echo '<div class="error"><p>Sorry, but Collapsing Content requires Word Wrap to be installed and active.</p></div>';
}

function autoload($className) {
    $fileName = str_replace("CollapsingContent\\", "", $className);
    $fileName = str_replace("\\", "/", $fileName);
    if(file_exists(__DIR__ . "/classes/" . $fileName . ".php"))
        require(__DIR__ . "/classes/" . $fileName . ".php");
}

spl_autoload_register(__NAMESPACE__ . "\\autoload");


include_once(__DIR__ . '/../word-wrap/word-wrap.php');
WordWrap::init(basename(__DIR__));
