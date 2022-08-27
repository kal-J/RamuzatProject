<div class="ibox-title">
     <ul class="breadcrumb">
        <li><a href="<?php echo site_url("dashboard"); ?>">Dashboard</a></li>
        <li><a href="<?php echo site_url("setting"); ?>">Settings</a></li>
        <li><span  style="font-weight:bold; color:gray;  font-size:14px;"><?php echo $title; ?></span></li>
    </ul>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="tabs-container">
            <ul class="nav nav-tabs" role="tablist">
                <li><a class="nav-link active" data-toggle="tab" href="#tab-1"> Share Issuance Details</a></li>
                <li><a class="nav-link" data-toggle="tab" href="#tab-share-product-fees">Share Issuance Fee</a></li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" id="tab-1" class="tab-pane active">
                    <div class="panel-body">
                    <div class="panel-title pull-right">
                    <?php if(in_array('3', $subscription_privilege)){ ?>
                        <a href="#add_share_issuance-modal" data-bind="click: initialize_edit" data-toggle="modal"  class="btn btn-default btn-sm">
                            <i class="fa fa-pencil"></i> Edit</a>
                    <?php }?>
                            <?php
                            $modalTitle = "Edit Share Issuance Info";
                            $saveButton = "Update";
                            $this->load->view('setting/shares/share_issuance/add_share_issuance');
                            ?>
                        </div>
                        <table class="table table-user-information table-stripped  m-t-md">
                            <tbody data-bind="with: product">
                                 <tr>
                                    <td colspan="6">
										<span class="col-lg-12"><h3>Product Name : <a data-bind="text:(product_name)?product_name:'None'" ></a></h3></span>
																		
							           <fieldset class="col-lg-12">     
                                            <legend>Share Prices</legend>

                                            <div class="form-group row">  
                                                <label class="col-lg-2 col-form-label"><strong>Default </strong> </label>
                                                <div class="col-lg-2">
                                                    <div data-bind="text: (default_price)? 	curr_format(default_price*1):'None'"></div>
                                                </div>
                                                <label class="col-lg-2 col-form-label"><strong>Minimum </strong> </label>
                                                <div class="col-lg-2">
                                                    <div data-bind="text: (min_price)? curr_format(min_price*1):'None'"></div>
                                                </div>
                                                <label class="col-lg-2 col-form-label"><strong>Maximum </strong></label>
                                                <div class="col-lg-2">
                                                    <div data-bind="text: (max_price)? 	curr_format(max_price*1):'None'"></div>
                                                </div>
                                            </div>
                                       </fieldset>
									   <div class="hr-line-dashed"> </div>
								       <fieldset class="col-lg-12">     
                                            <legend>Number of shares</legend>

                                            <div class="form-group row">  
                                                <label class="col-lg-2 col-form-label"><strong>Default </strong> </label>
                                                <div class="col-lg-2">
                                                    <div data-bind="text: (default_shares)?default_shares:'None'"></div>
                                                </div>
                                                <label class="col-lg-2 col-form-label"><strong>Minimum </strong> </label>
                                                <div class="col-lg-2">
                                                    <div data-bind="text: (min_shares)?min_shares :'None'"></div>
                                                </div>
                                                <label class="col-lg-2 col-form-label"><strong>Maximum </strong></label>
                                                <div class="col-lg-2">
                                                    <div data-bind="text: (default_active_period)?default_active_period:'None'"></div>
                                                </div>
                                            </div>
                                        </fieldset>
										<div class="hr-line-dashed"> </div>
									    <fieldset class="col-lg-12">     
                                            <legend>Active period</legend>

                                            <div class="form-group row">  
                                                <label class="col-lg-2 col-form-label"><strong>Default </strong> </label>
                                                <div class="col-lg-1">
                                                    <div data-bind="text: (default_active_period)?default_active_period:'None'"></div>
                                                </div>
                                                <label class="col-lg-2 col-form-label"><strong>Minimum </strong> </label>
                                                <div class="col-lg-1">
                                                    <div data-bind="text: (min_active_period)?min_active_period:'None'"></div>
                                                </div>
                                                <label class="col-lg-2 col-form-label"><strong>Maximum </strong></label>
                                                <div class="col-lg-1">
                                                    <div data-bind="text: (max_active_period)?max_active_period:'None'"></div>
                                                </div>
												 <label class="col-lg-2 col-form-label"><strong>Active Period</strong></label>
                                                <div class="col-lg-1">
                                                    <div data-bind="text: (active_period_id)?made_every_name:'None'">
													</div>
                                                </div>
                                            </div>
                                        </fieldset>
										<div class="hr-line-dashed"> </div>
                                        <fieldset class="col-lg-12" data-bind="">     
                                            <legend>Lock in period</legend>
                                            <div class="form-group row">  
                                                <label class="col-lg-2 col-form-label"><strong>Default </strong> </label>
                                                <div class="col-lg-1">
                                                    <div data-bind="text: (default_lock_in_period)? 	default_lock_in_period:'None'"></div>
                                                </div>
                                                <label class="col-lg-2 col-form-label"><strong>Minimum </strong> </label>
                                                <div class="col-lg-1">
                                                    <div data-bind="text: (min_lock_in_period)? 	min_lock_in_period:'None'"></div>
                                                </div>
                                                <label class="col-lg-2 col-form-label"><strong>Maximum </strong></label>
                                                <div class="col-lg-1">
                                                    <div data-bind="text: (max_lock_in_period)? 	max_lock_in_period:'None'"></div>
                                                </div>
                                                <label class="col-lg-2 col-form-label"><strong>Locked in</strong></label>
                                                <div class="col-lg-1">
                                                    <div data-bind="text: (lock_in_period_id)?made_every_name:'None'">
													</div>
                                                </div>
												
                                            </div>
                                        </fieldset>
										<div class="hr-line-dashed"> </div>
                                     <fieldset class="col-lg-12 ">
										  <div class="form-group row">  
											<label class="col-lg-3 col-form-label"><strong>Allow Inactive Clients Dividends</strong></label>
                                                <div class="col-lg-9">
                                                    <div data-bind="text: (allow_inactive_clients_dividends)?(allow_inactive_clients_dividends==1)?'Yes':'No':'None'"></div>
                                                </div>
										  </div>
									 </fieldset>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div role="tabpanel" id="tab-share-product-fees" class="tab-pane">
                    <div class="panel-body">
                        <div><strong>Assign Fees</strong> <?php if(in_array('1', $subscription_privilege)){ ?> <a data-toggle="modal" href="#add_share_issuance_fees-modal" class="btn btn-sm btn-default pull-right"><i class="fa fa-plus-circle"></i> Assign share </a>  <?php } ?></div>
                        <div class="table-responsive">
                                <table id="tblShare_issuance_fees" class="table table-striped table-bordered table-hover m-t-md" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Fee Name</th>
                                            <th>Amount Calculated As</th>
                                            <th>Amount/Rate</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div><!-- /.table-responsive--> 
                    </div>
                </div><!--End of Fees section-->
              
            </div>
        </div>
    </div>
