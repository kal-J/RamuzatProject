//Disbursed client loan javascript 
         if ($('#tblloan_reports_repayments').length && tabClicked === "tab-loan_reports_repayments") {
         //reinitailizing daterange picker
         daterangepicker_initializer();
                if(typeof(dTable['tblloan_reports_repayments'])!=='undefined'){
                    $(".loans").removeClass("active");
                    $(".tab-pane").removeClass("active");
                    $("#tab-loans").addClass("active");
                    $("#tab-disbursed_loans").addClass("active");
                    dTable['tblloan_reports_repayments'].ajax.reload(null,true);
                }else{
         dTable['tblloan_reports_repayments']= $('#tblloan_reports_repayments').DataTable({
            "pageLength": 10,
            "processing": true,
            "lengthMenu": [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
            "language": {
              processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
            },
            //"serverSide": true,
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

              let date_to = $("#end_date_filter").val();
              let date_from = $("#start_date_filter").val();

              if(date_to) {
                d.date_to_filter = moment(date_to,'DD-MM-YYYY').format('YYYY-MM-DD');
              }
              if(date_from) {
                d.date_from_filter = moment(date_from,'DD-MM-YYYY').format('YYYY-MM-DD');
              }
              d.state_ids = [7,11,12,13,14];
              d.credit_officer_id = $("#selected_credit_officer").val();
              <?php if(isset($user['id'])){ ?>
              d.client_id = <?php echo $user['id'] ?>; 
              <?php } ?>
              <?php if(isset($group_id)){ ?>
              d.group_id = <?php echo $group_id ?>; 
              <?php } ?>
              }
              },
              "footerCallback": function (tfoot, data, start, end, display) {
                    var api = this.api();
                $.each([4,5,6,7,8,9,10], function(key,val){
                                    
                    var current_page_amount = api.column(val, {page: 'current'}).data().sum();
                    var all_page_amount = api.column(val).data().sum();
                  
                    if(val==9) {
                      var current_page_amount = parseFloat(api.column(4, {page: 'current'}).data().sum()) - parseFloat(api.column(7, {page: 'current'}).data().sum());
                      var all_page_amount = parseFloat(api.column(4).data().sum()) - parseFloat(api.column(7).data().sum());
                    }
                    if(val==10) {
                      var current_page_amount = parseFloat(api.column(5, {page: 'current'}).data().sum()) - parseFloat(api.column(8, {page: 'current'}).data().sum());
                      var all_page_amount = parseFloat(api.column(5).data().sum()) - parseFloat(api.column(8).data().sum());
                    }
                    
                   if(val==4){
                        $(api.column(val).footer()).html(curr_format(round(current_page_amount,0)) +"<br>["+curr_format( round(all_page_amount,0)) +"]");
                    }else{
                        $(api.column(val).footer()).html(curr_format(round(current_page_amount,2)) +"<br>["+curr_format( round(all_page_amount,2)) +"]");
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
                          return (full.group_name == null)?link2:link1;
                      }
                  },
                  { data: "credit_officer_name",render:function( data, type, full, meta ){
                      return data;
                    }  },
                  { data: "member_name",render:function( data, type, full, meta ){
                      return (data&&full.group_name)?full.group_name+' [ '+data+' ]':(!data&&full.group_name)?full.group_name:data;
                    }  },
                  { data: "product_name", render:function( data, type, full, meta ){
                  return data;
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
                    { data: "paid_principal", render:function( data, type, full, meta ){
                  return curr_format(data*1);
                    } },
                    { data: "paid_interest", render:function( data, type, full, meta ){
                  return curr_format(data*1);
                    } },
                    { data: "paid_principal", render:function( data, type, full, meta ){
                  return curr_format(full.expected_principal - data);
                    } },
                    { data: "paid_interest", render:function( data, type, full, meta ){
                  return curr_format(full.expected_interest - data);
                    } },
                  
                  
                  
                  
                  
                ]     

                });
                }
                }