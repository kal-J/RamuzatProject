        if($('#tblContact').length && tabClicked === "tab-contact") {
                if(typeof(dTable['tblContact'])!=='undefined'){
                    $(".biodata").removeClass("active");
                    $("#tab-contact").addClass("active");
                    $("#tab-biodata").addClass("active");
                    //dTable['tblContact'].ajax.reload(null,true);
                }else{
         dTable['tblContact']=$('#tblContact').DataTable({
            "searching": false,
            "paging": false,
            "responsive": true,
            "dom": '<"html5buttons"B>lTfgitp',
            "buttons": <?php if(in_array('6', $member_staff_privilege)){ ?> getBtnConfig('<?php echo $title; ?>'), <?php } else { echo "[],"; } ?>
            "ajax":{
             "url": "<?php echo base_url('contact/jsonList'); ?>",
             "dataType": "json",
             "type": "POST",
             "data": function (d) {
                            d.user_id = <?php echo $user['user_id']; ?>;
                     }
                     },
        "columns": [
                  { "data": "mobile_number" },
                  { "data": "contact_type" },
                  { "data": "id", render:function ( data, type, full, meta ) {
                    var ret_txt ="";
                    <?php if(in_array('3', $member_staff_privilege)){ ?>
                    ret_txt += "<a href='#add_contact-modal' data-toggle='modal' class='btn btn-sm btn-default edit_me'><i class='fa fa-edit'></i></a>";
                    <?php } if(in_array('4', $member_staff_privilege)){ ?> 
                    ret_txt += "<a href='#' data-toggle='modal' class='btn btn-sm btn-default delete_me' data-toggle='tooltip' title='Delete record'><i class='fa fa-trash'></i></a>";
                  <?php } ?>
                    return ret_txt;
                  }}
               ]     
        });
        }
}
