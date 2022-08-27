    if ($("#tblShares_Active_Account").length && tabClicked === "tab-share_active_accounts" ) {
        if (typeof (dTable['tblShares_Active_Account']) !== 'undefined') {
           $(".tab-pane").removeClass("active");
            $("#tab-share_active_accounts").addClass("active");
            dTable['tblShares_Active_Account'].ajax.reload(null, true);
        } else {
            dTable['tblShares_Active_Account'] = $('#tblShares_Active_Account').DataTable({
                "dom": '<"html5buttons"B>lTfgirtp',
                 "processing": true,
                 "lengthMenu": [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
            "language": {
              processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
            },
            "deferRender": true,
            "searching": true,
            "paging": true,
            "responsive": true,
                ajax: {
                    "url": "<?php  
                    if(isset($user['id'])){
                        echo site_url('Shares/jsonList_member_shares');
                     }else if(isset($group['id'])){
                        echo site_url('Shares/jsonList_group_shares');
                     }else{
                       echo site_url('Shares/jsonList');
                     }
                    ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data":
                            function (e) {
                                e.end_date = $('#end_date1').val() ? moment($('#end_date1').val(), 'DD-MM-YYYY').format('YYYY-MM-DD') : '';
                                e.status_id = '1';
                                e.state_id = '7';     //Active 
                                e.transaction_status ='1';
                                <?php if (isset($user['id'])) { ?>
                                e.client_id = <?php echo $user['id'] ?>;
                                e.client_type = 1; 
                                <?php } ?>

                                <?php if(isset($group['id'])) { ?>
                                e.group_id = <?php echo $group['id'] ?>;
                                e.client_type = 2; 
                              
                                <?php } ?>
                            }

                },
                "footerCallback": function (tfoot, data, start, end, display) {
                        var api = this.api();
                        var num_share = api.column(3, {page: 'current'}).data().sum();
                        var total_num_shares = api.column(3).data().sum();                        
                        $(api.column(3).footer()).html(curr_format(round(parseFloat(total_num_shares),2)));
                        
                        var amount_page = api.column(4, {page: 'current'}).data().sum();
                        var amount_overall = api.column(4).data().sum();                        
                        $(api.column(4).footer()).html(curr_format(round(amount_overall,2)) );

                        
                    },
                    
                "columnDefs": [{
                        "orderable": true,
                        "searchable": true
                    }],
                columns: [

                    {data: 'share_account_no', render: function (data, type, full, meta) {
                             return "<a href='<?php echo site_url('shares/view'); ?>/" + full.id + "/" + full.client_type + "' title='View share details'>" +data+ "</a>";
                        }},
                    {data: 'salutation', render: function (data, type, full, meta) {
                        if(full.group_name) {
                            return full.group_name;
                        }
                            
                             return "<a href='<?php echo site_url('member/member_personal_info'); ?>/" + full.member_id + "' title='View user profile'>" + full.salutation+' '+full.firstname+' '+full.lastname+' '+full.othernames + "</a>";
                        }},
                 
                   {data: 'price_per_share', render: function (data, type, full, meta){
                            return (data)?curr_format(data*1):0;
                            
                        }
                    },
                    {data: 'num_of_shares', render: function (data, type, full, meta){
                            return data?round((parseFloat(data)),2):0;
                            
                        }
                    },
                    {data: 'total_amount', render: function (data, type, full, meta){
                            return (data)?curr_format(data*1):0;
                            
                        }
                    },
                     {data: 'id', render: function (data, type, full, meta) {
                            var display_btn ="<div class='flex justify-content-center px-1 py-1 m-0' style='width: max-content;'> <span>";
                            <?php if(in_array('3', $share_privilege)){ ?>
                            display_btn += '<a href="#" class="btn btn-xs btn-success mr-2 buy_shares" data-toggle="modal" data-target="#buy_shares" title="Buy shares from sacco/organisation"><i class="fa fa-check-circle"></i> Buy </a>';

                            if(parseFloat(full.total_amount)>0){
                                display_btn += '<a href="#" class="btn btn-xs btn-outline-primary mr-2 convert_shares" data-toggle="modal" data-target="#convert_shares" title="Sell shares back to sacco/organisation"><i class="fa fa-money"></i> Refund</a>';
                                display_btn += '<a href="#" class="btn btn-xs btn-outline-info mr-2 share_transfer" data-toggle="modal" data-target="#share_transfer" title="Transfer Shares"><i class="fa fa-exchange"></i> Transfer</a>';
                              
                            }

                            display_btn += '<a href="#" class="btn btn-xs btn-warning change_status_inactivate"  title="Deactivated Share Account"><i class="fa fa-undo"></i></a>';
                            <?php } ?>
                            return display_btn + '</span></div>';
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

            change_status({id: data.id, status_id: 2,state_id: 17,narrative:'Deactivated'}, url, tbl_id);
        });

        $('table tbody').on('click', 'tr .buy_shares', function (e) {
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
            sharesModel.accountd(data);
            get_accounts_details(data);
            get_member_savings_account(data.member_id);
        });

        $('table tbody').on('click', 'tr .convert_shares', function (e) {
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
            sharesModel.accountd(data);
            get_accounts_details(data);
            get_member_savings_account(data.member_id);
        });


        $('table tbody').on('click', 'tr .share_transfer', function (e) {
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
            sharesModel.account_tr(data);
             sharesModel.account_trans(data);
        });

        $('#transfer_to_select').select2({
            dropdownParent: $('#share_transfer')
        });

