(function($, OC) {
    var appname = 'files_report';
    var table = {
        getReports: function(status) {
            return $.ajax({
                method: 'GET',
                url: OC.generateUrl('apps/files_report/getReport'),
                data: {
                    status: status
                }
            });
        }     
    
    };


    var action = {
        loading: false,

        setLoading: function() {
            if(this.loading) {
                $('#container').hide();
                $('#loading_reports').show();
            } else {
                $('#container').show();
                $('#loading_reports').hide();
            }
        },

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
        },

        appendDownload: function() {
            $('tbody #download').each(function() {
                var filePath = $(this).closest('tr').find('#filename').attr('data');
                var owner = $(this).closest('tr').find('#owner').text();
                
                filePath = encodeURIComponent(filePath);
                $(this).find('a').attr({
                    href:OC.linkToOCS('apps/files_report/api/v1')+'download?owner='+owner+'&filePath='+filePath
                });
    
            });
        },

    };
    
    


    $(function() {
        action.appendDownload();
        action.setLoading();
        $('ul #unaudited').addClass('active');
        $('table .audited').hide();

        $('table').on('change', 'select', function() {
            var owner = $(this).closest('tr').find('#owner').text();
            var id = $(this).closest('tr').attr('id');
            var reason = $(this).val();
            var path = $(this).closest('tr').find('#filename').attr('data');
            var tr = $(this).closest('tr');
            var select = $(this);

            var dialog = $('<div>').dialog({
                modal: true,
                autoOpen: false,
                draggable: false,
                resizeable: false,
                title: t(appname, 'Action'),
                buttons: [
                {
                    text: t(appname, "Submit"),
                    click: function () {
                        action.returnReport(id, reason, path, owner).done(function(result) {

                            OC.Notification.showTemporary(t(appname, "Your action is success"));
                            tr.remove();
                            dialog.dialog('close');
            
                        });
                    }
                },
                {
                    text: t(appname, "Cancel"),
                    click: function() {
                        select.find('option:first-child').attr('selected', true);
                        $(this).dialog('close');
                    }
                }],
                close:  function() {
                    select.find('option:first-child').attr('selected', true);
                    $(this).remove();
                }
                     
            });

            if($(this).find('option:selected').text() != t(appname,'please select a reason for deletion') ) {
                var p = $('<p>');

                p.append(t(appname, 'Are you sure this file is '));
                p.append($(this).find('option:selected').text()+ ' ?');
                dialog.append(p);
                $(this).val() != 'legal' && p.after($('<small>').css('color', 'red').text(t(appname, 'Notice! This action will delete this file.')));
                dialog.dialog('open');
            }
        
        });
        $('li').click(function() {
            var status = $(this).find('a').data('status');

            if(!$(this).hasClass('active')) { 
                $(this).siblings('li').removeClass('active');
                $(this).addClass('active');
            }

            action.loading = true;
            action.setLoading();

            status ? $('table .unaudited').hide() : $('table .unaudited').show();
            !status ? $('table .audited').hide() : $('table .audited').show();
            $('tbody tr').remove();
            table.getReports(status).done(function(data) {
                $.each(data.reports, function(index, value) {
                    var tr = $('<tr>').attr({id: value.id});
                    var select = $('<select>');
                    
                    select.append($('<option>').text(t(appname, 'please select a reason for deletion')));
                    select.append($('<option>').attr({value: '0'}).text(t(appname, 'include bad words or graphs')));
                    select.append($('<option>').attr({value: '1'}).text(t(appname, 'uncomfortable file')));
                    select.append($('<option>').attr({value: '2'}).text(t(appname, 'should not be on custom cloud')));
                    select.append($('<option>').attr({value: '3'}).text(t(appname, 'spam file')));
                    select.append($('<option>').attr({value: 'legal'}).text(t(appname, 'not illegal file')));
                    select.append($('<option>').attr({value: 'cancel'}).text(t(appname, 'Cancel')));


                    tr.append($("<td id='owner'>").append(value.owner));
                    tr.append($("<td id='filename'>").attr({data: value.file_path}).text(value.file_name));
                    tr.append($("<td id='reporter'>").append(value.reporter));
                    tr.append($("<td id='reason'>").append(t('files_report', value.reason)));
                    !status && tr.append($("<td id='action'>").append(select));
                    !status && tr.append($("<td id='download'>").append($('<a>').text(t(appname, 'Download'))));
                    status && tr.append($("<td id='time'>").append(value.time));
                    $('tbody').append(tr);

                    
                });

                !status && action.appendDownload();
                action.loading = false;
                action.setLoading();

            }); 
        }); 
        
             
    });
})(jQuery, OC);
