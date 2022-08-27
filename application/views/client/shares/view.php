
<!-- <pre data-bind="text: ko.toJSON($data, null, 2)"></pre> -->
<div class="ibox-title">
     <ul class="breadcrumb">
        <li><a href="<?php echo site_url("u/home"); ?>">Home</a></li>
        <li><a href="<?php echo site_url("u/shares"); ?>">Share Accounts</a></li>
        <li><span  style="font-weight:bold; color:gray;  font-size:14px;"><?php echo $title; ?></span></li>
    </ul>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="tabs-container">
             <ul class="nav nav-tabs" role="tablist">
                <li><a class="nav-link active" data-toggle="tab" data-bind="click: display_table" href="#tab-overview"><i class="fa fa-address-book-o"></i>Share Overview</a></li>
                <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table" href="#tab-share_transaction"><i class="fa fa-money"></i>Transactions</a></li>
                <!-- <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table" href="#tab-apply_share_fee"><i class="fa fa-bars"></i>Share fees payment</a></li> -->
                <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table" href="#tab-dividends"><i class="fa fa-bars"></i>Dividend Paid </a></li>
                 <!--  <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table" href="#tab-apply_share_fee"><i class="fa fa-bars"></i>Share fees payment</a></li>
                <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table" href="#tab-dividends"><i class="fa fa-bars"></i>Dividend payments</a></li> -->
              </ul>
              <div class="tab-content">
                <div role="tabpanel" id="tab-overview" class="tab-pane active">
                    <div class="panel-body">
              
                        <table class="table table-user-information  table-bordered table-stripped  m-t-md">
                             <!-- ko foreach: share_details -->
                            <tbody>
                                <tr>
                                    <td><strong>Account Name</strong></td>
                                    <td colspan="5"><a data-bind="text: salutation+' '+firstname+' '+lastname+' '+othernames,attr: {href:'<?php echo site_url("u/profile"); ?>'}" ></a></td>
                                </tr>
                           
                                <tr>
                                    <td><strong>Share account</strong></td>
                                    <td  data-bind="text: account_no"></td> 
                                </tr>
                
                                <tr> 
                                    <td><strong>Total Amount paid</strong></td>
                                    <td colspan="3" data-bind="text: curr_format(total_amount)"></td>
                                    <td><strong>Share State</strong></td>
                                    <td  data-bind="text: (state_id)?((state_id==6)?'Approved':((state_id==12)?'Locked':((state_id==0)?'Deleted':((state_id==5)?'Pending':((state_id==17)?'Dormant':((state_id==7)?'Active':'Undefined')))))):'None'"></td>
                                <tr>
                                    <td><strong>Comment</strong></td>
                                    <td colspan="5" data-bind="text: narrative"></td> 
                                </tr>
                            </tbody>
                             <!-- /ko -->
                          </table>
                        <br>
                    </div>
                </div>
                <?php //$this->load->view('shares/fees/tab_view'); ?>
                <div id="tab-dividends" class="tab-pane">
                    <div class="panel-body">
                    <?php $this->load->view('client/shares/dividends/tab_view.php'); ?>
                    
                        </div>
                </div>
                <?php $this->load->view('client/shares/transaction/transaction_tab'); ?>
             </div>
        </div>
    </div>
</div>


<script>
    var dTable = {};
    var viewModel = {};
    var TableManageButtons = {};
$(document).ready(function() {


  
    var ShareFee = function () {
        var self = this;
        self.selected_fee = ko.observable();
    };

    var ViewModel = function(){
        var self = this;
        self.display_table = function (data, click_event) {
            TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
        };

        self.share_price_amount = ko.observableArray( <?php // echo $share_price_amount ?> );
        self.new_account_no  = ko.observable();

        self.available_share_fees = ko.observableArray(<?php echo (!empty($available_share_fees) ? json_encode($available_share_fees) : '') ?>);
        self.share_issuance = ko.observableArray([<?php echo json_encode($share_issuances); ?>]);
        self.share_details = ko.observable(<?php echo (!empty($share_details) ? json_encode($share_details ) : '') ?>);
        self.applied_share_fee = ko.observableArray([new ShareFee()]);
        self.share_issuance = ko.observable();
        self.account_state = ko.observable();
        self.action_msg = ko.observable();
       
        self.addShareFee = function () {
            self.applied_share_fee.push(new ShareFee());
        };
        self.removeShareFee = function (selected_member) {
            self.applied_share_fee.remove(selected_member);
        };

        self.initialize_edit = function () {
            self.share_details(<?php echo json_encode($share_details); ?>);
                edit_data(self.share_details(),"formShares_state");
               
        }
        self.set_action = function (state_id) {

                if (state_id == 5) {
                    self.action_msg("pend");
                    self.account_state(5);
                }
                if (state_id == 7) {
                    self.action_msg("approve");
                    self.account_state(7);
                }
                if (state_id == 12) {
                    self.action_msg("lock");
                    self.account_state(12);
                }
                if (state_id == 17) {
                    self.action_msg("attach dormant on");
                    self.account_state(17);
                }
                if (state_id == 18) {
                    self.action_msg("delete");
                    self.account_state(18);
                }
            };
    };

    viewModel = new ViewModel();
    ko.applyBindings(viewModel);

    var handleDataTableButtons = function (tabClicked) {
        <?php 
        //$this->view('shares/fees/table_js'); 
        $this->view('client/shares/dividends/table_js');
        $this->load->view('client/shares/transaction/transaction_js');
        ?>
    };
    TableManageButtons = function () {
        "use strict";
        return {
            init: function (tblClicked) {
                handleDataTableButtons(tblClicked);

            }
        };
    }();
        TableManageButtons.init("tab-apply_share_fee");
    });
function reload_data(form_id, response){
    switch(form_id){
        case "formShares":
            viewModel.share_details(response.share_details);
            break;

        default:
            //nothing really to do here
            break;
    }
}

</script>
