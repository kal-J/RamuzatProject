//Disbursed client loan javascript 
         if ($('#tblDisbursed_client_loan').length && tabClicked === "tab-disbursed_loans") {
       
                if(typeof(dTable['tblDisbursed_client_loan'])!=='undefined'){
                    $(".disbursed_loans").removeClass("active");
                    $("#tab-disbursed_loans").addClass("active");
                    dTable['tblDisbursed_client_loan'].ajax.reload(null,true);
                }else{
         dTable['tblDisbursed_client_loan']= $('#tblDisbursed_client_loan').DataTable({
            "pageLength": 10,
            "processing": true,
            "serverSide": true,
            "deferRender": true,
            "language": {
              processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
            },
            "searching": true,
            "paging": true,
            "responsive": true,
            "dom": '<"html5buttons"B>lTfgirtp',
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "buttons": <?php if(in_array('6', $report_privilege)){ ?> getBtnConfig('<?php echo $title; ?>'), <?php } else { echo "[],"; } ?>
            "ajax":{
             "url": "<?php echo site_url('client_loan/jsonList');?>",
             "dataType": "json",
             "type": "POST",
             "data": function(d){
              d.start_date_at       =   moment(start_date_at,'X').format('YYYY-MM-DD');
              d.end_date_at = moment(end_date_at,'X').format('YYYY-MM-DD');
                d.state_ids = [7,8,9,10,11,12,13,14,15];
               /* d.min_amount = active_min_amount;
                d.max_amount = active_max_amount;
                d.product_id = active_product_id;
                d.loan_type = active_loan_type;
                d.condition = active_condition;
                d.due_days = active_due_days;
                d.credit_officer_id = credit_officer_id;
                d.next_due_month = next_due_month;
                d.next_due_year = next_due_year;
                d.report =true;*/
              <?php if(isset($user['id'])){ ?>
              d.client_id = <?php echo $user['id'] ?>; 
              <?php } ?>
              <?php if(isset($group_id)){ ?>
              d.group_id = <?php echo $group_id ?>; 
              <?php } ?>
              }
              },
              "order": [[ 0, "desc" ]],

              "footerCallback": function (tfoot, data, start, end, display) {
                    var api = this.api();
                $.each([2,3,4,5,6], function(key,val){
                    if(val==9){

                    var current_page_expected_val=(parseFloat(api.column(7, {page: 'current'}).data().sum()) + parseFloat(api.column(9, {page: 'current'}).data().sum()));

                    var total_page_amount = (parseFloat(api.column(8, {page: 'current'}).data().sum()) !== NaN)?parseFloat(current_page_expected_val)-parseFloat(api.column(8, {page: 'current'}).data().sum()):current_page_expected_val;

                    $(api.column(val).footer()).html(curr_format(round(total_page_amount,2)));


                    
                  }else{                  
                    var total_page_amount = api.column(val, {page: 'current'}).data().sum();
                    $(api.column(val).footer()).html(curr_format(round(total_page_amount,2)));
                  }
                    });
                },
             
            "columns":[

                  {data: 'loan_no', render: function (data, type, full, meta) {
                          if (type === "sort" || type === "filter") {
                              return data;
                          }
                          var link1="<a href='<?php echo site_url('client_loan/view'); ?>/" + full.group_loan_id + "/1' title='View this Loan details'>" + data + "</a>";
                          var link2="<a href='<?php echo site_url('client_loan/view'); ?>/" + full.id + "' title='View this Loan details'>" + data + "</a>";
                          return (full.member_name == null)?link1:link2;
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
                  return curr_format(round(data,2));
                    } }, 
                  
                  { data: "paid_amount", render:function( data, type, full, meta ){
                  return curr_format(data*1);
                    } },
                  
                  { data: "expected_interest" , render:function( data, type, full, meta ){
                    if( !Number.isNaN(parseFloat(full.expected_principal)) && !Number.isNaN(parseFloat(data))){
                      return (full.paid_amount)?curr_format( round(((parseFloat(full.expected_principal)+parseFloat(data))-parseFloat(full.paid_amount)) ,2)):curr_format( round((parseFloat(full.expected_principal)+parseFloat(data)),2));
                    }else{
                      return '';
                    }
                    } },
                  {
                    data: 'state_name',
                  },
                
                ]     

                });
                }
                }

