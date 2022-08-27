<div class="row">
    <div class="col-lg-12">
        <div role="tabpanel" id="tab-details" class="tab-pane">
            <div class="panel-body">
                <h3 class="heading"> <i class="fa fa-hashtag text-navy"></i> <?php echo $tax_rate_source['source']; ?>: <?php echo $tax_rate_source['description']; ?></h3>
                <div class="tabs-container">
                    <ul class="nav nav-tabs" role="tablist">
                        <li><a class="nav-link active" data-toggle="tab" href="#tab-tax_rate">Rates</a></li>
                        <li><a class="nav-link" data-toggle="tab" href="#tab-tax_application"> Tax application</a></li>
                    </ul>
                    <div class="tab-content">
                        <?php $this->view('setting/tax_rate/tab_view'); ?>
                        <?php $this->view('setting/tax_application/tab_view'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $this->view('setting/tax_rate/add_modal'); ?>
    <?php $this->view('setting/tax_application/add_modal'); ?>
</div>
<script>
    var dTable = {};
    var viewModel = {};
    $(document).ready(function () {
        $('form#formTax_rate').validator().on('submit', saveData);
        $('form#formTax_application').validator().on('submit', saveData);
        /*********************************** Page Data Model (Knockout implementation) *****************************************/
        
        var IncomeSource = function () {
            var self = this;
            self.selected_income_source = ko.observable();
        };
        var ViewModel = function () {
            var self = this;

            self.available_income_sources = ko.observableArray(<?php echo (!empty($available_income_sources) ? json_encode($available_income_sources) : '[]') ?>);
            self.tax_applied_to_income_sources = ko.observableArray([new IncomeSource()]);
            self.addIncomeSource = function () {
                self.applied_tax_fee.push(new TaxFees());
            };
            self.removeIncomeSource = function (selected_tax) {
                self.applied_tax_fee.remove(selected_tax);
            };
        };
        viewModel = new ViewModel();
       ko.applyBindings(viewModel);

        var handleDataTableButtons = function () {
<?php $this->view('setting/tax_rate/tax_rate_js'); ?>
<?php $this->view('setting/tax_application/table_js'); ?>
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
</script>