</div>
<?php echo $add_share_issuance_fees_modal; ?>
 <script>
    var dTable = {};
    var savePdtDetailModel = {};
    var TableManageButtons = {};
$(document).ready(function() {
    $('form#formShare_issuance').validator().on('submit', saveData);
    $('form#formShare_issuance_fees').validator().on('submit', saveData);

    var SavePdtDetailModel = function() {
        var self = this;
        self.product = ko.observable(<?php echo json_encode($share_issuance);?>);
        self.repayment_made_every_options = ko.observableArray(<?php echo json_encode($repayment_made_every); ?>);
        self.active_period = ko.observable();
        self.active_lock_period = ko.observable();
		self.share_fees = ko.observable(<?php echo json_encode($share_fee); ?>);
        self.feename = ko.observable();
        self.initialize_edit = function(){
            edit_data(self.product(),"formShare_issuance");
           
    }
    self.display_table = function (data, click_event) {
           TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
         };

    };
    var handleDataTableButtons = function (tabClicked) {
        <?php $this->view('setting/shares/share_issuance_fees/table_js'); ?>
     };
		TableManageButtons = function () {
            "use strict";
            return {
                init: function (tblClicked) {
                    handleDataTableButtons(tblClicked);
                }
            };
        }();

     TableManageButtons.init("tab-transaction"); 


    savePdtDetailModel = new SavePdtDetailModel();
    ko.applyBindings(savePdtDetailModel);
    });
function reload_data(form_id, response){
   switch(form_id){
        case "formShare_issuance":
           savePdtDetailModel.product(response.share_issuance);
           break;
        case "formShare_application":
            share_applicationDetailModel.share_application(response.share_application);
            break;
        default:
           //nothing really to do here
           break;
   }
}
</script>
