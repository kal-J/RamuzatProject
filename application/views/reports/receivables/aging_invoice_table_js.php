
if ($("#tblAginginvoice").length && tabClicked === "tab-tabular") {
    
    if (typeof (dTable['tblAginginvoice']) !== 'undefined') {
        $(".tab-pane").removeClass("active");
        $("#tab-tabular").addClass("active");
        dTable['tblAginginvoice'].ajax.reload(null, true);
    } else {
        dTable['tblAginginvoice'] = $('#tblAginginvoice').DataTable({
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50,  100, "All"]],
            order: [[0, 'asc'],[1,'asc']],
            ajax:{
                        "url":  "<?php echo site_url('reports/aging_accounts_json') ?>",
                        "dataType": "json",
                        "type": "POST",
                        "data": function(d){
                        d.status_id ='1';
                        d.type ='1';
                        }
                        },
           
            "footerCallback": function (tfoot, data, start, end, display) {
                var api = this.api();
                    display_footer_sum(api,[1]);
            },
            columns: [
                {data: "client_names", render: function (data, type, full, meta) {
                return "<a  href='<?php echo site_url("member/member_personal_info");?>/"+full.client_id+"' title='Click to view client details'>"+data+"</a>";
            }
            },
                {data: 'client_id', render: function (data, type, full, meta) {
                        var total_discount = (full.total_discount)?full.total_discount:0;
                        var total_amount = (full.total_amount)?full.total_amount:0;
                        var amount_paid = (full.amount_paid)?(full.amount_paid):0;
                return curr_format(total_amount*1-total_discount*1-amount_paid*1);}}
            ],
            buttons: <?php if(in_array('6', $report_privilege)){ ?> getBtnConfig('Aging Accounts Receivables'), <?php } else { echo "[],"; } ?>
            responsive: true
        });
    }
    }