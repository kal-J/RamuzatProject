    if ($('#tblUser_role').length && tabClicked === "tab-role") {
    if(typeof(dTable['tblUser_role'])!=='undefined'){
        $(".tab-pane").removeClass("active");
        $("#tab-role").addClass("active");
        $("#tab-biodata").addClass("active");
        //dTable['tblUser_role'].ajax.reload(null,true);
       
    }else{
        dTable['tblUser_role'] =
            $('#tblUser_role').DataTable({
        "pageLength": 25,
        "searching": false,
        "paging": false,
        "info": false,
        "responsive": true,
        "dom": '<"html5buttons"B>lTfgitp',
        "buttons":[],
        "ajax": {
            "url": "<?php echo site_url('user_role/jsonList'); ?>",
            "dataType": "json",
            "type": "POST",
            "data": function(d){d.staff_id = <?php echo $user['id'] ?>;}
        },
        "columns": [
            {"data": 'role', render: function (data, type, full, meta) {
                    var ret_txt = '<span class="btn btn-sm btn-default"><b>'+full.role+'</b></span>';
                    return ret_txt;
                }},
            {data: 'status_id', render:function ( data, type, full, meta ) {return (data==1)?"Active ":'Deactivated'; }},
            {"data": 'status_id', render: function (data, type, full, meta) {
                    var ret_txt ="";
                    <?php if(in_array('7', $staff_privilege)){ ?>
                                var title_text = parseInt(data)===1?"De":"A";
                                var fa_class = parseInt(data)===1?"ban":"undo";
                                var icon_color = parseInt(data)===1?"warning":"default";
                    ret_txt += '<a href="#" data-toggle="modal" class="btn btn-sm btn-default change_status" title="'+title_text+'ctivate role"><i class="fa fa-'+fa_class+' text-'+icon_color+'"></i></a>';
                  <?php } if(in_array('4', $staff_privilege)){ ?>
                    ret_txt += '<a href="#" data-toggle="modal" class="btn btn-sm btn-default delete_me"><i class="fa fa-trash text-danger"></i></a></div>';
                  <?php } ?>
                    return ret_txt;
                }}
        ]

    });
    }
}
