//Withdrawn client loan javascript 
         if ($('#tblPartial_application_loan').length && tabClicked ==="tab-partial_application") {
         //reinitailizing daterange picker
         daterangepicker_initializer();
             if(typeof(dTable['tblPartial_application_loan'])!=='undefined'){
                    $(".loans").removeClass("active");
                    $("#tab-partial_application").addClass("active");
                    $("#tab-loans").addClass("active");
                    dTable['tblPartial_application_loan'].ajax.reload(null,true);
                }else{
         dTable['tblPartial_application_loan']= $('#tblPartial_application_loan').DataTable({
            "pageLength": 10,
            "searching": true,
            "paging": true,
            "responsive": true,
            "dom": '<"html5buttons"B>lTfgitp',
            "processing": true,
            "serverSide": true,
            "deferRender": true,
            "buttons": <?php if(in_array('6', $client_loan_privilege)){ ?> getBtnConfig('<?php echo $title; ?>'), <?php } else { echo "[],"; } ?>
            "ajax":{
             "url": "<?php echo site_url('client_loan/jsonList');?>",
             "dataType": "json",
             "type": "POST",
             "data": function(d){
              //d.date_to = moment(end_date,'X').format('YYYY-MM-DD');
              //d.date_from = moment(start_date,'X').format('YYYY-MM-DD');
              d.state_id = 1;
              <?php if(isset($user['id'])){ ?>
              d.client_id = <?php echo $user['id'] ?>; 
              <?php } ?>
              <?php if(isset($group_id)){ ?>
              d.group_id = <?php echo $group_id ?>; 
              <?php } ?>
              }
              },
            "order": [[ 7, "desc" ]],
            "footerCallback": function (tfoot, data, start, end, display) {
                    var api = this.api();
                $.each([4], function(key,val){
                      var total_page_amount = api.column(val, {page: 'current'}).data().sum();
                      $(api.column(val).footer()).html(curr_format(round(total_page_amount,2)));
                    });
                },
            "columnDefs": [{
                              "targets": [6],
                              "orderable": false,
                              "searchable": false
                          }],
          "columns":[

                  {data: 'loan_no', render: function (data, type, full, meta) {
                          if (type === "sort" || type === "filter") {
                              return data;
                          }
                         var link1="<a href='<?php echo site_url('client_loan/view'); ?>/" + full.group_loan_id + "/1' title='View this Loan details'>" + data + "</a>";
                          var link2="<a href='<?php echo site_url('client_loan/view'); ?>/" + full.id + "' title='View this Loan details'>" + data + "</a>";
                          return (full.group_name == null)?link2:link1;
                      }
                  },
                  { data: "member_name",render:function( data, type, full, meta ){
                      return (data&&full.group_name)?full.group_name+' [ '+data+' ]':(!data&&full.group_name)?full.group_name:data;
                    } },
                  { data: "product_name" },
                  { data: "credit_officer_name" },
                  { data: "requested_amount", render:function( data, type, full, meta ){
                  return curr_format(data*1);
                    } },
                  { data: "application_date", render:function( data, type, full, meta ){
                  return (data)?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'None';;
                    }  },
                  { data: "comment" },
                  { data: "id", render:function ( data, type, full, meta ) {
                    var ret_txt ="<div class='btn-group'>";
                        if(parseInt(full.credit_officer_id) !=0){//checking credit officer
                          <?php  if($org['loan_app_stage']==0){
                              if(in_array('20', $client_loan_privilege)){?>

                                  ret_txt +="<a href='#forward_application-modal' data-toggle='modal' title='Forward application' class='btn btn-sm action_on_loan'><i class='fa fa-forward' aria-hidden='true'></i></a>";
                              <?php } 
                          }elseif ($org['loan_app_stage']==1){
                              if(in_array('14', $client_loan_privilege)){ ?>
                                  ret_txt +="<a href='#approve-modal' data-toggle='modal' title='Approve' class='btn btn-sm approve_loan'><i class='fa fa-check-square-o' style='font-size:16px'></i>"+full._approvals+"</a>";
                                  <?php }
                          }elseif ($org['loan_app_stage']==2) {
                              if(in_array('22', $client_loan_privilege)){ ?>
                                  ret_txt += "<a href='#disburse-modal' data-toggle='modal' class='btn btn-sm disburse' data-toggle='tooltip' title='Disburse Loan'><i class='text-success fa fa-check-square-o fa-fw fa-sm'></i></a>";
                              <?php }

                          }  ?>
                      }//end of check for credit officer
                    <?php  if(in_array('10', $client_loan_privilege)){ ?>
                    ret_txt += "<a href='#reject-modal' data-toggle='modal' class='btn btn-sm action_on_loan' data-toggle='tooltip' title='Reject Loan Application'><i class='text-danger fa fa-ban'></i></a>";
                    <?php } if(in_array('12', $client_loan_privilege)){ ?>
                    ret_txt += "<a href='#application_withdraw-modal' data-toggle='modal' class='btn btn-sm action_on_loan' data-toggle='tooltip' title='Withdraw Loan Application'><i class='text-danger fa fa-undo'></i></a>";
                    <?php } ?>
                    ret_txt +="</div>";
                    return ret_txt;
                  }}
                ]     

                });
                }
                }

