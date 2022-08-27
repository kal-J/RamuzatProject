  <?php
$start_date = date('d-m-Y', strtotime($fiscal_active['start_date']));
$end_date = date('d-m-Y', strtotime($fiscal_active['end_date']));
?>
  <div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2> <?php echo $title; ?></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?php echo base_url('u/home')?>">Home</a>
            </li>
            <li class="breadcrumb-item active">
                <strong><?php echo $title; ?></strong>
            </li>
        </ol>
    </div>
    <div class="col-sm-8">
        <h2></h2>
      <div class="pull-right add-record-btn">
       <ul class="nav nav-tabs" role="tablist">
        <?php if($org['loan_app_comp']==1){ ?>
          <li> <a class="nav-link btn btn-default btn-sm" data-toggle="modal"  data-target="#add_client_loan-modal">
            <i class="fa fa-plus-circle"></i> New Application</a> </li> 
        <?php } ?>
         <li> <a class="nav-link btn btn-default btn-sm" data-toggle="modal" data-target="#loan_calculator-modal"><i class="fas fa-calculator"></i> Loan Calculator</a> </li> 
        </ul>
        </div>
        
    </div>
</div>
<div class="row white-bg">
 <div class="col-lg-12">
    <br>
     <div class="panel-title">
        <center><h3 style="font-weight: bold;">Active Loans</h3></center>
    </div>
    <div class="table-responsive">
               <table class="table table-striped table-bordered table-hover dataTables-example" id="tblActive_client_loan" style="width: 100%">
            <thead>
                <tr>
                  <th>Ref #</th>
                  <th>Requested Amount (UGX)</th>
                  <th>Disbursed Amount (UGX)</th>
                  <th>Expected Interest (UGX)</th>
                  <th>Paid Amount (UGX)</th>
                  <th>Remaining bal (UGX)</th>
                  <th>Action Date</th>
                  <th>Next Pay Date</th>
                  <th>Loan Due Date</th>
                  <th>Status</th>

                </tr>
            </thead>
          <tbody>
        </tbody>
      <tfoot>
          <tr>
              <th >Totals</th>
              <th>0</th>
              <th>0</th>
              <th>0</th>
              <th>0</th>
              <th>0</th>
              <th colspan="3">&nbsp;</th>
          </tr>
      </tfoot>
      </table>
    </div>

