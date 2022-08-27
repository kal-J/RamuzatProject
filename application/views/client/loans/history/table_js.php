if ($("#tblLoan_history").length && tabClicked === "tab-loan_history") {
                if (typeof (dTable['tblLoan_history']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-loan_history").addClass("active");
                    dTable['tblLoan_history'].ajax.reload(null, true);
                } else {
                    dTable['tblLoan_history'] = $('#tblLoan_history').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        "ajax":{
                            "url": "<?php echo base_url('loan_state/jsonList'); ?>",
                            "dataType": "json",
                            "type": "POST",
                            "data": function (d) {
                             d.client_loan_id = <?php echo $loan_detail['id']; ?>
                            }
                        },
            "columns": [
                        { data: "action_date", render:function( data, type, full, meta ){
                            if (type === "sort" || type === "filter") {
                                return data;
                            }
                          return (!(data=='0000-00-00'))?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'';
                            }  },
                        { data: "state_name"},
                        { data: "firstname", render:function(data, type, full, meta){
                          return (data)?full.salutation+' '+full.firstname+' '+full.lastname+' '+full.othernames:data;
                        }},
                        { data: "comment"},
                      ],
                        buttons:  getBtnConfig('Loan History'),
                        responsive: true
                    });
               }
            }