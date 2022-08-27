if ($('#tblBusiness').length && tabClicked === "tab-business") {
    if(typeof(dTable['tblBusiness'])!=='undefined'){
        $(".biodata").removeClass("active");
        $("#tab-business").addClass("active");
        $("#tab-biodata").addClass("active");
        //dTable['tblBusiness'].ajax.reload(null,true);
    }else{
        dTable['tblBusiness'] =
            $('#tblBusiness').DataTable({
        "pageLength": 25,
        "searching": false,
        "paging": false,
        "responsive": true,
        "dom": '<"html5buttons"B>lTfgitp',
        buttons:  getBtnConfig('<?php echo $title; ?>-Businesses Held'),
        "ajax": {
            "url": "<?php echo site_url('business/jsonList'); ?>",
            "dataType": "json",
            "type": "POST",
            "data": function(d){
            d.status_id=1,
            d.member_id = <?php echo $user['id']; ?>;}
        },
        "columns": [
            {"data": "businessname"},
            {"data": "natureofbusiness"},
            {"data": "businesslocation"},
            {"data": "numberofemployees"},
            {"data": "businessworth"},
            {"data": "ursbnumber"},
            {"data": "certificateofincorporation", render: function(data, type, full,meta){
                    if(full.certificateofincorporation!==""){
                        var organisation_id=<?php echo $_SESSION['organisation_id'];?>;
                        //var link="<a target='blank' href='http://docs.google.com/gview?url=<?php echo site_url(); ?>uploads/organisation_"+organisation_id+"/user_docs/certificate_of_incorporation/"+data+"&amp;embedded=true' width='500' height='250' style='border-style:none;'>view file </a>";
                        var link= "<a target='blank' href='<?php echo site_url(); ?>uploads/organisation_"+organisation_id+"/user_docs/certificate_of_incorporation/"+data+"' title='View document details'>View File</a>";
                        return link;
                        }
                    return "No file";
            }}
        ]
    });
    }
}
