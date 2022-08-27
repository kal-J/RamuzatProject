if ($("#tblMember_fees").length && tabClicked === "tab-member_fees") {
                if (typeof (dTable['tblMember_fees']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-member_fees").addClass("active");
                    dTable['tblMember_fees'].ajax.reload(null, true);
                } else {
                    dTable['tblMember_fees'] = $('#tblMember_fees').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[1, 'asc']],
                        deferRender: true,
                        "ajax":{
                          "url": "<?php echo base_url('member_fees/jsonList/'); ?>",
                          "dataType": "json",
                          "type": "POST",
                          "data": function (d) {
                          d.status_id = 1
                          }
                        },
            "columns": [
                      { "data": "feename" },
                      { "data": "amount", render:function( data, type, full, meta ){ return data?curr_format(data*1):data; } },
                      { "data": "requiredfee", render:function( data, type, full, meta ){ return full.requiredfee == 1  ? 'Yes' : 'No' } },
                      { "data": "income_account" },
                      { "data": "receivable_account" },
                      { "data": "description" },
                      { "data": "id", render:function ( data, type, full, meta ) {
                        var ret_txt ="";
                        <?php if(in_array('3', $membership_privilege)){ ?>
                        ret_txt +="<a href='#add_member_fees-modal' data-toggle='modal' title='Edit record' class='btn text-primary btn-sm  edit_me'><i class='fa fa-edit'></i></a>";
                      <?php } if(in_array('7', $membership_privilege)){ ?>
                        ret_txt += "<a href='#' data-toggle='modal' class='btn btn-sm text-danger  change_status' data-toggle='tooltip' title='Deactivate record'><i class='fa fa-ban'></i></a>";
                        <?php } ?>
                        return ret_txt;
                      }}
                   ],
                   buttons: <?php if(in_array('6', $membership_privilege)){ ?> getBtnConfig('Memeber Fees setup'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }
