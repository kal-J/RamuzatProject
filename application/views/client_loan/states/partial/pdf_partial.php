<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/pdf.css">
<table border="0" cellspacing="0">
    <tr>
        <td></td>
        <td></td>
        <td colspan="1"><img src="<?php echo base_url(); ?>images/logo2.png" height="40" /></td>
        <td colspan="6" style="color:#333;font-size:8"><?php echo $loan_detail['name']; ?><br />
            <?php echo $loan_detail['physical_address']; ?>, <?php echo $loan_detail['branch_name']; ?><br />
            <?php echo $loan_detail['postal_address']; ?><br />
            <?php echo $loan_detail['office_phone']; ?>, <?php echo $loan_detail['email_address']; ?>
        </td>
        <td></td>
    </tr>
    <tr>
        <td></td><td></td><td colspan="1"></td>
        <td colspan="6"><b>ILS LOAN APPRAISAL FORM</b></td>
        <td></td>
    </tr><hr />
</table>

<table border="0" cellspacing="0">

<tr><td><b>Borrower's details</b></td><td></td><td></td><td></td></tr>
    <tr>
        <td>Name:</td><td><?php echo $loan_detail['member_name']; ?></td>
        <td>Membership&nbsp;no.:</td><td><?php echo $loan_detail['client_no']; ?></td>    
    </tr>
    <tr>
        <td>Address:</td><td><?php echo $loan_detail['address1']; ?><?php echo   isset( $loan_detail['address2'] ) ? ', '.$loan_detail['address2'] : ''; ?></td>
        <td>Contact:</td><td><?php echo $loan_detail['mobile_number']; ?></td>    
    </tr><p></p>

    <tr>
        <td><b>Loan details:</b></td><hr />
    </tr> 

    <tr>
        <td>Loan period (months):</td><td><?php $loanPeriod = $loan_detail['approved_installments'] * $loan_detail['approved_repayment_made_every']; ?><?php echo $loanPeriod; ?></td>
        <td>Interest rate (flat):</td><td><?php echo $loan_detail['interest_rate']; ?> %</td>
    </tr>
    <tr>       
        <td>Loan instalment (monthly):</td><td><?php echo number_format( $loan_detail['approved_installments'] ); ?></td>
        <td>Loan type:</td><td><?php echo $loan_detail['type_name']; ?></td>
    </tr>
    <tr>       
        <td>Loan purpose:</td><td><?php echo $loan_detail['comment']; ?></td>
        <td>Disbursement date:</td><td><?php echo date('d-M-Y',strtotime($loan_detail['action_date'])) ; ?></td>   
    </tr><p></p>

    <tr><td><b>Required payments</b></td><td></td><td></td><td></td><hr /></tr>
    <?php foreach( $applied_fees as $applied_fee ){  ?>
        <tr><td><?php echo $applied_fee['feename']; ?></td><td><?php echo number_format($applied_fee['amount']); ?> UGX</td></tr>
    <?php } ?>
</table><p></p>

<table border="0" cellspacing="0">
    <tr><td><b>Loan payment schedule</b></td><td></td><td></td><td></td></tr><p></p>
</table><hr />

<table border="0" cellspacing="0">    
        <tr>
            <td>Inst.&nbsp;No.</td>
            <td>Due&nbsp;date</td>
            <td>Interest&nbsp;(UGX)</td>
            <td>Principal&nbsp;(UGX)</td>
            <td>Monthly&nbsp;Debit<br>(UGX)</td>
            <td>Loan&nbsp;Balance<br>(UGX)</td>
        </tr>
        <?php $paidPrincipal = 0;
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
        <?php } ?>
    <tr>
</table><p></p>


<table border="0" cellspacing="0">
    <tr><td><b>Loan security</b></td><td></td><td></td><td></td></tr><p></p>
</table><hr />

<table border="0" cellspacing="0">   
        <?php foreach( $loan_guarantors as $loan_guarantor ){  ?>    
            <tr>
                <td colspan="2"><?php echo $loan_guarantor['guarantor_name'] . ' | ' . $loan_guarantor['client_no']  ; ?></td>
                <!-- <td><?php // echo $loan_guarantor['amount_locked']; ?></td> -->
            </tr>
        <?php } ?>
</table><p></p><p></p>

<table border="0" cellspacing="0">
    <tr>        
        <td>I <span class="blueText"><b><?php echo $loan_detail['member_name']; ?></b></span> acknowledge receipt of Ugx. <b> <?php echo number_format( $loan_detail['amount_approved'] ) ; ?> </b> (<?php echo ucfirst(convert_number_to_words( round( $loan_detail['amount_approved'],0)  )) ; ?>  Shillings only) as a loan extended to me, 
that I MUST pay back as per the above Loan payment schedule.
        </td><br>   
    </tr>
</table><p></p>

<table border="0" cellspacing="0">
    <tr>      
        <td><?php echo $loan_detail['member_name']; ?></td><td><?php // echo $loan_detail['member_name']; ?></td><td><span class="blueText"><b><?php echo date( 'd-M-Y' ); ?></b></span></td> 
    </tr>

    <tr>      
        <td>Borrower's name</td><td>Borrower's signature</td><td>Date</td> 
    </tr>
</table>

<?php
function convert_number_to_words($number) {

    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'zero',
        1                   => 'one',
        2                   => 'two',
        3                   => 'three',
        4                   => 'four',
        5                   => 'five',
        6                   => 'six',
        7                   => 'seven',
        8                   => 'eight',
        9                   => 'nine',
        10                  => 'ten',
        11                  => 'eleven',
        12                  => 'twelve',
        13                  => 'thirteen',
        14                  => 'fourteen',
        15                  => 'fifteen',
        16                  => 'sixteen',
        17                  => 'seventeen',
        18                  => 'eighteen',
        19                  => 'nineteen',
        20                  => 'twenty',
        30                  => 'thirty',
        40                  => 'fourty',
        50                  => 'fifty',
        60                  => 'sixty',
        70                  => 'seventy',
        80                  => 'eighty',
        90                  => 'ninety',
        100                 => 'hundred',
        1000                => 'thousand',
        1000000             => 'million',
        1000000000          => 'billion',
        1000000000000       => 'trillion',
        1000000000000000    => 'quadrillion',
        1000000000000000000 => 'quintillion'
    );

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    return $string;
}

?>