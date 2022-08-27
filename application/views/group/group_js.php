if ($("#tblGroup").length && tabClicked === "tab-groups") {
    if (typeof(dTable['tblGroup']) !== 'undefined') {
        $(".tab-pane").removeClass("active");
        $("#tab-groups").addClass("active");
        dTable['tblGroup'].ajax.reload(null, true);
    } else {
        dTable['tblGroup'] = $('#tblGroup').DataTable({
            dom: '<"html5buttons"B>lTfgitp',
            "deferRender": true,
            "order": [
                [0, 'DESC']
            ],
            "ajax": {
                "url": "<?php echo site_url("group/jsonList"); ?>",
                "dataType": "JSON",
                "type": "POST",
                "data": function(d) {
                    d.active = 1;
                    d.group_client_type = 2;
                }
            },
            "columnDefs": [{
                "targets": [0, 3, 4],
                "orderable": false,
                "searchable": false
            }],
            columns: [{
                    data: 'group_no',
                    render: function(data, type, full, meta) {
                        return "<a href='<?php echo site_url("group/view");?>/" + full.id +
                            "' title='Click to view full details'>" + (data != 0 ? data : '') + "</a>";
                    }
                },
                {
                    data: 'group_name',
                    render: function(data, type, full, meta) {
                        return "<a href='<?php echo site_url("group/view");?>/" + full.id +
                            "' title='Click to view full details'>" + data + "</a>";
                    }
                },
                {
                    data: 'member_count',
                    render: function(data, type, full, meta) {
                        return (data) ? data : '-';
                    }
                },
                {
                    data: 'description'
                },
                {
                    data: 'id',
                    render: function(data, type, full, meta) {
                        var anchor_class = (full.member_count ? 'turn_off' : 'delete_me');
                        var itag_class = (full.member_count ? 'power-off' : 'trash');
                        var anchor_title = (full.member_count ? 'Deactivate' : 'Delete');
                        var ret_txt = "";
                        <?php if(in_array('3', $group_privilege)){ ?>
                        ret_txt +=
                            "<a href='#add_group-modal' data-toggle='modal' class='btn btn-sm btn-default edit_me' ><i class='fa fa-edit'></i></a>";
                        <?php } if((in_array('5', $group_privilege))||(in_array('7', $group_privilege))){ ?>
                        ret_txt += "<a href='#' class='btn btn-sm btn-default " + anchor_class +
                            "' title='" + anchor_title + "'><i class='fa fa-" + itag_class +
                            " text-danger'></i></a>";
                        <?php } ?>
                        return ' <div class="btn-group">' + ret_txt + '</div>';
                    }
                }
            ],
            buttons: <?php if(in_array('6', $group_privilege)){ ?> getBtnConfig('<?php echo $title; ?>'),
            <?php } else { echo "[],"; } ?>
        });
    }


}
