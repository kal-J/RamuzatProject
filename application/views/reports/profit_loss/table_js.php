
if ($("#tblTabular").length && (tabClicked === "tab-pl_tabular" || tabClicked === "tab-profit_and_loss")){
        if (typeof (dTable['tblTabular']) !== 'undefined') {
            $(".tab-pane").removeClass("active");
            $("#tab-pl_tabular").addClass("active");
            $("#tab-profit_and_loss").addClass("active");
            dTable['tblTabular'].ajax.reload(null, true);
        } else {
            dTable['tblTabular'] = $('#tblTabular').DataTable({
                "lengthMenu": [[10, 25], [10, 25]],
                order: [],
                    "bInfo": true,
                    "dom": '<"html5buttons"B>lTfgitp',
                "buttons": <?php if(in_array('6', $report_privilege)){ ?> getBtnConfig('Income Statement'), <?php } else { echo "[],"; } ?>
                ajax:{
                            "url":  "<?php echo site_url('reports/profit_loss_tabular') ?>",
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
                            const balance = (value.credit_sum)?value.credit_sum-value.debit_sum:0;
                            if(value.normal_balance_side ==1){
                                dr_total += (debit_sum*1-credit_sum*1);
                            } else{
                                cr_total += (credit_sum*1-debit_sum*1);
                            }
                            rem_balance += balance;
                        });
                        $(api.column(2).footer()).html(curr_format(round(dr_total,2)));
                        $(api.column(1).footer()).html(curr_format(round(cr_total,2)));
                        $(api.column(3).footer()).html(curr_format(round(rem_balance,2)));
                },
                columns: [
             
                 {data: "account_name", render: function (data, type, full, meta) {
                var level = (full.account_code).toString().split("-");
                    if(level.length< 2){
                    var padding=0; 
                    } else {
                    var padding= (level.length)*10;
                    }
                    return "<a style='padding-left:"+padding+"px;' href='<?php echo site_url("accounts/view");?>/"+full.id+"' title='Click to view full details'>["+full.account_code+ "]  "+full.account_name+"</a>";
                }
                },

                {data: 'credit_sum', render: function (data, type, full, meta) {
                    var debit_sum = (full.debit_sum)?full.debit_sum:0;
                     var credit_sum = (full.credit_sum)?full.credit_sum:0;
                    if(full.normal_balance_side ==2){
                    return curr_format(credit_sum*1-debit_sum*1);
                    } else{
                    return 0;
                }
                }},
                {data: 'debit_sum', render: function (data, type, full, meta) {
                     var debit_sum = (full.debit_sum)?full.debit_sum:0;
                     var credit_sum = (full.credit_sum)?full.credit_sum:0;
                    if(full.normal_balance_side ==1){
                    return curr_format(debit_sum*1-credit_sum*1);
                    } else{
                    return 0;
                     }
                }},
                 {data: 'debit_sum', render: function (data, type, full, meta) {
                     var credit_sum = (full.credit_sum)?full.credit_sum:0;
                     var debit_sum = (full.debit_sum)?full.debit_sum:0;
                     var balance = credit_sum-debit_sum;
                    return curr_format(balance*1);
                }}
                ],
                responsive: true
            });
        }
        }
