<?php
/*
Plugin Name: Randomize CSS
Plugin URI: http://blog.fleischer.hu/wordpress/randomize-css/
Description: Randomizes css in a customizable way.
Version: 0.4
Author: Gavriel Fleischer
Author URI: http://blog.fleischer.hu/author/gavriel/
*/

require_once 'common.php';

define('RANDOMIZE_CSS_BASE_DIR', PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)).'/');
define('RANDOMIZE_CSS_BASE_URL', WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)) . '/');
define('RANDOMIZE_CSS_CSS_URL', RANDOMIZE_CSS_BASE_URL . 'css.php');


// Multi-language support
if (defined('WPLANG') && function_exists('load_plugin_textdomain')) {
	load_plugin_textdomain('randomize-css', RANDOMIZE_CSS_BASE_DIR . 'lang', dirname(plugin_basename(__FILE__)).'/lang');
}

function randomize_css_style($args) {
	$defaults = array(
		'source' => 'files',
		'template' => '',
	);
	$args = wp_parse_args($args, $defaults);
	$options = get_option('randomize_css');
	$options = wp_parse_args($options, $args);

	$source = $options['source'];

	if ('files' == $source) {
		echo '<' . 'link rel="stylesheet" href="' . RANDOMIZE_CSS_CSS_URL .'" type="text/css" media="screen" id="randomize-css-style" /' . '>' ."\n";
	} else if ('db' == $source) {
		$template = attribute_escape($options['template']);
		if (!empty($template)) {
			echo '<style type="text/css">' . "\n";
			echo "/* Randomize CSS */\n";
			echo randomize_css_process_tpl($template) . "\n";
			echo "</style>\n";
		}
	}
}

/**
 * Manage WordPress Tag Cloud widget options.
 *
 * Displays management form for changing the tag cloud widget title.
 *
 * @since 2.3.0
 */
