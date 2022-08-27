    if ($("#tblShares_Active_Account").length && tabClicked === "tab-stakeholders" ) {
        if (typeof (dTable['tblShares_Active_Account']) !== 'undefined') {
           $(".tab-pane").removeClass("active");
            $("#tab-stakeholders").addClass("active");
            $("#tab-share_accounts").addClass("active");
            dTable['tblShares_Active_Account'].ajax.reload(null, true);
        } else {
            dTable['tblShares_Active_Account'] = $('#tblShares_Active_Account').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                ajax: {
                    "url": "<?php echo site_url('dividend_declaration/jsonList2') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data":
                            function (e) {
                                e.status_id = '1';
                                e.state_id = '7';     //Active 
                                <?php if (isset($dividend_declaration['record_date'])) { ?>
                                e.record_date = '<?php echo $dividend_declaration['record_date'] ?>';
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
                 
                   {data: 'price_per_share', render: function (data, type, full, meta){
                            return (data)?curr_format(data*1):0;
                            
                        }
                    },
                    {data: 'total_amount', render: function (data, type, full, meta){
                            return round((parseFloat(data)/parseFloat(full.price_per_share)),2);
                            
                        }
                    },
                    {data: 'total_amount', render: function (data, type, full, meta){
                            return (data)?curr_format(data*1):0;
                            
                        }
                    },
                    {data: 'total_amount', render: function (data, type, full, meta){
                    return round(((parseFloat(data)/parseFloat(full.price_per_share))*(viewModel.dividend_declaration().dividend_per_share)),2);
                    }
                    }
                  
                ],
                buttons: <?php if(in_array('6', $accounts_privilege)){ ?> getBtnConfig('Active Shares Accounts'), <?php } else { echo "[],"; } ?>
                responsive: true
            });
        }
    }
