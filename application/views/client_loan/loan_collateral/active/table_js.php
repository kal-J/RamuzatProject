if ($('#tblMember_collateral_active_loan').length && tabClicked === "tab-active_loan_collaterals") {
        if (typeof dTable['tblMember_collateral_active_loan'] !== 'undefined') {
            $(".tab-pane").removeClass("active");
            $("#tab-active_loan_collaterals").addClass("active");
            
            dTable['tblMember_collateral_active_loan'].ajax.reload(null, true);
        } else {
            dTable['tblMember_collateral_active_loan'] = $('#tblMember_collateral_active_loan').DataTable({
                "pageLength": 25,
                "responsive": true,
                "dom": '<"html5buttons"B>lTfgitp',
                buttons: getBtnConfig('<?php echo $title; ?>- Collaterals'),
                "ajax": {
                    "url": "<?php echo site_url('member_collateral/active_loan'); ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d) {}
                },

                "columns": [
                    {
                        "data": "client_no"
                    },
                    {
                        "data": "loan_no"
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
                            let state = parseInt(data) === 7 ? 'Active' :(
                                parseInt(data) === 12 ? 'Locked' : ''
                            );

                            return state;
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

                            return `
                                <div class="d-flex justify-content-center">
                                    <a href="#edit-loan-collateral0" data-toggle="modal" title="Edit" class="btn btn-xs btn-success edit_me">
                                    <i class="fa fa-edit"></i></a>
                                    
                                    <button class="btn btn-danger btn-xs ml-1 delete_active_collateral">
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

    $('table tbody').on('click', 'tr .delete_active_collateral', function (e) {
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
            var controller = tbl_id.replace("tbl", "");
            var url = "<?php echo site_url(); ?>member_collateral" + "/delete";

            change_status({id: data.id,narrative:'Deleted'}, url, tbl_id);
        });
