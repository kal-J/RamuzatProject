         if ($('#tblPayable_today').length && tabClicked === "tab-loan_payable_today") {
         daterangepicker_initializer();
                if(typeof(dTable['tblPayable_today'])!=='undefined'){
                    $(".loans").removeClass("active");
                    $(".tab-pane").removeClass("active");
                    $("#tab-loans").addClass("active");
                    $("#tab-loan_payable_today").addClass("active");
                    dTable['tblPayable_today'].ajax.reload(null,true);
                }else{
         dTable['tblPayable_today']= $('#tblPayable_today').DataTable({
            "pageLength": 10,
            "processing": true,
            "lengthMenu": [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
            "language": {
              processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
            },
            
            "deferRender": true,
            "searching": true,
            "paging": true,
            "responsive": true,
            "dom": '<"html5buttons"B>lTfgirtp',
            "buttons": <?php if(in_array('6', $client_loan_privilege)){ ?> getBtnConfig('<?php echo $title; ?>'), <?php } else { echo "[],"; } ?>
            "ajax":{
             "url": "<?php echo site_url('client_loan/loans_payable_today');?>",
             "dataType": "json",
             "type": "POST",
             "data": function(d){
              let date_to = $("#repayment_expected_end_date").val();
              let date_from = $("#repayment_expected_start_date").val();

              if(date_to) {
                d.repayment_expected_end_date = moment(date_to,'DD-MM-YYYY').format('YYYY-MM-DD');
              }
              if(date_from) {
                d.repayment_expected_start_date = moment(date_from,'DD-MM-YYYY').format('YYYY-MM-DD');
              }

              //d.date_to = moment(end_date,'X').format('YYYY-MM-DD');
              //d.date_from = moment(start_date,'X').format('YYYY-MM-DD');
              d.state_id = 7;
              <?php if(isset($user['id'])){ ?>
              d.client_id = <?php echo $user['id'] ?>; 
              <?php } ?>
              <?php if(isset($group_id)){ ?>
              d.group_id = <?php echo $group_id ?>; 
              <?php } ?>
              }
              },
              "order": [[ 13, "desc" ]],
              "footerCallback": function (tfoot, data, start, end, display) {
                    var api = this.api();
                $.each([4,5,6,7,8], function(key,val){
                  if(val==8){

                    var current_page_expected_val=((parseFloat(api.column(6, {page: 'current'}).data().sum()) + parseFloat(api.column(7, {page: 'current'}).data().sum())) );

                    var all_page_expected_val=((parseFloat(api.column(6).data().sum()) + parseFloat(api.column(7).data().sum())) );


                    $(api.column(val).footer()).html(curr_format(round(current_page_expected_val,2)) +"<br>["+curr_format(round(all_page_expected_val,2)) +"]");

                  }else{                  
                    var current_page_amount = api.column(val, {page: 'current'}).data().sum();
                    var all_page_amount = api.column(val).data().sum();
                   if(val==4){
                        $(api.column(val).footer()).html(curr_format(round(current_page_amount,0)) +"<br>["+curr_format( round(all_page_amount,0)) +"]");
                    }else{
                        $(api.column(val).footer()).html(curr_format(round(current_page_amount,2)) +"<br>["+curr_format( round(all_page_amount,2)) +"]");
                    }
                  }
                    });
                },
              rowCallback: function (row, data) {
                  <!-- defaulting installment -->
                  if(data.unpaid_installments >= 1){
                      $(row).addClass('text-danger');
                  }

              },
              "columnDefs": [
                { "searchable": true, "sortable": true, 'targets' : [0,1,2,3,4,5,6,7,8,9,10,11]  }
              ],
            "columns":[
                  {data: 'loan_no', render: function (data, type, full, meta) {
                          if (type === "sort" || type === "filter") {
                              return data;
                          }
                          var link1="<a href='<?php echo site_url('client_loan/view'); ?>/" + full.group_loan_id + "/1' title='View this Group Loan details'>" + data + "</a>";
                          var link2="<a href='<?php echo site_url('client_loan/view'); ?>/" + full.id + "' title='View this Loan details'>" + data + "</a>";
                          return (full.group_name == null)?link2:link1;
                      }
                  },
                  { data: "credit_officer_name",render:function( data, type, full, meta ){
                      return data;
                    },
                  },
                  
                  { data: "member_name",render:function( data, type, full, meta ){
                      return (data&&full.group_name)?full.group_name+' [ '+data+' ]':(!data&&full.group_name)?full.group_name:data;
                    }  },
                 
                  { data: "expected_principal", render:function( data, type, full, meta ){
                  return curr_format(round(data,0));
                    } },
                  { data: "expected_interest" , render:function( data, type, full, meta ){
                  return curr_format(round(data,2));
                    } }, 
                  { data: "paid_amount", render:function( data, type, full, meta ){
                  return curr_format(data*1);
                    } },
                  { data: "principal_in_demand", render:function( data, type, full, meta ){
                  return curr_format(data*1);
                    } },
                  { data: "interest_in_demand", render:function( data, type, full, meta ){
                  return curr_format(data*1);
                    } },
                  
                  { data: "principal_in_demand" , render:function( data, type, full, meta ){
                        return curr_format(parseFloat(data) + parseFloat(full.interest_in_demand));
                    } 
                  },
                  { data: "days_in_demand" , render:function( data, type, full, meta ){
                        return data;
                    } 
                  },
                  { data: "action_date", render:function( data, type, full, meta ){
                  return (data)?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'None';;
                    }  },
                  { data: "next_pay_date", render:function( data, type, full, meta ){
                  return (data)?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'None';;
                    }  },
                  { data: "last_pay_date", render:function( data, type, full, meta ){
                  return (data)?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'None';;
                    }  },
                  { data: "id", render:function ( data, type, full, meta ) {
                  var ret_txt ="<div class='btn-group'>";
                  <?php if(in_array('18', $client_loan_privilege)){ ?>
                    ret_txt = "<a href='#lock-modal' data-toggle='modal' class='btn btn-sm action_on_loan' data-toggle='tooltip' title='Lock Loan'><i class='text-danger fa fa-lock fa-fw'></i></a>"; <!-- -->
                    <?php } if(in_array('15', $client_loan_privilege)){ ?>
                    <!-- ret_txt += "<a href='#write_off-modal' data-toggle='modal' class='btn btn-sm money_action' data-toggle='tooltip' title='Writte off Loan'><i class='text-danger fa fa-power-off fa-fw fa-sm'></i></a>"; -->
                     <?php } if(in_array('16', $client_loan_privilege)){ ?>
                     
                       ret_txt += "<a href='#pay_off-modal' data-toggle='modal' class='btn btn-sm money_action' data-toggle='tooltip' title='Pay off Loan'><i class='text-danger fa fa-money fa-fw fa-sm'></i></a>";
                     
                    <?php }  ?>
                    ret_txt +="</div>";
                    return ret_txt;
                  }},
                  {
                    data: "id",
                    render: function(data, type, full, meta) {
                      return `
                      <div class="dropdown border-0">
                        <button class="btn btn-secondary btn-xs dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="fa fa-print"></i>
                        </button>
                        <div style="border: none; border-radius: 0; -webkit-box-shadow: none; box-shadow: none;" class="dropdown-menu pl-5 border-0 bg-transparent" aria-labelledby="dropdownMenuButton">
                        ${full.paid_amount ? `
                              <button style="width: 60px;" class="dropdown-item btn btn-primary btn-xs mb-1 ml-5" onclick="handlePrint_installment_payments(${full.id}, ${full.status_id})">
                                Payments
                              </button>
                            ` : ''}

                            <button style="width: 60px;" class="dropdown-item btn btn-primary btn-xs ml-5" onclick="handlePrint_loan_schedule(${data})">
                              Schedule
                            </button>
                        </div>
                      </div>
                      `;
                    }
                  }
                ]     

                });
                }
                }