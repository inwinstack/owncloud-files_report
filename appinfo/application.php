<?php

/**
 * ownCloud - Activity App
 *
 * @author Joas Schilling
 * @copyright 2014 Joas Schilling nickvergessen@owncloud.com
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace OCA\Files_Report\AppInfo;

use OCP\AppFramework\App;
use OCP\IContainer;
use OCA\Files_Report\Data;
use OCA\Files_Report\Controller\FilesReportController;

class Application extends App {
	public function __construct (array $urlParams = array()) {
		parent::__construct('files_report', $urlParams);
		$container = $this->getContainer();
        

        $container->registerService('ActivityApplication', function($c){
                return new \OCA\Activity\AppInfo\Application();
        });

		$container->registerService('ReportData', function(IContainer $c) {
			/** @var \OC\Server $server */
			$server = $c->query('ServerContainer');
			return new Data(
				$server->getDatabaseConnection(),
				$server->getUserSession(),
                $c->query('ActivityApplication')->getContainer()->query('ActivityData')
			);
		});

		$container->registerService('FilesReportController', function(IContainer $c) {
			/** @var \OC\Server $server */
			$server = $c->query('ServerContainer');

			return new FilesReportController(
				$c->query('AppName'),
				$server->getRequest(),
				$c->query('ReportData')
			);
		});
	}
}
