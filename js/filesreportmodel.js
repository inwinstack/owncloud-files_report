(function() {
    var filePath;
    var fileID;

	var ReportModel = OC.Backbone.Model.extend({
        
        sendReport: function(filePath, id, reportId) {
            $.ajax({
                method:'POST',
                url: OC.generateUrl('/apps/files_report/sendReport'),
                data: {
                    path : filePath,
                    id : id,
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

        setFileID: function(fileInfo) {
            this.fileID = fileInfo.id;
        
        },

        getFilePath: function() {
            return this.filePath;
        },

        getFileID: function() {
            return this.fileID;
        },


       
	});

	OCA.Files_Reports = OCA.Files_Reports || {};

	OCA.Files_Reports.ReportModel = ReportModel;
})();

