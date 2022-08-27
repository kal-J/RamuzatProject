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
            "buttons": getBtnConfig('<?php echo $title; ?>'), 
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
                 
               ]     
        });
        }
      }

