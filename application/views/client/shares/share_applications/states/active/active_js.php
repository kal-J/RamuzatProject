    if ($("#tblShares_Active_Application").length && tabClicked === "tab-active_application") {
        if (typeof (dTable['tblShares_Active_Application']) !== 'undefined') {
           $(".tab-pane").removeClass("active");
            $("#tab-share_application").addClass("active");
            $("#tab-active_application").addClass("active");
            dTable['tblShares_Active_Application'].ajax.reload(null, true);
        } else {
            dTable['tblShares_Active_Application'] = $('#tblShares_Active_Application').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                ajax: {
                    "url": "<?php echo site_url('shares/jsonList2') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data":
                            function (e) {
                                e.status_id = '1';
                                e.app_status_id='1';
                                e.state_id = '7';     //Active approval
                                <?php if (isset($user['id'])) { ?>
                                e.client_id = <?php echo $user['id'] ?>;
                                <?php } ?>
                            }

                },
                "columnDefs": [{
                        "targets": [7],
                        "orderable": false,
                        "searchable": false
                    }],
                columns: [

                    {data: 'share_application_no', render: function (data, type, full, meta) {
                             return "<a href='<?php echo site_url('shares/view'); ?>/" + full.id + "' title='View Applications details'>" +data+ "</a>";
                        }},
                    {data: 'salutation', render: function (data, type, full, meta) {
                            if (type === "sort" || type === "filter") {
                                return "<a href='<?php echo site_url('member/member_personal_info'); ?>/" + full.member_id + "' title='View user profile'>" + data.salutation+' '+data.firstname+' '+data.lastname+' '+data.othernames+ "</a>";
                            }
                             return "<a href='<?php echo site_url('member/member_personal_info'); ?>/" + full.member_id + "' title='View user profile'>" + full.salutation+' '+full.firstname+' '+full.lastname+' '+full.othernames + "</a>";
                        }},
                 
                    {data: 'shares_requested', render: function (data, type, full, meta){
                            return data;
                           
                        }
                    },
                    {data: 'approved_shares', render: function (data, type, full, meta){
                            return data;
                           
                        }
                    },
                   
                    { data: "approval_date", render:function( data, type, full, meta ){
                        return (data)?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'None';;
                    }},
                    
                    {data: 'total_amount', render: function (data, type, full, meta){
                            return (data)?curr_format(data*1):0;
                            
                        }
                    },
                    {data: 'paid_amount', render: function (data, type, full, meta){
                           return (data)?curr_format(data*1):0;
                        }
                    },
                    {data: 'total_amount', render: function (data, type, full, meta){
                            return curr_format(parseFloat(data?data:0)-parseFloat(full.paid_amount?full.paid_amount:0));
                        }
                    }
                ],
                buttons: <?php if(in_array('6', $share_privilege)){ ?> getBtnConfig('Approved Share Applications'), <?php } else { echo "[],"; } ?>
                responsive: true
            });
        }
    }
