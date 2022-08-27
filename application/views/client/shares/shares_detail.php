<?php
$start_date = date('d-m-Y', strtotime($fiscal_year['start_date']));
$end_date   = date('d-m-Y', strtotime($fiscal_year['end_date']));
?>
<div class="ibox-title">
     <ul class="breadcrumb">
        <li><a href="<?php echo site_url("dashboard"); ?>">Dashboard</a></li>
        <li><a href="<?php echo site_url("shares"); ?>">Share Accounts (Holders)</a></li>
        <li><span  style="font-weight:bold; color:gray;  font-size:14px;"><?php echo $title; ?></span></li>
    </ul>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="tabs-container">
        <ul class="list-unstyled">
                <li class="dropdown pull-right">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
                        <i class="fa fa-modx"></i> Actions </a>
                    <ul class="dropdown-menu" x-placement="bottom-start" style="position: absolute; top: 39px; left: 0px; will-change: top, left;">
                        <li><a href="#" data-toggle="modal" data-target="#change_states_modal" class="btn btn-sm" data-bind="click:function(){set_action(5);}"><i class="fa fa-hourglass-start"></i> Pend</a></li>
                        <li><a href="#" data-toggle="modal" data-bind="click: initialize_edit" data-target="#approve-modal" class="btn btn-sm" data-bind="click:function(){set_action(7);}"><i class="fa fa-line-chart"></i> Approve</a></li>
                        <!-- <li><a href="#" data-toggle="modal" data-target="#change_states_modal" class="btn btn-sm" data-bind="click:function(){set_action(17);}"><i class="fa fa-bars"></i>  Dormant</a></li> -->
                        <li><a href="#" data-toggle="modal" data-target="#change_states_modal" class="btn btn-sm"  data-bind="click:function(){set_action(12);}"><i class="fa fa-lock"></i>  Lock</a></li>
                        <li><a href="#" data-toggle="modal" data-target="#change_states_modal" class="btn btn-sm"  data-bind="click:function(){set_action(18);}"> <i class="fa fa-trash"></i> Delete</a></li>
                    </ul>
                </li>
            </ul>
             <ul class="nav nav-tabs" role="tablist">
                <li><a class="nav-link active" data-toggle="tab" data-bind="click: display_table" href="#tab-overview"><i class="fa fa-address-book-o"></i>Share Overview</a></li>
                <li id="hide_template_btn"><a class="nav-link" data-toggle="tab" data-bind="click: display_table"  href="#tab-transaction"><i class="fa fa-money"></i>Transactions</a></li>
                <!-- <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table" href="#tab-apply_share_fee"><i class="fa fa-bars"></i>Share fees payment</a></li> -->
                <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table" href="#tab-dividends"><i class="fa fa-bars"></i>Dividend Paid </a></li>
                 <!--  <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table" href="#tab-apply_share_fee"><i class="fa fa-bars"></i>Share fees payment</a></li>
                <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table" href="#tab-dividends"><i class="fa fa-bars"></i>Dividend payments</a></li> -->
              </ul>
              <div class="tab-content">
                <div role="tabpanel" id="tab-overview" class="tab-pane active">
                    <div class="panel-body">
                    <?php if(in_array('3', $share_privilege)){ ?>
                        <div class="panel-title pull-right">
                            <a href="#add_share_account-modal" data-bind="click: initialize_edit" data-toggle="modal" class="btn btn-default btn-sm">
                                <i class="fa fa-pencil "></i> Edit</a>
                        </div>
                        <?php } ?>
                        <table class="table table-user-information  table-bordered table-stripped  m-t-md">
                            <tbody data-bind="with:share_details">
                                <tr>
                                    <td><strong>Account Name</strong></td>
                                    <!-- ko if: group_name -->
                                    <td colspan="5" data-bind="text: group_name"></td>
                                    <!-- /ko -->
                                    <!-- ko ifnot: group_name -->
                                    <td colspan="5"><a data-bind="text: salutation+' '+firstname+' '+lastname+' '+othernames,attr: {href:'<?php echo site_url("member/member_personal_info"); ?>/'+member_id}" ></a></td>
                                    <!-- /ko -->
                                </tr>
                                <tr>
                                    <td><strong>Account Number</strong></td>
                                    <td colspan="3" data-bind="text: share_account_no"></td> 
                                    <td><strong>Category</strong></td>
                                    <td  data-bind="text:issuance_name"></td>
                                </tr>
                                <tr> 
                                    <td><strong>Total Amount paid</strong></td>
                                    <td colspan="3" data-bind="text: curr_format(total_amount)"></td>
                                    <td><strong>Total Shares</strong></td>
                                    <td  data-bind="text: parseFloat(total_amount)/parseFloat(price_per_share)"></td>
                                <tr>
                                     <td><strong>Share State</strong></td>
                                    <td colspan="3" data-bind="text: (state_id)?((state_id==6)?'Approved':((state_id==12)?'Locked':((state_id==0)?'Deleted':((state_id==5)?'Pending':((state_id==17)?'Dormant':((state_id==7)?'Active':'Undefined')))))):'None'"></td>
                                    <td><strong>Comment</strong></td>
                                    <td  data-bind="text: narrative"></td> 
                                </tr>
                            </tbody>
                          </table>
                        <br>
                    </div>
                </div>
                <?php //$this->load->view('shares/fees/tab_view'); ?>
                <div id="tab-dividends" class="tab-pane">
                    <div class="panel-body">

                    <?php $this->load->view('user/member/dividends/tab_view.php'); ?>
                    
                        </div>
                </div>
            <div role="tabpanel" id="tab-transaction" class="tab-pane tabparent" >
                <?php $this->load->view('shares/transaction/transaction_tab'); ?>
                <?php $this->load->view('shares/transaction/reverse_modal'); ?>
            </div>
             </div>
        </div>
    </div>
