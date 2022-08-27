<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-content">
                <div class="tabs-container">
                    <ul class="nav nav-tabs" role="tablist">
                        <li><a class="nav-link active" data-toggle="tab" href="#tab-details"><i class="fa fa-list-alt"></i> Payment Details</a></li> 
                        <li><a class="nav-link" data-toggle="tab" href="#tab-payments"><i class="fa fa-money"></i> Payment items</a></li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" id="tab-details" class="tab-pane active">
                            <?php $this->load->view('accounts/bill/payment/detail_view'); ?>
                        </div>
                        <div role="tabpanel" id="tab-payments" class="tab-pane">
                            <?php $this->load->view('accounts/bill/payment/tab_view'); ?>
                        </div>
                    </div>
                </div>
        <?php //$this->load->view('accounts/bill/add_modal'); ?>
            </div>
        </div>
    </div>
</div>
<script>
    var dTable = {};
    var viewModel = {};
    var TableManageButtons = {};
    $(document).ready(function () {
        $('form#formBill_line').validate({submitHandler: saveData2});
        var ViewModel = function () {
            var self = this;
            self.bill_payment_detail = ko.observable(<?php echo json_encode($bill_payment_detail); ?>);
            self.initialize_edit = function () {
                edit_data(self.bill_detail(), "formBill");
            };
        };

        viewModel = new ViewModel();
        ko.applyBindings(viewModel);
        var handleDataTableButtons = function () {
            <?php $this->view("accounts/bill/payment/table_js"); ?>
        };

        TableManageButtons = function () {
            "use strict";
            return {
                init: function () {
                    handleDataTableButtons();
                }
            };
        }();

        TableManageButtons.init();

    });
    function set_selects(data) {
        edit_data(data, 'formBill');
    }
</script>