(function() {
    var filePath;
    var shareOwner

	var ReportModel = OC.Backbone.Model.extend({
        
        sendReport: function(filePath, shareOwner, reportId) {
            $.ajax({
                method:'POST',
                url: OC.generateUrl('/apps/files_report/sendReport'),
                data: {
                    path : filePath,
                    owner : shareOwner,
                    reportId : reportId 
                },
            });
        },

        setFilePath: function(fileInfo) {
            if(fileInfo.attributes.path == '/') {
                this.filePath = fileInfo.attributes.path + fileInfo.attributes.name;
            }
            else {
                this.filePath = fileInfo.attributes.path + '/' + fileInfo.attributes.name;
            }
        },

        setShareOwner: function(fileInfo) {
            this.shareOwner = fileInfo.attributes.shareOwner;
        },

        getFilePath: function() {
            return this.filePath;
        },

        getShareOwner: function() {
            return this.shareOwner;
        }

	});

	OCA.Files_Reports = OCA.Files_Reports || {};

	OCA.Files_Reports.ReportModel = ReportModel;
})();

