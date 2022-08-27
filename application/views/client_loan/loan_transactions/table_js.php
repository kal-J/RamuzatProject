if ($("#tblLoan_installment_payment").length && tabClicked === "tab-loan_installment_payment") {
if (typeof (dTable['tblLoan_installment_payment']) !== 'undefined') {
$(".tab-pane").removeClass("active");
$("#tab-loan_installment_payment").addClass("active");
dTable['tblLoan_installment_payment'].ajax.reload(null, true);
} else {
dTable['tblLoan_installment_payment'] = $('#tblLoan_installment_payment').DataTable({
"dom": '<"html5buttons"B>lTfgitp',
  "ajax":{
  "url": "<?php echo base_url('loan_installment_payment/jsonList'); ?>",
  "dataType": "json",
  "type": "POST",
  "data": function (d) {
  d.client_loan_id = <?php echo (isset($loan_detail['id'])) ? $loan_detail['id'] : '0'; ?>,
  d.status_id=1
  }
  },
  "footerCallback": function (tfoot, data, start, end, display) {
  var api = this.api();
  $.each([2,3,4,5,6], function(key,val){
  if(val == 6){
  var total_page_amount = (api.column(2, {page: 'current'}).data().sum() + api.column(3, {page: 'current'}).data().sum() + api.column(4, {page: 'current'}).data().sum());
  var total_overall_amount = (api.column(2).data().sum() + api.column(3).data().sum() + api.column(4).data().sum());
  $(api.column(val).footer()).html(curr_format(round(total_page_amount,2)) + " (" + curr_format(round(total_overall_amount,2)) + ") ");
  }else{
  var total_page_amount = api.column(val, {page: 'current'}).data().sum();
  var total_overall_amount = api.column(val).data().sum();
  $(api.column(val).footer()).html(curr_format(round(total_page_amount,2)) + " (" + curr_format(round(total_overall_amount,2)) + ") ");

  }
  });
  },
  "columns": [
  { data: "loan_no"},
  { data: "installment_number", render:function( data, type, full, meta ){
  return (full.installment_number !='')?data:'Pay off';}},
  { data: "paid_interest", render:function( data, type, full, meta ){
  return curr_format(data*1);}
  },
  { data: "paid_principal", render:function( data, type, full, meta ){
  return curr_format(data*1);}
  },
  { data: "paid_penalty", render:function( data, type, full, meta ){
  return curr_format(data*1);}
  },
  { data: "forgiven_interest", render:function( data, type, full, meta ){
  return curr_format(data*1);}
  },
  {"data": "paid_principal", render: function (data, type, full, meta) {
  return curr_format( round((parseFloat(full.paid_principal) + parseFloat(full.paid_interest) +parseFloat(full.paid_penalty)),2));
  }
  },
  { data: "end_balance", render:function( data, type, full, meta ){
  return curr_format(data*1);}
  },
  { data: "payment_date", render:function( data, type, full, meta ){
  if (type === "sort" || type === "filter") {
  return data;
  }
  return (!(data=='0000-00-00'))?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'';
  }
  },
  { data: "firstname", render:function( data, type, full, meta ){
  return full.staff_no+'-'+full.firstname+' '+full.lastname+' '+full.othernames;}
  },
  { data: "comment"},
  { data: "paid_principal", render: function(data, type, full, meta) {
  if(data) {
  let url = "<?php echo site_url('client_loan/print_receipt'); ?>" + `/${full.loan_no}/${full.id}`;
  return `
  <div class="d-flex align-items-center">
    <form action=${url} method="post">
      <input name="client_loan_id" type="hidden" value=${full.client_loan_id}>
      <input name="status_id" type="hidden" value=${full.status_id}>
      <input name="payment_id" type="hidden" value=${full.id}>
      <button type="submit" class="btn btn-danger btn-xs m-1">
        <i class="fa fa-print"></i> Receipt
      </button>
    </form>
    <?php if ($_SESSION['id'] == 1) { ?>
      <span>
        <button type="button" data-toggle="modal" data-target="#edit_transaction_date_modal" class=" edit_me3 btn btn-primary btn-xs">Edit</button>
      </span>

    <?php } ?>
  </div>

  `;
  }

  return '';

  }
  }
  ],
  buttons:<?php if (in_array('6', $client_loan_privilege)) { ?> getBtnConfig('Loan Payment Transactions'), <?php } else {
                                                                                                          echo "[],";
                                                                                                        } ?>
responsive: true
});
}
}

$('table tbody').on('click', 'tr .edit_me3', function (e) {
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
var formId = "edit_loan_trans_date";
edit_data(data, formId);
});