        if ($('#tblDetail_subscription_fees').length) {
                if(typeof dTable['tblDetail_subscription_fees'] !=='undefined'){
                    dTable['tblDetail_subscription_fees'].ajax.reload(null,true);
                }else{
                    dTable['tblDetail_subscription_fees'] = $('#tblDetail_subscription_fees').DataTable({
                    "pageLength": 10,
                    "responsive": true,
                    "dom": '<"html5buttons"B>lTfgitp',
                    buttons: <?php if(in_array('6', $member_privilege)){ ?> getBtnConfig('<?php echo $title; ?>- Member Fees'), <?php } else { echo "[],"; } ?>
                    "ajax": {
                        "url": "<?php echo site_url('automated_fees/user_subscription'); ?>",
                        "dataType": "json",
                        "type": "POST",
                        "data": {
                            member_id: "<?php echo isset($member_id) ? $member_id : 'null'; ?>",
                        }
                    },
                    "footerCallback": function (tfoot, data, start, end, display) {
                        var api = this.api();
                        var amount_page = api.column(2, {page: 'current'}).data().sum();
                        var amount_overall = api.column(2).data().sum();                        
                        $(api.column(2).footer()).html(curr_format(amount_page) );
                    },
                    "columns": [                    
                        {"data": "member_name", render:function(data, type, full, meta){
                            return data;
                        }},                    
                        {"data": "plan_name"},
                        {"data": "amount", render: function(data, type, full, meta){ 
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
