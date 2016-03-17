<?php
namespace OCA\Files_Report;

class Hooks {

	public static function connectHooks() {
		// Listen to write signals
		$eventDispatcher = \OC::$server->getEventDispatcher();
		$eventDispatcher->addListener('OCA\Files::loadAdditionalScripts', ['OCA\Files_Report\Hooks', 'onLoadFilesAppScripts']);
	}
	/**
	 * Load additional scripts when the files app is visible
	 */
	public static function onLoadFilesAppScripts() {
		\OCP\Util::addScript('files_report', 'filesplugin');
		\OCP\Util::addScript('files_report', 'filesreporttabview');
		\OCP\Util::addScript('files_report', 'filesreportmodel');
	}
}
