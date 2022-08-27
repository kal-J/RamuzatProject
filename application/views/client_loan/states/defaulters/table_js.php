//Defaulting client loan javascript 
         if ($('#tblDefaulter_loans').length && tabClicked === "tab-defaulters") {
         //reinitailizing daterange picker
         daterangepicker_initializer();
                if(typeof(dTable['tblDefaulter_loans'])!=='undefined'){
                    $(".loans").removeClass("active");
                    $("#tab-loans").addClass("active");
                    $("#tab-defaulters").addClass("active");
                    dTable['tblDefaulter_loans'].ajax.reload(null,true);
                }else{
         dTable['tblDefaulter_loans']= $('#tblDefaulter_loans').DataTable({
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
            "dom": '<"html5buttons"B>lTfgirtp',"buttons": <?php if(in_array('6', $client_loan_privilege)){ ?> getBtnConfig('<?php echo $title; ?>'), <?php } else { echo "[],"; } ?>
            "ajax":{
             "url": "<?php echo site_url('client_loan/jsonList');?>",
             "dataType": "json",
             "type": "POST",
             "data": function(d){
              d.date_to = moment(end_date,'X').format('YYYY-MM-DD');
              d.date_from = moment(start_date,'X').format('YYYY-MM-DD');
              d.state_id = 7;
              <?php if(isset($user['id'])){ ?>
              d.client_id = <?php echo $user['id'] ?>; 
              <?php } ?>
              <?php if(isset($group_id)){ ?>
              d.group_id = <?php echo $group_id ?>; 
              <?php } ?>
              }
              },
            "order": [[ 0, "desc" ]],
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
                    } },
                  { data: "product_name" },
                  { data: "amount_approved", render:function( data, type, full, meta ){
                      return curr_format(data*1);
                    } },
                  { data: "paid_principal", render:function( data, type, full, meta ){
                        return curr_format(data*1);
                    } },
                  { data: "paid_interest", render:function( data, type, full, meta ){
                        return curr_format(data*1);
                    } },
                    { data: "total_payment", render:function( data, type, full, meta ){
                        return curr_format(data*1);
                    }  }
                ]     

                });
                }
                }

