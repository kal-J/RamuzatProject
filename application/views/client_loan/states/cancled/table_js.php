//cancled client loan javascript 
         if ($('#tblCancled_loan').length && tabClicked === "tab-cancled") {
         //reinitailizing daterange picker
         daterangepicker_initializer();
         
                if(typeof(dTable['tblCancled_loan'])!=='undefined'){
                    $(".loans").removeClass("active");
                    $("#tab-loans").addClass("active");
                    $("#tab-cancled").addClass("active");
                    dTable['tblCancled_loan'].ajax.reload(null,true);
                }else{
         dTable['tblCancled_loan']= $('#tblCancled_loan').DataTable({
            "pageLength": 10,
            "processing": true,
            "serverSide": true,
            "deferRender": true,
            "searching": true,
            "paging": true,
            "responsive": true,
            "dom": '<"html5buttons"B>lTfgitp',
            "buttons": <?php if(in_array('6', $client_loan_privilege)){ ?> getBtnConfig('<?php echo $title; ?>'), <?php } else { echo "[],"; } ?>
            "ajax":{
             "url": "<?php echo site_url('client_loan/jsonList');?>",
             "dataType": "json",
             "type": "POST",
             "data": function(d){
              //d.date_to = moment(end_date,'X').format('YYYY-MM-DD');
              //d.date_from = moment(start_date,'X').format('YYYY-MM-DD');
              d.state_id = 3;
              <?php if(isset($user['id'])){ ?>
              d.client_id = <?php echo $user['id'] ?>; 
              <?php } ?>
              <?php if(isset($group_id)){ ?>
              d.group_id = <?php echo $group_id ?>; 
              <?php } ?>
              }
              },
            "order": [[ 5, "desc" ]],
            "columnDefs": [{
                              "targets": [6],
                              "orderable": false,
                              "searchable": false
                          }],
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
                  { data: "product_name" },
                  { data: "requested_amount", render:function( data, type, full, meta ){
                  return curr_format(data*1);
                    } },
                  { data: "application_date", render:function( data, type, full, meta ){
                  return (data)?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'None';
                    }  },
                  { data: "action_date", render:function( data, type, full, meta ){
                  return (data)?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'None';
                    }  },
                  { data: "comment" },
                ]     

                });
                }
                }

