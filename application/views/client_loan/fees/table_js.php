if ($("#tblApplied_loan_fee").length && tabClicked === "tab-loan_fee") {
                if (typeof (dTable['tblApplied_loan_fee']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-loan_fee").addClass("active");
                    dTable['tblApplied_loan_fee'].ajax.reload(null, true);
                } else {
                    dTable['tblApplied_loan_fee'] = $('#tblApplied_loan_fee').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[1, 'asc']],
                        deferRender: true,
                        "ajax":{
                            "url": "<?php echo base_url('applied_loan_fee/jsonList/'); ?>",
                            "dataType": "json",
                            "type": "POST",
                            "data": function (d) {
                             d.status_id = 1;
                             <?php if (isset($loan_detail)) { ?>
                             
                                d.client_loan_id = <?php echo $loan_detail['id']; ?>
                             <?php } else{ ?>
                             d.start_date = $('#loan_fees_start_date').val() ? moment($('#loan_fees_start_date').val(), 'DD-MM-YYYY').format('YYYY-MM-DD') : '';
                             d.end_date = $('#loan_fees_end_date').val() ? moment($('#loan_fees_end_date').val(), 'DD-MM-YYYY').format('YYYY-MM-DD') : '';
                             <?php }  ?>
                            }
                        },
                         "columnDefs": [{
                                "targets": [4],
                                "orderable": false,
                                "searchable": false
                            }],
            "columns": [
                        <?php if (!isset($loan_detail)) { ?>
                            {data: 'loan_no', render: function (data, type, full, meta) {
                                    if (type === "sort" || type === "filter") {
                                        return data;
                                    }
                                    var link1="<a href='<?php echo site_url('client_loan/view'); ?>/" + full.client_loan_id + "/1' title='View this Loan details'>" + data + "</a>";
                                    var link2="<a href='<?php echo site_url('client_loan/view'); ?>/" + full.client_loan_id + "' title='View this Loan details'>" + data + "</a>";
                                    return (full.member_name == null)?link1:link2;
                                }
                            },
                            { data: "member_name",render:function( data, type, full, meta ){
                                return (data)?data:full.group_name;
                            }  },
                        <?php } ?>
                        {"data": "feename" },
                        {"data": "date_created", render: function(data, type, full, meta){ if(type == 'sort' || type=='filter'){ return data;} return moment(data,'X').format('D-MMM-YYYY');} },   
                        {"data": "amount",render:function(data, type,full ,meta ){
                            return curr_format(data*1);
                          } },
                        {"data": "date_paid", render: function(data, type, full, meta){ if(type == 'sort' || type=='filter'){ return data;} 
                        if(full.paid_or_not==0){ return "N/A";} else {return moment(data, 'YYYY-MM-DD').format("D-MMM-YYYY");}
                         } },   
                        
                        {"data": "paid_or_not", render: function(data, type,full ,meta ){
                            return (data==0)?'<a href="#" role="button" class="badge badge-danger" >Not Paid</a>':'<a href="#" role="button" class="badge badge-primary" >Paid</a>';
                        } },
                        <?php if (isset($loan_detail)) { ?>
                        {"data": "id", render: function (data, type, full, meta) {
                            var ret_txt ="";
                                <?php if(in_array('6', $client_loan_privilege)){ ?>
                                    <!-- ret_txt += "<a href='<?php //echo base_url(); ?>applied_loan_fee/pdf/<?php //echo $loan_detail['id']; ?>/"+full.transaction_no+"'  target = '_blank' class='btn btn-primary aquaBtn' ><i class='ti-printer'></i>&nbsp;Print Reciept&nbsp;</a>"; -->
                                <?php } if(in_array('4', $client_loan_privilege)){ ?>
                                    if(full.paid_or_not==0){
                                        ret_txt += '<a href="#" data-toggle="modal"   title="delete loan fee" class="btn btn-xs btn-danger delete_fee"><i class="fa fa-trash" ></i></a> &nbsp;&nbsp;';
                                    }
                                    
                                 <?php } if(in_array('16', $client_loan_privilege)||in_array('13', $client_loan_privilege)){ ?>
                                    if(full.paid_or_not==0){
                                        ret_txt += '<a href="#pay_loan_fee-modal" data-toggle="modal"   title="Pay Loan Fees" class="btn btn-xs btn-success"><i class="fa fa-money" ></i> Pay</a>';
                                    } else{
                                  ret_txt += 'N/A';
                                   }
                                    
                                <?php } ?>
                                return ret_txt;
                            }
                        }
                        <?php } ?>

                   ],
                        buttons: <?php if(in_array('6', $client_loan_privilege)){ ?> getBtnConfig('Apply Loan Fees'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }

$('table tbody').on('click', 'tr .delete_fee', function (e) {
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
            var url = "<?php echo site_url(); ?>" + controller.toLowerCase() + "/delete";

            change_status({id: data.id,loan_product_id:loanDetailModel.loan_detail().loan_product_id,client_loan_id:data.client_loan_id,status_id: (parseInt(data.status_id) === 3)}, url, tbl_id,"Are you sure, you want to delete this record?");
        });