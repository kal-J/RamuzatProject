<?php
    $start_date = date('d-m-Y', strtotime($fiscal_year['start_date']));
    $end_date = date('d-m-Y', strtotime($fiscal_year['end_date']));
?>
<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
        <div class="ibox-title">
             <ul class="breadcrumb">
                <li><a href="<?php echo site_url("dashboard"); ?>">Dashboard</a></li>
                <li><span  style="font-weight:bold; color:gray;  font-size:14px;"><?php echo $title; ?></span></li>
            </ul>
             <div class="pull-right" style="padding-left: 2%">
                <div id="reportrange" class="reportrange">
                    <i class="fa fa-calendar"></i>
                    <span>December 30, 2018 - Feb 11, 2019</span> <b class="caret"></b>
                </div>
            </div>
        </div>
           
            <div class="ibox-content">
                <div class="tabs-container">  
                    <ul class="nav nav-tabs" role="tablist">
                        <li><a class="nav-link active" data-toggle="tab" data-bind="click: display_table"  href="#tab-client_subscriptions"><i class="fa fa-money"></i> <?php echo $this->lang->line('cont_subscription');  ?></a></li>
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table"  href="#tab-member_fees"><i class="fa fa-money"></i>Membership Fees</a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="tab-member_fees" class="tab-pane">
                            <?php $this->load->view('fees/member_fees/tab_view.php'); ?>
                        </div>
                        <div id="tab-client_subscriptions" class="tab-pane active">
                            <?php $this->load->view('fees/subscriptions/tab_view.php'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var dTable = {};
    var TableManageButtons = {};
    var feesDetailModel = {};
    $(document).ready(function () {
        // $(".select2able").select2({
        //     allowClear: true
        // });
        start_date = moment('<?php echo $start_date; ?>', "DD-MM-YYYY"); end_date = moment('<?php echo $end_date; ?>', "DD-MM-YYYY");

        $("#subscription_selects").select2({allowClear: true, dropdownParent: $("#add_client_subscription-modal") });
        $("#membership_selects").select2({allowClear: true, dropdownParent: $("#attach_member_fees-modal") });

            var GeneralTabs = function () {
                var self = this;
                
                self.display_table = function (data, click_event) {
                    TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
                };
            };
            var general_tabs = new GeneralTabs();
 
        var MemberFee = function () {
            var self = this;
            self.selected_fee = ko.observable();
        };
        var MemberFee1 = function () {
            var self = this;
            self.selected_fee1 = ko.observable();
        };

        var FeesDetailModel = function () {
            var self = this;

            self.initialize_edit = function () {
                edit_data();
            };
            self.display_table = function (data, click_event) {
                TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
            };
            self.members = ko.observableArray(<?php echo (isset($sorted_clients))?json_encode($sorted_clients):''; ?>);
            self.User = ko.observable();
            self.member = ko.observable();
            self.end_date=  ko.observable();
            self.checkbox = ko.observable();
            self.subscription_plan = ko.observableArray();
            self.amount_payable = ko.observable();
            self.next_payment_date = ko.observable();

            self.oncheck= function() {
                    return true;
            };

          
             self.subscription_plans = ko.observable(<?php echo isset($subscription_plans)?json_encode($subscription_plans):"{}"; ?>);
            self.get_last_subscription_date =ko.computed(function(){
                var dataarray={}; 
                if (self.User()) {
              var default_date = moment(self.User.date_registered>"<?php echo $fiscal_year['start_date'];?>"?self.User.date_registered:"<?php echo $fiscal_year['start_date']; ?>", "YYYY-MM-DD").format("DD-MM-YYYY");
                  var dataobj=dataobj2={};
                  dataobj['client_id']=self.User().id;
                  dataobj2['id']=self.User().subscription_plan_id;
                  get_user_subscription(dataobj2);
                  get_last_subscription(dataobj,default_date);
                  get_user_savings_accounts(self.User().id);

                  dataarray['amount_payable']=self.amount_payable();
                  dataarray['next_payment_date']=self.next_payment_date();
                }
                return dataarray;
            });
            self.transaction_channels = ko.observableArray(<?php echo json_encode($tchannel); ?>);
                self.trans_channel = ko.observable();

            self.payment_modes = ko.observableArray(<?php echo json_encode($payment_modes); ?>);
                self.payment_mode = ko.observable();
            self.available_member_fees = ko.observableArray(<?php echo (!empty($available_member_fees) ? json_encode($available_member_fees) : '') ?>);

            self.applied_member_fee = ko.observableArray([new MemberFee()]);
            self.addMemberFee = function () {
                self.applied_member_fee.push(new MemberFee());
            };
            self.removeMemberFee = function (selected_member) {
                self.applied_member_fee.remove(selected_member);
            };

            self.fee_paid = ko.observable();

            self.attach_member_fees = ko.observableArray();

             self.get_attached_fees =ko.computed(function(){
                if (self.member()) {
                  var dataobj={};
                  dataobj['id']=self.member().id;
                  get_attached_fees(dataobj);
                  get_user_savings_accounts2(dataobj);
                }
            });

            self.attach_member_fee = ko.observableArray([new MemberFee1()]);
            self.addMemberFee1 = function () {
                self.attach_member_fee.push(new MemberFee1());
            };
            self.removeMemberFee1 = function (selected_member) {
                self.attach_member_fee.remove(selected_member);
            };

            //=================================================================
           
                self.sub_fee_paid = ko.observable();
                self.available_saving_accounts = ko.observableArray();
             
               
                   
        };
        feesDetailModel = new FeesDetailModel();
        ko.applyBindings(feesDetailModel);
   

        $('#formApplied_member_fees').validate({submitHandler: saveData2});
        $('#formApplied_member_fees1').validate({submitHandler: saveData2});
        $('#formReverseClient_subscription').validate({submitHandler: saveData2});
        $('#formClient_subscription').validator().on('submit', saveData); 
        $('#formClient_subscription1').validator().on('submit', saveData); 

             daterangepicker_initializer();
          
        //contact javascript 
        var handleDataTableButtons = function (tabClicked) {
    <?php $this->view('fees/subscriptions/table_js'); ?>
    <?php $this->view('fees/member_fees/table_js'); ?>

        };
        TableManageButtons = function () {
            "use strict";
            return {
                init: function (tabClicked) {
                    handleDataTableButtons(tabClicked);
                }
            };
        }();
        TableManageButtons.init("tab-client_subscriptions");
        
    });

    function reload_data(form_id, response) {
        switch (form_id) {
            case "formApplied_member_fees1":    
                dTable['tblApplied_member_fees'].ajax.reload(null, true);
             break;
             case "formApplied_member_fees":    
                dTable['tblApplied_member_fees'].ajax.reload(null, true);
             break;
             case "formClient_subscription1":    
                dTable['tblClient_subscription'].ajax.reload(null, true);
             break;
             
            case "formClient_subscription":    
                dTable['tblClient_subscription'].ajax.reload(null, true);
             break;
            case "formReverseClient_subscription":    
                dTable['tblClient_subscription'].ajax.reload(null, true);
             break;        
            default:
                //nothing really to do here
                break;
        }
    }
 //getting subscription data
    function get_last_subscription(id,default_date) {
        var url = "<?php echo site_url("client_subscription/get_max"); ?>";
        $.ajax({
            url: url,
            data: id,
            type: 'POST',
            dataType:'json',
            success:function (response) {
                 feesDetailModel.amount_payable(typeof feesDetailModel.subscription_plan()!=='undefined'?feesDetailModel.subscription_plan().amount_payable:0);
                   if(response.data!==null){
                       feesDetailModel.next_payment_date(moment(response.data.subscription_date, 'YYYY-MM-DD').add(typeof feesDetailModel.subscription_plan()!=='undefined'?feesDetailModel.subscription_plan().repayment_frequency:0, (typeof feesDetailModel.subscription_plan()!=='undefined'?feesDetailModel.subscription_plan().made_every_name:"days").toString().toLowerCase().replace("(","").replace(")","")).format("DD-MM-YYYY"));
                       
                   }else{
                       
                       feesDetailModel.next_payment_date(default_date);
             }

            },
            fail:function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
                }
            });
    }
  function get_user_subscription(id) {
        var url = "<?php echo site_url("subscription_plan/get_user_sub"); ?>";
        $.ajax({
            url: url,
            data: id,
            type: 'POST',
            dataType:'json',
            success:function (response) {
                feesDetailModel.subscription_plan(response.data);
            },
            fail:function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
                }
            });
    }
    
    function get_user_savings_accounts(id) {
        var url = "<?php echo site_url("client_subscription/get_accounts"); ?>";
        $.ajax({
            url: url,
            data: {id:id},
            type: 'POST',
            dataType:'json',
            success:function (response) {
                feesDetailModel.available_saving_accounts(response.data);
            },
            fail:function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
                }
            });
    }
    function get_user_savings_accounts2(id) {
        var url = "<?php echo site_url("client_subscription/get_accounts"); ?>";
        $.ajax({
            url: url,
            data: id,
            type: 'POST',
            dataType:'json',
            success:function (response) {
                feesDetailModel.available_saving_accounts(response.data);
            },
            fail:function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
                }
            });
    }
    function get_attached_fees(id) {
        var url = "<?php echo site_url("client_subscription/get_attached_fees"); ?>";
        $.ajax({
            url: url,
            data: id,
            type: 'POST',
            dataType:'json',
            success:function (response) {
                feesDetailModel.attach_member_fees(response.data);
            },
            fail:function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
                }
            });
    }
     function display_footer_sum(api, columns) {
        $.each(columns, function (key, col) {
            //var page_total = api.column(col, {page: 'current'}).data().sum();
            var overall_total = api.column(col).data().sum();
            $(api.column(col).footer()).html((overall_total===parseInt(100))?overall_total:'<font color="red">'+overall_total+'    {Percentage is less than 100}</font>');
           
        });
    }
    

    //for dateranger picker
    function handleDateRangePicker(startDate, endDate) {
                
        if(typeof displayed_tab !== 'undefined'){
                start_date = startDate;
                end_date = endDate;
                TableManageButtons.init(displayed_tab);
            }
    }

</script>
