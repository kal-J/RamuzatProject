if ($("#tblDetail_transaction").length) {
    if (typeof (dTable['tblDetail_transaction']) !== 'undefined') {
        dTable['tblDetail_transaction'].ajax.reload(null, true);
    } else {
        dTable['tblDetail_transaction'] = $('#tblDetail_transaction').DataTable({
            "dom": '<"html5buttons"B>lTfgitp',
            "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [[0, 'asc']],
            deferRender: true,
            ajax: {
                "url": "<?php echo site_url('SummaryReports/jsonList2') ?>",
                "dataType": "json",
                "type": "POST",
                "data": function(d){
                    d.status_id = '1';
                    d.start_date = start_date;
                    d.end_date = end_date;
                    d.journal_type_id = "<?php echo isset($journal_types) ? $journal_types : 'null'; ?>";
                }
            },
            "footerCallback": function (tfoot, data, start, end, display) {
                var api = this.api();
                var amount_page = api.column(5, {page: 'current'}).data().sum();
                var amount_overall = api.column(5).data().sum();                        
                $(api.column(5).footer()).html(curr_format(round(amount_overall,2)) );
             },
            "columnDefs": [{
                "targets": [3],
                "orderable": false,
                "searchable": false
            }],
            columns: [
                { data: 'jt_id',render: function (data, type, full, meta) { 
                    return "<a href='<?php echo site_url("journal_transaction/view"); ?>/"+data+"'>#"+data+"</a>";
                    }},
                { data: 'reference_no' },
                { data: 'reference_id' },
                {
                    data: 'transaction_date', render: function (data, type, full, meta) {
                        if (type == 'sort') {
                            return moment(data, 'YYYY-MM-DD').format('X');
                        }
                        return data ? moment(data, 'YYYY-MM-DD').format('D-MMM-YYYY') : '';
                    }
                },
                { data: 'type_name' },
                {
                    data: 'tt_amount', render: function (data, type, full, meta) {
                        return data ? curr_format(data * 1) : '';
                    }
                },
                { data: 'description' },

                {
                    data: 'firstname', render: function (data, type, full, meta) {
                        return data+" "+full.lastname;
                    }
                },
                

            ],
            buttons: <?php if(in_array('6', $billing_privilege)) { ?> getBtnConfig('<?php echo $title; ?>- Login Log'),
    <?php } else {
            echo "[],";
        } ?>
            responsive: true
});
}
}



