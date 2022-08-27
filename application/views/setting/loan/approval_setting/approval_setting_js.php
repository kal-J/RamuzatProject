if ($("#tblApproval_setting").length && tabClicked === "tab-approval_setting") {
                if (typeof (dTable['tblApproval_setting']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-approval_setting").addClass("active");
                    dTable['tblApproval_setting'].ajax.reload(null, true);
                } else {
                    dTable['tblApproval_setting'] = $('#tblApproval_setting').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[1, 'asc']],
                        deferRender: true,
                        "ajax":{
                            "url": "<?php echo base_url('loan_approval_setting/jsonList'); ?>",
                            "dataType": "json",
                            "type": "POST",
                            "data": function (d) {
                            d.organisation_id = 1
                            }
                        },
            "columns": [
                      { "data": "min_amount", render:function(data,type,full,meta){
                      return (data)?curr_format(data*1):'Not set';
                        }},
                      { "data": "max_amount", render:function(data,type,full,meta){
                      return (data)?curr_format(data*1):'Not set';
                            } },
                      { "data": "min_approvals"},
                      { "data": "num_of_attached_staff", render: function (data, type, full, meta) {
                            if (type === "sort" || type === "filter") {
                                return data;
                            }

                            return "<a href='<?php echo site_url('loan_approval_setting/view'); ?>/" + full.id + "' title='View Attached staffs'>" + data + " <span class='badge badge-warning'> <i class='fa fa-eye'></i> View list</span></a>";
                        } },
                      { "data": "id", render:function ( data, type, full, meta ) {
                        var ret_txt ="";
                        <?php if(in_array('3', $approval_privilege)){ ?>
                        ret_txt +="<a href='#add_approval_setting-modal' data-toggle='modal' title='Edit record' class='btn text-primary btn-sm  edit_me'><i class='fa fa-edit'></i></a>";
                        <?php } ?>
                        return ret_txt;
                      }}
                    ],
                        buttons: <?php if(in_array('6', $approval_privilege)){ ?> getBtnConfig('Loan Approval Setting'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }