if ($("#tblFixed_accounts").length && tabClicked === "tab-fixed-transaction") {
    if (typeof (dTable['tblFixed_accounts']) !== 'undefined') {
        $(".tab-pane").removeClass("active");
         $(".savings").removeClass("active");
        $("#tab-fixed-transaction").addClass("active");
        dTable['tblFixed_accounts'].ajax.reload(null, true);
    } else {
        dTable['tblFixed_accounts'] = $('#tblFixed_accounts').DataTable({
            "dom": '<"html5buttons"B>lTfgitp',
            order: [[1, 'asc']],
            deferRender: true,
            ajax: {
                "url": "<?php echo site_url('Fixed_savings/jsonList') ?>",
                "dataType": "json",
                "type": "POST",
                "data":
                    function (e) {
                        e.acc_id = '<?php echo $acc_id;?>';
                        e.start_date = $("#min").val();
                        e.end_date = $("#max").val();

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
                    data: 'member_name', render: function (data, type, full, meta) {
                        if(full.child_name) {
                                return full.child_name;
                            }
                        return data;
                    }
                },
                {
                    data: 'productname', render: function (data, type, full, meta) {
                        return data;
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
                    data: 'start_date', render: function (data, type, full, meta) {
                        if (type === "sort" || type === "filter") {
                            return data;
                        }
                        return (data) ? moment(data, 'YYYY-MM-DD').format('D-MM-YYYY') : '';
                    }
                },
                {
                    data: 'end_date', render: function (data, type, full, meta) {
                        if (type === "sort" || type === "filter") {
                            return data;
                        }
                        return (data) ? moment(data, 'YYYY-MM-DD').format('D-MM-YYYY') : '';
                    }
                },


            ],
            buttons: <?php if(in_array('6', $savings_privilege)){ ?> getBtnConfig('Charges'), <?php } else { echo "[],"; } ?>
responsive: true
});
}
}



