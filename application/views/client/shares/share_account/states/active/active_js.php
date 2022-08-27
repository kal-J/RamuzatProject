    if ($("#tblShares_Active_Account").length) {
    console.log('MF', tabClicked)
    if (typeof (dTable['tblShares_Active_Account']) !== 'undefined') {
    $(".tab-pane").removeClass("active");
    $("#tab-share_active_accounts").addClass("active");
    dTable['tblShares_Active_Account'].ajax.reload(null, true);
    console.log('Tugende');;
    } else {
    dTable['tblShares_Active_Account'] = $('#tblShares_Active_Account').DataTable({
    order: [[1, 'asc']],
    "pageLength": 10,
    "processing": true,
    "serverSide": true,
    "deferRender": true,
    "searching": true,
    "paging": true,
    "responsive": true,
    bInfo : false,
    "dom": '<"html5buttons"B>lTfgitp',
        "buttons": getBtnConfig('<?php echo $title; ?>'),
    ajax: {
    "url": "<?php echo site_url('u/shares/jsonList') ?>",
    "dataType": "json",
    "type": "POST",
    "data":
    function (e) {
    e.status_id = '1';
    e.state_id = '7'; //Active
    <?php if (isset($_SESSION['member_id'])) { ?>
        e.client_id = <?php echo $_SESSION['member_id']; ?>;
    <?php } ?>
    }

    },
    "columnDefs": [{
    "targets": [3],
    "orderable": false,
    "searchable": false
    }],
    columns: [

    {data: 'share_account_no', render: function (data, type, full, meta) {
    return "<a href='<?php echo site_url('u/shares/view'); ?>/" + full.id + "' title='View share details'>" +data+ "</a>";
    }},
    {data: 'salutation', render: function (data, type, full, meta) {
    if (type === "sort" || type === "filter") {
    return "<a href='<?php echo site_url('u/profile'); ?>' title='View user profile'>" + data.salutation+' '+data.firstname+' '+data.lastname+' '+data.othernames+ "</a>";
    }
    return "<a href='<?php echo site_url('u/profile'); ?>' title='View user profile'>" + full.salutation+' '+full.firstname+' '+full.lastname+' '+full.othernames + "</a>";
    }},

    {data: 'price_per_share', render: function (data, type, full, meta){
    return (data)?curr_format(data*1):0;

    }
    },
    {data: 'total_amount', render: function (data, type, full, meta){
    return round((parseFloat(data)/parseFloat(full.price_per_share)),2);

    }
    },
    {data: 'total_amount', render: function (data, type, full, meta){
    return (data)?curr_format(data*1):0;

    }
    }

    ],
    buttons: getBtnConfig('Active Shares Accounts'),
    responsive: true
    });

    }
    }