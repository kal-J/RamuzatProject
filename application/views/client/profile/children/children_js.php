if ($('#tblChildren').length && tabClicked === "tab-children") {
        if (typeof (dTable['tblChildren']) !== 'undefined') {
            $(".biodata").removeClass("active");
            $("#tab-children").addClass("active");
            $("#tab-biodata").addClass("active");
            //dTable['tblChildren'].ajax.reload(null,true);
        } else {
            dTable['tblChildren'] = $('#tblChildren').DataTable({
                "pageLength": 25,
                "searching": false,
                "paging": false,
                "responsive": true,
                "dom": '<"html5buttons"B>lTfgitp',
                buttons: getBtnConfig('<?php echo $title; ?>-Children'),
                "ajax": {
                    "url": "<?php echo site_url('children/jsonList'); ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data": function (d) {
                        d.member_id = <?php echo $user['id'] ?>;
                    }
                },
                "columns": [
                    {"data": "firstname", render: function (data, type, full, meta) {
                            return data + " " + full.lastname + " " + full.othernames;
                        }},
                    {"data": "gender"},
                    {"data": "date_of_birth", render: function (data, type, full, meta) {
                            if (type == 'sort') {
                                return data ? moment(data, 'YYYY-MM-DD').format('X') : '';
                            }
                            return data ? (moment(data, 'YYYY-MM-DD').format('DD-MM-YYYY')) : '';
                        }
                    },
                    {"data": "date_of_birth", render: function (data, type, full, meta) {
                            return data ? (moment().diff(moment(data, 'YYYY-MM-DD'), 'years')) : '';
                        }
                    }
                ]

            });
        }
    }
