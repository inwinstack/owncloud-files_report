<?php
/**
 * ownCloud - files_report
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author eric <eric.y@inwinstack.com>
 * @copyright eric 2016
 */

/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\Files_Report\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
return [
    'routes' => [
	   ['name' => 'filesreport#readReport', 'url' => '/', 'verb' => 'GET'],
	   ['name' => 'page#do_echo', 'url' => '/echo', 'verb' => 'POST'],
	   ['name' => 'filesreport#sendReport', 'url' => '/sendReport', 'verb' => 'POST'],
	   ['name' => 'filesreport#returnReport', 'url' => '/returnReport', 'verb' => 'POST'],
    ]
];
