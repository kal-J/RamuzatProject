<?php 
if (isset($loan_detail['group_name'])) { 
    $client_name = ucwords(strtolower($loan_detail['group_name'])); 
}else{ $client_name = ucwords(strtolower($loan_detail['member_name'])); }
?>

<div id="printable_client_loan_report">

<div class="row d-flex flex-column align-items-center mx-auto w-100">
                        <img style="height: 50px;" src="<?php echo base_url("uploads/organisation_" . $_SESSION['organisation_id'] . "/logo/" . $org['organisation_logo']);  ?>" alt="logo">

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


<h6 class="text-success"><center>LOAN REPORT FOR <?php echo $loan_detail['loan_no'].' AS OF '.date('jS F, Y'); ?></center></h6>

<br>

<style type="text/css">
    table {
        border-collapse: collapse;
    }
    tbody {
        font-size: 14px;
        font-weight: normal
    }
</style>
<div class="col-lg-12">

    <div>
        <h5>Client</h5>
    </div>
    <br>

    <div>
        <table class="table table-sm table-bordered">
            <tr>
                <td>Name</td>
                <td>
                    <strong>
                        <?php echo $client_name; ?>
                    </strong>
                </td>
            </tr>
            <tr>
                <td>Membership Number</td>
                <td>
                    <strong><?php echo $loan_detail['client_no']; ?></strong>
                </td>
            </tr>
            <tr>
                <td>Contact</td>
                <td>
                    <strong>
                        <?php echo $loan_detail['mobile_number']; ?>
                    </strong>
                </td>
            </tr>
        </table>
    </div>

    <br>
    <div>
        <h5>Loan details</h5>
    </div>
    <br>

    <div>
        <table class="table table-sm table-bordered">
        <tr>
            <td>Loan State</td>
            <td><?php echo $loan_detail['state_name']; ?></td>
            <td>Disbursed Amount</td>
            <td><?php echo number_format($loan_detail['amount_approved']); ?></td>
        </tr>
        <tr>
            <td>Loan Product</td>
            <td><?php echo $loan_detail['product_name']; ?></td>
            <td>Loan period in <?php if ($loan_detail['approved_repayment_made_every'] == 1) {
                                        echo "days";
                                    } elseif ($loan_detail['approved_repayment_made_every'] == 2) {
                                        echo "weeks";
                                    } elseif ($loan_detail['approved_repayment_made_every'] == 3) {
                                        echo "months";
                                    } ?>:</td>
                <td><?php $loanPeriod = $loan_detail['approved_installments'] * $loan_detail['approved_repayment_frequency']; ?><?php echo $loanPeriod; ?></td>
        </tr>
        <tr>                            
            <td>Interest rate:</td>
            <td><?php echo $loan_detail['interest_rate']; ?> %</td>
            <td>Loan instalment:</td>
            <td><?php echo number_format($loan_detail['approved_installments']); ?></td>
        </tr>
            <tr>
                <td>Interest Calculated:</td>
                <td><?php echo $loan_detail['type_name']; ?></td>
                <td>Loan purpose:</td>
                <td><?php echo $loan_detail['loan_purpose']; ?></td>
            </tr>
            <tr>
                <td>Application date:</td>
                <td><?php echo date('jS F, Y',strtotime($loan_detail['application_date'])); ?></td>

            <?php if (!empty($active_state)) {?>    
                    <td>Disbursement date:</td>
                    <td><?php echo date('d-M-Y', strtotime($loan_detail['action_date'])); ?></td>
                <?php } else { ?>
                <td></td>
                <?php } ?>
                
            </tr>
        </table>
    </div>

    <br>
    <div>
        <h5>Required Loan Fees</h5>
    </div>
    <br>

    <div  class="table-responsive">
        <table class="table table-sm table-bordered" width="100%">
            <tbody>
                <tr style="font-weight: bold;">
                    <td>Fee Name</td>
                    <td>Amount <small>(UGX)</small></td>
                    <td>Paid?</td>
                </tr>
                <?php 
                if (empty($applied_fees)) { ?>
                    <tr>
                        <td colspan="3">This loan had no required fees to be paid.</td>
                    </tr>
                <?php }else{ 
                    foreach($applied_fees as $applied_fee ){  ?>
                        <tr>
                            <td><?php echo $applied_fee['feename']; ?></td>
                            <td><?php echo number_format($applied_fee['amount']); ?></td>
                            <td><?php echo ($applied_fee['paid_or_not'] ==0)?'No':'Yes'; ?></td>
                        </tr>
                <?php } }?>
            </tbody>
        </table>
        
        <br>
        <div>
            <h5>Loan Collateral Security</h5>
        </div>
        <br>

        <table class="table table-sm table-bordered" width="100%"> 
        <tbody>
            <tr style="font-weight: bold;">
                <td>#</td>
                <td>Item Name</td>
                <td>Item Value <small>(UGX)</small></td>
            </tr>  
            <?php 
            
            if(empty($loan_collateral)){?>
                <tr><td colspan="3">No collateral security was provided by the applicant</td> </tr>
            <?php }else{
                foreach( $loan_collateral as $key => $collateral ){  ?>    
            <tr>
                <td><?php echo $key+1;?></td>
                <td><?php echo $collateral['item_name']; ?></td>
                <td><?php  echo number_format($collateral['item_value'],2); ?></td>
            </tr>
        <?php } } ?>
        </tbody>
        </table>
        
        <br>
        <div>
            <h5>Loan Guarantor</h5>
        </div>
        <br>

        <table class="table table-sm table-bordered" width="100%"> 
            <tbody>
                <tr style="font-weight: bold;">
                    <td>#</td>
                    <td>Guarantor Name</td>
                    <td>Amount Locked <small>(UGX)</small></td>
                </tr>  
                <?php                 
                if(empty($loan_guarantors)){?>
                    <tr><td colspan="3">No Guarantor was submitted by the loan applicant </td> </tr>
                <?php }else{
                foreach( $loan_guarantors as $key => $loan_guarantor ){  ?>    
                    <tr>
                        <td><?php echo $key+1;?></td>
                        <td><?php echo ucwords(strtolower($loan_guarantor['guarantor_name'])) . ' | ' . $loan_guarantor['client_no']  ; ?></td>
                        <td><?php  echo number_format($loan_guarantor['amount_locked'],2); ?></td>
                    </tr>
                <?php } } ?>

            </tbody>
        </table>
        

        <br>
        <div>
            <h5>Loan Approvals</h5>
        </div>
        <br>

        <table class="table table-sm table-bordered" width="100%"> 
            <tbody>
                <tr style="font-weight: bold;">
                    <td>#</td>
                    <td>Approved By</td>
                    <td>Date Approved</td>
                    <td>Amount <small>(UGX)</small></td>
                </tr>  
                <?php                 
                if(empty($loan_approvals)){?>
                    <tr><td colspan="3">No Approval was submitted for this loan applicantion </td> </tr>
                <?php }else{
                foreach($loan_approvals as $key => $loan_approval){  ?>    
                    <tr>
                        <td><?php echo $key+1;?></td>
                        <td><?php echo ucwords(strtolower($loan_approval['staff_name'])); ?></td>
                        <td><?php echo date('jS F, Y', strtotime($loan_approval['action_date'] )); ?></td>
                        <td><?php  echo number_format($loan_approval['amount_approved'],2); ?></td>
                    </tr>
                <?php } } ?>

            </tbody>
        </table>

        <br>
        <div>
            <h5>Repayment Schedule</h5>
        </div>
        <br>
        
        <table class="table table-sm table-bordered" width="100%"> 
            <tbody> 
                <tr style="font-weight: bold;">
                    <td>#</td>
                    <td>Due&nbsp;Date</td>
                    <td>Interest</td>
                    <td>Principal</td>
                    <td>Total</td>
                    <td>Principal&nbsp;Balance</td>
                </tr>
                <?php if (empty($repayment_schedules)) { ?>
                    <tr>
                        <td colspan="6">This loan has no payment schedule yet.</td>
                    </tr>
                <?php }else{  $paidPrincipal = 0;
                    foreach( $repayment_schedules as $repayment_schedule ){  ?>    
                        <tr>
                            <?php
                            $paidPrincipal += $repayment_schedule['principal_amount'];
                            $loanBalance = $loan_detail['amount_approved'] - $paidPrincipal; ?>
                            <td><?php echo $repayment_schedule['installment_number']; ?></td>
                            <td><?php echo date('d-M-Y', strtotime( $repayment_schedule['repayment_date'] )); ?></td>
                            <td><?php echo number_format(  $repayment_schedule['interest_amount'] ); ?></td>
                            <td><?php echo number_format(  $repayment_schedule['principal_amount'] ); ?></td>
                            <td><?php echo number_format(  $repayment_schedule['principal_amount'] + $repayment_schedule['interest_amount'] );  ?></td>
                            <td><?php echo number_format( $loanBalance ); ?></td>
                        </tr>
                <?php } } ?>
                <tr>
            </tbody>  
        </table>
        
        <br>
        <div>
            <h5>Loan Transactions</h5>
        </div>
        <br>

        <table class="table table-sm table-bordered" width="100%"> 
            <tbody> 
                <tr style="font-weight: bold;">
                    <td>#</td>
                    <td>Payment&nbsp;Name</td>
                    <td>Interest</td>
                    <td>Principal</td>
                    <td>Penalty</td>
                    <td>Transaction&nbsp;<small>Date</small></td>
                    <td>Amount</td>

                </tr>
                <?php
                $rows=0;

                 foreach($applied_fees as $applied_fee ){ 

                    if ($applied_fee['paid_or_not'] ==1) {
                        $rows +=1;?>
                        <tr>
                            <td><?php echo $rows; ?></td>
                            <td><?php echo $applied_fee['feename']; ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><?php echo date('d-M-Y h:i:s',strtotime($applied_fee['date_modified'])) ?></td>
                            <td><?php echo $applied_fee['amount']; ?></td>
                        </tr>
                <?php } }  $paidPrincipal = 0;
                    foreach($paid_schedules as $paid_schedule ){  
                        $rows +=1?>    
                        <tr>
                            <td><?php echo $rows; ?></td>
                            <td>Loan repayment</td>
                            <td><?php echo number_format(($paid_schedule['paid_interest']) ,2); ?></td>
                            <td><?php echo number_format( ($paid_schedule['paid_principal']),2 ); ?></td>
                            <td><?php echo number_format($paid_schedule['paid_penalty'],2);?></td>
                            <td><?php echo date('d-M-Y', strtotime($paid_schedule['payment_date'] )); ?></td>
                            <td><?php echo number_format(($paid_schedule['paid_penalty'] + $paid_schedule['paid_interest'] + $paid_schedule['paid_principal'] ),2 );  ?></td>
                        </tr>
                <?php }
                    if ($rows==0) {?>
                    <tr>
                        <td colspan="7">This loan has no payment transactions yet.</td>
                    </tr>
                   <?php } ?>
            </tbody>  
        </table>

      </div>
</div>

</div>