</div>

<?php $this->load->view('shares/fees/add_modal'); ?>
<?php $this->load->view('shares/share_account/states/pending/add_modal'); ?>
<?php $this->load->view('shares/share_account/states/change_state_modal'); ?>
<?php //$this->load->view('shares/share_account/states/active/approve_modal'); ?>

<script>
    var dTable = {};
    var viewModel = {};
    var TableManageButtons = {};
    var start_date, end_date;
$(document).ready(function() {
    $(".select2able").select2({
    allowClear: true
    });
    start_date = moment('<?php echo $start_date; ?>', "DD-MM-YYYY"); end_date = moment('<?php echo $end_date; ?>', "DD-MM-YYYY");


    $('#dddd').on('click', function () {
                get_product_details();
        
    });

    var ShareFee = function () {
        var self = this;
        self.selected_fee = ko.observable();
    };
    $('form#formShares').validator().on('submit', saveData);
    $('form#formChange_state').validate({submitHandler: saveData2});
    $('form#formShares_state').validate({submitHandler: saveData2});
    $('form#formApplied_share_fee').validate({submitHandler: saveData2});
    $('form#formReverseShare_transaction').validate({submitHandler: saveData2});
    $('form#formReverseShare_transaction').validate({submitHandler: saveData2});


    var ViewModel = function(){
        var self = this;
        self.display_table = function (data, click_event) {
            TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
        };

        self.available_share_fees = ko.observableArray(<?php echo (!empty($available_share_fees) ? json_encode($available_share_fees) : '') ?>);
        self.share_issuance = ko.observableArray([<?php echo json_encode($share_issuances); ?>]);
        self.share_details = ko.observable(<?php echo json_encode($share_details); ?>);
        self.applied_share_fee = ko.observableArray([new ShareFee()]);
        self.issuance = ko.observable();
        self.account_state = ko.observable();
        self.action_msg = ko.observable();
        self.new_account_no = ko.observable();
        self.addShareFee = function () {
            self.applied_share_fee.push(new ShareFee());
        };
        self.removeShareFee = function (selected_member) {
            self.applied_share_fee.remove(selected_member);
        };

        self.initialize_edit = function () {
                //edit_data(self.share_details(),"formShares_state");
                 edit_data(self.share_details(), "formShares");
               
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
        $this->view('shares/fees/table_js'); 
        $this->view('user/member/dividends/table_js');
        $this->load->view('shares/transaction/transaction_js');
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

    daterangepicker_initializer();
    TableManageButtons.init("tab-apply_share_fee");
    });
function reload_data(form_id, response){
    switch(form_id){
        case "formApplied_share_fee":
            viewModel.share_fee_application(response.share_fee_application);
            break;
        case "formReverseShare_transaction":
            dTable['tblShare_transaction'].ajax.reload(null, true);
            break; 
        case "formChange_state":
            viewModel.share_details(response.share_details);
            break;
        case "formShares":
            viewModel.share_details(response.share_details);
            break;

        default:
            //nothing really to do here
            break;
    }
}
function get_product_details(data) {
        var url = "<?php echo site_url("shares/get_product"); ?>";
        $.ajax({
            url: url,
            data: data,
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                //populate the observables
                viewModel.share_issuance(response.share_issuances);
            },
            fail: function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }

    const handlePrint = () => {

     if (typeof moment(start_date,'X').format('YYYY-MM-DD') !== 'undefined') {
            window.open("<?php echo site_url('shares/share_acc_transactions/').$acc_id."/"; ?>" +
            moment(start_date,'X').format('YYYY-MM-DD') + "/" + moment(end_date,'X').format('YYYY-MM-DD') , '_blank');
    }
}

// hides the download template and bulk transaction button from the UI.
$("#hide_template_btn").on("click", function() {
    $("#btn1").hide();
    $("#btn2").hide();
    $("#btn3").css('display','none');
    $("#btn4").css('display','none');
    $("#btn5").css('display','none');
    
});

</script>
