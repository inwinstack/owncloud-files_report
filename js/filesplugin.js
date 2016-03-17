/*
 * Copyright (c) 2015
 *
 * This file is licensed under the Affero General Public License version 3
 * or later.
 *
 * See the COPYING-README file.
 *
 */

(function() {

	OCA.Files_Reports = OCA.Files_Reports || {};

	/**
	 * @namespace
	 */
	OCA.Files_Reports.Util = {
		/**
		 * Initialize the versions plugin.
		 *
		 * @param {OCA.Files.FileList} fileList file list to be extended
		 */
		attach: function(fileList) {
			if (fileList.id === 'trashbin' || fileList.id === 'files.public') {
				return;
			}

			fileList.registerTabView(new OCA.Files_Reports.Files_ReportTabView('filesReportTabView', {order: -10}));
		}
	};
})();

OC.Plugins.register('OCA.Files.FileList', OCA.Files_Reports.Util);

