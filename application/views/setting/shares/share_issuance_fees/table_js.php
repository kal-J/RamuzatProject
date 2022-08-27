  if ($("#tblShare_issuance_fees").length && tabClicked === "tab-share-product-fees") {
                if (typeof (dTable['tblShare_issuance_fees']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-share-product-fees").addClass("active");
                    dTable['tblShare_issuance_fees'].ajax.reload(null, true);
                } else {
                    dTable['tblShare_issuance_fees'] = $('#tblShare_issuance_fees').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[0, 'asc']],
                        deferRender: true,
                        "ajax":{
                            "url": "<?php echo base_url('share_issuance_fees/jsonList/'); ?>",
                            "dataType": "json",
                            "type": "POST",
                            "data": function (d) {
                            d.shareproduct_id = "<?php echo $share_issuance['id']?>"
                            }
                        },
            "columns": [
                      { "data": "feename" },
                      { "data": "amountcalculatedas" },
                      { "data": "amount" , render:function ( data, type, full, meta ) {
                      return (full.amountcalculatedas_id==2)?"UGX "+curr_format(data*1):((data*1)+"%"); 
                    }},
                      { "data": "account_name_ac" },

                      { "data": "account_name_rec" },
                      {data: 'status_id', render:function ( data, type, full, meta ) {return (data==1)?"Active ":'Deactivated'; }},
            
                      { "data": "id", render:function ( data, type, full, meta ) {
                        var ret_txt ="";
                    
                       <?php if(in_array('7', $share_issuance_privilege)){ ?>
                        ret_txt += "<a href='#' class='btn btn-sm btn-warning change_status' title='Deactivate share fee'><i class='fa fa-refresh'></i></a></div>";
                        ret_txt += "<a href='#' class='btn btn-sm btn-danger delete_me' title='delete share fee'><i class='fa fa-trash '></i></a></div>";
                       <?php } ?>
                        return ret_txt;
                      }}
                   ],
                   buttons: <?php if(in_array('6', $share_issuance_privilege)){ ?> getBtnConfig('Share assignment'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
           }
