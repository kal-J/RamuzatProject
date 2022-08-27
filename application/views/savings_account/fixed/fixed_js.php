if ($("#tblFixed_savings").length && tabClicked === "tab-fixed") {
    if (typeof (dTable['tblFixed_savings']) !== 'undefined') {
        $(".tab-pane").removeClass("active");
        $("#tab-fixed").addClass("active");
        dTable['tblFixed_savings'].ajax.reload(null, true);
    } else {
        dTable['tblFixed_savings'] = $('#tblFixed_savings').DataTable({
            "dom": '<"html5buttons"B>lTfgitp',
            order: [[1, 'asc']],
            deferRender: true,
            ajax: {
                "url": "<?php echo site_url('Fixed_savings/jsonList2') ?>",
                "dataType": "json",
                "type": "POST",
                "data":
                    function (e) {
                        e.acc_id = '<?php echo $acc_id;?>';

                    }
            },
            "columnDefs": [{
                "targets": [3],
                "orderable": false,
                "searchable": false
            }],
            columns: [
                {
                    data: 'account_no', render: function (data, type, full, meta) {
                        if (type === "sort" || type === "filter") {
                            return data;
                        }
                        return "<a href='#'>" + data + "</a>";
                    }
                },
                {
                    data: 'type', render: function (data, type, full, meta) {
                        if(data == 0){
                        return curr_format(full.qualifying_amount * 1);
                        }else{
                        return curr_format(full.real_bal * 1);
                        }
                    }
                },

                {
                    data: 'type', render: function (data, type, full, meta) {
                        if(data == 0){
                        return "Fixed Amount";
                        }else{
                        return "Savings Account";
                        }
                    }
                },

                {
                    data: 'start_date', render: function (data, type, full, meta) {
                        if (type === "sort" || type === "filter") {
                            return data;
                        }
                        return (data) ? moment(data, 'YYYY-MM-DD').format('D-MMM-YYYY') : '';
                    }
                },
                {
                    data: 'end_date', render: function (data, type, full, meta) {
                        if (type === "sort" || type === "filter") {
                            return data;
                        }
                        return (data) ? moment(data, 'YYYY-MM-DD').format('D-MMM-YYYY') : '';
                    }
                },
                {
                    data: 'status', render: function (data, type, full, meta) {
                        if (data == "0") {
                        return 'Inactive';
                        }else{
                        return 'Active'
                        }

                    }
                },
                {
                    data: 'id', render: function (data, type, full, meta) {
                    var display_btn = "<div>";
                        <?php if(in_array('18', $savings_privilege)){ ?>
                            display_btn += '<a href="#add_fixed_amount" data-toggle="modal" class="edit_me" title="Edit Fixed Amount"><span class="fa fa-pencil text-primary"></span></a>';
                        <?php } if(in_array('25', $savings_privilege)){ ?>
                            display_btn += '<a href="#" data-toggle="modal"   title="Close Fixed amount" class="delete_me"> &nbsp;&nbsp;<i class="fa fa-close"  style="color:#bf0b05"></i></a>';
                        <?php } ?>
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



