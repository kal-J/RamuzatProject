        <div class="row">
            <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5> Partner's List</h5>
                      <div class="ibox-tools">
                      <?php if(in_array('1', $staff_privilege)){ ?>
                        <button type="button" class="btn btn-sm btn-default" data-toggle="modal" data-target="#add_partner-modal"><i class="fa fa-plus-circle"></i> Add</button>
                      <?php } ?>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example" id="tblPartner" >
                            <thead>
                                <tr>
                                    <th>Partner #</th>
                                    <th>Names</th>
                                    <th>Contact</th>
                                    <th>Email</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                                <tbody>
                                </tbody>
                            </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php require 'partner_add-modal.php';?>
<script type="text/javascript">
var dTable = {};
$(document).ready( function () {

        //**************************** Page View KO Model *********************************************************//
        var ViewModel = function () {
            var self = this;
            self.marital_status_id = ko.observable();
        };
        viewModel = new ViewModel();
        ko.applyBindings(viewModel);
        
var handleDataTableButtons = function() {
  if ($("#tblPartner").length) {
      
    dTable['tblPartner'] = $('#tblPartner').DataTable( {
      pageLength: 10,
      responsive: true,
      dom: '<"html5buttons"B>lTfgitp',
      buttons: <?php if(in_array('6', $staff_privilege)){ ?> getBtnConfig('<?php echo $title; ?>'), <?php } else { echo "[],"; } ?>
      ajax: {
        url:"<?php echo site_url("partner/jsonList");?>",
        dataType: 'JSON',
        type: 'POST',
        data: function(d){
          d.status_id=1;
        }
      },
      columnDefs: [{
                  "targets": [4],
                  "orderable": false,
                  "searchable": false
              }],
      columns:[
      { data: 'partner_no' },
      { data: 'salutation', render:function ( data, type, full, meta ) { 
         return "<a href='<?php echo site_url("partner/view"); ?>/" + full.id + "'>" + data + "  "+ full.firstname+" " + full.lastname + "  " + full.othernames + "</a>";}},
      { data: 'mobile_number' },
      { data: 'email'},
      { data: 'id', render:function ( data, type, full, meta ) {
        var ret_txt ="<a href='<?php echo base_url();?>partner/view/"+data+"'><i class='fa fa-edit'></i></a>";
        <?php if(in_array('4', $staff_privilege)){ ?>
        ret_txt += "<a href='#' data-toggle='modal' class='btn btn-sm delete_me'><i class='text-danger fa fa-trash'></i></a>";
        <?php } ?>
        return ret_txt;
      } }
      ]
      
    });
}
};
TableManageButtons = function(){
"use strict";
return {
init: function() {
  handleDataTableButtons();
}
};
}();

TableManageButtons.init();

$('#formPartner').validate({submitHandler: saveData2});
} );


function reload_data(formId, reponse_data)
    {
      if (typeof reponse_data.user !== 'undefined' ) {
          
        //window.location = "<?php  //echo site_url('staff/staff_data/');?>"+reponse_data.user;
            
    }
      
 }
</script>
