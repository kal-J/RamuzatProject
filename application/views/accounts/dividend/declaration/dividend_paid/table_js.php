    if ($("#tblDividend_paid").length && tabClicked === "tab-dividend_paid" ) {
        if (typeof (dTable['tblDividend_paid']) !== 'undefined') {
           $(".tab-pane").removeClass("active");
            $("#tab-dividend_paid").addClass("active");
            $("#tab-share_accounts").addClass("active");
            dTable['tblDividend_paid'].ajax.reload(null, true);
        } else {
            dTable['tblDividend_paid'] = $('#tblDividend_paid').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                ajax: {
                    "url": "<?php echo site_url('dividend_payment/jsonlist') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data":
                            function (e) {
                                e.status_id = '1';
                                e.state_id = '7';     //Active 
                                <?php if (isset($dividend_declaration['record_date'])) { ?>
                                e.record_date = '<?php echo $dividend_declaration['record_date'] ?>';
                                <?php } ?>
                                <?php if (isset($dividend_declaration['share_issuance_id'])) { ?>
                                    e.share_issuance_id = '<?php echo $dividend_declaration['share_issuance_id'] ?>';
                                <?php } ?>
                            }

                },
                "columnDefs": [{
                        "targets": [3],
                        "orderable": false,
                        "searchable": false
                    }],
                columns: [

                    {data: 'share_account_no', render: function (data, type, full, meta) {
                             return "<a href='<?php echo site_url('shares/view'); ?>/" + full.id + "' title='View share details'>" +data+ "</a>";
                        }},
                    {data: 'salutation', render: function (data, type, full, meta) {
                            if (type === "sort" || type === "filter") {
                                return "<a href='<?php echo site_url('member/member_personal_info'); ?>/" + full.member_id + "' title='View user profile'>" + data.salutation+' '+data.firstname+' '+data.lastname+' '+data.othernames+ "</a>";
                            }
                             return "<a href='<?php echo site_url('member/member_personal_info'); ?>/" + full.member_id + "' title='View user profile'>" + full.salutation+' '+full.firstname+' '+full.lastname+' '+full.othernames + "</a>";
                        }},
                 
                   {data: 'amount', render: function (data, type, full, meta){
                            return curr_format(<?php echo $dividend_declaration['dividend_per_share']; ?>);
                            
                        }
                    },
                    {data: 'total_amount', render: function (data, type, full, meta){
                            return round((parseFloat(data)/parseFloat(full.price_per_share)),2);
                            
                        }
                    },
                     {data: 'amount', render: function (data, type, full, meta){
                            return (data)?curr_format(data*1):0;
                            
                        }
                    },
                    {data: 'date_paid', render: function(data, type,full,meta){ 
                    if(type=='sort'){
                        return data && data!=='0000-00-00'?(moment(data,'YYYY-MM-DD').format('X')):'';
                        }
                        return data && data!=='0000-00-00'?moment(data,'YYYY-MM-DD').format('D/M/YYYY'):'';
                      }
                    },
                   
                    {data: 'paid_status', render:function ( data, type, full, meta ) {return (data==1)?"Paid Out":'Unpaid'; }}

                  
                ],
                buttons: <?php if(in_array('6', $accounts_privilege)){ ?> getBtnConfig('Active Shares Accounts'), <?php } else { echo "[],"; } ?>
                responsive: true
            });
        }
    }
