<?php

namespace App\baseClasses;

class AADBase {

	public $plugin_path;

	public $nameSpace;

	public $plugin_url;

	public $plugin;

	public $dbConfig;

	private $pluginPrefix;

	public function __construct() {

		$this->plugin_path = plugin_dir_path( dirname( __FILE__, 2 ) );
		$this->plugin_url  = plugin_dir_url( dirname( __FILE__, 2 ) );

		$this->nameSpace    = AWESOME_ADMIN_NAMESPACE;
		$this->pluginPrefix = AWESOME_ADMIN_PREFIX;

		$this->plugin = plugin_basename( dirname( __FILE__, 3 ) ) . '/awesome-admin.php';

		$this->dbConfig = [
			'user' => DB_USER,
			'pass' => DB_PASSWORD,
			'db'   => DB_NAME,
			'host' => DB_HOST
		];

	}

	public function get_namespace() {
		return $this->nameSpace;
	}

	protected function getPrefix() {
		return AWESOME_ADMIN_PREFIX;
	}

	protected function getPluginPrefix() {
		return $this->pluginPrefix;
	}

}
