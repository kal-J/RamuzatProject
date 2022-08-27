
if ($("#tblSavings_per_month").length && tabClicked === "tab-savings") {
        if (typeof (dTable['tblSavings_per_month']) !== 'undefined') {
            $(".tab-pane").removeClass("active");
            $("#tab-savings").addClass("active");
            dTable['tblSavings_per_month'].ajax.reload(null, true);
        } else {
            dTable['tblSavings_per_month'] = $('#tblSavings_per_month').DataTable({
                "lengthMenu": [[10, 25], [10, 25]],
                order: [],
                    "bInfo": true,
                    "dom": '<"html5buttons"B>lTfgitp',
                "buttons": <?php if(in_array('6', $report_privilege)){ ?> getBtnConfig('Monthly Savings Report '), <?php } else { echo "[],"; } ?>
                ajax:{
                            "url":  "<?php echo site_url('reports/savings_per_month') ?>",
                            "dataType": "json",
                            "type": "POST",
                            "data": function(d){
                            d.fisc_date_to = moment(end_date,'X').format('YYYY-MM-DD');
                            d.fisc_date_from = moment(start_date,'X').format('YYYY-MM-DD');
                            d.status_id ='1';
                            }
                            },
                /* "columnDefs": [{
                        "targets": [2],
                        "orderable": false,
                        "searchable": false
                    }], */
                "footerCallback": function (tfoot, data, start, end, display) {
                    var api = this.api();
                        //display_footer_sum(api,[1,2]);
                        let dr_total = 0;
                        let cr_total = 0;
                        let rem_balance =0
                        $.each(data, function(key, value){
                            const debit_sum = (value.debit_sum)?value.debit_sum:0;
                            const credit_sum = (value.credit_sum)?value.credit_sum:0;
                            const balance = (value.balance)?value.balance:0;
                                dr_total += debit_sum*1;
                                cr_total += credit_sum*1;
                                rem_balance += balance*1;
                        });
                        $(api.column(1).footer()).html(curr_format(round(dr_total,2)));
                        $(api.column(2).footer()).html(curr_format(round(cr_total,2)));
                        $(api.column(3).footer()).html(curr_format(round(rem_balance,2)));
                },
                columns: [
                    {data: "month", render: function (data, type, full, meta) {
                    return moment(data, 'M').format('MMMM');
                }
                },
                   
                {data: 'debit_sum', render: function (data, type, full, meta) {
                     var debit_sum = (full.debit_sum)?full.debit_sum:0;
                     return curr_format(debit_sum*1);
                }},

                {data: 'credit_sum', render: function (data, type, full, meta) {
                     var credit_sum = (full.credit_sum)?full.credit_sum:0;
                    return curr_format(credit_sum*1);
                }},
                 {data: 'balance', render: function (data, type, full, meta) {
                     var balance = (full.balance)?full.balance:0;
                    return curr_format(balance*1);
                }}
                ],
                responsive: true
            });
        }
        }
