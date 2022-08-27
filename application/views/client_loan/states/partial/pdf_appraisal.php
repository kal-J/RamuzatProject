<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/pdf.css">
<table border="0" cellspacing="0">
    <tr>
        <td></td>
        <td></td>
        <td></td><td></td><td></td><td></td>
        <td colspan="1"><img src="<?php echo base_url(); ?>images/logo2.png" height="40" /></td>
        <td colspan="3" style="color:#333;font-size:8; text-align:right;"><?php echo $loan_detail['name']; ?><br />
            <?php echo $loan_detail['physical_address']; ?>, <?php echo $loan_detail['branch_name']; ?><br />
            <?php echo $loan_detail['postal_address']; ?><br />
            <?php echo $loan_detail['office_phone']; ?>, <?php echo $loan_detail['email_address']; ?>
        </td>        
    </tr>

</table>
<table border="0" cellspacing="0">
    <tr>
        <td></td><td></td><td colspan="1"></td>
        <td colspan="6"><b>ILS LOAN APPRAISAL FORM</b></td>
        <td></td>
    </tr><hr />
</table>

<table border="0" cellspacing="0">
<tr><td><b>Client's details</b></td><td></td><td></td><td></td></tr>
    <tr>
        <td>Applicant Name:</td><td><?php echo $loan_detail['member_name']; ?></td>
        <td>Date&nbsp;of&nbsp;Birth:</td><td><?php echo date('d-M-Y',strtotime($loan_detail['date_of_birth'])); ?></td>    
    </tr>
    <tr>
        <td>Type of employee:</td><td> <?php // echo 'Type of employee'; // $loan_detail['member_name']; ?> </td>
        <td>Issuing Authority:</td><td><?php echo 'Issuing Authority'; // $loan_detail['member_name']; ?></td>
    </tr>
    <tr>
        <td>Date of Loan Application:</td><td><?php echo date('d-M-Y',strtotime($loan_detail['application_date'])); ?></td>
        <td>Marital status:</td><td><?php echo $loan_detail['marital_status_name']; ?></td>
    </tr>
    <tr>
        <td>No. of children:</td><td><?php echo $users['children_no']; ?></td>
        <td>No. of dependants:</td><td><?php echo $users['dependants_no']; ?></td>
    </tr>
</table><p></p>

<table border="0" cellspacing="0">
    <tr><td><b>Names of Children(Less than 16yrs)</b></td></tr><p></p>
</table><hr />
<table border="0" cellspacing="0">
    <tr>
        <td>Name</td>
        <td>Age(years)</td>
    </tr>
    <?php 
        foreach( $children as $child ){ 
        if(  calc_period_from_date( $child['date_of_birth'] ) < 16 ){            
    ?>
    
    <tr>
        <td><?php echo $child['firstname'] . ' '. $child['lastname'] . ' ' . $child['othernames']; ?></td>
        <td><?php echo calc_period_from_date( $child['date_of_birth'] ); ?></td>
    </tr>
    <?php }} ?>
    <p></p>
</table>
<p></p>
<table border="0" cellspacing="0">
    <tr><td><b>Insurance Information</b></td></tr><p></p>
</table><hr />
<table border="0" cellspacing="0">
    <tr>
        <td>Name of spouse: <?php echo $users['spouse_names']; ?></td>
        <td>Telephone of spouse: <?php echo $users['spouse_contact']; ?></td>
    </tr>
    <p></p>
</table><p></p>

<table border="0" cellspacing="0">
    <tr><td><b>Financial Information</b></td></tr><p></p>
