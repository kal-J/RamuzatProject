    if ($('#tblMember_collateral').length && tabClicked === "tab-collaterals") {
        if (typeof dTable['tblMember_collateral'] !== 'undefined') {
            $(".tab-pane").removeClass("active");
            $("#tab-collaterals").addClass("active");
            dTable['tblMember_collateral'].ajax.reload(null, true);
        } else {
            dTable['tblMember_collateral'] = $('#tblMember_collateral').DataTable({
                "pageLength": 25,
                "responsive": true,
                "dom": '<"html5buttons"B>lTfgitp',
                buttons: getBtnConfig('<?php echo $title; ?>- Collaterals'),
                "ajax": {
                    "url": "<?php echo site_url('member_collateral/jsonList'); ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d) {}
                },

                "columns": [{
                        "data": "client_no"
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
                            return curr_format(data);
                        }
                    },
                    {
                        "data": "description"
                    },
                    {
                        "data": "loan_state",
                        render: function(data, type, full, meta) {
                            if ([1, 5, 6, 7].includes(parseInt(data))) {
                                return 'Yes';
                            }
                            return 'No';
                        }
                    },
                    {
                        "data": "date_created",
                        render: function(data, type, full, meta) {
                            return (!(data == '0000-00-00')) ? moment.unix(data).format('D-MMM-YYYY') : '';
                        }
                    },
                    {
                        "data": "id",
                        render: function(data, type, full, meta) {

                            if ([1, 5, 6, 7].includes(parseInt(full.loan_state))) {
                                return `
    <div class="d-flex justify-content-center">
        <a href="#edit-loan-collateral" data-toggle="modal" title="Edit" class="btn btn-xs btn-success edit_me"><i class="fa fa-edit"></i></a>

    </div>

    `;
                            }
                            return `
    <div class="d-flex justify-content-center">
        <a href="#edit-loan-collateral" data-toggle="modal" title="Edit" class="btn btn-xs btn-success edit_me"><i class="fa fa-edit"></i></a>
        <button class="btn btn-danger btn-xs ml-1 delete_me">
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
