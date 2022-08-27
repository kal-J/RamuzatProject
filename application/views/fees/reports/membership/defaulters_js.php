    if ($('#tblDefaulters_member_fees').length && tabClicked === "tab-membership_fees") {
                if(typeof dTable['tblDefaulters_member_fees'] !=='undefined'){
                    //$("#tab-membership_fees").addClass("active");
                    dTable['tblDefaulters_member_fees'].ajax.reload(null,true);
                }else{
                    dTable['tblDefaulters_member_fees'] = $('#tblDefaulters_member_fees').DataTable({
                    "pageLength": 10,
                    "responsive": true,
                    "dom": '<"html5buttons"B>lTfgitp',
                    buttons: <?php if(in_array('6', $member_privilege)){ ?> getBtnConfig('<?php echo $title; ?>- Member Fees'), <?php } else { echo "[],"; } ?>
                    "ajax": {
                        "url": "<?php echo site_url('automated_fees/jsonList'); ?>",
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
                        var amount_page1 = api.column(1, {page: 'current'}).data().sum();
                        var amount_overall = api.column(2).data().sum();                        
                        var amount_overall1 = api.column(1).data().sum();                        
                        $(api.column(2).footer()).html(curr_format(amount_page) );
                        $(api.column(1).footer()).html(curr_format(amount_page1) );
                    },
                    "columns": [                    
                        {"data": "member_name", render:function(data, type, full, meta){
                            return "<a href='<?php echo site_url("automated_fees/view"); ?>/"+full.member_id+"'>"+data+"</a>";
                        }}, 
                        {"data": "due_amount", render: function(data, type, full, meta){ 
                            return data?curr_format(data*1):'';
                            }},
                            {"data": "amount_paid", render: function(data, type, full, meta){ 
                            return data?curr_format(data*1):'';
                            }},
                        { data: "subscription_date", render:function( data, type, full, meta ){
                            return (data)?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'None';;
                        }},
                        {"data": "state_name", render:function(data, type,full ,meta){
                            return '<a href="#" role="button" class="badge badge-danger" >'+data+'</a>';
                        }},                       
                     
                    ]
                });
                }
            }

