    if ($("#tblShares_payments").length && tabClicked === "tab-payments") {
        if (typeof (dTable['tblShares_payments']) !== 'undefined') {
           $(".tab-pane").removeClass("active");
            $("#tab-share_application").addClass("active");
            $("#tab-payments").addClass("active");
            dTable['tblShares_payments'].ajax.reload(null, true);
        } else {
            dTable['tblShares_payments'] = $('#tblShares_payments').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                ajax: {
                    "url": "<?php echo site_url('shares/payment_calls') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data":
                            function (e) {
                                e.status_id = '1';
                                e.app_status_id='1';
                                e.state_id = '7';     //Active approval
                                <?php if (isset($user['id'])) { ?>
                                e.client_id = <?php echo $user['id'] ?>;
                                <?php } ?>
                            }

                },
                "columnDefs": [{
                        "targets": [5],
                        "orderable": false,
                        "searchable": false
                    }],
                columns: [

                    {data: 'share_application_no', render: function (data, type, full, meta) {
                             return "<a href='<?php echo site_url('shares/view'); ?>/" + full.id + "' title='View Applications details'>" +data+ "</a>";
                        }},
                 
                    {data: 'call_name', render: function (data, type, full, meta){
                            return data;
                           
                        }
                    },
                
                    {data: 'total_call_amount', render: function (data, type, full, meta){
                            return (data)?curr_format(data*1):0;
                            
                        }
                    },
                    {data: 'amount_paid', render: function (data, type, full, meta){
                            return (data)?curr_format(data*1):0;
                        }
                    },
                    {data: 'total_call_amount', render: function (data, type, full, meta){
                            return curr_format(parseFloat(data?data:0)-parseFloat(full.amount_paid?full.amount_paid:0));
                        }
                    },
                    {data: 'id', render: function (data, type, full, meta) {
                            var display_btn ="";
                            <?php // if(in_array('3', $share_privilege)){ ?>
                                if(parseFloat(full.total_call_amount)>parseFloat(full.amount_paid)){
                           display_btn += '<a href="#" class="btn btn-sm btn-success make_call" data-toggle="modal" data-target="#make_call" title="Call Payment" style="margin-right: 10px;"><i class="fa fa-check-circle"></i> Pay </a>';
                           }
                            <?php //} ?>
                            return display_btn;
                        }
                    }
                ],
                buttons: <?php if(in_array('6', $share_privilege)){ ?> getBtnConfig('Share Application Payments'), <?php } else { echo "[],"; } ?>
                responsive: true
            });
        }
    }
$('table tbody').on('click', 'tr .make_call', function (e) {
    e.preventDefault();
    var row = $(this).closest('tr');
    var tbl = row.parent().parent();
    tbl_id = $(tbl).attr("id");
    var dt = dTable[tbl_id];
    var data = dt.row(row).data();
    if (typeof (data) === 'undefined') {
        data = dt.row($(row).prev()).data();
        if (typeof (data) === 'undefined') {
            data = dt.row($(row).prev().prev()).data();
        }
    }
        sharesModel.call_payment(data);

});