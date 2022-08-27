    if ($("#tblShares_Active_Account").length && tabClicked === "tab-member_shares") {
        if(typeof (dTable['tblShares_Active_Account']) !== 'undefined') {
          //$("#tab-member_shares").addClass("active");
            dTable['tblShares_Active_Account'].ajax.reload(null, true);
        } else {
            dTable['tblShares_Active_Account'] = $('#tblShares_Active_Account').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                ajax: {
                    "url": "<?php echo site_url('shares/jsonList') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data":
                            function (e) {
                                e.status_id = '1';
                                e.state_id = '7';     //Active 
                                <?php if (isset($user['id'])) { ?>
                                e.client_id = <?php echo $user['id'] ?>;
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
                     {data: 'id', render: function (data, type, full, meta) {
                            var display_btn ="";
                            <?php if(in_array('3', $share_privilege)){ ?>
                            display_btn += '<a href="#" class="btn btn-sm btn-success buy_shares" data-toggle="modal" data-target="#buy_shares" title="Buy Shares from Organisation/Sacco" style="margin-right: 10px;"><i class="fa fa-check-circle"></i> Buy </a>';

                            display_btn += '<a href="#" class="btn btn-sm transfer" data-toggle="modal" data-target="#transfer" title="Transfer Shares"><i class="fa fa-exchange"></i> </a>';

                            display_btn += '<a href="#" class="btn btn-sm btn-warning change_status_inactivate"  title="Deactivated Share Account" style="margin-right: 10px;"><i class="fa fa-undo"></i></a>';
                            <?php } ?>
                            return display_btn;
                        }
                    }
                  
                ],
                buttons: <?php if(in_array('6', $share_privilege)){ ?> getBtnConfig('Active Shares Accounts'), <?php } else { echo "[],"; } ?>
                responsive: true
            });
        }
    }

 $('table tbody').on('click', 'tr .change_status_inactivate', function (e) {
            e.preventDefault();
            var row = $(this).closest('tr');
            var tbl = row.parent().parent();
            var tbl_id = $(tbl).attr("id");
            var dt = dTable[tbl_id];
            var data = dt.row(row).data();
            if (typeof (data) === 'undefined') {
                data = dt.row($(row).prev()).data();
                if (typeof (data) === 'undefined') {
                    data = dt.row($(row).prev().prev()).data();
                }
            }
            var controller = tbl_id.replace("tbl", "");
            var url = "<?php echo site_url(); ?>shares"+ "/change_status";

            change_status({id: data.id, status_id: 2,state_id: 19,narrative:'Deactivated'}, url, tbl_id);
        });

        $('table tbody').on('click', 'tr .buy_shares', function (e) {
            e.preventDefault();
            var row = $(this).closest('tr');
            var tbl = row.parent().parent();
            tbl_id = $(tbl).attr("id");
            var dt = dTable[tbl_id];
            dt.search("").draw();
            var data = dt.row(row).data();
            if (typeof (data) === 'undefined') {
                data = dt.row($(row).prev()).data();
                if (typeof (data) === 'undefined') {
                    data = dt.row($(row).prev().prev()).data();
                }
            }
          
            sharesModel.call_payment(data);
            sharesModel.member(data.member_id);
        });


        $('table tbody').on('click', 'tr .transfer', function (e) {
            e.preventDefault();
            var row = $(this).closest('tr');
            var tbl = row.parent().parent();
            tbl_id = $(tbl).attr("id");
            var dt = dTable[tbl_id];
            var data = dt.row(row).data();
            if (typeof (data) === 'undefined') {
                data = dt.row($(row).prev()).data();
                if (typeof (data) === 'undefined') {
                    data = dt.row($(row).prev().prev()).data();
                }
            }
             sharesModel.account_trans( data );
        });

