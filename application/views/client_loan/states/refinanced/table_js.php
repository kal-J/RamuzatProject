//Refinanced client loan javascript 
         if ($('#tblRefinanced_loans').length && tabClicked === "tab-refinanced") {
         //reinitailizing daterange picker
         daterangepicker_initializer();
         if(typeof(dTable['tblRefinanced_loans'])!=='undefined'){
                    $(".loans").removeClass("active");
                    $("#tab-loans").addClass("active");
                    $("#tab-refinanced").addClass("active");
                    dTable['tblRefinanced_loans'].ajax.reload(null,true);
                }else{
         dTable['tblRefinanced_loans']= $('#tblRefinanced_loans').DataTable({
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
              <!-- d.date_to = moment(end_date,'X').format('YYYY-MM-DD'); -->
              <!-- d.date_from = moment(start_date,'X').format('YYYY-MM-DD'); -->
              d.state_id = 14;
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
                $.each([3,4,5,6], function(key,val){
                    if(val==6){

                    var current_page_expected_val=(parseFloat(api.column(4, {page: 'current'}).data().sum()) + parseFloat(api.column(3, {page: 'current'}).data().sum()));

                    var total_page_amount = (parseFloat(api.column(5, {page: 'current'}).data().sum()) !== NaN)?parseFloat(current_page_expected_val)+parseFloat(api.column(5, {page: 'current'}).data().sum()):current_page_expected_val;

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
                  {data: "member_name" ,render:function( data, type, full, meta ){
                      return (data)?data:full.group_name;
                  } },
                  {data: "product_name" },
                  {data: "expected_principal", render:function( data, type, full, meta ){
                        return curr_format(round(data*1,2));
                  } },
                  {data: "expected_interest" , render:function( data, type, full, meta ){
                        return curr_format(round(data*1,2));
                  } },
                  {data: "paid_amount", render:function( data, type, full, meta ){
                        return curr_format(round(data*1,2));
                  } },
                  {data: "expected_interest" , render:function( data, type, full, meta ){
                      return (full.paid_amount)?curr_format( round(((parseFloat(full.expected_principal)+parseFloat(data))-parseFloat(full.paid_amount) )*1,2) ):curr_format( round((parseFloat(full.expected_principal)+parseFloat(data))*1,2) );
                  } },
                  {data: "action_date", render:function( data, type, full, meta ){
                      return (data)?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'None';;
                  }},
                  {data: "last_pay_date", render:function( data, type, full, meta ){
                      return (data)?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'None';;
                  }}
                ]     

                });
                }
                }

