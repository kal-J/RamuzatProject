    if ($("#tblGuarantor").length && tabClicked === "tab-guarantors") {
        if (typeof(dTable['tblGuarantor']) !== 'undefined') {
            $(".tab-pane").removeClass("active");
            $("#tab-guarantors").addClass("active");
            dTable['tblGuarantor'].ajax.reload(null, true);
        } else {
            dTable['tblGuarantor'] = $('#tblGuarantor').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                "ajax": {
                    "url": "<?php echo base_url('guarantor/jsonList'); ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d) {
                        d.client_loan_id =" <?php echo $loan_detail['id']; ?>";
                        d.gender = $('#gender').val();
                    }
                },
                "bSort": false,
                "columns": [
                    
                   {"data": "[member_name", render: function (data, type, full, meta) {
                                   var mid=full.m_id;
                                   if(mid !=null){
                                    return "<a href='<?php echo site_url("member/member_personal_info"); ?>/" + full.m_id + "'>"+ full.member_name+"</a>";
                                  }
                                  else{
                                   return "<a href='#'>"+full.member_name+"</a>";
                              }
                                }},
                  
                      {"data": "guarantor_type_id", render: function (data, type, full, meta) {
                           const guarantor_type_id = full.guarantor_type_id ==1 ? "Existing Member" : "Non Existing Member";
                           return guarantor_type_id;

                                }
                            },
                       {"data": "gender", render: function (data, type, full, meta) {
                            const gender = full.gender ==1 ? "Male" : "Female";
                             return gender ;
                            }
                       },
                            
                     {
                        data: "mobile_number"
                    },
                     {
                        data: "nid_card_no"
                    },

                    {
                        data: "relationship_type"
                    },
                     {
                        data: "comment"
                    },
                    {
                        data: "id",
                        render: function(data, type, full, meta) {
                            return `
                            <div class="d-flex justify-content-center">
                                <button class="btn btn-xs btn-danger delete_me">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                            `;
                        }
                    },
                ],
                buttons: <?php if (in_array('6', $client_loan_privilege)) { ?> getBtnConfig('Guarantors'),
            <?php } else {
                                echo "[],";
                            } ?>
            responsive: true
            });
        }
    }
