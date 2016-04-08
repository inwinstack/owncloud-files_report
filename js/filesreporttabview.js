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
            '<input type="radio" name="reportMsg" value="4">{{reportMsg4}}<br>' +
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
            reportId = $('input[name=reportMsg]:checked').val();
            fileID = this.reportmodel.getFileID();
            this.reportmodel.sendReport(filePath, fileID, reportId);
            OC.Notification.showTemporary(t('files_report', "Your report will be send to administractor."));
        },
        
        template: function(data) {
			if (!this._template) {
				this._template = Handlebars.compile(TEMPLATE);
			}

			return this._template(data);
		},
	
        getLabel: function(){
            return t('files_report', 'Reports');
        },

		/**
		 * Renders this details view
		 */
		render: function() {
			this.$el.html(this.template({
                reportMsg1: t('files_report', 'Include bad words or graphs.'),
                reportMsg2: t('files_report', 'This is a uncomfortable file.'),
                reportMsg3: t('files_report', "I think it shouldn't be on custum cloud."),
                reportMsg4: t('files_report', "It's spam file."),
				submit: t('files_report', 'Submit'),
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
            this.reportmodel.setFileID(fileInfo);
            return true;
		}
	});


	OCA.Files_Reports = OCA.Files_Reports || {};
	OCA.Files_Reports.Files_ReportTabView = Files_ReportTabView;
})();
