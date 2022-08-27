<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-content">
                <div class="tabs-container">
                    <ul class="nav nav-tabs" role="tablist">
                        <li><a class="nav-link active" data-toggle="tab" data-bind="click: display_table" href="#tab-mobile_deposit">Mobile Deposit</a></li>
                    </ul>
                    <div class="tab-content">
                         <div role="tabpanel" id="tab-mobile_deposit" class="tab-pane active">
                            <div class="col-lg-12">
                                <div class="pull-right add-record-btn">
                                  <div class="panel-title">

                                  </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="tblMobileDeposit" class="table table-striped table-bordered table-hover dataTables-example"  >
                                      <thead>
                                          <tr>
                                              <th>Transaction #</th>
                                              <th>ChechoutID</th>
                                              <th>Account #</th>
                                              <th>Desired Deposit</th>
                                              <th>Deposited Amount</th>
                                              <th>Phone #</th>
                                              <th>Client Name</th>
                                              <th>Status Code</th>
                                              <th>Status</th>
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
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
var dTable = {};
var viewModel = {};
$(document).ready( function () {

  var ViewModel= function() {
    var self = this;
    self.display_table = function (data, click_event) {
        TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
      };
  };

  viewModel = new ViewModel();
  ko.applyBindings(viewModel);

  var handleDataTableButtons = function(tabClicked) {
    if ($("#tblMobileDeposit").length && tabClicked === "tab-mobile_deposit") {
        if (typeof (dTable['tblMobileDeposit']) !== 'undefined') {
            $("#tab-mobile_deposit").addClass("active");
            dTable['tblMobileDeposit'].ajax.reload(null, true);
        } else {
            dTable['tblMobileDeposit'] = $('#tblMobileDeposit').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                ajax: {
                    "url": "<?php  echo site_url('u/mula_payment/jsonList'); ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data":
                        function (e) {
                            e.status_id = '1';
                        }
                },
              "order": [[ 9, "desc" ]],
                columns:[
                    {data: 'merchant_transaction_id'},
                    {data: 'checkout_request_id'},
                    {data: 'account_no'},
                    {data: 'requested_amount', render:function( data, type, full, meta ){
                      return curr_format(data*1);
                    }},
                    {data: 'paid_amount', render:function( data, type, full, meta ){
                      return curr_format(data*1);
                    }},
                    {data: 'client_contact'},
                    {data: 'member_name'},
                    {data: 'payment_status' },
                    {data: 'status_description'},
                    {data: 'id', render:function ( data, type, full, meta ) {
                      var ret_txt ="";
                      <?php if(in_array('4', $savings_privilege)){ ?>
                        //ret_txt += "<a href='#' data-toggle='modal' class='btn btn-sm delete_me'><i class='text-danger fa fa-trash'></i></a>";
                      <?php } ?>
                      return ret_txt;
                    } }
                ],
                buttons: <?php if(in_array('6', $savings_privilege)){ ?> getBtnConfig('Mobile money Transaction'), <?php } else { echo "[],"; } ?>
                responsive: true
      
        
            });
        }
    }
  };
    TableManageButtons = function(){
          "use strict";
          return {
          init: function(tblClicked) {
                  handleDataTableButtons(tblClicked);
                }
          };
    }();

        TableManageButtons.init("tab-mobile_deposit");
});

</script>
