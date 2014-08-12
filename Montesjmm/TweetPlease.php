<?php
/**
 * Tweet Please
 *
 * @package   Tweet Please
 * @author    Javier Montes <javier@montesjmm.com> @montesjmm
 * @license   GPL-2.0
 * @link      http://montesjmm.com/tweet-please
 * @copyright 2014 Javier Montes
 */

namespace Montesjmm;

class TweetPlease {

	protected static $instance = null;
	protected $plugin_slug = 'tweet-please';

	protected function __construct()
	{
		add_action('init',             array($this, 'loadPluginTextdomain'));
		add_action('programmed_tweet', array($this, 'sendTweet'));
		add_action('publish_post',     array($this, 'postPublished'));
	}

	public static function getInstance()
	{
		if (null == self::$instance) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function loadPluginTextdomain()
	{
		$domain = $this->plugin_slug;
		$locale = apply_filters('plugin_locale', get_locale(), $domain);

		load_textdomain($domain, trailingslashit(WP_LANG_DIR) . $domain . '/' . $domain . '-' . $locale . '.mo');
		load_plugin_textdomain($domain, FALSE, basename(plugin_dir_path(dirname(__FILE__))) . '/languages/');
	}

	public function postPublished($postId)
	{
		$when              = get_option('tp_send_when');
		$scheduleTimestamp = NULL;

		if ($when == 'inmediately') {
			$this->sendTweet($postId);
		} elseif (is_numeric($when) && $when < 24) {
			if (date('H') >= $when) {
				$scheduleTimestamp = strtotime('next day ' . $when . ':00 ');
			} else {
				$scheduleTimestamp = strtotime(date('Y-m-d ') . $when . ':00 ');
			}
		} elseif (is_numeric($when) && $when > 23) {
			$scheduleTimestamp = strtotime('+' . $when . 'minute');
		}

		if ($scheduleTimestamp) {
			wp_schedule_single_event($scheduleTimestamp, 'programmed_tweet', array($postId));
		}
	}

	public function sendTweet($postId)
	{
		$post = get_post($postId);

		Codebird\Codebird::setConsumerKey(get_option('tp_twitter_consumer_key'), get_option('tp_twitter_consumer_secret'));
		$cb = Codebird\Codebird::getInstance();
		$cb->setToken(get_option('tp_twitter_access_token'), get_option('tp_twitter_access_token_secret'));

		$status = get_option('tp_tweet_text');
		$status = str_ireplace('{POST_LINK}', get_permalink($postId), $status);
		$status = str_ireplace('{POST_TITLE}', __($post->post_title), $status);

		$tweet = array(
			'status' => $status
		);

		$this->saveResponse($postId, $cb->statuses_update($tweet));
	}

	protected function saveResponse($ID, $response)
	{
		if (!empty($response->created_at) &&
			!empty($response->text)) {

			add_post_meta($ID, 'TPLog', array('text' => 'POSTED: ' . $response->text, 'date' => date('Y-m-d H:i')), false);
		} elseif (!empty($response->errors)) {
			add_post_meta($ID, 'TPLog', array('text' => 'ERROR: ' . $response->errors[0]->message, 'date' => date('Y-m-d H:i')), false);
		} else {
			add_post_meta($ID, 'TPLog', array('text' => 'ERROR: Unknown error', 'date' => date('Y-m-d H:i')), false);
		}
	}

}
