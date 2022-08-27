<style type="text/css">
  /* ==========TOOL TIP ==================  */  
.tooltip {
  font-size: 14px;
  font-weight: bold;
}

.tooltip-arrow {
  display: none;
  opacity: 0;
}

.tooltip-inner {
  background-color: #FAE6A4;
  border-radius: 4px;
  box-shadow: 0 1px 13px rgba(0, 0, 0, 0.14), 0 0 0 1px rgba(115, 71, 38, 0.23);
  color: #734726;
  min-width: 200px;
  padding: 6px 10px;
  text-align: center;
  text-decoration: none;
}
.tooltip-inner:after {
  content: "";
  display: inline-block;
  left: 100%;
  margin-left: -56%;
  position: absolute;
}
.tooltip-inner:before {
  content: "";
  display: inline-block;
  left: 100%;
  margin-left: -56%;
  position: absolute;
}

.tooltip.top {
  margin-top: -11px;
  padding: 0;
}
.tooltip.top .tooltip-inner:after {
  border-top: 11px solid #FAE6A4;
  border-left: 11px solid transparent;
  border-right: 11px solid transparent;
  bottom: -10px;
}
.tooltip.top .tooltip-inner:before {
  border-top: 11px solid rgba(0, 0, 0, 0.2);
  border-left: 11px solid transparent;
  border-right: 11px solid transparent;
  bottom: -11px;
}

.tooltip.bottom {
  margin-top: 11px;
  padding: 0;
}
.tooltip.bottom .tooltip-inner:after {
  border-bottom: 11px solid #FAE6A4;
  border-left: 11px solid transparent;
  border-right: 11px solid transparent;
  top: -10px;
}
.tooltip.bottom .tooltip-inner:before {
  border-bottom: 11px solid rgba(0, 0, 0, 0.2);
  border-left: 11px solid transparent;
  border-right: 11px solid transparent;
  top: -11px;
}

.tooltip.left {
  margin-left: -11px;
  padding: 0;
}
.tooltip.left .tooltip-inner:after {
  border-left: 11px solid #FAE6A4;
  border-top: 11px solid transparent;
  border-bottom: 11px solid transparent;
  right: -10px;
  left: auto;
  margin-left: 0;
}
.tooltip.left .tooltip-inner:before {
  border-left: 11px solid rgba(0, 0, 0, 0.2);
  border-top: 11px solid transparent;
  border-bottom: 11px solid transparent;
  right: -11px;
  left: auto;
  margin-left: 0;
}

.tooltip.right {
  margin-left: 11px;
  padding: 0;
}
.tooltip.right .tooltip-inner:after {
  border-right: 11px solid #FAE6A4;
  border-top: 11px solid transparent;
  border-bottom: 11px solid transparent;
  left: -10px;
  top: 0;
  margin-left: 0;
}
.tooltip.right .tooltip-inner:before {
  border-right: 11px solid rgba(0, 0, 0, 0.2);
  border-top: 11px solid transparent;
  border-bottom: 11px solid transparent;
  left: -11px;
  top: 0;
  margin-left: 0;
}

</style>
<?php
$start_date = date('d-m-Y', strtotime($fiscal_year['start_date']));
$end_date = date('d-m-Y', strtotime($fiscal_year['end_date']));
?>
<div role="tabpanel" id="tab-query_reports" class="tab-pane">
    <div class="panel-body">
    <table><tr >
      <td style="padding-right: 10px;">
        <input type="hidden" name="membership1" id="membership" value="0"><input type="checkbox" onclick="this.previousSibling.value=1-this.previousSibling.value">
        <label style="display: inline;"><strong>Membership</strong></label>
      </td>
       <td style="padding-right: 10px;">
        <input type="hidden" name="shares1" id="shares" value="0"><input type="checkbox" onclick="this.previousSibling.value=1-this.previousSibling.value">
        <label style="display: inline;"><strong>Shares</strong></label>
      </td>
       <td style="padding-right: 20px;">
        <input type="hidden" name="loans1" id="loans" value="0"><input type="checkbox" onclick="this.previousSibling.value=1-this.previousSibling.value">
        <label style="display: inline;"><strong>Loans</strong></label>
      </td>
       <td style="padding-right: 20px;">
         <input type="hidden" name="savings1" id="savings" value="0"><input type="checkbox" onclick="this.previousSibling.value=1-this.previousSibling.value">
        <label style="display: inline;"><strong>Savings</strong></label>
      </td>
      <td style="padding-right: 20px;" >
        <label ><strong>Period</strong></label>
           <select   id="period" name="period1" rel="tooltip"  title="Select time period" data-bind='options: period_types, optionsText: "period_name", optionsAfterRender: setOptionValue("id"), value: period'>
          </select>
      </td>
