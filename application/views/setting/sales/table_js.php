if ($("#tblItemsForSale").length && tabClicked === "tab-items_for_sale") {
        if (typeof (dTable['tblItemsForSale']) !== 'undefined') {
            $(".tab-pane").removeClass("active");
            $("#tab-items_for_sale").addClass("active");
            dTable['tblItemsForSale'].ajax.reload(null, true);
        } else {
            dTable['tblItemsForSale'] = $('#tblItemsForSale').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
                order: [[0, 'asc'], [1, 'asc']],
                "processing": true,
                "serverSide": true,
                "orderable": false,
                "searchable": false,
                "deferRender": true,
                responsive: true,
                ajax: {
                    "url": "<?php echo site_url('Sales/jsonList_items'); ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data": function (d) {
                        d.start_date = start_date ? moment(start_date,'X').format('YYYY-MM-DD') : '';
                        d.end_date = end_date ? moment(end_date,'X').format('YYYY-MM-DD') : '';
                        d.status_id = '3';
                    }
                },
                "columnDefs": [{
                        "targets": [2],
                        "orderable": false,
                        "searchable": false
                    }],
                
                columns: [
                    {data: 'name', render: function (data, type, full, meta) { 
                        return data; 
                    }},
                    {data: 'narrative'},
                    
                    {data: 'id', render: function (data, type, full, meta) {
                        var display_btn ="";
                        display_btn += "<a href='#new_item-modal' data-toggle='modal' class='btn btn-sm edit_me' title='Update branch details'><i class='fa fa-edit'></i></a>";
                        <?php if(in_array('3', $deposit_product_privilege)){ ?>
                        display_btn +="<a href='<?php echo base_url(); ?>Sales/deactivate/" + data + "' title='Deactivate Item'><i style='margin-right: 15px;' class='fa fa-ban '></i></a>";
                        <?php } ?>
                        display_btn += "</div>";
                        return display_btn;
                        }
                    }
                    
                        
                ],
                buttons: <?php if (in_array('6', $deposit_product_privilege)) { ?> getBtnConfig('Module and accounts_privilege'), <?php
    } else {
        echo "[],";
    }
    ?>
                responsive: true
            });
        }
    }

        $('table tbody').on('click', 'tr .edit_item', function (e) {
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
            var controller = tbl_id.replace("tbl", "form");
            edit_data(data, formId);
        });