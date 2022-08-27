if ($("#tblOrganisation").length && tabClicked === "tab-branch") {
    if (typeof (dTable['tblOrganisation']) !== 'undefined') {
        $(".tab-pane").removeClass("active");
        $("#tab-branch").addClass("active");
        dTable['tblOrganisation'].ajax.reload(null, true);
    } else {
        dTable['tblOrganisation'] = $('#tblOrganisation').DataTable({
            "dom": '<"html5buttons"B>lTfgitp',
            order: [[1, 'asc']],
            deferRender: true,
             "searching": false,
            "paging": false,
            ajax: {
                "url":"<?php echo site_url('Organisation/jsonList') ?>",
                "dataType":"json",
                "type":"POST",
                "data":
                function(e){
                    e.status_id = '1';
                    e.organisation_id =<?php echo $_SESSION['organisation_id']; ?>
                    }
                
            },
            
            columns: [
            { data: 'name'},
            { data: 'org_initial'},
            { data: 'description'},
            {data: 'status_id', render:function ( data, type, full, meta ) {return (data==1)?"Active ":'Deactivated'; }},
            { data: 'id', render: function(data, type, full, meta) {
                var display_btn = "<div class='btn-grp'>";
                <?php if(in_array('3', $privileges)){ ?>
                    display_btn += "<a href='#organisation-modal' data-toggle='modal' class='btn btn-sm edit_me' title='Organisation details'><i class='fa fa-edit'></i></a>";
                <?php } ?>
                     display_btn += "</div>";
                    return display_btn; 
                }
            }
            ],
            buttons: <?php if(in_array('6', $privileges)){ ?> getBtnConfig('Organisations'), <?php } else { echo "[],"; } ?>
            responsive: true
        });
    }
}
