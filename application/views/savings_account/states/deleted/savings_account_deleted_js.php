if ($("#tblSavings_account_deleted").length && tabClicked === "tab-savings_account_deleted") {
        if (typeof (dTable['tblSavings_account_deleted']) !== 'undefined') {
            $(".savings").removeClass("active");
            $("#tab-savings_account_deleted").addClass("active");
            $("#tab-savings").addClass("active");
            dTable['tblSavings_account_deleted'].ajax.reload(null, true);
        } else {
            dTable['tblSavings_account_deleted'] = $('#tblSavings_account_deleted').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                order: [[5, 'asc']],
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
                                e.state_id = '18';  //deleted
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
                            if (type === "sort" || type === "filter") {
                                return data;
                            }
                            return "<a class='btn btn-sm text-muted' href='<?php echo site_url('Savings_account/view'); ?>/" + full.id + "' title='View account details'><i class='fa fa-edit'></i></a>";
                        }
                    }
                ],
                buttons: <?php if(in_array('6', $savings_privilege)){ ?> getBtnConfig('Deleted savings account'), <?php } else { echo "[],"; } ?>
                responsive: true
            });
        }
    }
