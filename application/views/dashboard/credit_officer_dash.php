<?php
$start_date = date('d-m-Y', strtotime($fiscal_year['start_date']));
$end_date = date('d-m-Y', strtotime($fiscal_year['end_date']));

if(empty($fiscal_all)){ ?>
<br>
<div class ="alert alert-danger"><center><b>Please Set your Fiscal Year to continue </b> &nbsp; &nbsp;&nbsp;<a data-toggle="modal" href="#fiscal-modal" class="btn btn-sm btn-flat btn-success">Set Fiscal Year</a></center></div>
<?php } else { 
if(empty($fiscal_active)){ 
$this->view('dashboard/activate_fiscal_year');
 } else { ?>

<div class="row">
<div class="col-lg-8">
</div>
   <div class="col-lg-4">
    <div id="reportrange" class="reportrange pull-right">
        <i class="fa fa-calendar"></i>
        <span>December 30, 2018 - Feb 11, 2019</span> <b class="caret"></b>
    </div>
    </div>
</div>
  
 <div class="row">
 <div class="col-lg-3">
        <div class="ibox" style="background:transparent; padding:0px; ">
            <div class="ibox-content" style=" padding:5px;" >
            <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="border-right-style: dotted;">
            <h5>Clients</h5>
                <div class ="row">
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" data-bind="with: client_count_active">
                    <a class="btn btn-default btn-sm" style="width:100%;" href="<?php echo site_url("member"); ?>">
                        <span style="font-size:10px;">Active<br><span class="badge bg-green" data-bind="text: client_count">0</span></span>
                    </a>  
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" data-bind="with: client_count_inactive">
                    <a class="btn btn-default btn-sm" style="width:100%;" href="<?php echo site_url("member"); ?>">
                        <span style="font-size:10px;">Inactive<br><span class="badge bg-red" data-bind="text: client_count">0</span></span>
                    </a>
                </div>
            </div>
            </div>
           
            </div>
          </div>
        </div>
    </div>
    <div class="col-lg-9">

        <div class="ibox" style="background-color:transparent;">
            <div class="ibox-content " style=" padding:5px;">
            <h5>Loans <span class="label label-default float-right" style="font-weight:bold; font-size:11px;" data-bind="text:start_datev() +'   -   '+ end_datev()"></span> </h5>
                <div class ="row">
                <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2"  data-bind="with: loan_count_active">
                    <a class="btn btn-default btn-sm" style="width:100%;" href="<?php echo site_url("client_loan"); ?>">
                        <span style="font-size:10px;">Active Loans<br><span class="badge bg-green" data-bind="text: loan_count">0</span>
                        </span>
                    </a>
                </div>
                <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2"  data-bind="with: loan_count_partial">
                    <a class="btn btn-default btn-sm" style="width:100%;" href="<?php echo site_url("client_loan"); ?>">
                        <span style="font-size:10px;">Partial <br><span class="badge bg-warning" data-bind="text: loan_count">0</span>
                        </span>
                    </a>
                </div>
                <?php if($org['loan_app_stage']==0){ ?>
                <div class="col-xs-6 col-sm-2 col-md-1 col-lg-2"  data-bind="with: loan_count_pend_approval">
                    <a class="btn btn-default btn-sm" style="width:100%;" href="<?php echo site_url("client_loan"); ?>">
                        <span style="font-size:10px;">Pending Approval<br><span class="badge bg-yellow" data-bind="text: loan_count">0</span>
                        </span>
                    </a>
                </div>
                <?php } if(($org['loan_app_stage']==0)||($org['loan_app_stage']==1)){ ?>
                <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2"  data-bind="with: loan_count_approved">
                    <a class="btn btn-default btn-sm" style="width:100%;" href="<?php echo site_url("client_loan"); ?>">
                        <span style="font-size:10px;">Approved<br><span class="badge bg-navy" data-bind="text: loan_count">0</span>
                        </span>
                    </a>
                </div>
                <?php } ?>
                <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2"  data-bind="with: loan_count_writeoff">
                    <a class="btn btn-default btn-sm" style="width:100%;" href="<?php echo site_url("client_loan"); ?>">
                        <span style="font-size:10px;">Written Off<br><span class="badge bg-red" data-bind="text: curr_format(loan_count)">0</span>
                        </span>
                    </a>
                </div>
                <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2"  data-bind="with: loan_count_arrias">
                    <a class="btn btn-default btn-sm" style="width:100%;" href="<?php echo site_url("client_loan"); ?>">
                        <span style="font-size:10px;">In arrears<br><span class="badge bg-brown" data-bind="text: curr_format(loan_count)">0</span>
                        </span>
                    </a>
                </div>
            </div>
           
            </div>
        </div>
    </div>
 </div>
<div class="row">
    <div class="col-lg-12">
       
    </div>
</div>
 
<!-- ============================================================= Dashboard ========================================== -->
<div class="row">
<div class="col-lg-8">
<div class="row">
<div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-title">
               <span class="label label-default float-right" style="font-weight:bold; font-size:11px;" data-bind="text:start_datev() +'   -   '+ end_datev()"></span> 
                <h5>Balance Changes <span class="text-success"> ( Clients )</span></h5>
            </div>
            <div class="ibox-content">
            <div class="row">
              <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4" data-bind="with:amount_paid">
                <span style ="font-weight:bold; font-size:11px;" >Total Principal Collected </span>
               <h4 class="no-margins"><span style ="font-weight:bold;" class="text-success" data-bind="text:already_principal_amount?curr_format(round(already_principal_amount,2)*1):0">0</span></h4>
               </div>
               <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
               <span style ="font-weight:bold; font-size:11px;" >Total Principal Disbursed </span>
               <h4 class="no-margins"><span style ="font-weight:bold;" class="text-success" data-bind="text:principal_disbursed()?curr_format(round(principal_disbursed(),2)*1):0">0</span></h4>
               </div>
               <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
               <span style ="font-weight:bold; font-size:11px;" >Total Gross loan portfolio</span>
               <h4 class="no-margins"><span style ="font-weight:bold;" class="text-success" data-bind="text:gross_loan_portfolio()?curr_format(round(gross_loan_portfolio(),2)*1):0">0</span></h4>
               </div>
              <!--  <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
               <span style ="font-weight:bold; font-size:11px;" >Change in Portfolio</span>
               <h4 class="no-margins"><span style ="font-weight:bold;" class="text-success" data-bind="text:change_in_Portfolio()?curr_format(change_in_Portfolio()*1):0">0</span></h4>
               </div> -->
            </div>
            </div>
        </div>
    </div>
        <?php 
        if(in_array('4', $modules)){
        ?>
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-title">
               <span class="label label-default float-right" style="font-weight:bold; font-size:11px;" data-bind="text:start_datev() +'   -   '+ end_datev()"></span> 
                <h5>Active Loans status report </h5>
            </div>
            <div class="ibox-content">
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
      <div class="table-responsive">
          <table class="table table-striped table-hover dataTables-example margin bottom" id="tblActive_client_loan" style="width: 100%">
            <thead>
                <tr>
                  <th class="small"><b>Ref #</b></th>
                  <th class="small"><b>Client Name</b></th>
                  <th class="small"><b>Paid Amount</b> </th>
                  <th class="small"><b>Remaining bal</b> </th>
                  <th class="small"><b>Next Pay Date</b></th>
                  <th class="small"><b>Due Date</b></th>
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
    <?php
    }
     ?>


<div class="col-lg-12">
</br>

    </div>
    
</div>
</div>
<div class="col-lg-4">
<div class="ibox ">
    <div class="ibox-title">
    <span class="label label-default float-right" style="font-weight:bold; font-size:11px;" data-bind="text:start_datev() +'   -   '+ end_datev()"></span> 
        <h5>Loan Portfolio  </h5>
    </div>
    <div class="ibox-content">
    <table class="table table-sm">
        <tbody >
            <tr data-bind="with:loan_count_pend_approval">
                <th scope="row"  style ="font-weight:bold; font-size:11px;" >Portfolio pending approval </th>
                <td><h4 class="no-margins"><span style ="font-weight:bold;" class="text-success" data-bind="text:requested_amount?curr_format(round(requested_amount,2)*1):0">0</span></h4> 
               </td>
            </tr>
            <tr data-bind="with:unpaid_penalty">
                <th scope="row"  style ="font-weight:bold; font-size:11px;" >Unpaid Penalty </th>
                <td><h4 class="no-margins"><span style ="font-weight:bold;" class="text-success" data-bind="text:penalty_total?curr_format(round(penalty_total,2)):0">0</span></h4> 
               </td>
            </tr>
        </tbody>
    </table>
    </div>
</div>

<div class="ibox ">
        <div class="ibox-title">
        <span class="label label-default float-right" style="font-weight:bold; font-size:11px;" data-bind="text:start_datev() +'   -   '+ end_datev()"></span> 
            <h5>Risk Aging Analysis  </h5>
        </div>
        <div class="ibox-content">
              <table class="table table-sm">
        <tbody >
            <tr >
                <th scope="row" style ="font-weight:bold; font-size:11px;">Portifolio at Risk</th>
                <td><h4 class="no-margins"><span style ="font-weight:bold;" class="text-success"  data-bind="text:portfolio_at_risk()?round(portfolio_at_risk(),2)+'%':0+'%'">0</span></h4> 
               </td>
            </tr>
            <tr >
                <th scope="row" style ="font-weight:bold; font-size:11px; ">Value at Risk  </th>
                <td><h4 class="no-margins"><span style ="font-weight:bold;" class="text-success" data-bind="text:value_at_risk()?curr_format(round(value_at_risk(),2)*1):0">0</span></h4> 
               </td>
            </tr>
            <tr >
                <th scope="row" style ="font-weight:bold; font-size:11px;" >Interest in suspense </th>
                <td><h4 class="no-margins"><span style ="font-weight:bold;" class="text-success"  data-bind="text:intrest_in_suspense()?curr_format(round(intrest_in_suspense(),2)*1):0">0</span></h4> 
               </td>
            </tr>
        </tbody>
    </table>
           
        </div>
           
        </div>

</div>
</div>
 <!--  ===================end of row two ================ -->
 <div class="row">

</div>

<br>
 <?php
   }
 }
