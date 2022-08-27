if ($("#tblApplied_share_fee").length && tabClicked === "tab-apply_share_fee") {
                if (typeof (dTable['tblApplied_share_fee']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-apply_share_fee").addClass("active");
                    dTable['tblApplied_share_fee'].ajax.reload(null, true);
                } else {
                    dTable['tblApplied_share_fee'] = $('#tblApplied_share_fee').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[1, 'asc']],
                        deferRender: true,
                        "ajax":{
                            "url": "<?php echo base_url('applied_share_fee/jsonList'); ?>",
                            "dataType": "json",
                            "type": "POST",
                            "data": function (d) {
                             d.status_id = 1,
                             d.member_id = <?php echo $share_details['member_id']; ?>,
                             d.share_id = <?php echo $share_details['id']; ?>
                            }
                        },
                         "columnDefs": [{
                                "targets": [2],
                                "orderable": false,
                                "searchable": false
                            }],
            "columns": [
                      { "data": "transaction_no" },
                      { "data": "feename" },
                      { "data": "amountcalculatedas" },
                      { "data": "amount" },
                      { "data": "requiredfee_" },
                      {"data": "id", render: function (data, type, full, meta) {
                            var ret_txt ="";
                            // ret_txt += 
                                <?php if(in_array('4', $share_privilege)){ ?>
                                    ret_txt += "<a href='<?php echo base_url(); ?>applied_share_fee/pdf/<?php echo $share_details['member_id']; ?>/<?php echo $share_details['id']; ?>/"+full.transaction_no+"'  target = '_blank' class='btn btn-primary aquaBtn' ><i class='ti-printer'></i>&nbsp;Print reciept&nbsp;</a>";
                                    ret_txt += '<a href="#" data-toggle="modal"   title="delete fee details" class="delete_me"> &nbsp;&nbsp;<i class="fa fa-trash"  style="color:#bf0b05"></i></a>';
                                <?php } ?>
                                return ret_txt;
                            }}

                   ],
                        buttons: <?php if(in_array('6', $share_privilege)){ ?> getBtnConfig('Apply Share Fees'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }
