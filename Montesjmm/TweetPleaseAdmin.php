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

class TweetPleaseAdmin {

	protected static $instance = null;

	protected $pluginSlug = 'tweet-please';

	protected static $options = array(
		array('name' => 'Tweet Text',                  'slug' => 'tp_tweet_text',                  'type' => 'textarea', 'default' => 'New post on my blog: {POST_TITLE} {POST_LINK}'),
		array('name' => 'Twitter Consumer Key',        'slug' => 'tp_twitter_consumer_key',        'type' => 'text',     'default' => NULL),
		array('name' => 'Twitter Consumer Secret',     'slug' => 'tp_twitter_consumer_secret',     'type' => 'text',     'default' => NULL),
		array('name' => 'Twitter Access Token',        'slug' => 'tp_twitter_access_token',        'type' => 'text',     'default' => NULL),
		array('name' => 'Twitter Access Token Secret', 'slug' => 'tp_twitter_access_token_secret', 'type' => 'text',     'default' => NULL),
		array('name' => 'Attach Post Thumbnail',       'slug' => 'tp_attach_post_thumbnail',       'type' => 'checkbox', 'default' => NULL),
		array('name' => 'Send When',                   'slug' => 'tp_send_when',                   'type' => 'select',   'default' => array(
			array('name' => 'Inmediately',   'value' => 'inmediately'),
			array('name' => 'After 1 hour',  'value' => '60'),
			array('name' => 'After 2 hours', 'value' => '120'),
			array('name' => 'After 4 hours', 'value' => '240'),
			array('name' => 'At next 7am',   'value' => '7'),
			array('name' => 'At next 8am',   'value' => '8'),
			array('name' => 'At next 9am',   'value' => '9'),
			array('name' => 'At next 10am',  'value' => '10'),
			array('name' => 'At next 12am',  'value' => '12'),
			array('name' => 'At next 1pm',   'value' => '13'),
			array('name' => 'At next 5pm',   'value' => '17'),
			array('name' => 'At next 8pm',   'value' => '20'),
			array('name' => 'At next 10pm',  'value' => '22'),
			array('name' => 'At next 12pm',  'value' => '0'),
			)),
	);

	protected function __construct()
	{
		add_action('admin_menu', array($this, 'addSettingsMenu'));
	}

	public static function getInstance()
	{
		if (null == self::$instance) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function addSettingsMenu()
	{
		add_options_page('Tweet Please Settings', 'Tweet Please', 'manage_options', 'tweet-please', array($this, 'showSettingsForm'));
	}

	public function showSettingsForm()
	{
		if ($_SERVER['REQUEST_METHOD'] == 'GET') {
			$log     = \Montesjmm\TweetPleaseAdmin::getLog();
			$options = self::$options;

			include dirname(__FILE__) . '/../views/settings-form.php';
		} else {
			$this->saveSettings();
		}
	}

	protected function saveSettings()
	{
		foreach (self::$options as $option) {
			update_option($option['slug'], $_POST[$option['slug']]);
		}

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$this->showSettingsForm();
	}

	public static function getLog()
	{
		global $wpdb;

		$log = $wpdb->get_col($wpdb->prepare("
			SELECT meta_value FROM {$wpdb->postmeta}
			WHERE meta_key = '%s'
			ORDER BY meta_id DESC
			", 'TPLog'));

		array_walk($log, function(&$value) { $value = unserialize($value); });

		return $log;
	}

}


