   if ($("#tblJournal_transaction").length && tabClicked === "tab-transactions") {
        if (typeof (dTable['tblJournal_transaction']) !== 'undefined') {
            $(".tab-pane").removeClass("active");
            $("#tab-transactions").addClass("active");
            dTable['tblJournal_transaction'].ajax.reload(null, true);
        } else {
            dTable['tblJournal_transaction'] = $('#tblJournal_transaction').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
                order: [[0, 'asc'], [1, 'asc']],
                "processing": true,
                "serverSide": true,
                "orderable": false,
                "searchable": false,
                "deferRender": true,
                responsive: true,
                ajax: {
                    "url": "<?php echo site_url('journal_transaction/jsonList') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data": function (d) {
                        d.start_date = start_date ? moment(start_date,'X').format('YYYY-MM-DD') : '';
                        d.end_date = end_date ? moment(end_date,'X').format('YYYY-MM-DD') : '';
                        d.status_id = '1';
                    }
                },
                rowCallback: function (row, data) {
                  if(data.status_id == 3){
                      $(row).addClass('strikethrough');
                  }
                },
                "columnDefs": [{
                        "targets": [6],
                        "orderable": false,
                        "searchable": false
                    }],
                "footerCallback": function (tfoot, data, start, end, display) {
                    var api = this.api();
                
                    var tt_amount_page = api.column(6, {page: 'current'}).data().sum();
                    var tt_amount_overall = api.column(6).data().sum();

                   $(api.column(6).footer()).html(curr_format(round(tt_amount_page,2)) + "(" + curr_format(round(tt_amount_overall,2)) + ") ");
                },
                columns: [
                {data: 'id', render: function (data, type, full, meta) { return "<a href='<?php echo site_url("journal_transaction/view"); ?>/"+data+"'>#"+data+"</a>";}},
                    {data: 'ref_no'},
                    {data: 'ref_id'},
                    {data: 'transaction_date', render: function (data, type, full, meta) {
                            if (type == 'sort') {
                                return moment(data, 'YYYY-MM-DD').format('X');
                            }
                            return data ? moment(data, 'YYYY-MM-DD').format('D-MMM-YYYY') : '';
                        }
                    },
                    {data: 'type_name'},
                    {data: 'description', render: function (data, type, full, meta) {
                     
                            return data;
                        }
                    },
                    {data: 't_amount', render: function (data, type, full, meta) {
                            return data ? curr_format(data*1) : '';
                        }},
                    
                    {"data": 'id', render: function (data, type, full, meta) {
                            var ret_txt = "";
                     <?php if (in_array('3', $accounts_privilege)) { ?>
                                ret_txt += full.journal_type_id==1?'<a href="#post_entry-modal" data-toggle="modal" class="btn btn-sm btn-default edit_me"><i class="fa fa-pencil text-warning"></i></a>':'';
                        <?php } if (in_array('7', $accounts_privilege)) { ?>
                                ret_txt += full.journal_type_id==1?'<a href="#" data-toggle="modal" class="btn btn-sm btn-default change_status"><i class="fa fa-ban text-warning"></i></a>':'';
                    <?php } if (in_array('4', $accounts_privilege)) { ?>
                                ret_txt += full.journal_type_id==1?'<a href="#" data-toggle="modal" class="btn btn-sm btn-default delete_me"><i class="fa fa-trash text-danger"></i></a>':'';
                     <?php } if (in_array('4', $accounts_privilege)) { ?>
                                ret_txt += full.journal_type_id==1?'<a href="#reverse-modal" data-toggle="modal" class="btn btn-sm btn-danger edit_me2"><i class="fa fa-undo"></i></a>':'';
                     <?php } ?>
                     <?php if (in_array('4', $accounts_privilege)) { ?>

                        let url = "<?php echo site_url('accounts/print_receipt'); ?>";
                        let narrative = full.description;
                        ret_txt += full.journal_type_id==1? `
                                <span>
                                <form class="btn btn-sm m-0 p-0" action=${url} method="post">
                                    <input name="date" type="hidden" value=${full.transaction_date}>
                                `
                                +
                                "<input name='narrative' type='hidden' value='" +  narrative + "'>"
                                +
                                `
                                    <input name="amount" type="hidden" value=${full.t_amount}>
                                    <input name="trans_id" type="hidden" value=${full.id}>
                                    <button type="submit" class="btn btn-sm btn-secondary">
                                        <i class="fa fa-print"></i> Receipt
                                    </button>
                                </form>
                                </span>
                                
                                `:'';

                                
                              

                     <?php } ?>
                            return ret_txt;
                        }
                    }
                    
                ],
                buttons: <?php if (in_array('6', $accounts_privilege)) { ?> getBtnConfig('Module and accounts_privilege'), <?php
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