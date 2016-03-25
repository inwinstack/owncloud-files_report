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

namespace OCA\Files_Report\AppInfo;
if(\OC_User::isAdminuser(\OC_User::getUser())) {
    \OCP\App::addNavigationEntry([
        // the string under which your app will be referenced in owncloud
        'id' => 'files_report',

        // sorting weight for the navigation. The higher the number, the higher
        // will it be listed in the navigation
        'order' => 10,

        // the route that will be shown on startup
        'href' => \OCP\Util::linkToRoute('files_report.filesreport.readReport'),

        // the icon that will be shown in the navigation
        // this file needs to exist in img/
        'icon' => \OCP\Util::imagePath('files_report', 'app.svg'),

        // the title of your application. This will be used in the
        // navigation or on the settings page of your app
        'name' => \OC_L10N::get('files_report')->t('Files Report')
    ]);
}

\OCA\Files_Report\Hooks::connectHooks();
