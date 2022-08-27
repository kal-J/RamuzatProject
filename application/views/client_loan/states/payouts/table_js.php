// pending approval javascript
if ($('#tblPending_payout').length && tabClicked === "tab-pending_payout") {
//reinitailizing daterange picker
daterangepicker_initializer();
if(typeof(dTable['tblPending_payout'])!=='undefined'){
$(".loans").removeClass("active");
$("#tab-loans").addClass("active");
$("#tab-pending_payout").addClass("active");
dTable['tblPending_payout'].ajax.reload(null,true);
}else{
dTable['tblPending_payout']= $('#tblPending_payout').DataTable({
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
"dom": '<"html5buttons"B>lTfgitp',
  "buttons": <?php if (in_array('6', $client_loan_privilege)) { ?> getBtnConfig('<?php echo $title; ?>'), <?php } else {
                                                                                                        echo "[],";
                                                                                                      } ?>
  "ajax":{
  "url": "<?php echo site_url('client_loan/jsonList'); ?>",
  "dataType": "json",
  "type": "POST",
  "data": function(d){
  //d.date_to = moment(end_date,'X').format('YYYY-MM-DD');
  //d.date_from = moment(start_date,'X').format('YYYY-MM-DD');
  d.state_id = 20;
  <?php if (isset($user['id'])) { ?>
    d.client_id = <?php echo $user['id'] ?>;
  <?php } ?>
  <?php if (isset($group_id)) { ?>
    d.group_id = <?php echo $group_id ?>;
  <?php } ?>
  }
  },
  "order": [[ 0, "desc" ]],
  "footerCallback": function (tfoot, data, start, end, display) {
  var api = this.api();
  $.each([4], function(key,val){
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
  return (full.group_name == null)?link2:link1;
  }
  },
  { data: "member_name", render:function( data, type, full, meta ){
  return (data&&full.group_name)?full.group_name+' [ '+data+' ]':(!data&&full.group_name)?full.group_name:data;
  } },
  { data: "credit_officer_name" },
  { data: "product_name" },
  { data: "requested_amount", render:function( data, type, full, meta ){
  return curr_format(data*1);
  } },
  { data: "interest_rate", render:function( data, type, full, meta ){
  return (data*1)+"%";
  } },
  { data: "application_date", render:function( data, type, full, meta ){
  return (data)?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'None';;
  } },
  { data: "id", render:function ( data, type, full, meta ) {
  var ret_txt ="<div class='btn-group'>",required_approvals="4";
    <?php if (in_array('14', $client_loan_privilege)) { ?>
      if((full.ref_no)){
      ret_txt +="Pending";
      }else{
      ret_txt +="<a href='#' title='Resend Payment Request' class='btn btn-sm send_money'><i class='fa fa-money' style='font-size:16px'></i> Resend</a>";
      }

    <?php }  ?>
    ret_txt +="</div>";
  return ret_txt;
  }},
  { data: "mm_status_description" },
  { data: "mm_massage" }
  ]

  });
  }
  }

  $('table tbody').on('click', 'tr .send_money', function (e) {
  e.preventDefault();
  var row = $(this).closest('tr');
  var tbl = row.parent().parent();
  var tbl_id = $(tbl).attr("id");
  var dt = dTable[tbl_id];
  var data = dt.row(row).data();
  if (typeof (data) === 'undefined') {
  data = dt.row($(row).prev()).data();
  if (typeof (data) === 'undefined') {
  data = dt.row($(row).prev().prev()).data();
  }
  }
  var url = "<?php echo site_url(); ?>/u/Beyonic_payments/disburse_beyonic";
  resend_money(data, url, tbl_id);
  });