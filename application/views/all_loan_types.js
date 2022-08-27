const loan_states = {active:7, approved:6, cancelled:3, defaulting:7,in_arrears:13, locked:12, obligations_met:10, paid_off:9,partial:1, pending:5, refinanced:14, rejected:2, risky:7, withdrawn:4, written_off:8};
const loan_types = {
    "active":[
        { targets: ["_all"], visible: false},
        { targets: [0,1,3,4,5,6,11,12,13,14,15,16,23], visible: true},
        //{className: "text-danger", "targets": ["unpaid_installs"]}
    ],
    "locked":[
        { targets: [3,4,5], visible: false},
        {
            "targets": [7],
            "orderable": false,
            "searchable": false
        }
    ],
    "in_arrears":[
        { targets: [3,4,5], visible: false},
        {
            "targets": [9],
            "orderable": false,
            "searchable": false
        },
        {className: "text-danger", "targets": ["_all"]}
    ],
    "defaulting":[
        { targets: [3,4,5], visible: false},
    ],
    "risky":[
        { targets: [3,4,5], visible: false},
    ],
    "partial":[
        { targets: [3,4,5], visible: false},
        {
            "targets": [7],
            "orderable": false,
            "searchable": false
        }
    ],
    "pending":[
        { targets: [3,4,5], visible: false},
        {
            "targets": [7],
            "orderable": false,
            "searchable": false
        }
    ],
    "approved":[
        { targets: ["_all"], visible: false},
        { targets: [0,1,24,3,17,18,6,11,12,13,14,15,16,23], visible: true},
        {
            "targets": [24],
            "orderable": false,
            "searchable": false
        }
    ],
    "obligation_met":[
        { targets: [3,4,5], visible: false},
    ],
    "paid_off":[
        { targets: [3,4,5], visible: false},
        {
            "targets": [8],
            "orderable": false,
            "searchable": false
        }
    ],
    "refinanced":[
        { targets: [3,4,5], visible: false},
        {
            "targets": [7],
            "orderable": false,
            "searchable": false
        }
    ],
    "written_off":[
        { targets: [3,4,5], visible: false},
        {
            "targets": [8],
            "orderable": false,
            "searchable": false
        }
    ],
    "withdrawn":[
        { targets: [3,4,5], visible: false},
        {
            "targets": [7],
            "orderable": false,
            "searchable": false
        }
    ],
    "cancelled":[
        { targets: [3,4,5], visible: false},
        {
            "targets": [6],
            "orderable": false,
            "searchable": false
        }
    ],
    "rejected":[
        { targets: [3,4,5], visible: false},
        {
            "targets": [7],
            "orderable": false,
            "searchable": false
        }
    ]
}



