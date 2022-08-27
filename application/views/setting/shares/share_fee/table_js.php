if ($("#tblShare_fee").length && tabClicked === "tab-share_fee") {
                if (typeof (dTable['tblShare_fee']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-share_fee").addClass("active");
                    dTable['tblShare_fee'].ajax.reload(null, true);
                } else {
                    dTable['tblShare_fee'] = $('#tblShare_fee').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[1, 'asc']],
                        deferRender: true,
                        "ajax":{
                            "url": "<?php echo base_url('share_fee/jsonList/'); ?>",
                            "dataType": "json",
                            "type": "POST",
                            "data": function (d) {
                            d.status_id = 1
                            }
                        },
            "columns": [
                      {data: 'feename'},
                      { "data": "amountcalculatedas" },
                      { "data": "amount", render:function ( data, type, full, meta ) {
                      return (full.amountcalculatedas_id==2)?"UGX "+curr_format(data*1):(data+"%");} },
                      
                      { "data": "id", render:function ( data, type, full, meta ) {
                        var ret_txt ="";
                         <?php if(in_array('3', $share_issuance_privilege)){ ?>
                        ret_txt +="<a href='#add_share_fee-modal' data-toggle='modal' title='Edit record' class='btn text-primary btn-sm  edit_me'><i class='fa fa-edit'></i></a>";
                        <?php } if(in_array('7', $share_issuance_privilege)){ ?>
                        ret_txt += "<a href='#' data-toggle='modal' class='btn btn-sm text-danger  change_status' data-toggle='tooltip' title='Deactivate record'><i class='fa fa-ban'></i></a>";
                        <?php } ?>
                        return ret_txt;
                      }}
                   ],
                   buttons: <?php if(in_array('6', $share_issuance_privilege)){ ?> getBtnConfig('Share Fees'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }