<?php
/*
Plugin Name: Content Freeze
Plugin URI: http://www.wholegraindigital.com/
Description: The content freeze plugin would allow an admin to 'lock' all content on the site for a given time period
Version: 0.1.1
Author: Wholegrain Digital
Author URI: http://www.wholegraindigital.com/
License: GPL
Copyright: Wholegrain Digital
*/

if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

define('CONTENT_FREEZE_VERSION', '0.1.1');
define('CONTENT_FREEZE_RELEASE_DATE', date_i18n('F j, Y', '1397937230'));
define('CONTENT_FREEZE_DIR', plugin_dir_path(__FILE__));
define('CONTENT_FREEZE_URL', plugin_dir_url(__FILE__));

if (!class_exists("Content_Freeze")) :

class Content_Freeze {
    var $settings, $options_page;

    function __construct() {

        // Load example settings page
        if (!class_exists("Content_Freeze_Settings")) {
            require(CONTENT_FREEZE_DIR . 'settings.php');
        }

        $this->settings = new Content_Freeze_Settings();

        add_action('init', array($this,'init'));
        add_action('admin_init', array($this,'admin_init'));
        add_action('admin_menu', array($this,'admin_menu'));

        register_activation_hook(__FILE__, array($this,'activate'));
        register_deactivation_hook(__FILE__, array($this,'deactivate'));
    }

    /*
        Propagates pfunction to all blogs within our multisite setup.
        More details -
        http://shibashake.com/wordpress-theme/write-a-plugin-for-wordpress-multi-site

        If not multisite, then we just run pfunction for our single blog.
    */
    function network_propagate($pfunction, $networkwide) {
        global $wpdb;

        if (function_exists('is_multisite') && is_multisite()) {
            // check if it is a network activation - if so, run the activation function
            // for each blog id
            if ($networkwide) {
                $old_blog = $wpdb->blogid;
                // Get all blog ids
                $blogids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");
                foreach ($blogids as $blog_id) {
                    switch_to_blog($blog_id);
                    call_user_func($pfunction, $networkwide);
                }
                switch_to_blog($old_blog);
                return;
            }
        }
        call_user_func($pfunction, $networkwide);
    }

    function activate($networkwide) {
        $this->network_propagate(array($this, '_activate'), $networkwide);
    }

    function deactivate($networkwide) {
        $this->network_propagate(array($this, '_deactivate'), $networkwide);
    }

    /*
        Plugin activation code here.
    */
    function _activate() {}

    /*
        Plugin deactivation code here.
    */
    function _deactivate() {}


    /*
        Load language translation files (if any) for our plugin.
    */
    function init() {
        load_plugin_textdomain('content_freeze', CONTENT_FREEZE_DIR . 'lang', basename(dirname(__FILE__)) . '/lang');
    }

    function admin_init() {
    }

    function admin_menu() {
    }


    /*
        Example print function for debugging.
    */
    function print_example($str, $print_info=TRUE) {
        if (!$print_info) return;
        __($str . "<br/><br/>\n", 'content_freeze');
    }

    /*
        Redirect to a different page using javascript. More details-
        http://shibashake.com/wordpress-theme/wordpress-page-redirect
    */
    function javascript_redirect($location) {
        // redirect after header here can't use wp_redirect($location);
        ?>
          <script type="text/javascript">
          <!--
          window.location= <?php echo "'" . $location . "'"; ?>;
          //-->
          </script>
        <?php
        exit;
    }

} // end class
endif;

// Initialize our plugin object.
global $content_freeze;
if (class_exists("Content_Freeze") && !$content_freeze) {
    $content_freeze = new Content_Freeze();
}
?>