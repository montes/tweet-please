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

	protected static $options = array();

	protected $pluginSlug = 'tweet-please';


	protected function __construct()
	{
		add_action('admin_menu', array($this, 'addSettingsMenu'));
		add_action('init', array($this, 'setOptions'));
	}

	public static function getInstance()
	{
		if (null == self::$instance) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function setOptions()
	{
		self::$options = array(
			array('name' => __('Tweet Text', $this->pluginSlug),                  'slug' => 'tp_tweet_text',                  'type' => 'textarea', 'default' => 'New post on my blog: {POST_TITLE} {POST_LINK}'),
			array('name' => __('Twitter Consumer Key', $this->pluginSlug),        'slug' => 'tp_twitter_consumer_key',        'type' => 'text',     'default' => NULL),
			array('name' => __('Twitter Consumer Secret', $this->pluginSlug),     'slug' => 'tp_twitter_consumer_secret',     'type' => 'text',     'default' => NULL),
			array('name' => __('Twitter Access Token', $this->pluginSlug),        'slug' => 'tp_twitter_access_token',        'type' => 'text',     'default' => NULL),
			array('name' => __('Twitter Access Token Secret', $this->pluginSlug), 'slug' => 'tp_twitter_access_token_secret', 'type' => 'text',     'default' => NULL),
			array('name' => __('Attach Post Thumbnail', $this->pluginSlug),       'slug' => 'tp_attach_post_thumbnail',       'type' => 'checkbox', 'default' => NULL),
			array('name' => __('Send When', $this->pluginSlug),                   'slug' => 'tp_send_when',                   'type' => 'select',   'default' => array(
				array('name' => __('Inmediately', $this->pluginSlug),   'value' => 'inmediately'),
				array('name' => __('After 1 hour', $this->pluginSlug),  'value' => '60'),
				array('name' => __('After 2 hours', $this->pluginSlug), 'value' => '120'),
				array('name' => __('After 4 hours', $this->pluginSlug), 'value' => '240'),
				array('name' => __('At next 7am', $this->pluginSlug),   'value' => '7'),
				array('name' => __('At next 8am', $this->pluginSlug),   'value' => '8'),
				array('name' => __('At next 9am', $this->pluginSlug),   'value' => '9'),
				array('name' => __('At next 10am', $this->pluginSlug),  'value' => '10'),
				array('name' => __('At next 12am', $this->pluginSlug),  'value' => '12'),
				array('name' => __('At next 1pm', $this->pluginSlug),   'value' => '13'),
				array('name' => __('At next 5pm', $this->pluginSlug),   'value' => '17'),
				array('name' => __('At next 8pm', $this->pluginSlug),   'value' => '20'),
				array('name' => __('At next 10pm', $this->pluginSlug),  'value' => '22'),
				array('name' => __('At next 12pm', $this->pluginSlug),  'value' => '0'),
			)),
		);
	}

	public function addSettingsMenu()
	{
		add_options_page(__('Tweet Please Settings', $this->pluginSlug), 'Tweet Please', 'manage_options', 'tweet-please', array($this, 'showSettingsForm'));
	}

	public function showSettingsForm()
	{
		if ($_SERVER['REQUEST_METHOD'] == 'GET') {
			$log        = \Montesjmm\TweetPleaseAdmin::getLog();
			$options    = self::$options;
			$pluginSlug = $this->pluginSlug;

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


