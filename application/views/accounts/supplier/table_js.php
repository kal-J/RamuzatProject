if ($("#tblSupplier").length && tabClicked === "tab-supplier") {
    if (typeof (dTable['tblSupplier']) !== 'undefined') {
        $(".tab-pane").removeClass("active");
        $("#tab-supplier").addClass("active");
        dTable['tblSupplier'].ajax.reload(null, true);
    } else {
                dTable['tblSupplier'] = $("#tblSupplier").DataTable({
                    "dom": '<"html5buttons"B>lTfgitp',
                    order: [[0, 'desc']],
                    deferRender: true,
                    "ajax": {
                        "url": "<?php echo site_url('supplier/jsonlist') ?>",
                        "dataType": "JSON",
                        "type": "POST"
                    },
                    "initComplete": function( settings, json ){
                        accountsModel.supplier_list( json.data );
                     },
                    columnDefs: [{
                            "targets": [7],
                            "orderable": false,
                            "searchable": false
                        }],
                    columns: [
                        {data: 'supplier_names', render: function (data, type, full, meta) {return data ? curr_format(data) : '';}},
                        {data: 'tin'},
                        {data: 'supplier_type'},
                        {data: 'supply_count', render: function (data, type, full, meta) {
                                return data ? curr_format(data) : '';
                            }},
                        {data: 'phone1', render: function (data, type, full, meta) {
                                return '<a href="tel:' + data + '" title="Call now">' + data + '</a>';
                            }},
                        /*{data: 'phone2', render: function (data, type, full, meta) {
                                return '<a href="tel:' + data + '" title="Call now">' + data + '</a>';
                            }},*/
                        {data: 'email_contact1', render: function (data, type, full, meta) {
                                return '<a href="mailto:' + data + '" title="Send email">' + data + '</a>';
                            }},
                        /*{data: 'email_contact2', render: function (data, type, full, meta) {
                                return '<a href="mailto:' + data + '" title="Send email">' + data + '</a>';
                            }},
                        {data: 'physical_address'},
                        {data: 'postal_address'},*/
                        { data: 'country_name'},
                        {"data": 'id', render: function (data, type, full, meta) {
                                var ret_txt = "";
<?php if (in_array('3', $accounts_privilege)) { ?>
                                    ret_txt += '<a href="#add_supplier-modal" data-toggle="modal" class="btn btn-sm btn-default edit_me"><i class="fa fa-pencil text-warning"></i></a>';
<?php } if (in_array('7', $accounts_privilege)) { ?>
                                    ret_txt += '<a href="#" data-toggle="modal" class="btn btn-sm btn-default change_status"><i class="fa fa-ban text-warning"></i></a>';
<?php } if (in_array('4', $accounts_privilege)) { ?>
                                    ret_txt += (full.supply_count == 0 || !full.supply_count) ? '<a href="#" data-toggle="modal" class="btn btn-sm btn-default delete_me"><i class="fa fa-trash text-danger"></i></a>' : '';
<?php } ?>
                                return ret_txt;
                            }
                        }
                    ],
                    buttons:<?php if (in_array(8, $accounts_privilege)) { ?> getBtnConfig('Suppliers/Vendors'), <?php } else {
    echo "[],";
} ?>
                });
         }
}