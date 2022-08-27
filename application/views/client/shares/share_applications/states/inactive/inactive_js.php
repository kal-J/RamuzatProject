    if ($("#tblShares_Inactive_application").length && tabClicked === "tab-inactive_application") {
        if (typeof (dTable['tblShares_Inactive_application']) !== 'undefined') {
           $(".tab-pane").removeClass("active");
            $("#tab-share_application").addClass("active");
            $("#tab-inactive_application").addClass("active");
            dTable['tblShares_Inactive_application'].ajax.reload(null, true);
        } else {
            dTable['tblShares_Inactive_application'] = $('#tblShares_Inactive_application').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                ajax: {
                    "url": "<?php echo site_url('shares/jsonList2') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data":
                        function (e) {
                            e.status_id = '1';
                            e.state_id = '3';     //Inactive approval
                            <?php if (isset($user['id'])) { ?>
                            e.client_id = <?php echo $user['id'] ?>;
                            <?php } ?>
                        }
                },
                "columnDefs": [{
                        "targets": [5],
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
                    {data: 'submission_date', render: function (data, type, full, meta) {
                            if (type === "sort" || type === "filter") {
                                return data;
                            }
                            return (data)?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):data;
                        }
                    },
                    {data: 'shares'},
                    {data: 'state_id', render: function (data, type, full, meta){
                            if(parseInt(data)===3){
                            return '<span class="text-danger"><b>Canceled</b></span>';
                            }else{
                            return data;
                            }
                        }
                    },
                    {data: 'id', render: function (data, type, full, meta) {
                            var display_btn ="";
                            <?php if(in_array('4', $share_privilege)){ ?> 
                            display_btn += '<a href="#" title="Delete Shares Account"><span class="fa fa-trash text-danger change_status"></span></a>';
                            display_btn += "</div>";
                            <?php } ?>
                            return display_btn;
                        }
                    }
                ],
                buttons: <?php if(in_array('6', $share_privilege)){ ?> getBtnConfig('inactive Share Applications'), <?php } else { echo "[],"; } ?>
                responsive: true
            });
        }
    }
