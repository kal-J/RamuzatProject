    if ($("#tblShares_Pending_Account").length && tabClicked === "tab-share_pending_accounts") {
        if (typeof (dTable['tblShares_Pending_Account']) !== 'undefined') {
           $(".tab-pane").removeClass("active");
            $("#tab-share_pending_accounts").addClass("active");
            dTable['tblShares_Pending_Account'].ajax.reload(null, true);
        } else {
            dTable['tblShares_Pending_Account'] = $('#tblShares_Pending_Account').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                ajax: {
                    "url": "<?php echo site_url('shares/jsonList') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data":
                        function (e) {
                            e.status_id = '1';
                            e.state_id = '5';     //pending approval
                            <?php if (isset($user['id'])) { ?>
                            e.client_id = <?php echo $user['id'] ?>;
                            <?php } ?>
                            <?php if(isset($group)) { ?>
                            e.group_id = <?php echo $group['id']; ?>
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
                            if(full.group_name) {
                                return full.group_name;
                            }
                            if (type === "sort" || type === "filter") {
                                return "<a href='<?php echo site_url('member/member_personal_info'); ?>/" + full.member_id + "' title='View user profile'>" + data.salutation+' '+data.firstname+' '+data.lastname+' '+data.othernames+ "</a>";
                            }
                             return "<a href='<?php echo site_url('member/member_personal_info'); ?>/" + full.member_id + "' title='View user profile'>" + full.salutation+' '+full.firstname+' '+full.lastname+' '+full.othernames + "</a>";
                        }},
                    {data: 'total_amount', render: function (data, type, full, meta){
                            return (data)?curr_format(data*1):0;
                            
                        }
                    },
                   
                    {data: 'id', render: function (data, type, full, meta) {
                            var display_btn ="";
                            <?php if(in_array('3', $share_privilege)){ ?>
                            display_btn += '<a href="#" class="btn btn-sm btn-success change_status_active"  title="Activate" style="margin-right: 10px;"><i class="fa fa-check-circle"></i></a>';
                            
                            <?php } if(in_array('4', $share_privilege)){ ?> 
                            display_btn += '<a href="#" class="btn btn-sm btn-danger delete_account" title="Delete Shares Account"><span class="fa fa-trash"></span></a>';
                            display_btn += "</div>";
                            <?php } ?>
                            return display_btn;
                        }
                    }
                ],
                buttons: <?php if(in_array('6', $share_privilege)){ ?> getBtnConfig('Pending Share Accounts'), <?php } else { echo "[],"; } ?>
                responsive: true
            });
        }
    }
 $('table tbody').on('click', 'tr .change_status_active', function (e) {
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

            change_status({id: data.id, status_id: 1,state_id: 7,narrative:'Activated'}, url, tbl_id);
 });
 $('table tbody').on('click', 'tr .delete_account', function (e) {
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
            var url = "<?php echo site_url(); ?>/shares"+ "/delete";
            delete_item("Are you sure, you want to delete this Account?", data.id, url, tbl_id);
        });
