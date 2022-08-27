if ($("#tblClient_loan_doc").length && tabClicked === "tab-loan_docs") {
                if (typeof (dTable['tblClient_loan_doc']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-loan_docs").addClass("active");
                    dTable['tblClient_loan_doc'].ajax.reload(null, true);
                } else {
                    dTable['tblClient_loan_doc'] = $('#tblClient_loan_doc').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[1, 'asc']],
                        deferRender: true,
                        "ajax":{
                            "url": "<?php echo base_url('client_loan_doc/jsonList/'); ?>",
                            "dataType": "json",
                            "type": "POST",
                            "data": function (d) {
                             d.status_id = 1,
                             d.client_loan_id = <?php echo $loan_detail['id']; ?>
                            }
                        },
            "columns": [
                      { "data": "loan_doc_type" },
                      { "data": "description" },
                       { "data": "file_name", render: function(data, type, full,meta){
                                if(full.file_name!==""){
                                    var organisation_id=<?php echo $_SESSION['organisation_id']; ?>;
                                    //var link="<a target='blank' href='http://docs.google.com/gview?url=<?php echo site_url(); ?>uploads/organisation_"+organisation_id+"/loan_docs/other_docs/"+data+"&amp;embedded=true' width='500' height='250' style='border-style:none;'>view file </a>";
                                    var link= "<a target='blank' href='<?php echo site_url(); ?>uploads/organisation_"+organisation_id+"/loan_docs/other_docs/"+data+"' title='View document details'>View File</a>";
                                    return link;
                                    }
                                return "No file";
                        }  }
                   ],
                        buttons:getBtnConfig('Loan docs'), 
                        responsive: true
                    });
                }
            }
