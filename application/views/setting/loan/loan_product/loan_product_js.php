if ($("#tblLoan_product").length && tabClicked === "tab-loan_product") {
                if (typeof (dTable['tblLoan_product']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-loan_product").addClass("active");
                    $("#Products").addClass("active");
                    dTable['tblLoan_product'].ajax.reload(null, true);
                } else {
                    dTable['tblLoan_product'] = $('#tblLoan_product').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[1, 'asc']],
                        deferRender: true,
                        ajax:{
                                 "url":  "<?php echo site_url('loan_product/jsonList') ?>",
                                 "dataType": "json",
                                 "type": "POST",
                                 "data": function(d){

                                  d.status_id ='1';
                                  }
                                  },
                        "columnDefs": [{
                                "targets": [5],
                                "orderable": false,
                                "searchable": false
                            }],
                        columns: [
                            {data: 'product_name', render: function (data, type, full, meta) {
                                    if (type === "sort" || type === "filter") {
                                        return data;
                                    }
                                    return "<a href='<?php echo site_url('loan_product/view'); ?>/" + full.id + "' title='View Loan product details'>" + data + "</a>";
                                }
                            },
                            {data: 'type_name'},
                            {data: 'fund_source_account'},
                            {data: 'name'},
                            {data: 'status_id', render:function ( data, type, full, meta ) {return (data==1)?"Active ":'Deactivated'; }},
                            {data: 'id', render: function (data, type, full, meta) {
                                    var display_btn = "<div class='btn-grp'><a href='<?php echo base_url();?>loan_product/view/"+full.id+"' class='btn btn-sm' title='Update Loan Product details'><i class='fa fa-edit'></i></a>";
                                    <?php if(in_array('7', $loan_product_privilege)){ ?>
                                    display_btn += '<a href="#" title="Delete Loan Product record"><span class="fa fa-trash text-danger change_status"></span></a>';
                                    <?php } ?>
                                    display_btn += "</div>";
                                    return display_btn;
                                }
                            }
                        ],
                        buttons: <?php if(in_array('6', $loan_product_privilege)){ ?> getBtnConfig('Loan Products'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }
