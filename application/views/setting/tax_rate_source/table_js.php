
    if ($("#tblTax").length && tabClicked === "tab-tax_rate_source") {
        if (typeof (dTable['tblTax']) !== 'undefined') {
            $(".tab-pane").removeClass("active");
            $("#tab-tax_rate_source").addClass("active");
        } else {
            dTable['tblTax'] = $('#tblTax').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                order: [[0, 'asc']],
                deferRender: true,
                ajax: {
                    "url": "<?php echo base_url('tax_rate_source/jsonList') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data": {
                        function() {
                            status_id = '1';
                        }
                    }
                },
                columns: [
                {data: 'source', render: function(data, type, full, meta) { return "<a href='<?php echo site_url("tax_rate_source/view/");?>"+full.id+"' title='View tax rates'>"+data+"</a>";}},
                    {data: 'description'},
                    {data: 'id', render: function (data, type, full, meta) {
                            display_btn = "<div class='btn-grp'><a href='<?php echo site_url("tax_rate_source/view/");?>"+full.id+"' class='btn btn-sm btn-default' title='View rates'><i class='fa fa-ellipsis-h'></i></a>";
                            display_btn += "</div>";

                            return display_btn;
                        }
                    }
                ],
                buttons: <?php if(in_array('6', $privileges)){ ?> getBtnConfig('<?php echo $title; ?>'), <?php } else { echo "[],"; } ?>
                responsive: true
            });
        }
    }