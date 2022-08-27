//Rejected client loan javascript 
         if ($('#tblRejected_loan').length && tabClicked === "tab-rejected") {
         //reinitailizing daterange picker
         daterangepicker_initializer();
                if(typeof(dTable['tblRejected_loan'])!=='undefined'){
                    $(".loans").removeClass("active");
                    $("#tab-loans").addClass("active");
                    $("#tab-rejected").addClass("active");
                    dTable['tblRejected_loan'].ajax.reload(null,true);
                }else{
         dTable['tblRejected_loan']= $('#tblRejected_loan').DataTable({
            "pageLength": 10,
            "processing": true,
            "serverSide": true,
            "language": {
              processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
            },
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
              //d.date_to = moment(end_date,'X').format('YYYY-MM-DD');
              //d.date_from = moment(start_date,'X').format('YYYY-MM-DD');
              d.state_id = 2;
              <?php if(isset($user['id'])){ ?>
              d.client_id = <?php echo $user['id'] ?>; 
              <?php } ?>
              <?php if(isset($group_id)){ ?>
              d.group_id = <?php echo $group_id ?>; 
              <?php } ?>
              }
              },
            "order": [[ 7, "desc" ]],
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
                    } },
                  { data: "product_name" },
                  { data: "requested_amount", render:function( data, type, full, meta ){
                  return curr_format(data*1);
                    } },
                  { data: "application_date", render:function( data, type, full, meta ){
                  return (data)?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'None';;
                    }  },
                    { data: "action_date", render:function( data, type, full, meta ){
                  return (data)?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'None';;
                    }  },
                    { data: "comment" },
                  { data: "id", render:function ( data, type, full, meta ) {
                    var ret_txt ="<div class='btn-group'>";
                    <?php if(in_array('11', $client_loan_privilege)){ ?>
                    ret_txt += "<a href='#cancle-modal' data-toggle='modal' class='btn btn-sm action_on_loan' data-toggle='tooltip' title='Cancle Loan Application'><i class='text-danger fa fa-times'></i></a>";
                    <?php } if(in_array('21', $client_loan_privilege)){ ?>
                    ret_txt += "<a href='#reverse_action-modal' data-toggle='modal' class='btn btn-sm action_on_loan' data-toggle='tooltip' title='Reverse Reject'><i class='text-danger fa fa-undo'></i></a>";
                    <?php } ?>
                     ret_txt +="</div>";
                    return ret_txt;
                  }}
                ]     

                });
                }
                }

