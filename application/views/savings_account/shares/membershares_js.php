if ($("#tblMember_shares").length && tabClicked === "tab-member_shares") {
        if (typeof (dTable['tblMember_shares']) !== 'undefined') {
            $(".savings").removeClass("active");
            $("#tab-member_shares").addClass("active");
            $("#tab-savings").addClass("active");
            dTable['tblMember_shares'].ajax.reload(null, true);
        } else {
            dTable['tblMember_shares'] = $('#tblMember_shares').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                 "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]],
                order: [[1, 'desc']],
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
                        "targets": [2],
                        "orderable": false,
                        "searchable": false
                    }],
                columns: [
                    
                    {data: 'member_name', render: function(data, type, full, meta){
                           if(full.client_type==1){
                                 return "<a href='<?php echo site_url("member/member_personal_info"); ?>/" + full.member_id + "' title='View member details'>"+data+"</a>";
                            }else{
                                return "<a href='<?php echo site_url('Group/view'); ?>/" + full.member_id + "' title='View group details'>"+data+"</a>";
                            }
                        }
                    }
                    ,
         
                    {data: 'real_bal', render: function (data, type, full, meta) {
                            return curr_format(data * 1);
                        }
                    },
                    {data: 'real_bal', render: function (data, type, full, meta) {
                            return round((parseFloat(data)/parseFloat(savingsModel.total_savings()))*100,2);
                        }
                    }
        
                ],
                buttons: <?php if(in_array('6', $savings_privilege)){ ?> getBtnConfig('Active Accounts'), <?php } else { echo "[],"; } ?>
                responsive: true
            });
        }
    }