</table>
<table border="0" cellspacing="0">
    <tr>
        <td>
                <table border="0" cellspacing="0">
                    <tr>
                        <td border="1"><b>Monthly income (UGX)</b></td>
                        <td border="1"></td>
                    </tr>
                        <?php $monthly_income_total = 0; $monthly_expense_total = 0; ?>
                        <?php foreach( $monthly_incomes as $monthly_income ){ $monthly_income_total += $monthly_income['amount'];?>
                            <tr>
                            <td>
                                <?php echo $monthly_income['income_type']; ?>
                            </td>
                            <td><?php echo number_format(round( $monthly_income['amount'], 1)); ?></td>
                            </tr>
                        <?php } ?>
                    <p></p>
                </table>
        </td>
        <td>
                <table  cellspacing="0">
                    <tr>
                        <td border="1"><b>Monthly expense (UGX)</b></td>
                        <td border="1"></td>
                    </tr>
                    <?php $monthly_expense_total = 0; ?>
                        <?php foreach( $monthly_expenses as $monthly_expense ){ $monthly_expense_total += $monthly_expense['amount']; ?>
                            <tr>
                            <td>
                                <?php echo $monthly_expense['expense_type']; ?>
                            </td>
                            <td><?php echo number_format(round($monthly_expense['amount'] ,1)); ?></td>
                            </tr>
                        <?php } ?>
                    <p></p>
                </table>
        </td>
    </tr><br>
    <tr>
                <table border="1" cellspacing="0">
                    <tr>
                        <td><b>Total Income</b></td>
                        <td><?php echo number_format(round($monthly_income_total, 1)).' /='; ?></td>
                        <td><b>Total Expenditure</b></td>
                        <td><?php echo number_format(round($monthly_expense_total, 1)).' /='; ?></td>
                    </tr>
                    <?php $surplus = $monthly_income_total - $monthly_expense_total; ?>
                    <tr>
                        <td colspan="2"><b>Monthly surplus </b></td>
                        <td colspan="2"><b><?php echo number_format(round( $surplus,1 )).' /='; ?></b></td>
                    </tr>
                </table>
    </tr><br>
<table border="0" cellspacing="0">
    <tr><td><b>Details of Dependants not leaving with Applicants</b></td></tr><p></p>
</table><hr />
<table border="0" cellspacing="0">
    <tr>
        <td>Name</td>
        <td>Residential Address</td>
        <td>Telephone</td>
    </tr>
    <?php foreach( $nextofkins as $nextofkin ){ ?>
    <tr>
        <td><?php echo $nextofkin['firstname'] . ' ' . $nextofkin['lastname']. ' ' . $nextofkin['othernames']; ?></td>
        <td><?php echo $nextofkin['address']; ?></td>
        <td><?php echo $nextofkin['telphone']; ?></td>
    </tr>
    <?php } ?>
    <p></p>
</table><p></p>


<table border="0" cellspacing="0">
    <tr>
        <td>Card No.: <?php echo $loan_detail['crb_card_no']; ?></td>
        <td>Residential Address:<br> <?php echo $addresses['address1'] . ' ' . $addresses['address2']; ?>,<br> <?php echo $addresses['district'] . ' , ' . $addresses['subcounty']; ?></td>
        <td>Length of current residence:<br><?php echo calc_date_diff( $addresses['start_date'], $addresses['end_date'] ); ?> </td>
    </tr>
    <?php foreach( $employments as $employment ){ ?>
    <tr>
        <td>Name of employer : <?php echo $employment['employer']; ?> </td>
        <td>Address Location : </td>
        <?php $d1 = $employment['start_date'];
              $d2 = $employment['end_date'] == ''? 0: $employment['end_date'] ; ?>
        <td>No. of years with employer :  <?php // echo round(abs(strtotime($d1) - strtotime($d2)) / 86400); ?></td>
    </tr>    
    <tr>
        <td>Type of employment: <?php echo $employment['name']; ?></td><td>  </td><td></td>
    </tr>   
    <?php } ?> 
</table><p></p>

<table border="0" cellspacing="0">
    <tr><td><b>Contract details</b></td></tr><p></p>
