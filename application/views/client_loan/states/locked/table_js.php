//Locked client loan javascript 
         if ($('#tblLocked_loans').length && tabClicked === "tab-locked") {
         //reinitailizing daterange picker
         daterangepicker_initializer();

                if(typeof(dTable['tblLocked_loans'])!=='undefined'){
                    $(".loans").removeClass("active");
                    $("#tab-loans").addClass("active");
                    $("#tab-locked").addClass("active");
                    dTable['tblLocked_loans'].ajax.reload(null,true);
                }else{
         dTable['tblLocked_loans']= $('#tblLocked_loans').DataTable({
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
              //d.date_to = moment(end_date,'X').format('YYYY-MM-DD');
              //d.date_from = moment(start_date,'X').format('YYYY-MM-DD');
              d.state_id = 12;
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
                $.each([3,4], function(key,val){
                      var total_page_amount = api.column(val, {page: 'current'}).data().sum();
                      $(api.column(val).footer()).html(curr_format(round(total_page_amount,2)));
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
                  { data: "paid_amount", render:function( data, type, full, meta ){
                  return curr_format(data*1);
                    } },
                    { data: "amount_in_demand", render:function( data, type, full, meta ){
                    if(parseFloat(full.paid_amount) > parseFloat(0)) {
                      return round(((data)-parseFloat(full.paid_amount)) ,2) > 0 ? curr_format(round(((data)-parseFloat(full.paid_amount)) ,2)) : 0;
                    }
                    return curr_format( round(data,2));
                    } },
                    { data: "expected_interest" , render:function( data, type, full, meta ){
                  return (full.paid_amount)?curr_format( round(((parseFloat(full.expected_principal)+parseFloat(data))-parseFloat(full.paid_amount)) ,2)):curr_format( round((parseFloat(full.expected_principal)+parseFloat(data)),2));
                    } },
                    { data: "action_date", render:function( data, type, full, meta ){
                  return (data)?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'None';;
                    }  },
                    { data: "comment" },
                  { data: "id", render:function ( data, type, full, meta ) {
                    var ret_txt = "";
                    <?php if(in_array('21', $client_loan_privilege)){ ?>
                     ret_txt += "<a href='#reverse_action-modal' data-toggle='modal' class='btn btn-sm action_on_loan' data-toggle='tooltip' title='Unlock Loan A/C'><i class='text-primary fa fa-undo'></i></a>";
                  <?php } ?>

                    return ret_txt;
                  }}
                ]     

                });
                }
                }

