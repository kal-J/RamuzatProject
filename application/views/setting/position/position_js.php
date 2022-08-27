//  Position javascript 
        if($('#tblPosition').length && tabClicked === "tab-position") {
                if(typeof(dTable['tblPosition'])!=='undefined'){
                    $(".tab-pane").removeClass("active");
                    $("#tab-position").addClass("active");
                    $("#tab-biodata").addClass("active");
                    //dTable['tblPosition'].ajax.reload(null,true);
                }else{
         dTable['tblPosition']= $('#tblPosition').DataTable({
            "pageLength": 10,
            "searching": true,
            "paging": true,
            "responsive": true,
            "dom": '<"html5buttons"B>lTfgitp',
            "buttons": <?php if(in_array('6', $privileges)){ ?> getBtnConfig('<?php echo $title; ?>-Positions Held'), <?php } else { echo "[],"; } ?>
            "ajax":{
             "url": "<?php echo site_url("position/jsonList")?>",
             "dataType": "json",
             "type": "POST",
             "data": function(d){
             d.organisation_id = <?php  echo isset($_SESSION['organisation_id'])?$_SESSION['organisation_id']:'' ?>;
            }
                           },
        "columns": [
                  { "data": "position" },
                  { "data": "description" },
                  { "data": "id", render:function ( data, type, full, meta ) {
                    var ret_txt ="";
                    <?php if(in_array('3', $privileges)){ ?>
                    var ret_txt ="<a href='#add_position-modal' data-toggle='modal' class='edit_me' ><i class='fa fa-edit'></i></a>";
                  <?php } ?>
                    return ret_txt;
                  }}
               ]     

        });
        }
        }
