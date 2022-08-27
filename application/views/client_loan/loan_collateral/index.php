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
                        <!-- <li>
                        <a class="nav-link active" data-toggle="tab" data-bind="click: display_table"  href="#tab-collaterals">Member Collaterals</a>
                        </li>
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table"  href="#tab-loan_collaterals">Loan Collaterals</a></li> -->

                        <li><a class="nav-link active" data-toggle="tab" data-bind="click: display_table"  href="#tab-active_loan_collaterals">Active</a></li>
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table"  href="#tab-in_arrears_loan_collaterals">In Arrears</a></li>
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table"  href="#tab-pending_loan_collaterals">Pending</a></li>
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table"  href="#tab-approved_loan_collaterals">Approved</a></li>
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table"  href="#tab-closed_loan_collaterals">Closed</a></li>
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table"  href="#tab-reclaimed_loan_collaterals">Reclaimed</a></li>
                    </ul>
                    <div class="hr-line-dashed"></div>
                    <div class="tab-content">
                        <?php //$this->view('client_loan/loan_collateral/loan_collaterals_tab_view'); ?>
                        <?php //$this->view('client_loan/loan_collateral/collaterals_tab_view'); ?>

                        <?php $this->view('client_loan/loan_collateral/active/tab_view'); ?>
                        <?php $this->view('client_loan/loan_collateral/pending/tab_view'); ?>
                        <?php $this->view('client_loan/loan_collateral/approved/tab_view'); ?>
                        <?php $this->view('client_loan/loan_collateral/closed/tab_view'); ?>
                        <?php $this->view('client_loan/loan_collateral/reclaimed/tab_view'); ?>
                        <?php $this->view('client_loan/loan_collateral/in_arrears/tab_view'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <div id="modals">
    </div>

<script>
    var dTable = {};
    var loanCollateralModel = {};
    var TableManageButtons = {};
    var displayed_tab = '';
    var start_date, end_date,drp;
    $(document).ready(function () {
        let modals = '';
        let loan_collateral_modals = ['formMember_collateral_active_loan', 'formMember_collateral_approved_loan', 'formMember_collateral_pending_loan', 'formMember_collateral_in_arrears_loan'];
        loan_collateral_modals.forEach((lm, index) => {
            modals += `
            <div class="modal fade" id="edit-loan-collateral${index}" tabindex="-1" role="dialog"
        aria-labelledby="editLoanCollateralModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <form method="post" id=${lm} class="${lm} formValidate"
                    action="<?php echo site_url('member_collateral/update'); ?>">

                    <div class="modal-header">
                        <h5 class="modal-title" id="editLoanCollateralModalTitle">Edit Loan Collateral</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="item_value">Item Value</label>
                            <input name="item_value" required type="number" min="0" class="form-control"
                                id="collateral_item_value">
                            <input name="id" type="hidden" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" required class="form-control" id="collateral_description"
                                rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="file_name">File</label>
                            <input name="file_name" class="form-control" type="file">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary save_data">Save</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

            `;
        });

        $('#modals').html(modals);
        
        loan_collateral_modals.forEach((lm, index) => {
            $('form#' + lm).validator().on('submit', saveData);
        });

        var LoanCollateralModel = function () {
            var self = this;
            self.display_table = function (data, click_event) {
                displayed_tab = $(click_event.target).prop("hash").toString().replace("#", "");
                TableManageButtons.init(displayed_tab);
            };
         
        };

        loanCollateralModel = new LoanCollateralModel();

        ko.applyBindings(loanCollateralModel);

        start_date = moment('<?php echo $start_date; ?>', "DD-MM-YYYY"); end_date = moment('<?php echo ($end_date>date("d-m-Y"))?date("d-m-Y"):$end_date; ?>', "DD-MM-YYYY");

        daterangepicker_initializer(false, "<?php echo '01-01-2012'; ?>", "<?php echo ($end_date>date("d-m-Y"))?date("d-m-Y"):$end_date; ?>");
        var handleDataTableButtons = function (tabClicked) {
          <?php //$this->view('client_loan/loan_collateral/loan_collaterals_table_js'); ?>
          <?php //$this->view('client_loan/loan_collateral/collaterals_table_js'); ?>
          <?php $this->view('client_loan/loan_collateral/active/table_js'); ?>
          <?php $this->view('client_loan/loan_collateral/pending/table_js'); ?>
          <?php $this->view('client_loan/loan_collateral/approved/table_js'); ?>
          <?php $this->view('client_loan/loan_collateral/closed/table_js'); ?>
          <?php $this->view('client_loan/loan_collateral/reclaimed/table_js'); ?>
          <?php $this->view('client_loan/loan_collateral/in_arrears/table_js'); ?>
        };

        TableManageButtons = function () {
            "use strict";
            return {
                init: function (tblClicked) {
                    handleDataTableButtons(tblClicked);
                }
            };
        }();

        TableManageButtons.init("tab-active_loan_collaterals");

    });

    function reload_data(formId, reponse_data) {
        switch (formId) {
           
        }
    }

    function consumeDtableData(dTableData) {
        var theData = dTableData.data;
        if (theData.length > 0) {
            
            
            
        }
    }

    
   
    
    function set_selects(data) {
       
    }

    function handleDateRangePicker(startDate, endDate) {
        start_date = startDate;
        end_date = endDate;
        if(typeof displayed_tab !== 'undefined'){
            TableManageButtons.init(displayed_tab);
        }
    }

    let handle_btn_del_member_collateral = (id) => {
        $.ajax({
            url : "<?php echo site_url('member_collateral/delete') ?>",
            type: "POST",
            data : {
                id : id
            },
            async : true,
            success: function(response, textStatus, jqXHR) {
                dTable['tblApplied_collaterals'].ajax.reload(null, true);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
    }

    let handle_btn_edit_member_collateral = (full ={}) => {
        $('#collateral_id').val(full.id);
        $('#collateral_item_name').val(full.item_name ? full.item_name : '');
        $('#collateral_item_value').val(full.item_value);
        $('#collateral_description').val(full.description);

        $('#edit-loan-collateral').modal('show');
    }

</script>
