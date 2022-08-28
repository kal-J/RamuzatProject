//Active client loan javascript 
         if ($('#tblActive_client_loan').length && tabClicked === "tab-active") {
         //reinitailizing daterange picker
         daterangepicker_initializer();
                if(typeof(dTable['tblActive_client_loan'])!=='undefined'){
                    $(".loans").removeClass("active");
                    $(".tab-pane").removeClass("active");
                    $("#tab-loans").addClass("active");
                    $("#tab-active").addClass("active");
                    dTable['tblActive_client_loan'].ajax.reload(null,true);
                }else{
         dTable['tblActive_client_loan']= $('#tblActive_client_loan').DataTable({
            "pageLength": 10,
            "processing": true,
            "language": {
              processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
            },
            "serverSide": true,
            "deferRender": true,
            "searching": true,
            "paging": true,
            "responsive": true,
            "dom": '<"html5buttons"B>lTfgirtp',
            "buttons": <?php if(in_array('6', $client_loan_privilege)){ ?> getBtnConfig('<?php echo $title; ?>'), <?php } else { echo "[],"; } ?>
            "ajax":{
             "url": "<?php echo site_url('client_loan/jsonList');?>",
             "dataType": "json",
             "type": "POST",
             "data": function(d){
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
              "order": [[ 12, "desc" ]],
              "footerCallback": function (tfoot, data, start, end, display) {
                    var api = this.api();
                $.each([2,3,4,5,6,7], function(key,val){
                  if(val==7){

                    var current_page_expected_val=(parseFloat(api.column(4, {page: 'current'}).data().sum()) + parseFloat(api.column(3, {page: 'current'}).data().sum()));

                    var current_page_remaining_val = (parseFloat(api.column(5, {page: 'current'}).data().sum()) !== NaN)?parseFloat(current_page_expected_val)-parseFloat(api.column(5, {page: 'current'}).data().sum()):current_page_expected_val;

                    var all_page_expected_val=(parseFloat(api.column(4).data().sum()) + parseFloat(api.column(3).data().sum()));

                    var all_page_remaining_val = (parseFloat(api.column(5).data().sum()) !== NaN)?parseFloat(all_page_expected_val)-parseFloat(api.column(5).data().sum()):all_page_expected_val;

                    $(api.column(val).footer()).html(curr_format(round(current_page_remaining_val,0)) +"<br>["+curr_format(round(all_page_remaining_val,0)) +"]");

                  }else{                  
                    var current_page_amount = api.column(val, {page: 'current'}).data().sum();
                    var all_page_amount = api.column(val).data().sum();
                   if(val==3){
                        $(api.column(val).footer()).html(curr_format(round(current_page_amount,0)) +"<br>["+curr_format( round(all_page_amount,0)) +"]");
                    }else{
                        $(api.column(val).footer()).html(curr_format(round(current_page_amount,0)) +"<br>["+curr_format( round(all_page_amount,0)) +"]");
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
            "columns":[

                  {data: 'loan_no', render: function (data, type, full, meta) {
                          if (type === "sort" || type === "filter") {
                              return data;
                          }
                          var link1="<a href='<?php echo site_url('client_loan/view'); ?>/" + full.group_loan_id + "/1' title='View this Loan details'>" + data + "</a>";
                          var link2="<a href='<?php echo site_url('client_loan/view'); ?>/" + full.id + "' title='View this Loan details'>" + data + "</a>";
                          return (full.group_name == null)?link2:link2;
                      }
                  },
                  { data: "member_name",render:function( data, type, full, meta ){
                      return (data&&full.group_name)?full.group_name+' [ '+data+' ]':(!data&&full.group_name)?full.group_name:data;
                    }  },
                  { data: "requested_amount", render:function( data, type, full, meta ){
                  return curr_format(data*1);
                    } },
                  { data: "expected_principal", render:function( data, type, full, meta ){
                  return curr_format(round(data,0));
                    } },
                  { data: "expected_interest" , render:function( data, type, full, meta ){
                  return curr_format(round(data,0));
                    } }, 
                  { data: "paid_amount", render:function( data, type, full, meta ){
                  return curr_format(data*1);
                    } },
                  
                  { data: "days_in_demand", render:function( data, type, full, meta ){
                  return curr_format(data*1);
                    } },
                  { data: "expected_interest" , render:function( data, type, full, meta ){
                  return (full.paid_amount)?curr_format( round(((parseFloat(full.expected_principal)+parseFloat(data))-parseFloat(full.paid_amount)) ,0)):curr_format( round((parseFloat(full.expected_principal)+parseFloat(data)),0));
                    } },
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