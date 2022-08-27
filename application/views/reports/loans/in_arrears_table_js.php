//Active client loan javascript 
         if ($('#tblInarrears_client_loan').length && tabClicked === "tab-inarrears_loans") {
         //reinitailizing daterange picker
         //daterangepicker_initializer();
                if(typeof(dTable['tblInarrears_client_loan'])!=='undefined'){
                    $(".loans").removeClass("active");
                    $("#tab-inarrears_loans").addClass("active");
                    dTable['tblInarrears_client_loan'].ajax.reload(null,true);
                }else{
         dTable['tblInarrears_client_loan']= $('#tblInarrears_client_loan').DataTable({
            "pageLength": 10,
            "processing": true,
            "serverSide": true,
            "deferRender": true,
            "searching": true,
            "paging": true,
            "responsive": true,
            "dom": '<"html5buttons"B>lTfgitp',
            "buttons": <?php if(in_array('6', $report_privilege)){ ?> getBtnConfig('<?php echo $title; ?>'), <?php } else { echo "[],"; } ?>
            "ajax":{
             "url": "<?php echo site_url('client_loan/jsonList');?>",
             "dataType": "json",
             "type": "POST",
             "data": function(d){
                //d.date_to = moment(end_date,'X').format('YYYY-MM-DD');
                //d.date_from = moment(start_date,'X').format('YYYY-MM-DD');
                d.state_id = 13;
                d.min_amount =inarrear_min_amount;
                d.max_amount =inarrear_max_amount;
                d.min_days_in_arrears =inarrear_min_days;
                d.max_days_in_arrears =inarrear_max_days;
                d.product_id =inarrear_product_id;
                d.loan_type =inarrear_loan_type;
                d.report =true;
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
                $.each([5,6,7,8,9], function(key,val){
                    if(val==9){

                    var current_page_expected_val=(parseFloat(api.column(7, {page: 'current'}).data().sum()) + parseFloat(api.column(6, {page: 'current'}).data().sum()));

                    var total_page_amount = (parseFloat(api.column(8, {page: 'current'}).data().sum()) !== NaN)?parseFloat(current_page_expected_val)-parseFloat(api.column(8, {page: 'current'}).data().sum()):current_page_expected_val;

                    $(api.column(val).footer()).html(curr_format(round(total_page_amount,2)));
                    
                  }else{                  
                    var total_page_amount = api.column(val, {page: 'current'}).data().sum();
                    $(api.column(val).footer()).html(curr_format(round(total_page_amount,2)));
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
                          return (full.member_name == null)?link1:link2;
                      }
                  },
                  { data: "member_name",render:function( data, type, full, meta ){
                    return (data&&full.group_name)?full.group_name+' [ '+data+' ]':(!data&&full.group_name)?full.group_name:data;
                    }  },
                    { data: "installments_no", render:function( data, type, full, meta ){
                  return (data);
                    } },
                    { data: "paid_installments_no", render:function( data, type, full, meta ){
                  return (data ? data : 0);
                    } },
                    { data: "installments_no", render:function( data, type, full, meta ){
                  return full.paid_installments_no ? (parseInt(data) - parseInt(full.paid_installments_no)) : data;
                    } },
                  { data: "requested_amount", render:function( data, type, full, meta ){
                  return curr_format(data*1);
                    } },
                  { data: "expected_principal", render:function( data, type, full, meta ){
                  return curr_format(data*1);
                    } },
                  { data: "expected_interest" , render:function( data, type, full, meta ){
                  return curr_format(data*1);
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
                  { data: "last_pay_date", render:function( data, type, full, meta ){
                  return (data)?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'None';;
                    }  },
                  { data: "days_in_arrears", render:function( data, type, full, meta ){
                  return (data >0)?data:'';
                    }  },
                  {
                    data: 'paid_amount',
                    render: function(data, type, full, meta) {
                      return `
                            <i onclick="handlePrint_loan_installment_payments(${full.id}, ${full.status_id})"  title='Print Payments' class="btn btn-xs btn-secondary mx-1 fa fa-print">
                      `;
                    }
                  }
                ]     

                });
                }
                }

