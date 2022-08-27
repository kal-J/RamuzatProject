if ($('#tblDefaulters_fees_summary').length && tabClicked === "tab-membership_fees") {
                if(typeof dTable['tblDefaulters_fees_summary'] !=='undefined'){
                    //$("#tab-membership_fees").addClass("active");
                    dTable['tblDefaulters_fees_summary'].ajax.reload(null,true);
                }else{
                    dTable['tblDefaulters_fees_summary'] = $('#tblDefaulters_fees_summary').DataTable({
                    "responsive": true,
                    "paging":   false,
                    "ordering": false,
                    "info":     false,
                    "bFilter": false,
                    "ajax": {
                        "url": "<?php echo site_url('automated_fees/getSummary'); ?>",
                        "dataType": "json",
                        "type": "POST",
                        "data": function(d){
                            d.state_id = state_id;
                            d.start_date = start_date;
                            d.end_date = end_date;
                        }
                    },
                    "footerCallback": function (tfoot, data, start, end, display) {
                        var api = this.api();
                        var amount_page = api.column(2, {page: 'current'}).data().sum();
                        var amount_overall = api.column(2).data().sum();                        
                        //$(api.column(2).footer()).html(curr_format(amount_page) );
                    },
                    "columns": [                    
                        {"data": "due_users", render:function(data, type, full, meta){
                            return data;
                        }},                    
                        {"data": "total_due", render:function(data, type, full, meta){
                            return data?curr_format(data*1):0;
                        }},
                        {"data": "total_paid", render: function(data, type, full, meta){ 
                            return data?curr_format(data*1):0;
                        }},
                            
                                           
                     
                    ]
                });
                }
            }

