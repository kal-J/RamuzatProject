if ($("#tblAsset_payment").length && tabClicked === "tab-asset_payment") {
        if (typeof (dTable['tblAsset_payment']) !== 'undefined') {
            $(".tab-pane").removeClass("active");
            $("#tab-asset_payment").addClass("active");
            dTable['tblAsset_payment'].ajax.reload(null, true);
        } else {
            dTable['tblAsset_payment'] = $('#tblAsset_payment').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                order: [[0, 'asc']],
                ajax: {
                    "url": "<?php echo site_url('asset_payment/jsonList') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data": function (d) {
                        d.status_id = "1,3";
                        d.fixed_asset_id =<?php echo $fixed_asset['id']; ?>;
                    }
                },
                "columnDefs": [{
                        "targets": [7],
                        "orderable": false,
                        "searchable": false
                    }],
                
                "footerCallback": function (tfoot, data, start, end, display) {
                    var api = this.api();
                  
                    //var total_page = api.column(3, {page: 'current'}).data();
                    //var total_overall = api.column(3).data();
                    //var total_page_amount = 0;
                    var total_overall_amount = 0;

                    //$.each(data, function (key, val) {
                    //    total_page_amount += (val) ? (parseFloat(total_page)) : 0;
                    //});

                    $.each(data, function (key, val) {
                        if(val.status_id==1){
                        total_overall_amount += (val) ? (parseFloat(val.amount)) : 0;
                       }
                    });

                    $(api.column(3).footer()).html(curr_format(total_overall_amount));
                },
                rowCallback: function (row, data) {
                  if(data.status_id == 3){
                      $(row).addClass('text-danger strikethrough');
                  }
                },
                columns: [
                    {data: 'transaction_no'},
                    {data: 'transaction_date', render: function (data, type, full, meta) {
                            return data ? moment(data, 'YYYY-MM-DD').format('DD-MMM-YYYY') : '';
                        }},
                    {data: 'transaction_type_id', render: function (data, type, full, meta) {
                            return (parseInt(data)===1)?"CREDIT":(parseInt(data)===3)?"REVERSE":"DEBIT";
                        }},
                    {data: 'amount', render: function (data, type, full, meta) {
                            return data ? curr_format(data*1) : '0';
                        }},
                    {data: 'payment_mode'},
                    {data: 'account_name', render: function (data, type, full, meta) {
                    var ret_txt = '<a href="<?php echo site_url("accounts/view"); ?>/'+full.fund_source_account_id+'" title="View account transactions">'+"["+full.account_code+ "]  "+data+'</a>';
                            return ret_txt;
                        }
                    },
                    {data: 'narrative'},
                    {data: 'status_id', render: function (data, type, full, meta) {
                             var status;
                             if(parseInt(data)==1){
                                status="Active";
                             }else if(parseInt(data)==3){
                                status="Active";
                             }else{
                                status="";
                             }
                             return status;
                        }},
                    {"data": 'id', render: function (data, type, full, meta) {
                        var ret_txt = "";
                            ret_txt += ' &nbsp;<a href="#edit_transaction_payment-modal" data-toggle="modal" class="btn btn-sm btn-default edit_me"><i class="fa fa-pencil-square-o text-warning"></i></a>';
                            ret_txt += ' &nbsp;<a href="#reverse-modal" data-toggle="modal" title=" Reverse Transaction" class="btn btn-sm btn-warning edit_me2"><i class="fa fa-arrow-circle-o-left text-danger"></i></a>';
                        return ret_txt;
                        }
                    }
                ],
                buttons: <?php if (in_array('6', $accounts_privilege)) { ?> getBtnConfig('<?php echo $fixed_asset['asset_name']; ?> payment details'), <?php
    } else {
        echo "[],";
    }
    ?>
                responsive: true
            });
        }
}

$('table tbody').on('click', 'tr .edit_me2', function (e) {
    e.preventDefault();
    var row = $(this).closest('tr');
    var tbl = row.parent().parent();
    var tbl_id = $(tbl).attr("id");
    var dt = dTable[tbl_id];
    var data = dt.row(row).data();
    if (typeof (data) === 'undefined') {
        data = dt.row($(row).prev()).data();
        if (typeof (data) === 'undefined') {
            data = dt.row($(row).prev().prev()).data();
        }
    }
    var formId = tbl_id.replace("tbl", "formReverse");
    edit_data(data, formId);
});