const footer_callbacks = {
    "active":function (tfoot, data, start, end, display) {
                    var api = this.api();
                $.each([2,3,4,5,6], function(key,val){
                    if(val==6){

                    var current_page_expected_val=(parseFloat(api.column(4, {page: 'current'}).data().sum()) + parseFloat(api.column(3, {page: 'current'}).data().sum()));

                    var total_page_amount = (parseFloat(api.column(5, {page: 'current'}).data().sum()) !== NaN)?parseFloat(current_page_expected_val)-parseFloat(api.column(5, {page: 'current'}).data().sum()):current_page_expected_val;

                    $(api.column(val).footer()).html(curr_format(round(total_page_amount,2)));


                    
                  }else{                  
                    var total_page_amount = api.column(val, {page: 'current'}).data().sum();
                   if(val==3){
                        $(api.column(val).footer()).html(curr_format(round(total_page_amount,0)));
                    }else{
                        $(api.column(val).footer()).html(curr_format(round(total_page_amount,2)));
                    }
                  }
                    });
                },
    "in_arrears":function (tfoot, data, start, end, display) {
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
    "refinanced":function (tfoot, data, start, end, display) {
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
    "locked":null,
    "approved":null,
    "cancelled":null,
    "defaulting":null,
    "locked":null,
    "obligations_met":null,
    "paid_off":null,
    "partial":null,
    "pending":null,
    "rejected":null,
    "risky":null,
    "withdrawn":null,
    "written_off":null,
    
}

const action_col_renders = {
    "active":function ( data, type, full, meta ) {
                  var ret_txt ="<div class='btn-group'>";
                  <?php if(in_array('18', $client_loan_privilege)){ ?>
                    ret_txt = "<a href='#lock-modal' data-toggle='modal' class='btn btn-sm action_on_loan' data-toggle='tooltip' title='Lock Loan'><i class='text-danger fa fa-lock fa-fw'></i></a>"; <!-- -->
                    <?php } if(in_array('15', $client_loan_privilege)){ ?>
                    <!-- ret_txt += "<a href='#write_off-modal' data-toggle='modal' class='btn btn-sm money_action' data-toggle='tooltip' title='Writte off Loan'><i class='text-danger fa fa-power-off fa-fw fa-sm'></i></a>"; -->
                     <?php } if(in_array('16', $client_loan_privilege)){ ?>
                    ret_txt += "<a href='#pay_off-modal' data-toggle='modal' class='btn btn-sm money_action' data-toggle='tooltip' title='Pay off Loan'><i class='text-danger fa fa-money fa-fw fa-sm'></i></a>";
                    <?php }  ?>
                    ret_txt +="</div>";
                    return ret_txt;
                  },
    "approved":function ( data, type, full, meta ) {
                    var ret_txt ="<div class='btn-group'>";
                    <?php if(in_array('22', $client_loan_privilege)){ ?>
                    ret_txt += "<a href='#disburse-modal' data-toggle='modal' class='btn btn-sm disburse' data-toggle='tooltip' title='Disburse Loan'><i class='text-success fa fa-check-square-o fa-fw fa-sm'></i></a>";
                    <?php } if(in_array('21', $client_loan_privilege)){ ?>
                    ret_txt +='<a  data-toggle="modal" data-bind="click: action_on_loan" data-target="#reverse_approval-modal" data-toggle="tooltip" title="Reverse Approval" class="btn btn-sm"><i class="text-danger fa fa-undo fa-fw"></i> </a>';
                    <?php } ?>
                    ret_txt +="</div>";
                    return ret_txt;
                  },
    "in_arrears":function ( data, type, full, meta ) {
                    var ret_txt ="<div class='btn-group'>";
                    <?php if(in_array('15', $client_loan_privilege)){ ?>
                    ret_txt += "<a href='#write_off-modal' data-toggle='modal' class='btn btn-sm money_action' data-toggle='tooltip' title='Writte off Loan'><i class='text-danger fa fa-power-off fa-fw fa-sm'></i></a>";
                    <?php }  ?>
                    ret_txt +="</div>";
                    return ret_txt;
                  },
    "locked":function ( data, type, full, meta ) {
                    var ret_txt = "";
                    <?php if(in_array('21', $client_loan_privilege)){ ?>
                     ret_txt += "<a href='#reverse_action-modal' data-toggle='modal' class='btn btn-sm action_on_loan' data-toggle='tooltip' title='Unlock Loan A/C'><i class='text-primary fa fa-undo'></i></a>";
                  <?php } ?>

                    return ret_txt;
                  },
    "partial":function ( data, type, full, meta ) {
                    var ret_txt ="<div class='btn-group'>";

                  <?php  if($org['loan_app_stage']==0){
                              if(in_array('20', $client_loan_privilege)){?>
                                  ret_txt +="<a href='#forward_application-modal' data-toggle='modal' title='Forward application' class='btn btn-sm action_on_loan'><i class='fa fa-forward' aria-hidden='true'></i></a>";
                              <?php } 
                          }elseif ($org['loan_app_stage']==1){
                              if(in_array('14', $client_loan_privilege)){ ?>
                                  ret_txt +="<a href='#approve-modal' data-toggle='modal' title='Approve' class='btn btn-sm approve_loan'><i class='fa fa-check-square-o' style='font-size:16px'></i>"+full._approvals+"</a>";
                                  <?php }
                          }elseif ($org['loan_app_stage']==2) {
                              if(in_array('22', $client_loan_privilege)){ ?>
                                  ret_txt += "<a href='#disburse-modal' data-toggle='modal' class='btn btn-sm disburse' data-toggle='tooltip' title='Disburse Loan'><i class='text-success fa fa-check-square-o fa-fw fa-sm'></i></a>";
                              <?php }

                          } if(in_array('10', $client_loan_privilege)){ ?>
                    ret_txt += "<a href='#reject-modal' data-toggle='modal' class='btn btn-sm action_on_loan' data-toggle='tooltip' title='Reject Loan Application'><i class='text-danger fa fa-ban'></i></a>";
                    <?php } if(in_array('12', $client_loan_privilege)){ ?>
                    ret_txt += "<a href='#application_withdraw-modal' data-toggle='modal' class='btn btn-sm action_on_loan' data-toggle='tooltip' title='Withdraw Loan Application'><i class='text-danger fa fa-undo'></i></a>";
                    <?php } ?>
                    ret_txt +="</div>";
                    return ret_txt;
                  },
    "pending":function ( data, type, full, meta ) {
                    var ret_txt ="<div class='btn-group'>",required_approvals="4";
                    <?php if(in_array('14', $client_loan_privilege)){ ?>
                        ret_txt +="<a href='#approve-modal' data-toggle='modal' title='Approve' class='btn btn-sm approve_loan'><i class='fa fa-check-square-o' style='font-size:16px'></i> "+full._approvals+"</a>";
                    <?php } if(in_array('10', $client_loan_privilege)){ ?>
                    ret_txt += "<a href='#reject-modal' data-toggle='modal' class='btn btn-sm action_on_loan' data-toggle='tooltip' title='Reject Loan Application'><i class='text-danger fa fa-ban'></i></a>";
                    <?php } if(in_array('12', $client_loan_privilege)){ ?>
                    ret_txt += "<a href='#application_withdraw-modal' data-toggle='modal' class='btn btn-sm action_on_loan' data-toggle='tooltip' title='Withdraw Loan Application'><i class='text-danger fa fa-times'></i></a>";
                    <?php }  ?>
                     ret_txt +="</div>";
                    return ret_txt;
                  },
    "rejected":function ( data, type, full, meta ) {
                    var ret_txt ="<div class='btn-group'>";
                    <?php if(in_array('11', $client_loan_privilege)){ ?>
                    ret_txt += "<a href='#cancle-modal' data-toggle='modal' class='btn btn-sm action_on_loan' data-toggle='tooltip' title='Cancle Loan Application'><i class='text-danger fa fa-times'></i></a>";
                    <?php } if(in_array('21', $client_loan_privilege)){ ?>
                    ret_txt += "<a href='#reverse_action-modal' data-toggle='modal' class='btn btn-sm action_on_loan' data-toggle='tooltip' title='Reverse Reject'><i class='text-danger fa fa-undo'></i></a>";
                    <?php } ?>
                     ret_txt +="</div>";
                    return ret_txt;
                  },
    "withdrawn":function ( data, type, full, meta ) {
                      var ret_txt ="<div class='btn-group'>";
                    <?php if(in_array('11', $client_loan_privilege)){ ?>
                    ret_txt += "<a href='#cancle-modal' data-toggle='modal' class='btn btn-sm action_on_loan' data-toggle='tooltip' title='Cancle Loan Application'><i class='text-danger fa fa-times'></i></a>";
                    <?php } if(in_array('21', $client_loan_privilege)){ ?>
                    ret_txt += "<a href='#reverse_action-modal' data-toggle='modal' class='btn btn-sm action_on_loan' data-toggle='tooltip' title='Reverse Application Withdraw'><i class='text-danger fa fa-undo'></i></a>";
                    <?php } ?>
                    ret_txt +="</div>";
                    return ret_txt;
                  }
    
}
//Active client loan javascript 
         if ($('#tblActive_client_loan').length && tabClicked === "tab-active") {
         //reinitailizing daterange picker
         daterangepicker_initializer();
                if(typeof(dTable['tblActive_client_loan'])!=='undefined'){
                    $(".loans").removeClass("active");
                    $("#tab-loans").addClass("active");
                    $("#tab-active").addClass("active");
                    dTable['tblActive_client_loan'].ajax.reload(null,true);
                }else{
         dTable['tblActive_client_loan']= $('#tblActive_client_loan').DataTable({
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
            "dom": '<"html5buttons"B>lTfgitp',
            "buttons": <?php if(in_array('6', $client_loan_privilege)){ ?> getBtnConfig('<?php echo $title; ?>'), <?php } else { echo "[],"; } ?>
            columnDefs:[
                { targets: loan_types['active'], visible: false}
            ],
            "ajax":{
             "url": "<?php echo site_url('client_loan/jsonList');?>",
             "dataType": "json",
             "type": "POST",
             "data": function(d){
              //d.date_to = moment(end_date,'X').format('YYYY-MM-DD');
              //d.date_from = moment(start_date,'X').format('YYYY-MM-DD');
              d.state_id = 7;
              <?php if(isset($user['id'])){ ?>
              d.client_id = <?php echo $user['id'] ?>; 
              <?php } ?>
              <?php if(isset($group_id)){ ?>
              d.group_id = <?php echo $group_id ?>; 
              <?php } ?>
              }
              },
              "order": [[ 1, "desc" ]],
              "footerCallback": ,
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
                  { data: "requested_amount", render:function( data, type, full, meta ){
                  return curr_format(data*1);
                    } },
                  { data: "expected_principal", render:function( data, type, full, meta ){
                  return curr_format(round(data,0));
                    } },
                  { data: "expected_interest" , render:function( data, type, full, meta ){
                  return curr_format(round(data,2));
                    } }, 
                  { data: "paid_amount", render:function( data, type, full, meta ){
                  return curr_format(data*1);
                    } },
                  { data: "expected_interest" , render:function( data, type, full, meta ){
                  return (full.paid_amount)?curr_format( round(((parseFloat(full.expected_principal)+parseFloat(data))-parseFloat(full.paid_amount)) ,2)):curr_format( round((parseFloat(full.expected_principal)+parseFloat(data)),2));
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
                  { data: "id", render:last_col_renders["active"]}
                ]     

                });
                }
                }
