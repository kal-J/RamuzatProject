if ($("#tblShare_call").length && tabClicked === "tab-share_call") {
        if (typeof (dTable['tblShare_call']) !== 'undefined') {
            $(".tab-pane").removeClass("active");
            $("#tab-share_call").addClass("active");
            dTable['tblShare_call'].ajax.reload(null, true);
        } else {
            dTable['tblShare_call'] = $('#tblShare_call').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                order: [[1, 'asc']],
                deferRender: true,
                "ajax":{
                    "url": "<?php echo base_url('share_call/jsonList/'); ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data": function (d) {
                    d.status_id = 1,
                    d.issuance_id = <?php echo $share_issuance['id'];  ?>
                    }
                },
                "footerCallback": function (tfoot, data, start, end, display) {
                    var api = this.api();
                        display_footer_sum(api,[1]);
                },
    "columns": [
              { "data": 'call_name'},
              { "data": "percentage" },              
              { "data": "id", render:function ( data, type, full, meta ) {
                var ret_txt ="";
                if(parseInt(full.first_call) !=1){
                 <?php if(in_array('3', $share_issuance_privilege)){ ?>
                ret_txt +="<a href='#add_share_call-modal' data-toggle='modal' title='Edit record' class='btn text-primary btn-sm  edit_me'><i class='fa fa-edit'></i></a>";
                <?php } if(in_array('7', $share_issuance_privilege)){ ?>
                
                ret_txt += "<a href='#'style='padding-left:15px;' data-toggle='modal' class='btn btn-sm text-danger  change_status' data-toggle='tooltip' title='Remove this Call'><i class='fa fa-ban'></i></a>";
                <?php } ?>
              }
                return ret_txt;
              }}
           ],
           buttons: <?php if(in_array('6', $share_issuance_privilege)){ ?> getBtnConfig('Share Call'), <?php } else { echo "[],"; } ?>
                responsive: true
            });
        }
    }
      $('table tbody').on('click', 'tr .change_status_active', function (e) {
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
            var controller = tbl_id.replace("tbl", "");
            var url = "<?php echo site_url(); ?>" + controller.toLowerCase() + "/activate";

            change_status({id: data.id, first_call: 1,issuance_id: <?php echo $share_issuance['id']; ?>}, url, tbl_id);
        });
          $('table tbody').on('click', 'tr .change_status_inactive', function (e) {
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
            var controller = tbl_id.replace("tbl", "");
            var url = "<?php echo site_url(); ?>" + controller.toLowerCase() + "/inactivate";

            change_status({id: data.id, first_call: 0}, url, tbl_id);
        });