<section id="printable_client_loan_schedule">
    <div class="row d-flex flex-column justify-content-center align-items-center" style="font-size: small;">
        <img style="width: 200px;height: 40px;" src="<?php echo base_url('uploads/organisation_' . $_SESSION['organisation_id'] . '/logo/' . $org['organisation_logo']);  ?>" />

        
        <span class="my-1" style="font-size: 16px; text-align:left;"><?php echo $org['name']; ?>, <?php echo $branch['physical_address']; ?>, <?php echo $branch['branch_name']; ?>
        </span>
        <span class="my-1">
            <?php echo $branch['postal_address']; ?>, <b>Tel:</b> <?php echo $branch['office_phone']; ?>
        </span>

    </div>

<br>
<br>
    <div>
        <h5 class="text-success my-2">
            <center>Loan <small><?php echo $loan_detail['loan_no']; ?></small> Schedule</center>
        </h5>
    </div>


    <br>

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
                        <?php echo ucwords(strtolower($loan_detail['member_name'])); ?>
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
                <td>Loan period in <?php if ($loan_detail['approved_repayment_made_every'] == 1) {
                                        echo "days";
                                    } elseif ($loan_detail['approved_repayment_made_every'] == 2) {
                                        echo "weeks";
                                    } elseif ($loan_detail['approved_repayment_made_every'] == 3) {
                                        echo "months";
                                    } ?>:</td>
                <td><?php $loanPeriod = $loan_detail['approved_installments'] * $loan_detail['approved_repayment_frequency']; ?><?php echo $loanPeriod; ?></td>
                <td>Interest rate:</td>
                <td><?php echo $loan_detail['interest_rate']; ?> %</td>
            </tr>
            <tr>
                <td>Loan instalment:</td>
                <td><?php echo number_format($loan_detail['approved_installments']); ?></td>
                <td>Interest Calculated:</td>
                <td><?php echo $loan_detail['type_name']; ?></td>
            </tr>
            <tr>
                <td>Loan purpose:</td>
                <td><?php echo $loan_detail['loan_purpose']; ?></td>
                <td>Disbursement date:</td>
                <td><?php echo date('d-M-Y', strtotime($loan_detail['action_date'])); ?></td>
            </tr>
        </table>
    </div>

    <br>
    <div>
        <h5>Loan Schedule</h5>
    </div>
    <br>

    <div>
        <table class="table table-sm table-bordered">
            <tbody>
                <tr style="font-weight: bold;">
                    <td>#</td>
                    <td>Repayment&nbsp;Date</td>
                    <td>Interest</td>
                    <td>Principal</td>
                    <td>Penalty</td>
                    <td>Total Amount</td>
                    <td>Date Paid</td>
                    <td>Payment status</td>

                </tr>
                <?php if (empty($loan_schedule)) { ?>
                    <tr>
                        <td colspan="6">This loan has no schedule.</td>
                    </tr>
                    <?php } else {
                    $paidPrincipal = 0;
                    foreach ($loan_schedule as $schedule) {  ?>
                        <tr>
                            <?php
                            //$paidPrincipal += $schedule['principal_amount'];
                            // $loanBalance = $loan_detail['amount_approved'] - $paidPrincipal; 
                            ?>
                            <td><?php echo $schedule['installment_number']; ?></td>

                            <td><?php echo date('d-M-Y', strtotime($schedule['repayment_date'])); ?>
                            </td>
                            <td>
                                <?php echo number_format($schedule['interest_amount']); ?>
                            </td>
                            <td>
                                <?php echo number_format($schedule['principal_amount']); ?>
                            </td>
                            <td>
                                <?php echo is_numeric($schedule['penalty_value']) ? number_format($schedule['penalty_value']) : 0; ?>
                            </td>
                            <td>
                                <?php echo is_numeric($schedule['penalty_value']) ? number_format($schedule['penalty_value'] + $schedule['total_amount']) : number_format($schedule['total_amount']); ?>
                            </td>
                            <td><?php echo strtotime($schedule['actual_payment_date']) > 1 ? date('d-M-Y', strtotime($schedule['actual_payment_date'])) : 'N/A' ; ?>
                            </td>
                            <td><?php echo $schedule['payment_name']; ?></td>

                        </tr>
                <?php }
                } ?>
                <tr>
            </tbody>
        </table>
    </div>
    <br>
    <br>
    <div>
        <h5>Loan Payments</h5>
    </div>
    <br>

    <div>
        <table class="table table-sm table-bordered">
            <tbody>
                <tr style="font-weight: bold;">
                    <td>#</td>
                    <td>Interest</td>
                    <td>Principal</td>
                    <td>Penalty</td>
                    <td>Total Amount</td>
                    <td>Balance</td>
                    <td>Date Paid</td>

                </tr>
                <?php if (empty($loan_payments)) { ?>
                    <tr>
                        <td colspan="7">This loan has no Payments.</td>
                    </tr>
                    <?php } else {
                    foreach ($loan_payments as $payment) {  ?>
                        <tr>
                            <td><?php echo $payment['installment_number']; ?></td>
                            <td>
                                <?php echo number_format($payment['paid_interest']); ?>
                            </td>
                            <td>
                                <?php echo number_format($payment['paid_principal']); ?>
                            </td>
                            <td>
                                <?php echo number_format($payment['paid_penalty']); ?>
                            </td>
                            <td>
                                <?php echo number_format($payment['paid_principal'] + $payment['paid_interest'] +  $payment['paid_penalty']); ?>
                            </td>
                            <td>
                                <?php echo number_format($payment['end_balance']); ?>
                            </td>
                            <td>
                                <?php echo date('d-M-Y', strtotime($payment['payment_date'])); ?>
                            </td>
                        </tr>
                <?php }
                } ?>
                <tr>
            </tbody>
        </table>
    </div>


    <br>





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

</section>