if ($("#tblAccounts").length && tabClicked === "tab-chart_of_accounts") {
    if (typeof (dTable['tblAccounts']) !== 'undefined') {
        $(".tab-pane").removeClass("active");
        $("#tab-chart_of_accounts").addClass("active");
        dTable['tblAccounts'].ajax.reload(consumeDtableData, true);
    } else {
        dTable['tblAccounts'] = $('#tblAccounts').DataTable({
            "dom": '<"html5buttons"B>lTfgitp',
            order: false,
            deferRender: true,
            "initComplete": function( settings, json ){
                accountsModel.accounts_list(json.data);
            },
            ajax:{
                        "url":  "<?php echo site_url('Accounts/jsonList') ?>",
                        "dataType": "json",
                        "type": "POST",
                        "data": function(d){
                        d.status_id ='1';
                        }
                        },
            "columnDefs": [{
                    "targets": [5],
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'account_type_id', render: function (data, type, full, meta) { 
                    var level = (full.account_code).toString().split("-");
                      if(level.length<2){
                        var padding=0; 
                      } else {
                       var padding= (level.length)*15;
                      }
                        return "<a style='padding-left:"+padding+"px;' href='<?php echo site_url("accounts/view");?>/"+full.id+"' title='Click to view full details'>["+full.account_code+ "]  "+full.account_name+"</a>";
                }
                },
                {data: 'p_account_name'},
                {data: 'normal_balance_side', render: function (data, type, full, meta) {
                    if(parseInt(data)===1){
                    return "<b>Dr</b>  ";
                    } else {
                    return "<b>Cr</b>  ";
                    }
                }},

                {data: "manual_entry", render: function (data, type, full, meta) {
                    if (parseInt(full.manual_entry)!==1) {return "No"; } else{ return "Yes"; }
                }},

                {data: 'description'},
                {data: 'status_id', render:function ( data, type, full, meta ) {return (data==1)?"Deactivated ":'Active'; }},
                {"data": 'id', render: function (data, type, full, meta) {
                    var ret_txt ="";
                    <?php if(in_array('3', $accounts_privilege)){ ?>
                        ret_txt += '<a href="#add_account-modal" data-toggle="modal" class="btn btn-sm btn-default edit_me"><i class="fa fa-pencil text-warning"></i></a>';
                     <?php } if(in_array('7', $accounts_privilege)){ ?>
                        ret_txt += '<a href="#" data-toggle="modal" class="btn btn-sm btn-default change_status"><i class="fa fa-ban text-warning"></i></a>';
                    <?php } if(in_array('4', $accounts_privilege)){ ?>
                        ret_txt += '<a href="#" data-toggle="modal" class="btn btn-sm btn-default delete_me"><i class="fa fa-trash text-danger"></i></a>';
                    <?php } ?>
                        return ret_txt;
                    }
                }
            ],
            buttons: <?php if(in_array('6', $accounts_privilege)){ ?> getBtnConfig('Chart of Accounts'), <?php } else { echo "[],"; } ?>
            responsive: true
        });
    }
}
