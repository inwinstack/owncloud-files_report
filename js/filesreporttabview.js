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

		var TEMPLATE =
            
            '<input type="radio" name="reportMsg" value="1">{{reportMsg1}}<br>' +
            '<input type="radio" name="reportMsg" value="2">{{reportMsg2}}<br>' +
            '<input type="radio" name="reportMsg" value="3">{{reportMsg3}}<br>' +
            '<input type="submit" id="FileReportSend" value="{{submit}}">';
;
	var Files_ReportTabView = OCA.Files.DetailTabView.extend({
		id: 'filesReportTabView',
		className: 'tab',

		_template: null,

        reportmodel: undefined,

		$versionsContainer: null,

		events: {
            'click #FileReportSend': '_sendReport',
		},

		initialize: function() {
			OCA.Files.DetailTabView.prototype.initialize.apply(this, arguments);
            this.reportmodel = new OCA.Files_Reports.ReportModel();
		},

        _sendReport: function(){
            filePath = this.reportmodel.getFilePath();
            shareOwner = this.reportmodel.getShareOwner();
            reportId = $('input[name=reportMsg]:checked').val();

            this.reportmodel.sendReport(filePath, shareOwner, reportId);
        },
        
        template: function(data) {
			if (!this._template) {
				this._template = Handlebars.compile(TEMPLATE);
			}

			return this._template(data);
		},
	
        getLabel: function(){
            return t('filereports', 'Reports');
        },

		/**
		 * Renders this details view
		 */
		render: function() {
			this.$el.html(this.template({
				submit: t('files_report', 'submit'),
			}));
		},

		/**
		 * Returns true for files, false for folders.
		 *
		 * @return {bool} true for files, false for folders
		 */
		canDisplay: function(fileInfo) {
			if(!fileInfo || fileInfo.attributes.shareOwner == undefined) {
				return false;
			}
			if(fileInfo.isDirectory()) {
                return false;
            }

            this.reportmodel.setFilePath(fileInfo);
            this.reportmodel.setShareOwner(fileInfo);
            return true;
		}
	});


	OCA.Files_Reports = OCA.Files_Reports || {};

	OCA.Files_Reports.Files_ReportTabView = Files_ReportTabView;
})();
