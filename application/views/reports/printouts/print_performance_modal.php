<section>
    <div class="modal fade" id="print_performance_report" tabindex="-1" role="dialog" aria-labelledby="printLayoutTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="max-width: 80vw; width: 80vw">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="ModalLongTitle">Performance Report</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">

        <div id="performance_report_printable"  data-bind="visible:  (parseInt(selected_period()) == parseInt(1) || parseInt(selected_period()) == parseInt(2)) && (parseInt(membership()) !== parseInt(0) || parseInt(period_savings()) !== parseInt(0) || parseInt(loans()) !== parseInt(0) || parseInt(shares()) !== parseInt(0))">
            <div class="row d-flex flex-column align-items-center mx-auto w-100">
                <img style="height: 50px;"
                    src="<?php echo base_url("uploads/organisation_".$_SESSION['organisation_id']."/logo/".$org['organisation_logo']);  ?>"
                    alt="logo">

                <div class="mx-auto text-center mb-2">
                    <span>
                        <?php echo $org['name']; ?> ,
                    </span>
                    <span>
                        <?php echo $branch['physical_address']; ?>, <?php echo $branch['branch_name']; ?>
                    </span><br>
                    <span>
                        <?php echo $branch['postal_address']; ?> ,
                    </span>
                    <span>
                        <b>Tel:</b> <?php echo $branch['office_phone']; ?>
                    </span>
                    <br><br>
                </div>
            </div>

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
                   <td><h4 class="no-margins"><span  class="text-default" data-bind="text:price_per_share?parseFloat(amount)/parseFloat(price_per_share):0">0</span></h4>  
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
                </tr>
           
                </tbody>
        </table>
      </div>


        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button onclick="printJS({printable: 'performance_report_printable', type: 'html', targetStyles: ['*'], documentTitle: 'Performance-Report'})" type="button" class="btn btn-primary">Print</button>
        </div>
        </div>
    </div>
    </div>
</section>
