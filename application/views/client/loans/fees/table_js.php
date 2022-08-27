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
                             d.status_id = 1,
                             d.client_loan_id = <?php echo $loan_detail['id']; ?>
                            }
                        },
                         "columnDefs": [{
                                "targets": [3],
                                "orderable": false,
                                "searchable": false
                            }],
            "columns": [
                        {"data": "feename" },
                        {"data": "date_created", render: function(data, type, full, meta){ if(type == 'sort' || type=='filter'){ return data;} return moment(data,'X').format('D-MMM-YYYY');} },    
                        {"data": "amount",render:function(data, type,full ,meta ){
                            return curr_format(data*1);
                          } },
                        {"data": "paid_or_not", render: function(data, type,full ,meta ){
                            return (data==0)?'Not':'Yes';
                        } }

                   ],
                        buttons:  getBtnConfig('Apply Loan Fees'),
                        responsive: true
                    });
                }
            }
