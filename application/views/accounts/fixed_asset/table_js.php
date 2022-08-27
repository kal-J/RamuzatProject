if ($("#tblFixed_asset").length && tabClicked === "tab-assets") {
        if (typeof (dTable['tblFixed_asset']) !== 'undefined') {
            $(".tab-pane").removeClass("active");
            $("#tab-assets").addClass("active");
            dTable['tblFixed_asset'].ajax.reload(null, true);
        } else {
            dTable['tblFixed_asset'] = $('#tblFixed_asset').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                order: false,
                deferRender: true,
                ajax: {
                    "url": "<?php echo site_url('fixed_asset/jsonList') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data": function (d) {
                        d.status_id = '1';
                    }
                },
                "columnDefs": [{
                        "targets": [9],
                        "orderable": false,
                        "searchable": false
                    }],
                columns: [
                    {data: 'asset_name', render: function (data, type, full, meta) {
                    var ret_txt = '<a href="<?php echo site_url("fixed_asset/view"); ?>/'+full.id+'" title="'+full.description+'">'+data+'</a>';
                            return ret_txt;
                        }
                    },
                    {data: 'account_name', render: function (data, type, full, meta) {
                    var ret_txt = '<a href="<?php echo site_url("accounts/view"); ?>/'+full.asset_account_id+'" title="View account transactions">'+"["+full.account_code+ "]  "+data+'</a>';
                            return ret_txt;
                        }
                    },
                    {data: 'purchase_date', render:function(data,type,full,meta){
                            if(type == 'sort'){
                                return data?(moment(data,'YYYY-MM-DD').format('X')):'';
                            }
                            return data?(moment(data,'YYYY-MM-DD').format('D-MMM-YYYY')):'';
                        }
                    },
                    {data: 'purchase_cost', render:function(data,type,full,meta){return data?curr_format(data*1):'';}},
                    {data: 'salvage_value', render:function(data,type,full,meta){return data?curr_format(data*1):'';}},
                    {data: 'purchase_date', render:function(data,type,full,meta){return data?(moment(<?php echo time();?>,'X').diff(moment(data,'YYYY-MM-DD'),'years')):'';}},
                    {data: 'expected_age'},
                    {data: 'cumm_dep', render:function(data,type,full,meta){return data?curr_format(data*1):'';}},
                    {data: 'status_id', render: function (data, type, full, meta) {
                            return (data == 1) ? "Active" : 'Deactivated';
                        }},
                    {"data": 'id', render: function (data, type, full, meta) {
                            var ret_txt = "";
    <?php if (in_array('3', $accounts_privilege)) { ?>
                                ret_txt += '<a href="#add_asset-modal" data-toggle="modal" class="btn btn-sm btn-default edit_me"><i class="fa fa-pencil text-warning"></i></a>';
    <?php } if (in_array('7', $accounts_privilege)) { ?>
                                ret_txt += '<a href="#" data-toggle="modal" class="btn btn-sm btn-default change_status"><i class="fa fa-ban text-warning"></i></a>';
    <?php } if (in_array('4', $accounts_privilege)) { ?>
                                ret_txt += '<a href="#" data-toggle="modal" class="btn btn-sm btn-default delete_me"><i class="fa fa-trash text-danger"></i></a>';
    <?php } ?>
                            return ret_txt;
                        }
                    }
                ],
                buttons: <?php if (in_array('6', $accounts_privilege)) { ?> getBtnConfig('Module and accounts_privilege'), <?php } else {
    echo "[],";
} ?>
                responsive: true
            });
        }
    }