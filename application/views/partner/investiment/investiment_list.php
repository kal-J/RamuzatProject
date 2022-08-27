        <div class="row">
            <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5> Investiment List</h5>
                      <div class="ibox-tools">
                      <?php if(in_array('1', $staff_privilege)){ ?>
                        <button type="button" class="btn btn-sm btn-default" data-toggle="modal" data-target="#add_investiment-modal"><i class="fa fa-plus-circle"></i> Add</button>
                      <?php } ?>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example" id="tblInvestiment" >
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Linked A/C</th>
                                    <th>Collection Frequency</th>
                                    <th>Current Amount</th>
                                    <th>Target Amount</th>
                                    <th>End Date</th>
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

<?php require 'investiment_add-modal.php';?>
<script type="text/javascript">
var dTable = {};
$(document).ready( function () {

        //**************************** Page View KO Model **********************************//
         var Partner = function () {
                var self = this;
                self.selected_partner = ko.observable();
            };
        var ViewModel = function () {
            var self = this;
            self.start_date=  ko.observable('<?php echo date('d-m-Y'); ?>'); 
            self.end_date=  ko.observable('<?php echo date('d-m-Y'); ?>'); 
            
            self.partner_list = ko.observableArray(<?php echo json_encode($partner_list); ?>);

            self.accounts_list = ko.observableArray(<?php echo json_encode($account_list); ?>);

            self.formatAccount2 = function (account) {
                return account.account_code + " " + account.account_name;
            };
            
            self.select2accounts = function (sub_category_id) {
                //its possible to send multiple subcategories as the parameter
                var filtered_accounts = ko.utils.arrayFilter(self.accounts_list(), function (account) {
                    return Array.isArray(sub_category_id)?(check_in_array(account.sub_category_id,sub_category_id)):(account.sub_category_id == sub_category_id);
                });
                return filtered_accounts;
            };

           //adding partner on step by step process
              self.added_partner = ko.observableArray([new Partner()]);
              self.addPartner = function () {
                  self.added_partner.push(new Partner());
              };
              self.removePartner= function (selected_type) {
                  self.added_income_type.remove(selected_type);
              };
          //end of the observables
        };
        viewModel = new ViewModel();
        ko.applyBindings(viewModel);
        
var handleDataTableButtons = function() {
  if ($("#tblInvestiment").length) {
      
    dTable['tblInvestiment'] = $('#tblInvestiment').DataTable( {
      pageLength: 10,
      responsive: true,
      dom: '<"html5buttons"B>lTfgitp',
      buttons: <?php if(in_array('6', $staff_privilege)){ ?> getBtnConfig('<?php echo $title; ?>'), <?php } else { echo "[],"; } ?>
      ajax: {
        url:"<?php echo site_url("investiment/jsonList");?>",
        dataType: 'JSON',
        type: 'POST',
        data: function(d){
          d.status_id=1;
        }
      },
      columnDefs: [{
                  "targets": [6],
                  "orderable": false,
                  "searchable": false
              }],
      columns:[
      { data: 'investiment_name' },
      { data: 'investiment_account' },
      { data: 'collection_frequency', render:function ( data, type, full, meta ) { 
        if (full.collection_made_every == 1) {
          return data +'day(s)';
        }else if (full.collection_made_every == 2) {
          return data +'week(s)';
        }else if (full.collection_made_every == 3) {
          return data +'month(s)';
        }
        }},
      { data: 'target_amount', render: function (data, type, full, meta) {
          return curr_format(data * 1);
        } },
      { data: 'target_amount', render: function (data, type, full, meta) {
          return curr_format(data * 1);
        }},
      { data: "end_date" , render:function ( data, type, full, meta ) {
        return (data)?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'';
      }},
      { data: 'id', render:function ( data, type, full, meta ) {
        var ret_txt ="<a href='#'><i class='fa fa-edit'></i></a>";
        <?php if(in_array('4', $staff_privilege)){ ?>
        ret_txt += "<a href='#' data-toggle='modal' class='btn btn-sm change_status'><i class='text-danger fa fa-trash'></i></a>";
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

$('#formInvestiment').validate({submitHandler: saveData2});
} );


function reload_data(formId, reponse_data)
    {
      if (typeof reponse_data.user !== 'undefined' ) {
          
        //window.location = "<?php  //echo site_url('staff/staff_data/');?>"+reponse_data.user;
            
    }
      
 }
</script>
