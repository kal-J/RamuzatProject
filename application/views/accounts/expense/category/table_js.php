if ($("#tblExpense_category").length && tabClicked === "tab-expense_category") {
    if (typeof (dTable['tblExpense_category']) !== 'undefined') {
        $(".tab-pane").removeClass("active");
        $("#tab-expense_category").addClass("active");
        dTable['tblExpense_category'].ajax.reload(null, true);
    } else {
        dTable['tblExpense_category'] = $('#tblExpense_category').DataTable({
            "dom": '<"html5buttons"B>lTfgitp',
            order: false,
            deferRender: true,
            ajax:{
                    "url": "<?php echo site_url('expense_category/jsonList') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d){
                        d.status_id ='1';
                    }
                },
            "initComplete": function( settings, json ){
                accountsModel.expenseCategoryList( json.data );
             },
            "columnDefs": [{
                    "targets": [5],
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'expense_category_name'},
                {data: 'expense_category_code'},
                {data: 'account_name', render: function (data, type, full, meta) {
                var ret_txt = '<a href="<?php echo site_url("accounts/view"); ?>/'+full.linked_account_id+'" title="View account transactions">'+"["+full.account_code+ "]  "+data+'</a>';
                        return ret_txt;
                    }
                },
                {data: 'description'},
                {data: 'status_id', render:function ( data, type, full, meta ) {return (data==1)?"Active":'Deactivated'; }},
                {"data": 'id', render: function (data, type, full, meta) {
                    var ret_txt ="";
                    <?php if(in_array('3', $accounts_privilege)){ ?>
                        ret_txt += '<a href="#add_expense_category-modal" data-toggle="modal" class="btn btn-sm btn-default edit_me"><i class="fa fa-pencil text-warning"></i></a>';
                     <?php } if(in_array('7', $accounts_privilege)){ ?>
                        ret_txt += '<a href="#" data-toggle="modal" class="btn btn-sm btn-default change_status"><i class="fa fa-ban text-warning"></i></a>';
                    <?php } if(in_array('4', $accounts_privilege)){ ?>
                        ret_txt += '<a href="#" data-toggle="modal" class="btn btn-sm btn-default delete_me"><i class="fa fa-trash text-danger"></i></a>';
                    <?php } ?>
                        return ret_txt;
                    }
                }
            ],
            buttons: <?php if(in_array('6', $accounts_privilege)){ ?> getBtnConfig('Expense categories'), <?php } else { echo "[],"; } ?>
            responsive: true
        });
    }
}