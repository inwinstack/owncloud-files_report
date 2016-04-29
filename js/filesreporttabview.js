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

        var TEMPLATE = "<input type='radio' name='reportMsg' value='{{data.value}}' id='{{data.value}}' {{#if isreported}} disabled {{/if}} {{#if data.checked}} checked {{/if}} /> <label for='{{data.value}}'>{{data.msg}}</label> <br>";

        var BUTTON = "<input type='submit' id= '{{id}}' value='{{submit}}' data-id={{data}}>";

        
	var Files_ReportTabView = OCA.Files.DetailTabView.extend({
		id: 'filesReportTabView',
		className: 'tab',

		_template: null,

        reportmodel: undefined,

		$versionsContainer: null,

		events: {
            'click #FileReportSend': '_sendReport',
            'click #cancelReport': '_cancelReport',
		},

		initialize: function() {
			OCA.Files.DetailTabView.prototype.initialize.apply(this, arguments);
            this.reportmodel = new OCA.Files_Reports.ReportModel();
		},

        _sendReport: function() {
            var filePath = this.reportmodel.getFilePath();
            var reportId = $('input[name=reportMsg]:checked').val();
            var fileID = this.reportmodel.getFileID();
            var tabview = this;

            this.reportmodel.sendReport(filePath, fileID, reportId).done(function(data) {
                var msg = data.status == 'success' ? 'Your report will be send to administractor.' : 'Send Report failed';

                OC.Notification.showTemporary(t('files_report', msg));
                tabview.render();
            });
        },

        _cancelReport: function() {
            var id = $('#cancelReport').data('id');
            var tabview = this;

            this.reportmodel.cancelReport(id).done(function(data) {
                var msg = data.status == 'success' ? 'Cancel report success' : 'Cancel report failed';

                OC.Notification.showTemporary(t('files_report', msg));
                tabview.render();
            });
        },
        
        template: function(data, templatestr) {
		    this._template = Handlebars.compile(templatestr);

			return this._template(data);
		},
	
        getLabel: function(){
            return t('files_report', 'Reports');
        },

		/**
		 * Renders this details view
		 */
		render: function() {

            this.$el.find('input').remove();
            this.$el.find('label').remove();
            this.$el.find('br').remove();

            var datas = [{value: '0', msg: t('files_report', 'Include bad words or graphs.'), checked: false},
                         {value: '1', msg: t('files_report', 'This is a uncomfortable file.'), checked: false},
                         {value: '2', msg: t('files_report', "I think it shouldn't be on custum cloud."), checked: false},
                         {value: '3', msg: t('files_report', "It's spam file."), checked: false}];

            var id = false;
           

            this.reportmodel.checkReport(this.reportmodel.getFilePath(), this.reportmodel.getFileID()).done(function(data) {
                if(data.reported && data.reported != 'error') {
                    datas[parseInt(data.reported.reason)].checked = true;
                    id = data.reported.id;
                }
            });

            
            for(var i = 0; i < datas.length; i++) {
                this.$el.append(this.template({
                    data: datas[i],
                    isreported: id ? true : false,
                },TEMPLATE));
            }

            this.$el.append(this.template({
                submit: id ? t('files_report', 'Cancel Report'): t('files_report', 'Submit'),
                data: id ? id : -1,
                id : id ? 'cancelReport': 'FileReportSend'
            },BUTTON));
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
