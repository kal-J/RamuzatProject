    if ($("#tblSavings").length) {
        if (typeof (dTable['tblSavings']) !== 'undefined') {
         
            dTable['tblSavings'].ajax.reload(null, true);
        } else {
            dTable['tblSavings'] = $('#tblSavings').DataTable({
           
                order: [[1, 'asc']],
                deferRender: true,
                searching: false,
                paging: false,
                bInfo : false,
                ajax: {
                    "url": "<?php  
                    if(isset($user['id'])){
                        echo site_url('u/savings/jsonList_member');
                     }else if(isset($group['id'])){
                        echo site_url('u/savings/jsonList_group');
                     }else{
                       echo site_url('u/savings/jsonList');
                     }
                    ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data":
                            function (e) {
                                e.client_id = <?php echo $_SESSION['member_id'] ?>;
                                e.client_type = 1;  
                                <?php if(isset($group['id'])){ ?>
                                e.client_id = <?php echo $group['id'] ?>; 
                                e.client_type = 2; 
                                <?php } ?>
                            }
                },
                columns: [
                    {data: 'account_no', render: function (data, type, full, meta) {
                            if (type === "sort" || type === "filter") {
                                return data;
                            }
                            return "<a class='project-title' href='<?php echo site_url('u/savings/view'); ?>/" + full.id + "' title='View account details'>" + data + "</a>";
                        }
                    },
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
                    {data: 'id', render: function (data, type, full, meta) {
                          var display_btn ='<div> <a href="#" class="btn btn-xs btn-info mr-2 deposit" data-toggle="modal" data-target="#request_withdraw" title="Deposit Money"><i class="fa fa-money"></i> Request a withdraw</a>';
                            <?php if(isset($payment_engine['payment_id']) && $payment_engine['payment_id'] != NULL){?>
                            /* display_btn += '<a href="#" class="btn btn-xs btn-success mr-2 deposit" data-toggle="modal" data-target="#add_transaction" title="Deposit Money"><i class="fa fa-money"></i> Deposit</a>'; */
                            <?php  } ?>
                            display_btn += "<a class='btn btn-sm text-muted' href='<?php echo site_url('u/savings/view'); ?>/" + full.id + "' title='View account details'><i class='fa fa-eye'></i> View</a>";
                            display_btn += "</div>";
                            return display_btn;
                        }
                    }
                ],
                buttons: getBtnConfig('Saving Accounts'), 
                responsive: true
            });
        }
    }
