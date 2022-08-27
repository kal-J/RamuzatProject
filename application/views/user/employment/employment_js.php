//  employment javascript 
         if ($('#tblEmployment').length && tabClicked === "tab-employment") {
                if(typeof(dTable['tblEmployment'])!=='undefined'){
                    $(".biodata").removeClass("active");
                    $("#tab-employment").addClass("active");
                    $("#tab-biodata").addClass("active");
                    //dTable['tblEmployment'].ajax.reload(null,true);
                }else{
         dTable['tblEmployment']= $('#tblEmployment').DataTable({
            "pageLength": 10,
            "searching": true,
            "paging": true,
            "responsive": true,
            "dom": '<"html5buttons"B>lTfgitp',
            "buttons": <?php if(in_array('6', $member_staff_privilege)){ ?> getBtnConfig('<?php echo $title; ?>'), <?php } else { echo "[],"; } ?>
            "ajax":{
             "url": "<?php echo site_url('employment/jsonList');?>",
             "dataType": "json",
             "type": "POST",
             "data": function(d){

              d.user_id = <?php  echo $user['user_id']?>;
              }
              },
        "columns": [
                  { "data": "position" },
                  { "data": "employer" },
                  { "data": "" , render:function ( data, type, full, meta ) {
                      
                      if(moment(full.start_date, "YYYY-MM-DD", true).isValid()===true && moment(full.end_date, "YYYY-MM-DD", true).isValid()===true){
                          var a = moment(full.end_date);
                          var b = moment(full.start_date);
                          var years = a.diff(b, 'year');
                          b.add(years, 'year');
                          var months = a.diff(b, 'months');
                          b.add(months, 'month');
                          //var days = a.diff(b, 'days');
                          return years + ' years ' + months + ' months ';
                        }else if(moment(full.start_date, "YYYY-MM-DD", true).isValid()===true){
                                return "<span data-toggle='tooltip' class='' data-placement='top' title='From: "+ full.start_date+"  To: Current'>Current</span>";
                                }else{
                                    return "Invalid Date";
                                }  
                } },
                  { "data": "name" },
                  { "data": "start_date" , render:function ( data, type, full, meta ) {return (data)?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'None';}},
                  { "data": "end_date" , render:function ( data, type, full, meta ) {return (data)?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'None';}},
                  { "data": "monthly_salary"  , render:function ( data, type, full, meta ) {return data?curr_format(data*1):''; } },
                  { "data": "id", render:function ( data, type, full, meta ) {
                    var ret_txt ="";
                     <?php if(in_array('3', $member_staff_privilege)){ ?>
                    ret_txt +="<a href='#add_employment-modal' data-toggle='modal' class='edit_me' ><i class='fa fa-edit'></i></a>";
                    <?php } if(in_array('4', $member_staff_privilege)){ ?>
                    ret_txt += "<a href='#' data-toggle='modal' class='btn btn-sm delete_me'><i class='text-danger fa fa-trash'></i></a>";
                    <?php } ?>
                    return ret_txt;
                  }}
               ]     

                });
                }
                }
