  if($("#tblMember_referrals").length && tabClicked === "tab-member_referral") {
        if(typeof (dTable['tblMember_referrals']) !== 'undefined') {
            $(".tab-pane").removeClass("active");
            $("#tab-member_referral").addClass("active");
            dTable['tblMember_referrals'].ajax.reload(null, true);
           //get_member_referrals();
        } else {    
        dTable['tblMember_referrals'] = $('#tblMember_referrals').DataTable({
            "pageLength": 25,
"responsive": true,
"dom": '<"html5buttons"B>lTfgitp',
    buttons: <?php if (in_array('6', $till_privilege)) {?> getBtnConfig('<?php echo $title; ?>- Billing'),
    <?php } else {echo "[],";}?>
    "ajax": {
    "url": "<?php echo site_url('member/memberReferrals'); ?>",
    "dataType": "json",
    "type": "POST",
    "data": function(d){
        d.status_id = 1,
        d.introduced_by_id = $('#introduced_by_id').val();
    }
    },
    "footerCallback": function (tfoot, data, start, end, display) {
    /*

    var api = this.api();
    var amount_page = api.column(2, {page: 'current'}).data().sum();
    var amount_overall = api.column(2).data().sum();
    $(api.column(2).footer()).html(curr_format(amount_page) );

    */
    },

    "columns": [
    {"data": "member_name"},
    {data: 'saving_account_balance', render: function (data, type, full, meta) {
            return curr_format(data*1);
            }
    },
    {data: 'shares_bought', render: function (data, type, full, meta) {
            return curr_format(data*1);
            }
    },
     
    {"data": "fees_paid", render:function( data, type, full, meta ){
         const fees_status = full.fees_paid;
         return parseInt(fees_status)!=0 ? "<i class='fa fa-check-circle' style='color:green;'></i> Paid":"<i class='fa fa-cancel-circle' style='color:red;'></i>Not Paid";
    }}
    
  
    ]
    });
    }
    }

    $('table tbody').on('click', 'tr .edit_me4', function (e) {
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

    });
    