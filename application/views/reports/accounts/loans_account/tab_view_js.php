if ($("#tbl_loans_report").length && tabClicked === "tab-loans_report") {
    if (typeof (dTable['tbl_loans_report']) !== 'undefined') {
        $(".tab-pane").removeClass("active");
        $("#tab-loans_report").addClass("active");
        dTable['tbl_loans_report'].ajax.reload(null, true);
    } else {
        dTable['tbl_loans_report'] = $('#tbl_loans_report').DataTable({
            "dom": '<"html5buttons"B>lTfgitp',
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "processing": true,
            "deferRender": true,
            responsive: true,
            "order": [[0, "asc"]],
            "ajax":{
             "url": "<?php echo site_url('reports/report_loans_accounts');?>",
             "dataType": "json",
             "type": "POST",
             "data": function(d){
              d.date_to = $("#end").val();
              d.state_id = 7;
              <?php if(isset($user['id'])){ ?>
              d.client_id = <?php echo $user['id'] ?>; 
              <?php } ?>
              <?php if(isset($group_id)){ ?>
              d.group_id = <?php echo $group_id ?>; 
              <?php } ?>
              }
              },
            "columnDefs": [{
                "orderable": false,
                "searchable": false
            }],
            "footerCallback": function (tfoot, data, start, end, display) {
                        var api = this.api();
                        var amount_page = api.column(5, {page: 'current'}).data().sum();
                        var amount_overall = api.column(5).data().sum();                        
                        $(api.column(5).footer()).html(curr_format(round(amount_overall,2)) );
                        
                    },
            columns: [
                {data: 'loan_no', render: function (data, type, full, meta) {
                          if (type === "sort" || type === "filter") {
                              return data;
                          }
                          var link1="<a href='<?php echo site_url('client_loan/view'); ?>/" + full.group_loan_id + "/1' title='View this Loan details'>" + data + "</a>";
                          var link2="<a href='<?php echo site_url('client_loan/view'); ?>/" + full.id + "' title='View this Loan details'>" + data + "</a>";
                          return (full.group_name == null)?link2:link2;
                      }
                  },
                  { data: "member_name",render:function( data, type, full, meta ){
                      return (data&&full.group_name)?full.group_name+' [ '+data+' ]':(!data&&full.group_name)?full.group_name:data;
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
                  { data: "days_in_demand", render:function( data, type, full, meta ){
                  return curr_format(data*1);
                    } },
                  { data: "expected_interest" , render:function( data, type, full, meta ){
                  return (full.paid_amount)?curr_format( round(((parseFloat(full.expected_principal)+parseFloat(data))-parseFloat(full.paid_amount)) ,2)):curr_format( round((parseFloat(full.expected_principal)+parseFloat(data)),2));
                    } },
                  { data: "action_date", render:function( data, type, full, meta ){
                  return (data)?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'None';
                    }  },
                  
            ],

            buttons: <?php if (in_array('6', $report_privilege)) { ?> getBtnConfig('Loan Accounts Report'), <?php } else {
    echo "[],";
} ?>
responsive: true
});
}
}