<!-- ko with: period -->
      <td style="padding-right: 20px;" data-bind="visible:  parseInt(id) ==parseInt(2)">
        <label style="display: inline;"><strong>From </strong></label>
         <input autocomplete="off"  onkeydown="return false" id="start_date" name="start_date1" data-bind="datepicker: $root.start_dater,textInput:'<?php echo $start_date; ?>'" type="text">
      </td>
      <td style="padding-right: 20px;" data-bind="visible:  parseInt(id) ==parseInt(2)">
        <label style="display: inline;"><strong>To </strong></label>
         <input onkeydown="return false" autocomplete="off" id="end_date" name="end_date1" data-bind="datepicker: $root.end_dater,textInput:'<?php echo $end_date; ?>'" type="text" >
      </td>
      <td style="padding-right: 20px;" data-bind="visible:  parseInt(id) ==parseInt(1)">
        <label style="display: inline;"><strong>Date </strong></label>
         <input onkeydown="return false" autocomplete="off" id="date_at" name="date_at1" data-bind="datepicker: $root.end_daterd,textInput:'<?php echo $end_date; ?>'" type="text" >
      </td>
      <td style="padding-right: 10px;" data-bind="visible:  parseInt(id) ==parseInt(3)">
        <label ><strong>Year #1</strong></label>

        <select   id="fiscal_one" name="fiscal_one1" data-bind='options: $root.fiscal_years, optionsText:function(data_item){return moment(data_item.start_date,"YYYY-MM-DD").format("MMM/YYYY") +" - " + moment(data_item.end_date,"YYYY-MM-DD").format("MMM/YYYY");}, optionsAfterRender: setOptionValue("id")'>
        </select>
      </td>
      <td style="padding-right: 10px;" data-bind="visible:  parseInt(id) ==parseInt(3)">
        <label ><strong>Year #2</strong></label>

         <select id="fiscal_two" name="fiscal_two1" data-bind='options: $root.fiscal_years, optionsText:function(data_item){return moment(data_item.start_date,"YYYY-MM-DD").format("MMM/YYYY") +" - " + moment(data_item.end_date,"YYYY-MM-DD").format("MMM/YYYY");},optionsCaption: "--select--", optionsAfterRender: setOptionValue("id")'>
        </select>
      </td>
        <td style="padding-right: 20px;" data-bind="visible:  parseInt(id) ==parseInt(3)">
        <label ><strong>Year #3</strong></label>

        <select id="fiscal_three" name="fiscal_three1" data-bind='options: $root.fiscal_years, optionsText:function(data_item){return moment(data_item.start_date,"YYYY-MM-DD").format("MMM/YYYY") +" - " + moment(data_item.end_date,"YYYY-MM-DD").format("MMM/YYYY");},optionsCaption: "--select--", optionsAfterRender: setOptionValue("id")'>
        </select>
      </td>
     <!--/ko -->
      <td><button class="btn btn-success btn-sm btn-flat" onclick="get_query_report_data(this)" >Preview</button></td>
     </tr>
    </table>
     <hr>

    <div class="col-lg-12">
   <img src="<?php echo base_url(); ?>images/loading.gif" id="gif" style="display: block; margin: 0 auto; width: 30px; visibility: hidden;">
      <div data-bind="visible:  parseInt(membership()) == parseInt(0) && parseInt(period_savings()) == parseInt(0) &&parseInt(loans()) == parseInt(0) &&parseInt(shares()) == parseInt(0)"><h5 class="alert alert-danger"> <center> There is no <b>Report</b> selected. Please check any or all of the boxes above </center></h5></div>
     <?php if(in_array('6', $report_privilege)){ ?>
          <div  data-bind="visible:(parseInt(membership()) === parseInt(1) || parseInt(period_savings()) === parseInt(1) || parseInt(loans()) === parseInt(1) || parseInt(shares()) === parseInt(1))" class="pull-right add-record-btn">
                  <a href="#print_performance_report" data-toggle="modal" class="btn btn-primary btn-sm"> <i class="fa fa-print fa-2x"></i> </a>
          </div>
          <?php $this->load->view('reports/printouts/print_performance_modal'); ?>

      <?php } ?>
      
      <div  data-bind="visible:  (parseInt(selected_period()) == parseInt(1) || parseInt(selected_period()) == parseInt(2)) && (parseInt(membership()) !== parseInt(0) || parseInt(period_savings()) !== parseInt(0) || parseInt(loans()) !== parseInt(0) || parseInt(shares()) !== parseInt(0))">
         <table class="table table-sm table-bordered" width="100%">
            <tbody>
              <tr style="background-color: #1c84c6;">
                    <td  colspan="3"> <span style="font-size: 14px;color: #fff;font-weight: bold;"><center>Performance Report </center></span> </td>
                </tr>
                <tr style="background-color: #1c84c6;">
                    <td colspan="2" > <span style="font-size: 13px;color: #fff;font-weight: bold;">Details </span> </td>
                    <td data-bind="visible:parseInt(selected_period())===1"> <span style="font-size: 13px;color: #fff;font-weight: bold;"><center> <b > As At &nbsp;&nbsp;</b><span data-bind="text:moment(end_date(),'YYYY-MM-DD').format('DD-MMM-YYYY')"></span> </center></span> </td>
                    <td data-bind="visible:parseInt(selected_period())===2" > <span style="font-size: 13px;color: #fff;font-weight: bold;"><center><b style="color: #000;"> From &nbsp;&nbsp;</b> &nbsp;<span data-bind="text:moment(start_date(),'YYYY-MM-DD').format('DD-MMM-YYYY')"></span> &nbsp;<b style="color: #000;"> To &nbsp;&nbsp;</b> &nbsp; <span data-bind="text:moment(end_date(),'YYYY-MM-DD').format('DD-MMM-YYYY')"></span></center></span> </td>
                </td>
                <tr  data-bind="visible:  parseInt(period_savings()) == parseInt(1)">
                    <td rowspan="4" > <h3>Savings </h3> </td>
                    <td >Savings Accounts</td>
                    <td><h4 class="no-margins"><span  class="text-default" data-bind="text:savings_count?savings_count:0">0</span></h4> 
                </td>
               </tr> 
                <tr data-bind="visible:  parseInt(period_savings()) == parseInt(1),with:savings">
                    <td >Total Deposits</td>
                    <td><h4 class="no-margins"><span  class="text-default" data-bind="text:deposits?curr_format(round(deposits,2)*1):0">0</span></h4> 
                </td>
               </tr>
                <tr data-bind="visible: parseInt(period_savings()) == parseInt(1),with:savings" >
                <td>Total Withdraws  </td>
                   <td><h4 class="no-margins"><span class="text-default" data-bind="text:withdraws?curr_format(round(withdraws,2)*1):0">0</span></h4>  
                </tr>
                 <tr data-bind="visible: parseInt(period_savings()) == parseInt(1),with:savings">
                <td>Total Savings </td>
                   <td><h4 class="no-margins"><span  class="text-default" data-bind="text:cash_bal?curr_format(round(cash_bal,2)*1):0">0</span></h4>  
                </td>
               </tr>
              
                <tr data-bind="visible:  parseInt(loans()) == parseInt(1)">
                    <td rowspan="6" > <h3>Loans </h3> </td>
                    <td>Principal Disbursed</td>
                    <td><h4 class="no-margins"><span  class="text-default" data-bind="text:principal_disbursed()?curr_format(round(principal_disbursed(),2)*1):0">0</span></h4> 
                </td>
                <tr data-bind="visible:  parseInt(loans()) == parseInt(1)">
                    <td>Principal Collected</td>
                    <td data-bind="with: amount_paid"><h4 class="no-margins"><span  class="text-default" data-bind="text:already_principal_amount?curr_format(round(already_principal_amount,2)*1):0">0</span></h4> 
                </td>
                </tr>
                <tr data-bind="visible:  parseInt(loans()) == parseInt(1)">
                    <td>Gross loan portfolio</td>
                    <td><h4 class="no-margins"><span  class="text-default" data-bind="text:gross_loan_portfolio()?curr_format(round(gross_loan_portfolio(),2)*1):0">0</span></h4> 
                </td>
                </tr>
                <tr data-bind="visible:  parseInt(loans()) == parseInt(1)">
                    <td>Projected Loan Interest</td>
                    <td><h4 class="no-margins"><span  class="text-default" data-bind="text:projected_intrest_earnings()?curr_format(round(projected_intrest_earnings(),2)*1):0">0</span></h4> 
                </td>
                </tr>
                <tr data-bind="visible:  parseInt(loans()) == parseInt(1)">
                    <td>Interest in suspense</td>
                    <td><h4 class="no-margins"><span  class="text-default" data-bind="text:intrest_in_suspense()?curr_format(round(intrest_in_suspense(),2)*1):0">0</span></h4> 
                </td>
                </tr>
                <tr data-bind="visible:  parseInt(loans()) == parseInt(1)">
                    <td>Unpaid Penalty</td>
                    <td  data-bind="with:unpaid_penalty"><h4 class="no-margins"><span  class="text-default" data-bind="text:penalty_total?curr_format(round(penalty_total,2)*1):0">0</span></h4> 
                </td>
                </tr>
              
                <tr data-bind="visible:  parseInt(shares()) == parseInt(1)">
                    <td data-bind="attr:{'rowspan': $root.rowSpan_value}" ><h3> Share Capital </h3> </td>
                    <td >Number of Shareholders</td>
                    <td><h4 class="no-margins"><span  class="text-default" data-bind="text:no_of_shareholders?no_of_shareholders:0">0</span></h4> 
                </td>
               </tr>
             
                <!-- ko foreach: share_report-->
                <tr data-bind="visible:  parseInt($root.shares()) == parseInt(1)">
                <td>Number of <b data-bind="text:issuance_name"></b> @ <b data-bind="text:price_per_share?curr_format(price_per_share*1):0" >0</b> each </td>
                   <td><h4 class="no-margins"><span  class="text-default" data-bind="text:price_per_share?parseFloat(amount)/parseFloat(price_per_share):0">0</span></h4>  </td>
                </tr>
                <tr data-bind="visible: parseInt($root.shares()) == parseInt(1)">
                    <td>Total Amount</td>
                    <td><h4 class="no-margins"><span  class="text-default" data-bind="text:amount?curr_format(round(amount,2)*1):0">0</span></h4> 
                </td>
                </tr>
                <!--/ko-->
                <tr data-bind="visible:  parseInt(membership()) == parseInt(1)">
                    <td rowspan="2" ><h3> Membership</h3></td>
                    <td >Male</td>
                    <td><h4 class="no-margins"><span  class="text-default" data-bind="text:male_members?male_members:0">0</span></h4> 
                     </td>
                </tr>
                <tr data-bind="visible:  parseInt(membership()) == parseInt(1)">
                <td>Female</td>
                   <td><h4 class="no-margins"><span  class="text-default" data-bind="text:female_members?female_members:0">0</span></h4>  
                   </td>
                </tr>
           
                </tbody>
        </table>
      </div>

