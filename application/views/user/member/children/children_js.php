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
                buttons: <?php if (in_array('6', $member_privilege)) { ?> getBtnConfig('<?php echo $title; ?>-Children'), <?php } else {
    echo "[],";
} ?>
                "ajax": {
                    "url": "<?php echo site_url('children/jsonList'); ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data": function (d) {
                        d.member_id = <?php echo $user['id'] ?>;
                    },
                    "dataSrc": function (data) {
                        savingsModel.children(data.data);
                        return data.data;
                    },
                    
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
                    },
                    {"data": "id", render: function (data, type, full, meta) {
                            var ret_txt = "";
    <?php if (in_array('3', $member_privilege)) { ?>
                                ret_txt += "<div class='btn-grp'><a href='#add_children-modal' data-toggle='modal' class='btn btn-sm edit_me' title='Update children details'><i class='fa fa-edit'></i></a>";
    <?php } if (in_array('4', $member_privilege)) { ?>
                                ret_txt += '<a href="#" data-toggle="modal"   title="Delete details" class="delete_me"> &nbsp;&nbsp;<i class="fa fa-trash"  style="color:#bf0b05"></i></a>';
    <?php } ?>
                            return ret_txt;
                        }
                    }
                ]

            });
        }
    }
