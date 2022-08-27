if ($("#tblShare_transaction_report").length && tabClicked === "tab-shares_report" ) {
                if (typeof (dTable['tblShare_transaction_report']) !== 'undefined') {
                   $(".tab-pane").removeClass("active");
                    $("#tab-shares_report").addClass("active");
                    dTable['tblShare_transaction_report'].ajax.reload(null, true);
                } else {
                    dTable['tblShare_transaction_report'] = $('#tblShare_transaction_report').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[2, 'dsc']],
                        deferRender: true,
                        ajax: {
							"url":"<?php echo site_url('Share_transaction/full_share_report_data/') ?>",
							"dataType":"json",
							"type":"POST",
                            "data": function(d){
                               d.start_date = $('#start_date3').val();
                               d.end_date = $('#end_date3').val();
                               d.gender = $("#gender").val();
                               d.issuance_id = $("#issuance_id").val();
                               d.num_limit = $("#num_limit").val();
                               d.less_more_equal = $("#less_more_equal").val();
                               d.transaction_status = $("#transaction_status").val();
						  } 
                    },
                        "columnDefs": [{
                                
                                "orderable": false,
                                "searchable": true
                            }],
                          
                   "footerCallback": function (tfoot, data, start, end, display) {

                    var api = this.api();

                    var total_page = api.column(6,{page: 'current'}).data();
                    var total_overall = api.column(6).data();

                    var total_page = api.column(7,{page: 'current'}).data();
                    var total_overall = api.column(7).data();

                    var total_page = api.column(8,{page: 'current'}).data();
                    var total_overall = api.column(8).data();

                    var total_page = api.column(9,{page: 'current'}).data();
                    var total_overall = api.column(9).data();

                    var total_page = api.column(8,{page: 'current'}).data();
                    var total_overall = api.column(8).data();

                    var total_page = api.column(10,{page: 'current'}).data();
                    var total_overall = api.column(10).data();

                    var total_page_6 = api.column(6,{page: 'current'}).data();
                    var total_overall_6 = api.column(6).data();

                    var total_page_7 = api.column(7,{page: 'current'}).data();
                    var total_overall_7 = api.column(7).data();

                    var total_page_8 = api.column(8,{page: 'current'}).data();
                    var total_overall_8 = api.column(8).data();

                    var total_page_9 = api.column(9,{page: 'current'}).data();
                    var total_overall_9 = api.column(9).data();

                    var total_page_10 = api.column(10,{page: 'current'}).data();
                    var total_overall_10 = api.column(10).data();

                    var total_page_amount = 0;
                    var total_overall_amount = 0;

                    var total_page_amount_6 = 0;
                    var total_overall_amount_6 = 0;

                    var total_page_amount_7 = 0;
                    var total_overall_amount_7 = 0;

                    var total_page_amount_8 = 0;
                    var total_overall_amount_8 = 0;

                    var total_page_amount_9 = 0;
                    var total_overall_amount_9 = 0;

                    var total_page_amount_10 = 0;
                    var total_overall_amount_10 = 0;

                    $.each(total_page, function (key, val) {
                    total_page_amount += (val) ? (parseFloat(val)) : 0;

                    });

                     $.each(total_page_6, function (key, val) {
                    total_page_amount_6 += (val) ? (parseFloat(val)) : 0;

                    });

                    $.each(total_page_8, function (key, val) {
                    total_page_amount_8 += (val) ? (parseFloat(val)) : 0;

                    });

                    $.each(total_page_7, function (key, val) {
                    total_page_amount_7 += (val) ? (parseFloat(val)) : 0;

                    });

                    $.each(total_page_9, function (key, val) {
                    total_page_amount_9 += (val) ? (parseFloat(val)) : 0;

                    });

                    $.each(total_page_10, function (key, val) {
                    total_page_amount_10 += (val) ? (parseFloat(val)) : 0;

                    });
                   
                 
                    $(api.column(6).footer()).html(curr_format(total_page_amount_6));
                    $(api.column(7).footer()).html(curr_format(total_page_amount_7));
                    $(api.column(8).footer()).html(curr_format(total_page_amount_8));
                    $(api.column(9).footer()).html(curr_format(total_page_amount_9));
                    $(api.column(10).footer()).html(curr_format(total_page_amount_10));
 

                },
                       
                       columns: [ 
                            {data:'member_name'},
                            {"data": "gender", render: function (data, type, full, meta) {
                                    if (full.gender == 1) {
                                        return "M";
                                    }
                                    return "F";
                                }
                            },
                            //{data: 'client_no'},
                            {data: 'issuance_name'},
                            {data: 'share_account_no'},
                         {data: 'total_amount', render: function (data, type, full, meta){
                            return round((parseFloat(data)/parseFloat(full.price_per_share)),2);
                            
                        }
                        },
                             {data: 'price_per_share', render: function (data, type, full, meta) {
                                 return curr_format(data*1);
                                 }
                            },
                            {data: 'shares_bought', render: function (data, type, full, meta){
                             return curr_format(data*1);
                            }},
                            {data: 'shares_refund', render: function (data, type, full, meta){
                             return curr_format(data*1);
                            }
                            },
                            {data: 'shares_transfer', render: function (data, type, full, meta){
                             return curr_format(data*1);
                            }
                            },
                            {data: 'charges', render: function (data, type, full, meta){
                             return curr_format(data*1);
                            }
                            },
                            {data: 'total_amount', render: function (data, type, full, meta){
                             return curr_format(data*1);
                            
                            }},
                            {data: 'latest_transaction_date', render: function (data, type, full, meta){
                            if(full.issuance_name !=''){
                                return full.latest_transaction_date;
                            }
                            
                            }},
                          
                        ],
                        buttons: <?php if(in_array('6', $share_privilege)){ ?> getBtnConfig('Shares Transactions'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }

