//Active client loan javascript 
       if ($('#tblActive_client_loan').length) {
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
          "paging": false,
          "responsive": true,
          "dom": '<"html5buttons"B>lTfgitp',
          "buttons": <?php if(in_array('6', $client_loan_privilege)){ ?> getBtnConfig('<?php echo $title; ?>'), <?php } else { echo "[],"; } ?>
          "ajax":{
           "url": "<?php echo site_url('client_loan/jsonList');?>",
           "dataType": "json",
           "type": "POST",
           "data": function(d){
            d.state_id = 7;
            <?php if(isset($user['id'])){ ?>
            d.client_id = <?php echo $user['id'] ?>; 
            <?php } ?>
            <?php if(isset($group_id)){ ?>
            d.group_id = <?php echo $group_id ?>; 
            <?php } ?>
            }
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
                    return (data)?data:full.group_name;
                  }  },
                { data: "paid_amount" , render:function( data, type, full, meta ){
                return "<span class='text-success' style='font-weight: bold;'>"+ curr_format(data*1) +"</span>";
                  } }, 
                { data: "expected_interest", render:function( data, type, full, meta ){
                var rem_bal = (full.paid_amount)?curr_format( ((parseFloat(full.expected_principal)+parseFloat(data))-parseFloat(full.paid_amount) )*1):curr_format( (parseFloat(full.expected_principal)+parseFloat(data))*1);
                return "<span class='text-danger' style='font-weight: bold;'>" + rem_bal +"</span>";
                  } },
                { data: "next_pay_date", render:function( data, type, full, meta ){
                return (data)?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'None';;
                  }  },
                { data: "last_pay_date", render:function( data, type, full, meta ){
                return (data)?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'None';;
                  }  }
              ]     

              });
              }
              }