</table><hr />
<table border="0" cellspacing="0">
    <tr>
        <td>Date of Application: <?php echo $loan_detail['application_date']; ?></td>
        <td>Credit Officer: <?php echo $loan_detail['credit_officer_name']; ?></td>
        <td>Branch: <?php echo $loan_detail['branch_name']; ?></td>
    </tr>
    <tr>
        <td>Account No.: <?php echo $users['account_no']; ?></td>
        <td>Amount Applied for :<?php echo number_format(round($loan_detail['requested_amount'], 1)); ?> UGX</td>
        <?php ( $repayment_period =  $loan_detail['approved_repayment_frequency'] * $loan_detail['installments'] );?>
        <td>Repayment Period : <?php echo $repayment_period.' '. $loan_detail['approved_repayment_made_every']; ?> </td>
    </tr>
    <tr>
        <td>Cycle <?php // echo $loan_detail['member_name']; ?> </td>
        <td>Customer ID: <?php echo $loan_detail['client_no']; ?></td>
        <td>Proposed Instalment: <?php echo $loan_detail['approved_installments']; ?></td>
    </tr>
    <tr>
        <td>Purpose of Loan: <?php echo $loan_detail['loan_purpose']; ?></td>
    </tr>    
</table><p></p>
<table border="0" cellspacing="0">
    <tr><td><b>Previous Loan details</b></td></tr><p></p>
</table><hr />
<table border="0" cellspacing="0">
    <tr>
        <td>Previous Loan Amount: <?php echo number_format(round($loan_detail_prev['requested_amount'], 1)); ?></td>
        <td>Previous Loan Type: <?php echo $loan_detail_prev['product_name']; ?></td>
        <?php ( $repayment_period =  $loan_detail_prev['approved_repayment_frequency'] * $loan_detail_prev['approved_installments'] );?>
        <td>Previous Loan Term:  <?php echo $repayment_period.' '. $loan_detail_prev['approved_repayment_made_every']; ?> </td>
    </tr>
    <tr>
        <td>Purpose of Loan: <?php echo $loan_detail_prev['loan_purpose']; ?></td>
    </tr>    
</table><p></p>
<table border="0" cellspacing="0">
    <tr><td><b>Security Presented ( Give detailed description of land )</b></td></tr><p></p>
</table><hr />
<table border="0" cellspacing="0">
    <tr>
        <td>Guarantor's Name</td>
        <td>Telephone</td>
        <td>Address</td>
    </tr>
    <?php foreach( $guarantors as $guarantor ){ ?>
    <tr>
        <td><?php echo $guarantor['guarantor_name']; ?></td>
        <td><?php echo $guarantor['mobile_number']; ?></td>
        <td><?php echo $guarantor['address1'] .(isset( $guarantor['address2'] ) ? ', ' .$guarantor['address2'] : '' )?></td>
    </tr>
    <?php } ?>
    <br >
    <tr>
        <td>Security type</td>
        <td>Serial No.</td>
        <td>Value</td>
    </tr>
    <?php
    $item_value_total = 0; 
    foreach( $collaterals as $collateral ){ 
        $item_value_total +=$collateral['item_value'];?>
    <tr>
        <td><?php echo $collateral['collateral_type_name']; ?></td>
        <td><?php // echo $collateral['member_name']; ?></td>
        <td><?php echo number_format(round($collateral['item_value'], 1)); ?> UGX</td>
    </tr>
    <?php } ?>
    <tr>
        <td>Total Value of Security </td><td></td><td><?php  echo isset( $item_value_total )? number_format(round($item_value_total, 1)) . ' UGX' : '';  ?> </td>
    </tr>
</table><p></p>

<?php
    function calc_period_from_date( $date ){
        $birthDate = date( 'm-d-Y', strtotime( $date ) );
        $birthDate = explode("-", $birthDate);
        $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md")
        ? ((date("Y") - $birthDate[2]) - 1) : (date("Y") - $birthDate[2]));
        return $age;
    }  


    function calc_date_diff( $date1, $date2 ){
        $diff = abs(strtotime($date2) - strtotime($date1));
        $years = floor($diff / (365*60*60*24));
        $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
        $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
        return  $years . 'years ' . $months. 'months ' . $days . 'days';
    }
?>