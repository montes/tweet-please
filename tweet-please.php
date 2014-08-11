<?php
/**
 * Tweet Please is a minimal plugin that let's you have auto-tweeted all your
 * new posts.
 *
 * I was "forced" to write it because I was unable to find one that worked well
 * and was free.
 *
 * To send tweets this plugin uses Codebird: https://github.com/jublonet/codebird-php
 *
 * @copyright 2014 Javier Montes - montesjmm.com - @montesjmm
 *
 * Plugin Name: Tweet Please
 * Plugin URI: http://montesjmm.com/tweet-please
 * Description: Automatically writes a new tweet when a new post is published
 * Version: 1.0b
 * Author: Javier Montes
 * Author URI: http://montesjmm.com
 * Author twitter: @montesjmm
 * License: GPL2
 */

require_once(plugin_dir_path(__FILE__) . 'vendor/codebird.php');
require_once(plugin_dir_path(__FILE__) . 'Montesjmm/TweetPlease.php');

add_action('plugins_loaded', array('Montesjmm\\TweetPlease', 'getInstance'));

if (is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX)) {
	require_once(plugin_dir_path(__FILE__) . 'Montesjmm/TweetPleaseAdmin.php');
	add_action('plugins_loaded', array('Montesjmm\\TweetPleaseAdmin', 'getInstance'));
}
