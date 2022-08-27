//  Position javascript 
        if($('#tblDocument').length && tabClicked === "tab-document") {
                if(typeof(dTable['tblDocument'])!=='undefined'){
                   $(".biodata").removeClass("active");
                    $("#tab-document").addClass("active");
                    $("#tab-biodata").addClass("active");
                    //dTable['tblDocument'].ajax.reload(null,true);
                }else{
         dTable['tblDocument']= $('#tblDocument').DataTable({
            "pageLength": 10,
            "searching": true,
            "paging": true,
            "responsive": true,
            "dom": '<"html5buttons"B>lTfgitp',
            "buttons": getBtnConfig('<?php echo $title; ?>-Documents Held'),
            "ajax":{
             "url": "<?php echo base_url('document/jsonList/') ?>",
             "dataType": "json",
             "type": "POST",
             "data": function (d) {
                            d.user_id = <?php echo $user['id']; ?>;
                     }},
                     
              "columns": [
                        { "data": "document_name" },
                        { "data": "document_type" },
                        { "data": "description" },
                        { "data": "document_name", render:function ( data, type, full, meta ) {
                          var doc_name ="";
                          doc_name += "<a href='<?php echo base_url();?>uploads/organisation_<?php echo $_SESSION['organisation_id'];?>/user_docs/other_docs/"+data+"' title='Document preview'>View File</a>";
                             return doc_name;
                        }}
                     ]     

              });
        dTable['tblDocument'].on( 'order.dt search.dt', function () {
          dTable['tblDocument'].column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        } );
    } ).draw();
        
        }
        }
