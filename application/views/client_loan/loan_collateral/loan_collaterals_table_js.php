    if ($('#tblLoan_collateral').length && tabClicked === "tab-loan_collaterals") {
        if (typeof dTable['tblLoan_collateral'] !== 'undefined') {
            $("#tab-pane").removeClass("active");
            $("#tab-loan_collaterals").addClass("active");
            dTable['tblLoan_collateral'].ajax.reload(null, true);
        } else {
            dTable['tblLoan_collateral'] = $('#tblLoan_collateral').DataTable({
                "pageLength": 25,
                "responsive": true,
                "dom": '<"html5buttons"B>lTfgitp',
                buttons: getBtnConfig('<?php echo $title; ?>- Loan Collateral'),
                "ajax": {
                    "url": "<?php echo site_url('loan_collateral/jsonList'); ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d) {}
                },

                "columns": [
                    {data: 'loan_no', render: function (data, type, full, meta) {
                          if (type === "sort" || type === "filter") {
                              return data;
                          }
                          var link1="<a href='<?php echo site_url('client_loan/view'); ?>/" + full.group_loan_id + "/1' title='View this Loan details'>" + data + "</a>";
                          var link2="<a href='<?php echo site_url('client_loan/view'); ?>/" + full.loan_id + "' title='View this Loan details'>" + data + "</a>";
                          return (full.member_name == null)?link1:link2;
                      }
                  },
                    {
                        "data": "member_name"
                    },
                    {
                        "data": "collateral_type_name"
                    },
                    {
                        "data": "item_value",
                        render: function(data, type, full, meta) {
                            return data ? curr_format(data) : '';
                        }
                    },
                    {
                        "data": "description"
                    },
                    {
                        "data": "date_created",
                        render: function(data, type, full, meta) {
                            return (!(data == '0000-00-00')) ? moment.unix(data).format('D-MMM-YYYY') : '';
                        }
                    },
                    {
                        "data": "loan_state",
                        render: function(data, type, full, meta) {
                            let state = parseInt(data) === 7 ? 'Active' : (
                                parseInt(data) === 12 ? 'Locked' : (
                                    parseInt(data) === 13 ? 'In-arrears' : (
                                        parseInt(data) === 1 ? 'Partial Application' : (
                                            parseInt(data) === 5 ? 'Pending' : (
                                                parseInt(data) === 6 ? 'Approved' : (
                                                    parseInt(data) === 4 ? 'Withdrawn' : (
                                                        parseInt(data) === 3 ? 'Cancelled' : (
                                                            parseInt(data) === 2 ? 'Rejected' : (
                                                                parseInt(data) === 10 ? 'Obligations met' : (
                                                                    parseInt(data) === 8 ? 'Written Off' : (
                                                                        parseInt(data) === 9 ? 'Paid Off' : (
                                                                            parseInt(data) === 11 ? 'Rescheduled' : ''
                                                                        )
                                                                    )
                                                                )
                                                            )
                                                        ) 
                                                    )
                                                )
                                            )
                                        )
                                    )
                                )
                            );
                            
                            return state;
                        }
                    },
                    {
                        "data": "status_id",
                        render: function(data, type, full, meta) {

                            return `
                                <div class="d-flex justify-content-center">
                                <button class="btn btn-danger btn-sm delete_me">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ]
            });
        }
    }

    $('table tbody').on('click', 'tr .edit_me4', function(e) {
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

    });
