<section id="printable_client_loan_disbursement_sheet">
    <table class="table table-sm ">
            <tr><td>
<div class="row d-flex flex-column justify-content-center " style="font-size: small;padding-left: 10px;">
        <img style="width: 200px;height: 50px;" src="<?php echo base_url('uploads/organisation_' . $_SESSION['organisation_id'] . '/logo/' . $org['organisation_logo']);  ?>" />

        
        <span class="my-1" style="font-size: 16px; text-align:left;"><?php echo $org['name']; ?>, <?php echo $branch['physical_address']; ?>, <?php echo $branch['branch_name']; ?>
        </span>
        <span class="my-1">
            <?php echo $branch['postal_address']; ?>, <b>Tel:</b> <?php echo $branch['office_phone']; ?>
        </span>

    </div>
</td><td>
    <?php if(empty($loan_detail['photograph'])){?>
     <img style="width: 100px;height: 100px;float:right;" src="<?php echo base_url('images/avatar.png');?>" />
    <?php }else{ ?>
        <img style="width: 100px;height: 100px;" src="<?php echo base_url('uploads/organisation_' . $_SESSION['organisation_id'] . '/user_docs/profile_pics/' . $loan_detail['photograph']);  ?>" />
    <?php } ?>

</td>
</tr>
</table>
    <br>
    <div>
        <h5 class="text-success my-2">
            <center><small><?php echo $loan_detail['loan_no']; ?>-</small> Disbursement Sheet</center>
            </center>
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
        <h5>Required Loan Fees</h5>
    </div>
    <br>

    <div>
        <table class="table table-sm table-bordered">
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
                    <?php } else {
                    foreach ($applied_fees as $applied_fee) {  ?>
                        <tr>
                            <td><?php echo $applied_fee['feename']; ?></td>
                            <td><?php echo number_format($applied_fee['amount']); ?></td>
                            <td><?php echo ($applied_fee['paid_or_not'] == 0) ? 'No' : 'Yes'; ?></td>
                        </tr>
                <?php }
                } ?>
            </tbody>
        </table>
    </div>

    <br>
    <div>
        <h5>Repayment Schedule</h5>
    </div>
    <br>

    <div>
        <table class="table table-sm table-bordered">
            <tbody>
                <tr style="font-weight: bold;">
                    <td>#</td>
                    <td>Due&nbsp;Date</td>
                    <td>Interest</td>
                    <td>Principal</td>
                    <td>Total Amount</td>
                    <td>Principle&nbsp;Balance</td>

                </tr>
                <?php if (empty($repayment_schedules)) { ?>
                    <tr>
                        <td colspan="6">This loan has no payment schedule yet.</td>
                    </tr>
                    <?php } else {
                    $paidPrincipal = 0;
                    foreach ($repayment_schedules as $repayment_schedule) {  ?>
                        <tr>
                            <?php
                            $paidPrincipal += $repayment_schedule['principal_amount'];
                            $principleBalance = $loan_detail['amount_approved'] - $paidPrincipal; ?>
                            <td><?php echo $repayment_schedule['installment_number']; ?></td>
                            <td><?php echo date('d-M-Y', strtotime($repayment_schedule['repayment_date'])); ?></td>
                            <td><?php echo number_format($repayment_schedule['interest_amount']); ?></td>
                            <td><?php echo number_format($repayment_schedule['principal_amount']); ?></td>
                            <td><?php echo number_format($repayment_schedule['principal_amount'] + $repayment_schedule['interest_amount']);  ?></td>
                            <td><?php echo number_format($principleBalance); ?></td>

                        </tr>
                <?php }
                } ?>
                <tr>
            </tbody>
        </table>
    </div>

    <br>
    <br>
    <br>
    <br>
    <div>
        <h5>Loan Collateral Security</h5>
    </div>
    <br>

    <div>
        <table class="table table-sm table-bordered">
            <tbody>
                <tr style="font-weight: bold;">
                    <td>#</td>
                    <td>Item</td>
                    <td>Item Value <small>(UGX)</small></td>
                    <td>Description</td>
                </tr>
                <?php

                if (empty($loan_collateral)) { ?>
                    <tr>
                        <td colspan="3">No collateral security was provided by the applicant</td>
                    </tr>
                    <?php } else {
                    foreach ($loan_collateral as $key => $collateral) {  ?>
                        <tr>
                            <td><?php echo $key + 1; ?></td>
                            <td><?php echo $collateral['collateral_type_name']; ?></td>
                            <td><?php echo number_format($collateral['item_value'], 2); ?></td>
                            <td><?php echo $collateral['description']; ?></td>
                        </tr>
                <?php }
                } ?>
            </tbody>
        </table>
    </div>

    <br>
    <div>
        <h5>Loan Guarantor</h5>
    </div>
    <br>

    <div>
        <table class="table table-sm table-bordered">
            <tbody>
                <tr style="font-weight: bold;">
                    <td>#</td>
                    <td>Guarantor Name</td>
                    <td>Amount Locked <small>(UGX)</small></td>
                </tr>
                <?php
                if (empty($loan_guarantors)) { ?>
                    <tr>
                        <td colspan="3">No Guarantor was submitted by the loan applicant </td>
                    </tr>
                    <?php } else {
                    foreach ($loan_guarantors as $key => $loan_guarantor) {  ?>
                        <tr>
                            <td><?php echo $key + 1; ?></td>
                            <td><?php echo ucwords(strtolower($loan_guarantor['guarantor_name'])) . ' | ' . $loan_guarantor['client_no']; ?></td>
                            <td><?php echo number_format($loan_guarantor['amount_locked'], 2); ?></td>
                        </tr>
                <?php }
                } ?>

            </tbody>
        </table>
    </div>

    <br>
    <div>
        <table class="table border-0" style="border: none;" cellspacing="0">
            <tr>
                <td>I <span class="blueText"><b><?php echo $loan_detail['member_name']; ?></b></span> acknowledge receipt of Ugx. <b> <?php echo number_format($loan_detail['amount_approved']); ?> </b> (<?php echo ucfirst(convert_number_to_words(round($loan_detail['amount_approved'], 0))); ?> Shillings only) as a loan extended to me,
                    that I MUST pay back as per the above Loan payment schedule.
                </td><br>
            </tr>
        </table>
    </div>


    <p></p>

    <div>
        <table class="table border-0" style="border: none;" cellspacing="0">
            <tr>
                <td><?php echo $loan_detail['member_name']; ?></td>
                <td>................................. </td>
                <td><span class="blueText"><b><?php echo date('d-M-Y'); ?></b></span>&nbsp;</td>
            </tr>

            <tr>
                <td>Borrower's name</td>
                <td>Borrower's signature</td>
                <td>Print Date</td>
            </tr>
        </table>
    </div>


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