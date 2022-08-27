        if($('#tblBackup').length && tabClicked === "tab-backup") {
                if(typeof(dTable['tblBackup'])!=='undefined'){
                    $(".biodata").removeClass("active");
                    $("#tab-backup").addClass("active");
                    $("#tab-biodata").addClass("active");
                    //dTable['tblBackup'].ajax.reload(null,true);
                }else{
         dTable['tblBackup']=$('#tblBackup').DataTable({
            "searching": false,
            "paging": false,
            "responsive": true,
            "dom": '<"html5buttons"B>lTfgitp',
            "ajax":{
                "url": "<?php echo base_url('backup/jsonList'); ?>",
                "dataType": "json",
                "type": "POST"
            },
        "columns": [
                  { "data": "file_name" },
                  { "data": "id", render:function ( data, type, full, meta ) {
                    var ret_txt ="";
                    return ret_txt;
                  }}
               ],
        buttons: <?php if(in_array('6', $privileges)){ ?> getBtnConfig('Backup Files'), <?php } else { echo "[],"; } ?>
        responsive: true     
        });
        }
}
