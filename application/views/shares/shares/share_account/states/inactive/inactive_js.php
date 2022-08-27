    if ($("#tblShares_Inactive_Account").length && tabClicked === "tab-share_inactive_accounts") {
        if (typeof (dTable['tblShares_Inactive_Account']) !== 'undefined') {
           $(".tab-pane").removeClass("active");
            $("#tab-share_inactive_accounts").addClass("active");
            dTable['tblShares_Inactive_Account'].ajax.reload(null, true);
        } else {
            dTable['tblShares_Inactive_Account'] = $('#tblShares_Inactive_Account').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                ajax: {
                    "url": "<?php echo site_url('shares/jsonList') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data":
                        function (e) {
                            e.status_id = '1';
                            e.state_id = '17';     //Active approval
                            <?php if (isset($user['id'])) { ?>
                            e.client_id = <?php echo $user['id'] ?>;
                            <?php } ?>
                            <?php if(isset($group)) { ?>
                            e.group_id = <?php echo $group['id']; ?>
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
                            "targets": [3],
                            "orderable": true,
                            "searchable": true
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
                            display_btn += '<a href="#" class="btn btn-sm btn-success change_status_active"  title="Activated Share Account" style="margin-right: 10px;"><i class="fa fa-check-circle"></i></a>';
                            <?php } ?>
                            return display_btn;
                        }
                    }
                  
                ],
                buttons: <?php if(in_array('6', $share_privilege)){ ?> getBtnConfig('Inactive Shares Accounts'), <?php } else { echo "[],"; } ?>
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
