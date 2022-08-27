//Risking client loan javascript 
         if ($('#tblRisky_loans').length && tabClicked === "tab-risky_loans") {

         //options for daterangepicker
             var in_future_ranges={
                    'Today': [moment(), moment()],
                    'Next 7 Days': [moment().add(6, 'days'), moment()],
                    'Next 30 Days': [moment().add(30, 'days'), moment()],
                    'Next 60 Days': [moment().add(60, 'days'), moment()],
                    'Next 90 Days': [moment().add(90, 'days'), moment()]
                    }
                daterangepicker_initializer(in_future_ranges);
          //end of functionality call
                if(typeof(dTable['tblRisky_loans'])!=='undefined'){
                    $(".loans").removeClass("active");
                    $("#tab-loans").addClass("active");
                    $("#tab-risky_loans").addClass("active");
                    dTable['tblRisky_loans'].ajax.reload(null,true);
                }else{
         dTable['tblRisky_loans']= $('#tblRisky_loans').DataTable({
            "pageLength": 10,
            "processing": true,
            "serverSide": true,
            "deferRender": true,
            "searching": true,
            "paging": true,
            "responsive": true,
            "dom": '<"html5buttons"B>lTfgitp',"buttons": <?php if(in_array('6', $client_loan_privilege)){ ?> getBtnConfig('<?php echo $title; ?>'), <?php } else { echo "[],"; } ?>
            "ajax":{
             "url": "<?php echo site_url('client_loan/jsonList');?>",
             "dataType": "json",
             "type": "POST",
             "data": function(d){
              d.date_to = moment(start_date,'X').format('YYYY-MM-DD');
              d.date_from =moment(end_date,'X').format('YYYY-MM-DD') ;
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
                    }  },
                  
                ]     

                });
                }
                }

