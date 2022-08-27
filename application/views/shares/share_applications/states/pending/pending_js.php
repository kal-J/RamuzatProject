    if ($("#tblShares_Pending_application").length && tabClicked === "tab-pending_application") {
        if (typeof (dTable['tblShares_Pending_application']) !== 'undefined') {
           $(".tab-pane").removeClass("active");
            $("#tab-share_application").addClass("active");
            $("#tab-pending_application").addClass("active");
            dTable['tblShares_Pending_application'].ajax.reload(null, true);
        } else {
            dTable['tblShares_Pending_application'] = $('#tblShares_Pending_application').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                ajax: {
                    "url": "<?php echo site_url('shares/jsonList2') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data":
                            function (e) {
                                e.status_id = '1';
                                e.app_status_id = '2';     //pending approval
                                e.state_id = '7';     //Active share accounts
                                <?php if (isset($user['id'])) { ?>
                                e.client_id = <?php echo $user['id'] ?>;
                                <?php } ?>
                            }

                },
                "columnDefs": [{
                        "targets": [6],
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

                    {data: 'category', render: function (data, type, full, meta){
                            return data;
                        }
                    },
                  { data: "application_date", render:function( data, type, full, meta ){
                  return (data)?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'None';;
                    }  },

                    {data: 'shares_requested', render: function (data, type, full, meta){
                            return data;
                           
                        }
                    },
                   
                    {data: 'shares_requested', render: function (data, type, full, meta){
                            return curr_format(data*full.share_price);
                            
                        }
                    },
                   
                    {data: 'id', render: function (data, type, full, meta) {
                            var display_btn ="";
                            <?php if(in_array('3', $share_privilege)){ ?>
                           display_btn += '<a href="#" class="btn btn-sm btn-success approve_app" data-toggle="modal" data-target="#approve-modal" title="Approve Application" style="margin-right: 10px;"><i class="fa fa-check-circle"></i>  </a>';
                           
                            <?php } if(in_array('4', $share_privilege)){ ?> 
                            display_btn += '<a href="#" class="btn btn-sm btn-danger delete_me" title="Delete Shares Application"><span class="fa fa-trash"></span></a>';
                            display_btn += "</div>";
                            <?php } ?>
                            return display_btn;
                        }
                    }
                ],
                buttons: <?php if(in_array('6', $share_privilege)){ ?> getBtnConfig('Pending Share Applications'), <?php } else { echo "[],"; } ?>
                responsive: true
            });
        }
    }

     $('table tbody').on('click', 'tr .approve_app', function (e) {
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
       sharesModel.application_details(data);
        });
