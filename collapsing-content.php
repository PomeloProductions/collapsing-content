<?php
/*
   Plugin Name: Rules and Regulations
   Plugin URI: http://wordpress.org/extend/plugins/rules-regulations/
   Version: 0.1.0
   Author: Axolotl Interactive
   Description: 
   Text Domain: rules-regulations
   License: GPLv3
  */

namespace CoillapsingContent;

function has_word_wrap() {
    if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( 'word-wrap/word-wrap.php' ) ) {
        add_action( 'admin_notices', 'child_plugin_notice' );

        deactivate_plugins( plugin_basename( __FILE__ ) );

        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }
}
add_action( 'admin_init', '\RulesRegulations\has_word_wrap' );

function child_plugin_notice(){
    ?><div class="error"><p>Sorry, but Child Plugin requires the Parent plugin to be installed and active.</p></div><?php
}

function autoload($className) {
    $fileName = str_replace("RulesRegulations\\", "", $className);
    $fileName = str_replace("\\", "/", $fileName);
    if(file_exists(__DIR__ . "/classes/" . $fileName . ".php"))
        require(__DIR__ . "/classes/" . $fileName . ".php");
}

spl_autoload_register(__NAMESPACE__ . "\\autoload");

/*
    "WordPress Plugin Template" Copyright (C) 2015 Michael Simpson  (email : michael.d.simpson@gmail.com)

    This following part of this file is part of WordPress Plugin Template for WordPress.

    WordPress Plugin Template is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    WordPress Plugin Template is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Contact Form to Database Extension.
    If not, see http://www.gnu.org/licenses/gpl-3.0.html
*/

$minimalRequiredPhpVersion = '5.4';

/**
 * Check the PHP version and give a useful error message if the user's version is less than the required version
 * @return boolean true if version check passed. If false, triggers an error which WP will handle, by displaying
 * an error message on the Admin page
 */
function noticePhpVersionWrong() {
    global $minimalRequiredPhpVersion;
    echo '<div class="updated fade">' .
        __('Error: plugin "Rules Regulations" requires a newer version of PHP to be running.',  'rules-regulations').
        '<br/>' . __('Minimal version of PHP required: ', 'rules-regulations') . '<strong>' . $minimalRequiredPhpVersion . '</strong>' .
        '<br/>' . __('Your server\'s PHP version: ', 'rules-regulations') . '<strong>' . phpversion() . '</strong>' .
        '</div>';
}


function PhpVersionCheck() {
    global $minimalRequiredPhpVersion;
    if (version_compare(phpversion(), $minimalRequiredPhpVersion) < 0) {
        add_action('admin_notices', 'PhpVersionCheck');
        return false;
    }
    return true;
}


/**
 * Initialize internationalization (i18n) for this plugin.
 * References:
 *      http://codex.wordpress.org/I18n_for_WordPress_Developers
 *      http://www.wdmac.com/how-to-create-a-po-language-translation#more-631
 * @return void
 */
function i18n_init() {
    $pluginDir = dirname(plugin_basename(__FILE__));
    load_plugin_textdomain('rules-regulations', false, $pluginDir . '/languages/');
}


//////////////////////////////////
// Run initialization
/////////////////////////////////

// First initialize i18n
i18n_init();


// Next, run the version check.
// If it is successful, continue with initialization for this plugin
if (PhpVersionCheck()) {
    // Only load and run the init function if we know PHP version can parse it
    include_once(__DIR__ . '/../word-wrap/word-wrap.php');
    WordWrap_init(basename(__DIR__));
}