<!--     <div data-bind="visible: parseInt(selected_period())===parseInt(3) && (parseInt(membership()) !== parseInt(0) || parseInt(period_savings()) !== parseInt(0) || parseInt(loans()) !== parseInt(0) || parseInt(shares()) !== parseInt(0))">
         <table class="table table-sm table-bordered" width="100%">
            <tbody>
               <tr style="background-color: #1c84c6;">
                    <td colspan="5"> <span style="font-size: 14px;color: #fff;font-weight: bold;"><center>Performance Comparision</center></span> </td>
                </tr>
                <tr style="background-color: #1c84c6;">
                    <td colspan="2" rowspan="2" > <span style="font-size: 16px;color: #fff;font-weight: bold;"><center>Details</center></span> </td>
                    <td colspan="3"><span style="font-size: 12px;color: #fff;font-weight: bold;"><center>Financial Year</center></span></td>
                </tr>
                <tr style="background-color: #1c84c6;" >
                    <td><span style="font-size: 13px;color: #fff;font-weight: bold;"><center data-bind='text:moment(start_date(),"YYYY-MM-DD").format("MMM-YYYY") +" / "+moment(end_date(),"YYYY-MM-DD").format("MMM-YYYY")'></center></span ></td>
                    <td data-bind="visible:  parseInt(fiscal_2()) !== 0"><span style="font-size: 13px;color: #fff;font-weight: bold;"><center data-bind='text:start_date1() +" / "+end_date1()'><center></span></td>
                    <td data-bind="visible:  parseInt(fiscal_3()) !== 0"><span style="font-size: 13px;color: #fff;font-weight: bold;"><center data-bind='text:start_date2() +" / "+end_date2()'></center></span></td>
               </tr> 
                <tr data-bind="visible:  parseInt(period_savings()) == parseInt(1)" >
                    <td rowspan="4" > <h3>Savings </h3> </td>
                    <td >Savings Accounts</td>
                    <td><h4 class="no-margins"><span  class="text-default" data-bind="text:savings_count?savings_count:0">0</span></h4> </td>
                    <td data-bind="visible:  parseInt(fiscal_2()) !== 0"><h4 class="no-margins"><span  class="text-default" data-bind="text:savings_count1?savings_count1:0">0</span></h4> </td>
                    <td data-bind="visible:  parseInt(fiscal_3()) !== 0"><h4 class="no-margins"><span  class="text-default" data-bind="text:savings_count2?savings_count2:0">0</span></h4> </td>
               </tr> 
                <tr data-bind="visible:  parseInt(period_savings()) == parseInt(1)" >
                    <td >Total Deposits</td>
                    <td data-bind="with:savings"><h4 class="no-margins"><span  class="text-default" data-bind="text:deposits?curr_format(round(deposits,2)*1):0">0</span></h4> </td>
                    <td data-bind="visible:  parseInt(fiscal_2()) !== 0,with:savings1"><h4 class="no-margins"><span  class="text-default" data-bind="text:deposits?curr_format(round(deposits,2)*1):0">0</span></h4> </td>
                    <td data-bind="visible:  parseInt(fiscal_3()) !== 0,with:savings2"><h4 class="no-margins"><span  class="text-default" data-bind="text:deposits?curr_format(round(deposits,2)*1):0">0</span></h4> </td>
               </tr>
                <tr data-bind="visible:  parseInt(period_savings()) == parseInt(1)" >
                <td>Total Withdraws  </td>
                   <td data-bind="with:savings"><h4 class="no-margins"><span  class="text-default" data-bind="text:withdraws?curr_format(round(withdraws,2)*1):0">0</span></h4></td>
                   <td data-bind="visible:  parseInt(fiscal_2()) !== 0,with:savings1"><h4 class="no-margins"><span  class="text-default" data-bind="text:withdraws?curr_format(round(withdraws,2)*1):0">0</span></h4></td>
                   <td data-bind="visible:  parseInt(fiscal_3()) !== 0,with:savings2"><h4 class="no-margins"><span  class="text-default" data-bind="text:withdraws?curr_format(round(withdraws,2)*1):0">0</span></h4></td>
                </tr>
                 <tr data-bind="visible:  parseInt(period_savings()) == parseInt(1)">
                <td>Total Savings </td>
                   <td data-bind="with:savings"><h4 class="no-margins"><span  class="text-default" data-bind="text:cash_bal?curr_format(round(cash_bal,2)*1):0">0</span></h4></td>
                   <td data-bind="visible:  parseInt(fiscal_2()) !== 0,with:savings1"><h4 class="no-margins"><span  class="text-default" data-bind="text:cash_bal?curr_format(round(cash_bal,2)*1):0">0</span></h4></td>
                   <td data-bind="visible:  parseInt(fiscal_3()) !== 0,with:savings2"><h4 class="no-margins"><span  class="text-default" data-bind="text:cash_bal?curr_format(round(cash_bal,2)*1):0">0</span></h4></td>
               </tr>
              
                <tr data-bind="visible:  parseInt(loans()) == parseInt(1)">
                    <td rowspan="6" > <h3>Loans </h3> </td>
                    <td>Principal Disbursed</td>
                    <td><h4 class="no-margins"><span  class="text-default" data-bind="text:principal_disbursed()?curr_format(round(principal_disbursed(),2)*1):0">0</span></h4></td>
                    <td data-bind="visible:  parseInt(fiscal_2()) !== 0"><h4 class="no-margins"><span  class="text-default" data-bind="text:principal_disbursed1()?curr_format(round(principal_disbursed1(),2)*1):0">0</span></h4></td>
                    <td data-bind="visible:  parseInt(fiscal_3()) !== 0"><h4 class="no-margins"><span  class="text-default" data-bind="text:principal_disbursed2()?curr_format(round(principal_disbursed2(),2)*1):0">0</span></h4></td>
                <tr data-bind="visible:  parseInt(loans()) == parseInt(1)">
                    <td>Principal Collected</td>
                    <td data-bind="with: amount_paid"><h4 class="no-margins"><span  class="text-default" data-bind="text:already_principal_amount?curr_format(round(already_principal_amount,2)*1):0">0</span></h4> </td>
                    <td data-bind="visible:  parseInt(fiscal_2()) !== 0,with: amount_paid1"><h4 class="no-margins"><span  class="text-default" data-bind="text:already_principal_amount?curr_format(round(already_principal_amount,2)*1):0">0</span></h4> </td>
                    <td data-bind="visible:  parseInt(fiscal_3()) !== 0,with: amount_paid2"><h4 class="no-margins"><span  class="text-default" data-bind="text:already_principal_amount?curr_format(round(already_principal_amount,2)*1):0">0</span></h4> </td>
                </tr>
                <tr data-bind="visible:  parseInt(loans()) == parseInt(1)">
                    <td>Gross loan portfolio</td>
                    <td><h4 class="no-margins"><span  class="text-default" data-bind="text:gross_loan_portfolio()?curr_format(round(gross_loan_portfolio(),2)*1):0">0</span></h4> </td>
                    <td data-bind="visible:  parseInt(fiscal_2()) !== 0"><h4 class="no-margins"><span  class="text-default" data-bind="text:gross_loan_portfolio1()?curr_format(round(gross_loan_portfolio1(),2)*1):0">0</span></h4> </td>
                    <td data-bind="visible:  parseInt(fiscal_3()) !== 0"><h4 class="no-margins"><span  class="text-default" data-bind="text:gross_loan_portfolio2()?curr_format(round(gross_loan_portfolio2(),2)*1):0">0</span></h4> </td>
                </tr>
                <tr data-bind="visible:  parseInt(loans()) == parseInt(1)">
                    <td>Projected Loan Interest</td>
                    <td><h4 class="no-margins"><span  class="text-default" data-bind="text:projected_intrest_earnings()?curr_format(round(projected_intrest_earnings(),2)*1):0">0</span></h4> </td>
                    <td data-bind="visible:  parseInt(fiscal_2()) !== 0"><h4 class="no-margins"><span  class="text-default" data-bind="text:projected_intrest_earnings1()?curr_format(round(projected_intrest_earnings1(),2)*1):0">0</span></h4> </td>
                    <td data-bind="visible:  parseInt(fiscal_3()) !== 0"><h4 class="no-margins"><span  class="text-default" data-bind="text:projected_intrest_earnings2()?curr_format(round(projected_intrest_earnings2(),2)*1):0">0</span></h4> </td>
                </tr>
                <tr data-bind="visible:  parseInt(loans()) == parseInt(1)">
                    <td>Interest in suspense</td>
                    <td><h4 class="no-margins"><span  class="text-default" data-bind="text:intrest_in_suspense()?curr_format(round(intrest_in_suspense(),2)*1):0">0</span></h4> </td>
                    <td data-bind="visible:  parseInt(fiscal_2()) !== 0"><h4 class="no-margins"><span  class="text-default" data-bind="text:intrest_in_suspense1()?curr_format(round(intrest_in_suspense1(),2)*1):0">0</span></h4> </td>
                    <td data-bind="visible:  parseInt(fiscal_3()) !== 0"><h4 class="no-margins"><span  class="text-default" data-bind="text:intrest_in_suspense2()?curr_format(round(intrest_in_suspense2(),2)*1):0">0</span></h4> </td>
                </tr>
                <tr data-bind="visible:  parseInt(loans()) == parseInt(1)">
                    <td>Unpaid Penalty</td>
                    <td  data-bind="with:unpaid_penalty"><h4 class="no-margins"><span  class="text-default" data-bind="text:penalty_total?curr_format(round(penalty_total,2)*1):0">0</span></h4></td> 
                    <td  data-bind=" visible:  parseInt(fiscal_2()) !== 0,with:unpaid_penalty1"><h4 class="no-margins"><span  class="text-default" data-bind="text:penalty_total?curr_format(round(penalty_total,2)*1):0">0</span></h4></td> 
                    <td  data-bind="visible:  parseInt(fiscal_3()) !== 0,with:unpaid_penalty2"><h4 class="no-margins"><span  class="text-default" data-bind="text:penalty_total?curr_format(round(penalty_total,2)*1):0">0</span></h4></td> 
                </tr>
              
                <tr data-bind="visible:  parseInt(shares()) == parseInt(1)" >
                    <td data-bind="attr:{'rowspan': $root.rowSpan_value}" ><h3> Share Capital </h3> </td>
                    <td >Number of Shareholders</td>
                    <td><h4 class="no-margins"><span  class="text-default" data-bind="text:no_of_shareholders?no_of_shareholders:0">0</span></h4> </td>
                    <td data-bind="visible:  parseInt(fiscal_2()) !== 0"><h4 class="no-margins"><span  class="text-default" data-bind="text:no_of_shareholders1?no_of_shareholders1:0">0</span></h4> </td>
                    <td data-bind="visible:  parseInt(fiscal_3()) !== 0"><h4 class="no-margins"><span  class="text-default" data-bind="text:no_of_shareholders2?no_of_shareholders2:0">0</span></h4> </td>
               </tr>
              
                <tr data-bind="visible:  parseInt(shares()) == parseInt(1)" >
                <td>Number of <b data-bind="text:issuance_name"></b> @ <b data-bind="text:price_per_share?curr_format(price_per_share*1):0" >0</b> each </td>
                   <td><h4 class="no-margins"><span  class="text-default" data-bind="text:price_per_share?total_shares:0">0</span></h4></td>
                   <td data-bind="visible:  parseInt(fiscal_2()) !== 0"><h4 class="no-margins"><span  class="text-default" data-bind="text:total_shares1?total_shares1:0">0</span></h4></td>
                   <td data-bind="visible:  parseInt(fiscal_3()) !== 0"><h4 class="no-margins"><span  class="text-default" data-bind="text:total_shares2?total_shares2:0">0</span></h4></td>
                </tr>
            
                <tr data-bind="visible:  parseInt(shares()) == parseInt(1)">
                    <td>Total Share Capital</td>
                    <td data-bind ="with: share_report"><h4 class="no-margins"><span  class="text-default" data-bind="text:amount?curr_format(round(amount,2)*1):0">0</span></h4></td> 
                    <td data-bind ="visible:  parseInt(fiscal_2()) !== 0,with: share_report1"><h4 class="no-margins"><span  class="text-default" data-bind="text:amount?curr_format(round(amount,2)*1):0">0</span></h4></td> 
                    <td data-bind ="visible:  parseInt(fiscal_3()) !== 0,with: share_report2"><h4 class="no-margins"><span  class="text-default" data-bind="text:amount?curr_format(round(amount,2)*1):0">0</span></h4></td> 
                </tr>
             
                <tr data-bind="visible:  parseInt(membership()) == parseInt(1)" >
                    <td rowspan="2" ><h3> Membership</h3></td>
                    <td >Male</td>
                    <td><h4 class="no-margins"><span  class="text-default" data-bind="text:male_members?male_members:0">0</span></h4></td>
                    <td data-bind="visible:  parseInt(fiscal_2()) !== 0"><h4 class="no-margins"><span  class="text-default" data-bind="text:male_members1?male_members1:0">0</span></h4></td> 
                    <td data-bind="visible:  parseInt(fiscal_3()) !== 0"><h4 class="no-margins"><span  class="text-default" data-bind="text:male_members2?male_members2:0">0</span></h4></td>  
                </tr>
                <tr data-bind="visible:  parseInt(membership()) == parseInt(1)">
                <td>Female</td>
                   <td><h4 class="no-margins"><span  class="text-default" data-bind="text:female_members?female_members:0">0</span></h4></td> 
                    <td data-bind="visible:  parseInt(fiscal_2()) !== 0"><h4 class="no-margins"><span  class="text-default" data-bind="text:female_members1?female_members1:0">0</span></h4></td> 
                     <td data-bind="visible:  parseInt(fiscal_3()) !== 0"><h4 class="no-margins"><span  class="text-default" data-bind="text:female_members2?female_members2:0">0</span></h4></td>  
                </tr>
           
                </tbody>
        </table>
      </div>
 -->
    </div>
    </div>
</div>
<script type="text/javascript">
   $(function () {
        $('[rel="tooltip"]').tooltip();
      });
</script>