//approved client loan javascript 
         if ($('#tblApproved_client_loan').length && tabClicked === "tab-approved") {
                    $(".loans").removeClass("active");
                    $("#tab-loans").addClass("active");
                    $("#tab-approved").addClass("active");
                    
                    //reinitailizing daterange picker
                    daterangepicker_initializer();

                if(typeof(dTable['tblApproved_client_loan'])!=='undefined'){
                    dTable['tblApproved_client_loan'].ajax.reload(null,true);
                }else{
         dTable['tblApproved_client_loan']= $('#tblApproved_client_loan').DataTable({
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
              //d.date_to = moment(end_date,'X').format('YYYY-MM-DD');
              //d.date_from = moment(start_date,'X').format('YYYY-MM-DD');
              d.state_id = 6;
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
                $.each([3,4], function(key,val){
                      var total_page_amount = api.column(val, {page: 'current'}).data().sum();
                      $(api.column(val).footer()).html(curr_format(round(total_page_amount,2)));
                    });
                },
             "columnDefs": [{
                              "targets": [9],
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
                          return (full.group_name == null)?link2:link1;
                      }
                  },
                  { data: "member_name",render:function( data, type, full, meta ){
                     return (data&&full.group_name)?full.group_name+' [ '+data+' ]':(!data&&full.group_name)?full.group_name:data;
                    } },
                  { data: "product_name" },
                  { data: "requested_amount", render:function( data, type, full, meta ){
                  return curr_format(data*1);
                    } },
                  { data: "amount_approved", render:function( data, type, full, meta ){
                  return curr_format(data*1);
                    } },
                  { data: "application_date", render:function( data, type, full, meta ){
                  return (data)?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'None';;
                    }  },
                    { data: "action_date", render:function( data, type, full, meta ){
                  return (data)?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'None';;
                    }  },
                    { data: "suggested_disbursement_date", render:function( data, type, full, meta ){
                  return (data)?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'None';;
                    }  },
                    { data: "approval_note" },
                  { data: "id", render:function ( data, type, full, meta ) {
                    var ret_txt ="<div class='btn-group'>";
                    <?php if(in_array('22', $client_loan_privilege)){ ?>
                    ret_txt += "<a href='#disburse-modal' data-toggle='modal' class='btn btn-sm disburse' data-toggle='tooltip' title='Disburse Loan'><i class='text-success fa fa-check-square-o fa-fw fa-sm'></i></a>";
                    <?php } if(in_array('21', $client_loan_privilege)){ ?>
                    ret_txt +='<a  data-toggle="modal" data-bind="click: action_on_loan" data-target="#reverse_approval-modal" data-toggle="tooltip" title="Reverse Approval" class="btn btn-sm"><i class="text-danger fa fa-undo fa-fw"></i> </a>';
                    <?php } ?>
                    ret_txt +="</div>";
                    return ret_txt;
                  }}
                ]     

                });
                }
                }

