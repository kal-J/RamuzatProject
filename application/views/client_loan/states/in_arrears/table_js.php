//In arrears client loan javascript 
         if ($('#tblIn_arrears_loans').length && tabClicked === "tab-in_arrears") {
         //reinitailizing daterange picker
         daterangepicker_initializer();
         if(typeof(dTable['tblIn_arrears_loans'])!=='undefined'){
                    $(".loans").removeClass("active");
                    $("#tab-loans").addClass("active");
                    $("#tab-in_arrears").addClass("active");
                    dTable['tblIn_arrears_loans'].ajax.reload(null,true);
                }else{
         dTable['tblIn_arrears_loans']= $('#tblIn_arrears_loans').DataTable({
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
            "buttons": <?php if(in_array('6', $client_loan_privilege)){ ?> getBtnConfig('<?php echo $title; ?>'), <?php } else { echo "[],"; } ?>
            "ajax":{
             "url": "<?php echo site_url('client_loan/jsonList');?>",
             "dataType": "json",
             "type": "POST",
             "data": function(d){
              <!-- d.date_to = moment(end_date,'X').format('YYYY-MM-DD'); -->
              <!-- d.date_from = moment(start_date,'X').format('YYYY-MM-DD'); -->
              d.state_id = 13;
              <?php if(isset($user['id'])){ ?>
              d.client_id = <?php echo $user['id'] ?>; 
              <?php } ?>
              <?php if(isset($group_id)){ ?>
              d.group_id = <?php echo $group_id ?>; 
              <?php } ?>
              }
              },
            "order": [[ 9, "desc" ]],
            "footerCallback": function (tfoot, data, start, end, display) {
                    var api = this.api();
                $.each([3,4,5,6], function(key,val){
                    if(val==6){

                    var current_page_expected_val=(parseFloat(api.column(4, {page: 'current'}).data().sum()) + parseFloat(api.column(3, {page: 'current'}).data().sum()));

                    var total_page_amount = (parseFloat(api.column(5, {page: 'current'}).data().sum()) !== NaN)?parseFloat(current_page_expected_val)-parseFloat(api.column(5, {page: 'current'}).data().sum()):current_page_expected_val;

                    $(api.column(val).footer()).html(curr_format(round(total_page_amount,2)));


                    
                  }else{                  
                    var total_page_amount = api.column(val, {page: 'current'}).data().sum();
                    $(api.column(val).footer()).html(curr_format(round(total_page_amount,2)));
                  }
                    });
                },
        rowCallback: function (row, data) {
            <!-- defaulting installment -->
            $(row).addClass('text-danger');

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
                        return curr_format(data*1);
                  } },
                  {data: "expected_interest" , render:function( data, type, full, meta ){
                        return curr_format(data*1);
                  } },
                  {data: "paid_amount", render:function( data, type, full, meta ){
                        return curr_format(data*1);
                  } },
                  {data: "expected_interest" , render:function( data, type, full, meta ){
                      return (full.paid_amount)?curr_format( ((parseFloat(full.expected_principal)+parseFloat(data))-parseFloat(full.paid_amount) )*1):curr_format( (parseFloat(full.expected_principal)+parseFloat(data))*1);
                  } },
                  {data: "last_pay_date", render:function( data, type, full, meta ){
                      return (data)?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'None';;
                  }},
                  { data: "days_in_arrears"},
                  {data: "id", render:function ( data, type, full, meta ) {
                    var ret_txt ="<div class='btn-group'>";
                    <?php if(in_array('15', $client_loan_privilege)){ ?>
                    ret_txt += "<a href='#write_off-modal' data-toggle='modal' class='btn btn-sm money_action' data-toggle='tooltip' title='Writte off Loan'><i class='text-danger fa fa-power-off fa-fw fa-sm'></i></a>";
                    <?php }  ?>
                    ret_txt +="</div>";
                    return ret_txt;
                  }}
                ]     

                });
                }
                }

