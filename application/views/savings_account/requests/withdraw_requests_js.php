
if ($("#tblWithdraw_requests").length && tabClicked === "tab-withdraw_requests") {
    if (typeof (dTable['tblWithdraw_requests']) !== 'undefined') {
        $(".tab-pane").removeClass("active");
        $(".savings").removeClass("active");
        $("#tab-withdraw_requests").addClass("active");
        dTable['tblWithdraw_requests'].ajax.reload(null, true);
    } else {
        dTable['tblWithdraw_requests'] = $('#tblWithdraw_requests').DataTable({
            "dom": '<"html5buttons"B>lTfgitp',
            order: [[1, 'asc']],
            deferRender: true,
            ajax: {
                "url": "<?php echo site_url('u/Withdraw_requests/get_withdraw_requestsToJson') ?>",
                "dataType": "json",
                "type": "POST",
                "dataSrc": function ( json ) {
                    savingsModel.pending_requests_totals(json.data.length);
                    return json.data;
                } 
            },
            "columnDefs": [{
                "targets": [3],
                "orderable": false,
                "searchable": false
            }],
            columns: [
                {
                    data: 'account_no_id', render: function (data, type, full, meta) {
                        if (type === "sort" || type === "filter") {
                            return data;
                        }
                        return "<a href='#'>" + data + "</a>";
                    }
                },
                {
                    data: 'member_id', render: function (data, type, full, meta) {
                        return full.salutation +" " +full.firstname +" "+ full.lastname;
                    }
                },
                {
                    data: 'amount', render: function (data, type, full, meta) {
                        return data;
                    }
                },
                {
                    data: 'reason', render: function (data, type, full, meta) {
                        return data;
                    }
                },
                {data: 'id', render: function (data, type, full, meta) {
                          var display_btn ="<div>";
                        <?php  if(in_array('23', $savings_privilege)){ ?>
                            display_btn += '<a href="#" class="btn btn-sm edit_me " data-toggle="modal" data-target="#accept_withdraw" title="Accept withdraw"><i class="fa fa-arrow-up text-green"></i></a>';
                        <?php }  ?>

                            display_btn += "<a class='btn btn-sm text-muted edit_me2' href='#' title='Decline the request' data-toggle='modal' data-target='#decline_request'><i class='fa fa-trash text-danger'></i></a>";
                            display_btn += "</div>";
                            return display_btn;
                        }
                    }

            ],
            buttons: <?php if(in_array('6', $savings_privilege)){ ?> getBtnConfig('Charges'), <?php } else { echo "[],"; } ?>
responsive: true
});
}
}



