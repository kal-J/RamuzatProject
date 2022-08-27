    if ($("#tblTrans_tracking").length) {
        if (typeof(dTable['tblTrans_tracking']) !== 'undefined') {
            dTable['tblTrans_tracking'].ajax.reload(null, true);
        } else {
            dTable['tblTrans_tracking'] = $('#tblTrans_tracking').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                "lengthMenu": [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                order: [
                    [0, 'asc']
                ],
                deferRender: true,
                responsive: true,
                buttons: true,
                ajax: {
                    "url": "<?php echo site_url('Loan_reversal/jsonList') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d) {}
                },
                "columnDefs": [{
                    "targets": [3],
                    "orderable": false,
                    "searchable": false
                }],
                columns: [{
                        data: 'unique_id',
                        render: function(data, type, full, meta) {
                            return data;
                        }
                    },

                    {
                        data: 'loan_no'
                    },
                    {
                        data: 'date_created'
                    },
                    {
                        data: 'action_type_id',
                        render: function(data, type, full, meta) {
                            let action = parseInt(data) === 1 ? "Single Installment Paymnet" : (
                                parseInt(data) === 2 ? "Multiple Installment Payment" : (
                                    parseInt(data) === 3 ? "Single Installment payment with Curtailment" : (
                                        parseInt(data) === 4 ? "Loan Pay off" : (
                                            parseInt(data) === 5 ? " Write Off" : (
                                                parseInt(data) === 6 ? "Loan Reschedule" : (
                                                    parseInt(data) === 7 ? "Loan Disbursement" : 'Not specified')
                                            )
                                        )
                                    )
                                )
                            );
                            return action;
                        }
                    },

                    {
                        data: 'id',
                        render: function(data, type, full, meta) {
                            var display_btn = "<span>";
                            display_btn += "<a href='#reverse-modal' title='Reverse this Transaction' data-toggle='modal' class='btn btn-sm edit_me2'><i class='text-danger fa fa-undo'></i></a>";
                            display_btn += "</span>";

                            return display_btn;
                        }
                    },


                ],

            });
        }
    }

    $('table tbody').on('click', 'tr .edit_me2', function(e) {
        e.preventDefault();
        var row = $(this).closest('tr');
        var tbl = row.parent().parent();
        var tbl_id = $(tbl).attr("id");
        var dt = dTable[tbl_id];
        var data = dt.row(row).data();
        if (typeof(data) === 'undefined') {
            data = dt.row($(row).prev()).data();
            if (typeof(data) === 'undefined') {
                data = dt.row($(row).prev().prev()).data();
            }
        }
        var formId = "formReverseTransaction";
        edit_data(data, formId);
    });
