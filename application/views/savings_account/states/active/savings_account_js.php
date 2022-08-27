    if ($("#tblSavings_account").length && tabClicked === "tab-active_accounts") {
        if (typeof (dTable['tblSavings_account']) !== 'undefined') {
            $(".savings").removeClass("active");
            $(".tab-pane").removeClass("active");
            $("#tab-active_accounts").addClass("active");
            $("#tab-savings").addClass("active");
            dTable['tblSavings_account'].ajax.reload(null, true);
        } else {
            dTable['tblSavings_account'] = $('#tblSavings_account').DataTable({
                "dom": '<"html5buttons"B>lTfgirtp',
                order: [[5, 'asc']],
                 "lengthMenu": [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                  "processing": true,
                  "language": {
              processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
            },
                deferRender: true,
                ajax: {
                    "url": "<?php  
                    if(isset($user['id'])){
                        echo site_url('Savings_account/jsonList_member');
                     }else if(isset($group['id'])){
                        echo site_url('Savings_account/jsonList_group');
                     }else{
                       echo site_url('Savings_account/jsonList');
                     }
                    ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data":
                            function (e) {
                                
                                e.state_id = '7';
                                e.balance_end_date = $('#balance_end_date').val();
                                e.producttype = $('#producttype').val();
                                e.gender = $('#gender').val();
                                <?php if(isset($user['id'])){ ?>
                                e.client_id = <?php echo $user['id'] ?>;
                                e.client_type = 1;  
                                <?php } ?>
                                <?php if(isset($group['id'])){ ?>
                                e.client_id = <?php echo $group['id'] ?>; 
                                e.client_type = 2; 
                                <?php } ?>
                            }
                },
                "footerCallback": function (tfoot, data, start, end, display) {
                        var api = this.api();
                        var amount_page = api.column(4, {page: 'current'}).data().sum();
                        var amount_overall = api.column(4).data().sum();                        
                        $(api.column(4).footer()).html(curr_format(round(amount_overall,2)) );
                    },
                "columnDefs": [{
                        "targets": [5],
                        "orderable": false,
                        "searchable": false
                    }],
                columns: [
                    {data: 'account_no', render: function (data, type, full, meta) {
                            if (type === "sort" || type === "filter") {
                                return data;
                            }
                            return "<a href='<?php echo site_url('Savings_account/view'); ?>/" + full.id + "' title='View account details'>" + data + "</a>";
                        }
                    },
                    {data: 'member_name', render: function(data, type, full, meta){
                            if(full.child_name) {
                                return `${full.child_name} - [ ${data} ]`;
                            }
                            return data;
                           //if(full.client_type==1){
                                 //return "<a href='<?php //echo site_url("member/member_personal_info"); ?>/" + full.member_id + "' title='View member details'>"+data+"</a>";
                            // }else{
                            //    return "<a href='<?php //echo site_url('Group/view'); ?>/" + full.member_id + "' title='View group details'>"+data+"</a>";
                            //}
                        }
                    }
                    ,
                    {data: 'productname'},
                    {data: 'client_type', render: function (data, type, full, meta) {
                            if(data==1){
                                return "Individual";
                            }else if(data==2){
                                return "Group";
                            }else if(data==3){
                                return "Both";
                            }else{
                                return "Null";
                            }
                        }
                    },
                    {data: 'real_bal', render: function (data, type, full, meta) {
                            return curr_format(data * 1);
                        }
                    },
                    {data: 'cash_bal', render: function (data, type, full, meta) {
                    return curr_format(data * 1);
                    }
                    },
                    {data: 'id', render: function (data, type, full, meta) {
                          var display_btn ="<div>";
                        <?php  if(in_array('24', $savings_privilege)){ ?>
                            display_btn += '<a href="#" class="btn btn-sm deposit" data-toggle="modal" data-target="#add_transaction" title=" Deposit Money"><i class="fa fa-arrow-up text-green"></i></a>';
                        <?php } if(in_array('23', $savings_privilege)){ ?>

                            if(parseFloat(full.cash_bal)+parseFloat(Number(full.qualifying_amount))>(parseFloat(full.min_balance)+(full.type==1?full.cash_bal:full.qualifying_amount))){
                            display_btn += '<a href="#" class="btn btn-sm withdraw" data-toggle="modal" data-target="#add_witdraw" title="Withdraw Money"><i class="fa fa-arrow-down"></i> </a>';
                             display_btn += '<a href="#" class="btn btn-sm transfer" data-toggle="modal" data-target="#transfer" title="Transfer Money"><i class="fa fa-exchange"></i> </a>';

                            }
                        <?php } ?>
                            display_btn += "<a class='btn btn-sm text-muted' href='<?php echo site_url('Savings_account/view'); ?>/" + full.id + "' title='View account details'><i class='fa fa-edit'></i></a>";
                            display_btn += "</div>";
                            return display_btn;
                        }
                    }
                ],
                buttons: <?php if(in_array('6', $savings_privilege)){ ?> getBtnConfig('Active Accounts'), <?php } else { echo "[],"; } ?>
                responsive: true
            });
        }
    }