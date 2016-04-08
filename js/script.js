(function($, OC) {
    var action = {
        appname: 'files_report',

        returnReport: function(id, reason, path, owner) {
            return $.ajax({
                method: 'POST',
                url: OC.generateUrl('apps/files_report/returnReport'),
                data: {
                    id: id,
                    reason: reason,
                    path: path,
                    owner: owner
                }
            });
        },
        download: function (owner, filePath) {
            return $.ajax({
                method: 'GET',
                url: OC.generateUrl('apps/files_report/download'),
                data: {
                   owner: owner,
                   filePath: filePath
                },
            });
        }

    };
    



    $(function() {
        $('tbody #download').each(function() {
            var filePath = $(this).closest('tr').find('#filename').attr('data');
            var owner = $(this).closest('tr').find('#owner').text();
            
            filePath = encodeURIComponent(filePath);
            $(this).find('a').attr({
                href:OC.linkToOCS('apps/files_report/api/v1')+'download?owner='+owner+'&filePath='+filePath
            });

        });

        $('tbody select').change(function(){
            var owner = $(this).closest('tr').find('#owner').text();
            var id = $(this).closest('tr').attr('id');
            var reason = $(this).val();
            var path = $(this).closest('tr').find('#filename').attr('data');
            var tr = $(this).closest('tr');

            action.returnReport(id, reason, path, owner).done(function(result) {
                OC.Notification.showTemporary(t(action.appname, "Your action is success"));
                tr.remove();
            
            });
        
        });

    
    });
})(jQuery, OC);