function randomize_css_options() {
	$options = $newoptions = get_option('randomize_css');
	if (isset($_POST['randomize-css-submit'])) {
		$newoptions['source'] = strip_tags(stripslashes($_POST['randomize-css-source']));
		$newoptions['template'] = strip_tags(stripslashes($_POST['randomize-css-template']));
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('randomize_css', $options);
	}
	$source = attribute_escape($options['source']);
	$template = attribute_escape($options['template']);
?>
<form action="?page=randomize-css" method="post">
	<p><?php _e('It is highly recommended to save the templates into files.', 'randomize-css') ?></p>
	<p>
		<?php _e('Template source', 'randomize-css') ?>:
		<label for="randomize-css-source-files"><input type="radio" class="radio" id="randomize-css-source-files" name="randomize-css-source" value="files"<?php echo 'files' == $source || '' == $source ? ' checked="checked"' : '' ?> /> <?php _e('Files','randomize-css') ?></label>
		<?php printf(__('Save your templates into "%s" directory and name them "%s".', 'randomize-css'), dirname(__FILE__).'/templates/css/', '&lt;NAME&gt;.css.tpl') ?>
	</p>
	<p>
		<?php _e('Template source', 'randomize-css') ?>:
		<label for="randomize-css-source-db"><input type="radio" class="radio" id="randomize-css-source-db" name="randomize-css-source" value="db"<?php echo 'db' == $source ? ' checked="checked"' : '' ?> /> <?php _e('Database','randomize-css') ?></label>
		<?php _e('Edit your template below:', 'randomize-css') ?>
	</p>
	<p>
		<label for="randomize-css-template">
			<?php _e('CSS Template', 'randomize-css') ?>:<br/>
			<textarea class="widefat" id="randomize-css-template" name="randomize-css-template"><?php echo $template ?></textarea>
		</label>
	</p>
	<p>
		<span class="submit"><input name="Submit" value="Update Options &raquo;" type="submit"></span>
		<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=MDHEGFZF7ZSY2&lc=IL&item_name=Randomize%20CSS%20Wordpress%20Plugin&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_LG%2egif%3aNonHosted" target="_blank"><img src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" alt="<?php _e('Donate'); ?>" /></a>
	</p>
	<input type="hidden" name="randomize-css-submit" id="randomize-css-submit" value="1" />
</form>
	<div>
		<h3><?php _e('Template format', 'randomize-css') ?></h3>
		<table>
			<tr><th><?php _e('code', 'randomize-css'); ?></th><th><?php _e('example values', 'randomize-css'); ?></th></tr>
			<tr><td>[COLOR]</td><td>#14f2a3</td></tr>
			<tr><td>[HEX1]</td><td>3, a</td></tr>
			<tr><td>[HEX2]</td><td>0f, 12</td></tr>
			<tr><td>[HEX3]</td><td>3a4, bce, 163</td></tr>
			<tr><th colspan="2"><?php _e('ranges', 'randomize-css'); ?></th></tr>
			<tr><td>[HEX1-x-y]</td><td>4, d</td></tr>
			<tr><td>[HEX2-xx-yy]</td><td>34, a1</td></tr>
			<tr><td>[HEX3-xxx-yyy]</td><td>111, a3b</td></tr>
			<tr><th colspan="2"><?php _e('variables', 'randomize-css'); ?></th></tr>
			<tr><td>def COLOR001=[COLOR]</td></tr>
			<tr><td>[COLOR001]</td><td>#815384</td></tr>
			<tr><th colspan="2"><?php _e('color sets', 'randomize-css'); ?></th></tr>
			<tr><td colspan="2">def PALETTE001={C1:#aaaaaa,C2:#bbbbbb,C3:fff}</td></tr>
			<tr><td colspan="2">def PALETTE002={C1:#111111,C2:#222,C3:777}</td></tr>
			<tr><td>[PALETTE.C1]</td><td>#aaaaaa</td></tr>
			<tr><td>[PALETTE.C2]</td><td>#bbbbbb</td></tr>
			<tr><td>[PALETTE.C3]</td><td>#fff</td></tr>
		</table>
	</div>
	<div>
		<h3><?php _e('Example template', 'randomize-css') ?></h3>
		<pre><code>
<?php
include ABSPATH . 'wp-content/plugins/randomize-css/templates/css/twentyfourteen.css.tpl';
?>
		</code></pre>
	</div>
<?php
}

function randomize_css_menu() {
	add_options_page('Randomize CSS', 'Randomize CSS', 'manage_options', 'randomize-css', 'randomize_css_options');
}

function randomize_css_onclick($echo = 1) {
	$js = 'document.getElementById(\'randomize-css-style\').href=\'' . RANDOMIZE_CSS_CSS_URL . '?rnd=\'+Math.random();return false';
	if ($echo) {
		echo $js;
	}
	return $js;
}

function randomize_css_a($text, $echo = 1) {
	$a = '<a href="#" onclick="' . randomize_css_onclick(0) . '">' . $text . '</a>';
	if ($echo) {
		echo $a;
	}
	return $a;
}

function randomize_css_widget($args, $widget_args = 1) {
	extract($args, EXTR_SKIP);                                                                                                                                                           
	if (is_numeric($widget_args))
		$widget_args = array( 'number' => $widget_args );
	$widget_args = wp_parse_args($widget_args, array('number' => -1));
	extract($widget_args, EXTR_SKIP);
	$options = get_option('widget_authors');
	if (isset($options[$number]))
		$options = $options[$number];
	$options = wp_parse_args($args, $options);

	$widget = $before_widget;
	$widget .= $before_title . __('Randomize CSS', 'randomize-css') . $after_title;
	$widget .= randomize_css_a('<img src="' . RANDOMIZE_CSS_BASE_URL . 'randomize-16x16.png"> ' . __('Randomize colors', 'randomize-css'), 0);
	$widget .= $after_widget;
	echo $widget;
	return $widget;
}

/**
 * Register all of the default WordPress widgets on startup.
 *
 * Calls 'widgets_init' action after all of the WordPress widgets have been
 * registered.
 *
 * @since 2.2.0
 */
function randomize_css_register() {
	if (!is_blog_installed()) {
		return;
	}
	$name = __( 'Randomize CSS','randomize-css' );
	wp_register_sidebar_widget( 'randomize-css-1', $name, 'randomize_css_widget');
	add_action('admin_menu', 'randomize_css_menu');
	add_action('wp_head', 'randomize_css_style');
}

add_action('init', 'randomize_css_register', 99999);

