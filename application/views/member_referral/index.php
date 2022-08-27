<?php
$start_date = date('d-m-Y', strtotime($fiscal_year['start_date']));
$end_date = $fiscal_year['end_date'] <= date('Y-m-d') ? date('d-m-Y', strtotime($fiscal_year['end_date'])) : date('d-m-Y');
?>
    <div class="row">
          <div class="col-lg-12">
          <div class="ibox ">
                <div class="ibox-title">
                     <ul class="breadcrumb">
                        <li><a href="<?php echo site_url("dashboard"); ?>">Dashboard</a></li>
                        <li><span  style="font-weight:bold; color:gray;  font-size:14px;"><?php echo $title; ?></span></li>
                    </ul> 
                </div>
                <div class="ibox-content">
                <div class="tabs-container">
                    <ul class="nav nav-tabs" role="tablist">
                        <li><a class="nav-link active" data-toggle="tab" data-bind="click: display_table" href="#tab-member_referral">Member Referral</a></li>
                    </ul>

                    <div class="tab-content">
                    <?php $this->load->view('member_referral/tab_view'); ?>

                      </div>

                  </div>
                </div>
            </div>
        </div>
    </div>

<script type="text/javascript">
var dTable = {};
$(document).ready( function () {
    $('#introduced_by_id').select2();
      
        var ViewModel = function () {
            var self = this;
          
            self.initialize_edit = function () {
                edit_data(self.formatOptions(), "form");
            };
            self.member_name = ko.observable();
            self.data = ko.observable();
            self.introduced_by_id = ko.observable();
            self.all_rec= ko.observable('All');
            //self.memberReferralList =ko.observableArray(<?php //echo json_encode($member_referral_info); ?>);
            self.member_details =ko.observable();
             

            self.display_table = function (data, click_event) {
                TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
            };
        };
        viewModel = new ViewModel();
        ko.applyBindings(viewModel);
        
    var handleDataTableButtons = function(tabClicked) {
        
        <?php $this->load->view('member_referral/table_js'); ?>
 
    };
    TableManageButtons = function(){
    "use strict";
    return {
    init: function(tblClicked) {
      handleDataTableButtons(tblClicked);
    }
    };
    }();
    TableManageButtons.init("tab-member_referral");
    get_member_referrals();

  } );
 
 
</script>
