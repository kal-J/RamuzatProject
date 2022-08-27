if ($("#tblSavings_interest").length && tabClicked === "tab-interest") {
        if (typeof (dTable['tblSavings_interest']) !== 'undefined') {
            $(".tab-pane").removeClass("active");
            $("#tab-interest").addClass("active");
            dTable['tblSavings_interest'].ajax.reload(null, true);
        } else {
            dTable['tblSavings_interest'] = $('#tblSavings_interest').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]],
                "processing": true,
                "deferRender": true,
                responsive: true,
                "order": [[ 3, "desc" ]],
                ajax: {
                    "url": "<?php echo site_url('reports/savings_interest_payments') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data": function (d) {
                        d.status_id = '1';
                        d.fisc_date_to = moment(end_date,'X').format('YYYY-MM-DD');
                        d.fisc_date_from = moment(start_date,'X').format('YYYY-MM-DD');
                    }
                },
                "columnDefs": [{
                        "targets": [8],
                        "orderable": false,
                        "searchable": false
                    }],
             "footerCallback": function (tfoot, data, start, end, display) {
                    var api = this.api();
                        //display_footer_sum(api,[4,5]);
                        let total_savings = 0;
                        let total_interest = 0;
                        $.each(data, function(key, value){
                            const savings = (value.qualifying_amount)?value.qualifying_amount:0;
                            const interest = (value.interest_amount)?value.interest_amount:0;
                            total_savings += savings*1;
                            total_interest += interest*1;
                        });
                        $(api.column(4).footer()).html(curr_format(round(total_savings,2)));
                        $(api.column(5).footer()).html(curr_format(round(total_interest,2)));
                },
                columns: [
                    {data: 'transaction_no'},
                     {data: 'account_no', render: function (data, type, full, meta) {
                            return "<a href='<?php echo site_url('Savings_account/view'); ?>/" + full.savings_account_id + "' title='View account details'>" + data + "</a>";
                        }
                    },
                    {data: "member_name", render: function (data, type, full, meta) {
                                    return "<a href='<?php echo site_url("member/member_personal_info"); ?>/" + full.member_id + "'>" + data + "</a>";
                                }},
                    {data: 'date_calculated', render:function(data,type,full,meta){
                            return data?(moment(data,'YYYY-MM-DD').format('MMMM')):'';
                        }
                    },

                    {data: 'qualifying_amount', render:function(data,type,full,meta){return data?curr_format(data*1):'';}},
                    {data: 'interest_amount', render:function(data,type,full,meta){return data?curr_format(data*1):'';}},
                    {data: 'date_calculated', render:function(data,type,full,meta){
                            if(type == 'sort'){
                                return data?(moment(data,'YYYY-MM-DD').format('X')):'';
                            }
                            return data?(moment(data,'YYYY-MM-DD').format('D-MMM-YYYY')):'';
                        }
                    },

                    {data: 'status_id', render: function (data, type, full, meta) {
                            return (data == 1) ? "<span class='badge bg-green'>Paid</span>" : "<span class='badge bg-red'>Not Paid</span>";
                        }},

                    {"data": 'id', render: function (data, type, full, meta) {
                            var ret_txt = "";
    <?php  if (in_array('4', $report_privilege)) { ?>
                                ret_txt += '<a href="#" data-toggle="modal" class="btn btn-sm btn-default delete_me"><i class="fa fa-trash text-danger"></i></a>';
    <?php } ?>
                            return ret_txt;
                        }
                    }
                ],
                buttons: <?php if (in_array('6', $report_privilege)) { ?> getBtnConfig('Savings Interest'), <?php } else {
    echo "[],";
} ?>
                responsive: true
            });
        }
    }