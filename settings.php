<?php
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

if (!class_exists("Content_Freeze_Settings")) :

class Content_Freeze_Settings {

	public static $default_settings = array(
		'allowed_id' => 0,
		'content_frozen' => 0,
	);

	var $pagehook, $page_id, $settings_field, $options;

	function __construct() {
		$this->page_id = 'content_freeze';
		// This is the get_options slug used in the database to store our plugin option values.
		$this->settings_field = 'content_freeze_options';
		$this->options = get_option($this->settings_field);

		if (is_admin()) {
			add_action('admin_init', array($this,'admin_init'), 20);
			add_action('admin_menu', array($this, 'admin_menu'), 20);
		}

		add_action('init', array($this,'init'), 20);
	}

	function init() {
		if($this->get_field_value('content_frozen')) {
			add_filter('login_message', array($this, 'login_warning_message'));
		}
	}

	function admin_init() {
		register_setting($this->settings_field, $this->settings_field, array($this, 'sanitize_theme_options'));
		add_option($this->settings_field, Content_Freeze_Settings::$default_settings);

		if($this->get_field_value('content_frozen') && isset($_GET['page']) && $_GET['page']!='content_freeze') {
			add_action('admin_notices', array($this, 'content_frozen_notice'));
		}

		if($this->get_field_value('content_frozen') && $this->get_field_value('allowed_id') != get_current_user_id()) {
			wp_logout();
			wp_redirect(wp_login_url());
		}
	}

	function login_warning_message($message) {
		$user_info = get_userdata($this->get_field_value('allowed_id'));
	    return '<div id="login_error">Content is frozen'.(!empty($user_info->user_firstname) ? ' by ' . $user_info->user_firstname . ' ' . $user_info->user_lastname : '').'. You can\'t login now.</div>';
	}

	function content_frozen_notice() {
	    ?>
	    <div class="update-nag" style="display: block;">
	        <p style="margin: 0;"><?php _e('<b>Warning!</b> Content is currently frozen for other admins. <a href="'.admin_url('options-general.php?page=content_freeze').'">Click here to change the settings</a>.', 'content_freeze') ?></p>
	    </div>
	    <?php
	}

	function admin_menu() {
		if (! current_user_can('update_plugins'))
			return;

		// Add a new submenu to the standard Settings panel
		$this->pagehook = $page = add_options_page(__('Content Freeze', 'content_freeze'), __('Content Freeze', 'content_freeze'), 'administrator', $this->page_id, array($this,'render'));

		// Executed on-load. Add all metaboxes.
		add_action('load-' . $this->pagehook, array($this, 'metaboxes'));

		// Include js, css, or header *only* for our settings page
		add_action("admin_print_scripts-$page", array($this, 'js_includes'));

		// add_action("admin_print_styles-$page", array($this, 'css_includes'));
		add_action("admin_head-$page", array($this, 'admin_head'));
	}

	function admin_head() {
?>
		<style>
			.settings_page_content_freeze label { display:inline-block; width: 150px; }
		</style>
<?php
	}

	function js_includes() {
		// Needed to allow metabox layout and close functionality.
		wp_enqueue_script('postbox');
	}

	/*
		Sanitize our plugin settings array as needed.
	*/
	function sanitize_theme_options($options) {
		if($options['content_frozen']) {
			$options['content_frozen'] = stripcslashes($options['content_frozen']);
		}
		return $options;
	}

	/*
		Settings access functions.
	*/
	protected function get_field_name($name) {

		return sprintf('%s[%s]', $this->settings_field, $name);
	}

	protected function get_field_id($id) {

		return sprintf('%s[%s]', $this->settings_field, $id);
	}

	protected function get_field_value($key) {

		return $this->options[$key];
	}

	/*
		Render settings page.
	*/
	function render() {
		global $wp_meta_boxes;

		$title = __('Content Freeze', 'content_freeze');
		?>
		<div class="wrap">
			<h2><?php echo esc_html($title); ?></h2>

			<form method="post" action="options.php">
				<input type="hidden" name="<?php echo $this->get_field_name('allowed_id'); ?>" id="<?php echo $this->get_field_id('allowed_id'); ?>" value="<?php echo get_current_user_id() ?>" />
                <div class="metabox-holder">
                    <div class="postbox-container" style="width: 99%;">
                    <?php
						// Render metaboxes
                        settings_fields($this->settings_field);
                        do_meta_boxes($this->pagehook, 'main', null);
                      	if (isset($wp_meta_boxes[$this->pagehook]['column2']))
 							do_meta_boxes($this->pagehook, 'column2', null);
                    ?>
                    </div>
                </div>
			</form>
		</div>

        <!-- Needed to allow metabox layout and close functionality. -->
		<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready(function ($) {
				// close postboxes that should be closed
				$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
				// postboxes setup
				postboxes.add_postbox_toggles('<?php echo $this->pagehook; ?>');
			});
			//]]>
		</script>
	<?php }


	function metaboxes() {

		add_meta_box('content-freeze-settings-box', __('Settings', 'content_freeze'), array($this, 'settings_box'), $this->pagehook, 'main', 'high');
	}

	function settings_box() {
		if($this->get_field_value('content_frozen')) {
?>
			<input type="hidden" name="<?php echo $this->get_field_name('content_frozen'); ?>" id="<?php echo $this->get_field_id('content_frozen'); ?>" value="0" />
			<p>You have <b>frozen</b> the content for other admins</p>
			<p>
				<input type="submit" class="button button-primary" name="save_options" value="<?php esc_attr_e('Unfreeze Content'); ?>" />
			</p>
<?php
		} else {
?>
			<input type="hidden" name="<?php echo $this->get_field_name('content_frozen'); ?>" id="<?php echo $this->get_field_id('content_frozen'); ?>" value="1" />
			<p>Content is currently <b>not frozen</b></p>
			<!-- <p>
				<label for="<?php echo $this->get_field_id('mbox_example_text'); ?>"><?php _e('Example Text', 'shiba_example'); ?></label>
				<input type="text" name="<?php echo $this->get_field_name('mbox_example_text'); ?>" id="<?php echo $this->get_field_id('mbox_example_text'); ?>" value="<?php echo esc_attr($this->get_field_value('mbox_example_text')); ?>" style="width:50%;" />
			</p> -->
			<p>
				<input type="submit" class="button button-primary" name="save_options" value="<?php esc_attr_e('Freeze Content'); ?>" />
			</p>
<?php
		}
	}

} // end class
endif;
?>