</div>
</div>
<?php $this->view('client_loan/states/partial/steps_add_modal'); ?>
<?php $this->view('client_loan/states/partial/loan_calculator_modal'); ?>
<script>
    var dTable = {};
    var response='';
    var optionSet1 = {}
    var displayed_tab = '';
    var client_loanModel = {}, TableManageButtons;
    $(document).ready(function () {
     start_date = moment('<?php echo $start_date; ?>', "DD-MM-YYYY"); end_date = moment('<?php echo $end_date; ?>', "DD-MM-YYYY");
     <?php $this->load->view('client_loan/client_loan_knockout.php'); ?>
    var handleDataTableButtons = function (tabClicked) {
        <?php $this->view('client/loans/active/table_js'); ?>
    };
    TableManageButtons = function () { 
        "use strict";
        return {
            init: function (tblClicked) {
                handleDataTableButtons(tblClicked);
            }
        };
    }();
        TableManageButtons.init("tab-active");
        //initializing the date range picker
        daterangepicker_initializer();
         //$this->view('includes/daterangepicker.php'); ?>
    });
    <?php //$this->load->view('client_loan/client_loan_dtables_js.php'); ?>

     //Reloading a page after action
    function reload_data(form_id, response) {
        switch (form_id) {
            case "formClient_loan1":
                dTable['tblActive_client_loan'].ajax.reload(null, false);
                if(typeof response.loan_ref_no !== 'undefined'){
                    client_loanModel.loan_ref_no(response.loan_ref_no);
                }
                break;
           
            default:
                //nothing really to do here
                break;
        }
    }

    //getting payment schedule for a loan at application stage
    function get_payment_schedule(data) {
        var new_data = {};
            new_data['application_date1'] = typeof data.application_date === 'undefined' ? client_loanModel.application_date() : data.application_date;
            new_data['action_date1'] = typeof data.action_date === 'undefined' ? client_loanModel.app_action_date() : data.action_date;

            new_data['loan_product_id1'] = typeof client_loanModel.product_name() !== 'undefined' ? client_loanModel.product_name().id:client_loanModel.loan_details().loan_product_id;
            new_data['product_type_id1'] = typeof client_loanModel.product_name() !== 'undefined' ?client_loanModel.product_name().product_type_id:client_loanModel.loan_details().product_type_id;
            
            new_data['amount1'] = typeof data.amount === 'undefined' ?((typeof client_loanModel.app_amount() != 'undefined')? client_loanModel.app_amount(): ( (typeof client_loanModel.loan_details() !='undefined')?client_loanModel.loan_details().requested_amount:'' ) ) : data.amount;
            new_data['offset_period1'] = typeof data.offset_period === 'undefined' ?((typeof client_loanModel.app_offset_period() != 'undefined')?client_loanModel.app_offset_period(): ( (typeof client_loanModel.loan_details() !='undefined')?client_loanModel.loan_details().offset_period:'' ) ): data.offset_period;            
            new_data['offset_made_every1'] = typeof data.offset_made_every === 'undefined' ?((typeof client_loanModel.app_offset_every() != 'undefined')?client_loanModel.app_offset_every():( (typeof client_loanModel.loan_details() !='undefined' )?client_loanModel.loan_details().offset_made_every:'' )): data.offset_every;            
            new_data['interest_rate1'] = typeof data.interest === 'undefined' ?((typeof client_loanModel.app_interest() != 'undefined')?client_loanModel.app_interest(): ( (typeof client_loanModel.loan_details() !='undefined')?client_loanModel.loan_details().interest_rate:'')): data.interest;
            new_data['repayment_made_every1'] = typeof data.repayment_made_every === 'undefined' ?((typeof client_loanModel.app_repayment_made_every() != 'undefined')?client_loanModel.app_repayment_made_every():( (typeof client_loanModel.loan_details() !='undefined')?client_loanModel.loan_details().repayment_made_every:'')): data.repayment_made_every;
            
            new_data['repayment_frequency1'] = typeof data.repayment_frequency === 'undefined' ?((typeof client_loanModel.app_repayment_frequency() != 'undefined')?client_loanModel.app_repayment_frequency(): ( (typeof client_loanModel.loan_details() !='undefined')?client_loanModel.loan_details().repayment_frequency:'')): data.repayment_frequency;            
           
            new_data['installments1'] = typeof data.installments === 'undefined' ?((typeof client_loanModel.app_installments() != 'undefined')?client_loanModel.app_installments():( (typeof client_loanModel.loan_details() !='undefined')?client_loanModel.loan_details().installments:'')) : data.installments;

        var url = "<?php echo site_url("client_loan/disbursement1"); ?>";
        $.ajax({
            url: url,
            data: new_data,
            type: 'POST',
            dataType:'json',
            success:function (response) {
                //clear the the other fields because we are starting the selection afresh
                client_loanModel.payment_summation(null);
                client_loanModel.payment_schedule(null);
                //populate the observables
                client_loanModel.payment_schedule(response.payment_schedule);
                client_loanModel.payment_summation(response.payment_summation);
                },
            fail:function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
                }
            });
    }



    //getting new schedule
    function get_new_schedule(data, call_type) {
        var new_data = {};
        if (call_type === 1) {
            new_data['action_date'] = typeof data.action_date === 'undefined' ? client_loanModel.action_date() : data.action_date;
            new_data['id'] = client_loanModel.loan_details().id;
        } else {
            new_data['amount'] = typeof data.amount === 'undefined' ? client_loanModel.amount : data.amount;
            new_data['loan_product_id'] = typeof data.loan_product_id === 'undefined' ? client_loanModel.product_name().id : data.loan_product_id;
            new_data['interest_rate'] = typeof data.interest_rate === 'undefined' ? client_loanModel.interest_rate() : data.interest_rate;
            new_data['repayment_made_every'] = typeof data.repayment_made_every === 'undefined' ? client_loanModel.repayment_made_every() : data.repayment_made_every;
            new_data['repayment_frequency'] = typeof data.repayment_frequency === 'undefined' ? client_loanModel.repayment_frequency() : data.repayment_frequency;
            new_data['installments'] = typeof data.installments === 'undefined' ? client_loanModel.installments() : data.installments;
            new_data['new_repayment_date'] = typeof data.new_repayment_date === 'undefined' ? client_loanModel.payment_date() : data.new_repayment_date;
        }
        
        var url = "<?php echo site_url("u/loans/disbursement"); ?>";
        $.ajax({
            url: url,
            data: new_data,
            type: 'POST',
            dataType:'json',
            success:function (response) {
                //clear the the other fields because we are starting the selection afresh
                client_loanModel.payment_summation(null);
                client_loanModel.payment_schedule(null);
                //populate the observables
                client_loanModel.payment_schedule(response.payment_schedule);
                client_loanModel.payment_summation(response.payment_summation);
                },
            fail:function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
                }
            });
    }

    //getting payment data
    function get_payment_detail(new_data) {
        var url = "<?php echo site_url("loan_installment_payment/payment_data"); ?>";
        $.ajax({
            url: url,
            data: new_data,
            type: 'POST',
            dataType:'json',
            success:function (response) {
                //populate the observables
                client_loanModel.payment_data(response.payment_data);
                client_loanModel.penalty_amount(response.penalty_data);
                },
            fail:function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
                }
            });
    }

    //getting new penalty data
    function get_new_penalty(new_data) {
        var data = {};
        data['payment_date']=new_data;
        data['client_loan_id'] = client_loanModel.payment_data().id;
        data['installment_number'] = client_loanModel.payment_data().installment_number;
        var url = "<?php echo site_url("loan_installment_payment/get_penalty_data"); ?>";
        $.ajax({
            url: url,
            data: data,
            type: 'POST',
            dataType:'json',
            success:function (response) {
                //populate the observables
                client_loanModel.penalty_amount(response.penalty_data);
                },
            fail:function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
                }
            });
    }

    function handleDateRangePicker(startDate, endDate) {
                
        if(typeof displayed_tab !== 'undefined'){
                start_date = startDate;
                end_date = endDate;
                TableManageButtons.init(displayed_tab);
            }
    }
</script>