$this->view('dashboard/add_fiscal_modal');
  ?>
<script type="text/javascript">

    var dTable = {};
    var TableManageButtons = {};
    var displayed_tab = '';
    var start_date, end_date;
    var dashModel = {};
    $(document).ready(function () {
        $('form#formFiscal_year').validator().on('submit', msaveData);
        $('form#formFiscal_year2').validator().on('submit', msaveData);
        start_date = moment('<?php echo $start_date; ?>', "DD-MM-YYYY"); end_date = moment('<?php echo $end_date; ?>', "DD-MM-YYYY");
        var DashModel = function () {
            var self = this;
            self.start_datev = ko.observable();
            self.end_datev = ko.observable();
           
            self.savings_sums = ko.observable();
            self.withdraw_sum= ko.observable();
            self.deposits_sum= ko.observable();
            self.intrest_in_suspense = ko.observable();
            self.change_in_Portfolio = ko.observable();
            self.gross_loan_portfolio= ko.observable();
            self.principal_disbursed = ko.observable();
            self.amount_disbursed = ko.observable();
            self.projected_interest_amount = ko.observable();
            self.amount_paid = ko.observable();
            self.portfolio_at_risk=ko.observable();
            self.value_at_risk=ko.observable();
            self.extraordinary_writeoff=ko.observable();

            self.client_count_active = ko.observable();
            self.staff_count_active = ko.observable();
            self.client_count_inactive = ko.observable();
            self.staff_count_inactive = ko.observable();

            self.loan_count_active = ko.observable();
            self.unpaid_penalty = ko.observable();
            self.loan_count_writeoff = ko.observable();
            self.loan_count_pend_approval = ko.observable();
            self.loan_count_partial = ko.observable();
            self.loan_count_arrias = ko.observable();
            self.loan_count_approved = ko.observable();
            self.loan_count_locked = ko.observable();
            self.totalamount = ko.computed(function () {
                total =0;
                if((typeof self.loan_count_active() !== 'undefined')&&(typeof self.loan_count_arrias() !== 'undefined')){
                total_count=parseInt(self.loan_count_active().loan_count)+parseInt(self.loan_count_arrias().loan_count);
                total = parseFloat(self.gross_loan_portfolio())/parseInt(total_count);
                }
                
                return total;
            });
            self.start_datev(moment(start_date,'X').format('MMM Do YYYY'));
            self.end_datev(moment(end_date,'X').format('MMM Do YYYY'));
            //fetch the dashboard data from server
            self.get_data = function () {
                self.start_datev(moment(start_date,'X').format('MMM Do YYYY'));
                self.end_datev(moment(end_date,'X').format('MMM Do YYYY'));

                $.ajax({
                    data: {start_date: moment(start_date,'X').format('YYYY-MM-DD'), 
                           end_date: moment(end_date,'X').format('YYYY-MM-DD'),
                           credit_officer_id: "<?php if($_SESSION['role_id']==4){ echo $_SESSION['staff_id']; } ?>",
                           origin: "dashboard"},

                    url: '<?php echo site_url("dashboard/ajax_data"); ?>',
                    type: "post",
                    dataType: "json",
                    success: function (result) {
                        if (typeof result !== "undefined") {
                            self.client_count_active(result.client_count_active);
                            self.staff_count_active(result.staff_count_active);
                            self.client_count_inactive(result.client_count_inactive);
                            self.staff_count_inactive(result.staff_count_inactive);
                            self.loan_count_active(result.loan_count_active);
                            self.loan_count_writeoff(result.loan_count_writeoff);
                            self.loan_count_pend_approval(result.loan_count_pend_approval);
                            self.loan_count_partial(result.loan_count_partial);
                            self.loan_count_arrias(result.loan_count_arrias);
                            self.loan_count_approved(result.loan_count_approved);
                            self.loan_count_locked(result.loan_count_locked);
                        }
                    }
                });
            };
            TableManageButtons = function () {
            "use strict";
            return {
                init: function (tblClicked) {
                }
            };
            }();
            daterangepicker_initializer();
            self.updateData = function () {
                $.ajax({
                    type: "post",
                    dataType: "json",
                    data: {start_date: moment(start_date,'X').format('YYYY-MM-DD'),
                    end_date: moment(end_date,'X').format('YYYY-MM-DD'),
                    staff_id: "<?php if($_SESSION['role_id']==4){ echo $_SESSION['staff_id']; } ?>",
                     origin: "reports"},
                    url: "<?php echo site_url('dashboard/get_indicators_data') ?>",
                    success: function (response) {
                       //draw_line_chart("line_graph1",response.income_expense);

                       self.savings_sums(response.savings_sums);
                       self.deposits_sum(response.deposits_sum);
                       self.withdraw_sum(response.withdraw_sum);
                       self.intrest_in_suspense(response.intrest_in_suspense);
                       self.change_in_Portfolio(response.change_in_Portfolio);
                       self.gross_loan_portfolio(response.gross_loan_portfolio);                       
                       self.principal_disbursed(response.principal_disbursed);
                       self.amount_disbursed(response.amount_disbursed);
                       self.amount_paid(response.amount_paid);
                       self.extraordinary_writeoff(response.extraordinary_writeoff);
                       self.value_at_risk(response.value_at_risk);
                       self.portfolio_at_risk(response.portfolio_at_risk);
                       self.projected_interest_amount(response.projected_interest_amount);
                       self.unpaid_penalty(response.penalty_total);
                    }
                })
            };
        };
        dashModel = new DashModel();
        ko.applyBindings(dashModel);
        dashModel.get_data();
        dashModel.updateData();

        <?php $this->view('dashboard/table_js'); ?>
        <?php $this->view('dashboard/active_loans_js'); ?>

    });

    setInterval(function () {
        dashModel.get_data();
        dashModel.updateData();
    }, 300000);/*Refresh every thirty seconds*/
    <?php //$this->view('reports/highcharts_js'); ?>

    function handleDateRangePicker(startDate, endDate) {
        if (typeof displayed_tab !== 'undefined') {
          
        }
        start_date = startDate;
        end_date = endDate;
        TableManageButtons.init(displayed_tab);
        dashModel.updateData();
        dashModel.get_data();
    }

    function msaveData(e) {
        if (e.isDefaultPrevented()) {
            // handle the invalid form...
            console.log('Please fill all the fields correctly');
        } else {
            // everything looks good!
            e.preventDefault();
            mysaveData(e.target);
        }
    }//End of the saveData function
    function mysaveData(form) {
        var $form = $(form);//fv = $form.data('formValidation'),
        enableDisableButton(form, true);
        var formData = new FormData($form[0]);
        var id = $form.attr('id');
        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: formData,
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (feedback) {
                if (feedback.success) {
                    if (isNaN(parseInt($form.attr('id')))) {
                        $form[0].reset();
                        $modal = $form.parents('div.modal');
                        if ($modal.length) {
                            $($modal[0]).modal('hide');
                        }
                    }
                    setTimeout(function () {
                        var formId = $form.attr('id');
                        var tblId = formId.replace("form", "tbl");
                        if (typeof dTable !== 'undefined' && typeof dTable[tblId] !== 'undefined') {
                            dTable[tblId].ajax.reload((typeof consumeDtableData !== 'undefined') ? consumeDtableData : null, false);
                        }
                        if (typeof reload_data === "function") {
                            reload_data(formId, feedback);
                        }
                    }, 1000);
                    toastr.success(feedback.message, "Success");
                    location.reload(); 
                } else {
                    toastr.warning(feedback.message, "Failure!");
                }
                enableDisableButton(form, false);
            }
            , error: function (jqXHR, textStatus, errorThrown) {
                network_error(jqXHR, textStatus, errorThrown, form);
            }
        });
    }//End of the saveData2 function
</script>