//Active client loan javascript 
         if ($('#tblActive_client_loan').length ) {
         //reinitailizing daterange picker
         daterangepicker_initializer();
                if(typeof(dTable['tblActive_client_loan'])!=='undefined'){
                   
                    dTable['tblActive_client_loan'].ajax.reload(null,true);
                }else{
         dTable['tblActive_client_loan']= $('#tblActive_client_loan').DataTable({
            "pageLength": 10,
            "processing": true,
            "serverSide": true,
            "deferRender": true,
            "searching": true,
            "paging": true,
            "responsive": true,
            "dom": '<"html5buttons"B>lTfgitp',
            "buttons": getBtnConfig('<?php echo $title; ?>'),
            "ajax":{
             "url": "<?php echo site_url('u/loans/jsonList');?>",
             "dataType": "json",
             "type": "POST",
             "data": function(d){
              d.date_to = moment(end_date,'X').format('YYYY-MM-DD');
              d.date_from = moment(start_date,'X').format('YYYY-MM-DD');
              <?php if(isset($_SESSION['member_id'])){ ?>
              d.client_id = <?php echo $_SESSION['member_id'] ?>; 
              <?php } ?>
              <?php if(isset($group_id)){ ?>
              d.group_id = <?php echo $group_id ?>; 
              <?php } ?>
              }
              },

              "footerCallback": function (tfoot, data, start, end, display) {
                    var api = this.api();
                $.each([1,2,3,4], function(key,val){
                    if(val==5){

                    var current_page_expected_val=(parseFloat(api.column(3, {page: 'current'}).data().sum()) + parseFloat(api.column(2, {page: 'current'}).data().sum()));

                    var total_page_amount = (parseFloat(api.column(4, {page: 'current'}).data().sum()) !== NaN)?parseFloat(current_page_expected_val)+parseFloat(api.column(4, {page: 'current'}).data().sum()):current_page_expected_val;

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
                          var link1="<a href='<?php echo site_url('u/loans/view'); ?>/" + full.group_loan_id + "/1' title='View this Loan details'>" + data + "</a>";
                          var link2="<a href='<?php echo site_url('u/loans/view'); ?>/" + full.id + "' title='View this Loan details'>" + data + "</a>";
                          return (full.member_name == null)?link1:link2;
                      }
                  },
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
                  { data: "action_date", render:function( data, type, full, meta ){
                  return (data)?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'None';;
                    }  },
                  { data: "next_pay_date", render:function( data, type, full, meta ){
                  return (data)?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'None';;
                    }  },
                  { data: "last_pay_date", render:function( data, type, full, meta ){
                  return (data)?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'None';;
                    }  },
                  {data: 'state_id', render: function (data, type, full, meta) {
                      var state;
                        if (parseInt(data)===1){
                          state="<span class='badge'>"+full.state_name+"</span>";
                        }else if (parseInt(data)===2 || parseInt(data)===3 || parseInt(data)===4){
                          state="<span class='badge badge-warning'>"+full.state_name+"</span>";
                        } else if (parseInt(data)===5){
                          state="<span class='badge badge-information'>"+full.state_name+"</span>";
                        } else if (parseInt(data)===6){
                          state="<span class='badge badge-primary'>"+full.state_name+"</span>";
                        }else if(parseInt(data)===7 || parseInt(data)===9 || parseInt(data)===10) {
                          state="<span class='badge badge-success'>"+full.state_name+"</span>";
                        }else if (parseInt(data)===8 || parseInt(data)===13){
                          state="<span class='badge badge-danger'>"+full.state_name+"</span>";
                        }else if (parseInt(data)===11 || parseInt(data)===12 || parseInt(data)===14){
                          state="<span class='badge badge-warning'>"+full.state_name+"</span>";
                        }else{
                          state ="<span class='badge badge-warning'>"+data+"</span>";
                        }
                          
                          return state;
                      }
                  },
                ]     

                });
                }
                }

