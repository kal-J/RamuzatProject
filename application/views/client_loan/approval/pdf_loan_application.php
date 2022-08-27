<section id="printable_client_loan_application_form">
    <table class="table table-sm ">
        <tr>
            <td>
                <div class="row d-flex flex-column justify-content-center " style="font-size: small;padding-left: 10px;">
                    <img style="width: 200px;height: 50px;" src="<?php echo base_url('uploads/organisation_' . $_SESSION['organisation_id'] . '/logo/' . $org['organisation_logo']);  ?>" />


                    <span class="my-1" style="font-size: 16px; text-align:left;"><?php echo $org['name']; ?>, <?php echo $branch['physical_address']; ?>, <?php echo $branch['branch_name']; ?>
                    </span>
                    <span class="my-1">
                        <?php echo $branch['postal_address']; ?>, <b>Tel:</b> <?php echo $branch['office_phone']; ?>
                    </span>

                </div>
            </td>
            <td>
                <?php if (empty($loan_detail['photograph'])) { ?>
                    <img style="width: 100px;height: 100px;float:right;" src="<?php echo base_url('images/avatar.png'); ?>" />
                <?php } else { ?>
                    <img style="width: 100px;height: 100px;" src="<?php echo base_url('uploads/organisation_' . $_SESSION['organisation_id'] . '/user_docs/profile_pics/' . $loan_detail['photograph']);  ?>" />
                <?php } ?>

            </td>
        </tr>
    </table>
    <br>
    <div>
        <h5 class="text-success my-2">
            <center>Loan Application Form</center>
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
                <td>Client Number</td>
                <td>
                    <strong>
                        <?php echo ucwords(strtolower($loan_detail['client_no'])); ?>
                    </strong>
                </td>
                <td>Name</td>
                <td>
                    <strong>
                        <?php echo ucwords(strtolower($loan_detail['member_name'])); ?>
                    </strong>
                </td>
            </tr>
            <tr>
                <td>Date of Birth</td>
                <td>
                    <strong><?php echo $loan_detail['date_of_birth']; ?></strong>
                </td>
                <td>Marital Status</td>
                <td>
                    <strong><?php echo $loan_detail['marital_status_name']; ?></strong>
                </td>
            </tr>
            <tr>
                <td>Contact</td>
                <td>
                    <strong>
                        <?php echo $loan_detail['mobile_number']; ?>
                    </strong>
                </td>
                <td>Email</td>
                <td>
                    <strong>
                        <?php echo ''; ?>
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
                <td>Requested Loan Amount </td>

                <td colspan="3"><strong><?php echo $loan_detail['requested_amount']; ?></strong></td>
            </tr>
            <tr>
                <td>Loan period in <?php if ($loan_detail['repayment_made_every'] == 1) {
                                        echo "days";
                                    } elseif ($loan_detail['repayment_made_every'] == 2) {
                                        echo "weeks";
                                    } elseif ($loan_detail['repayment_made_every'] == 3) {
                                        echo "months";
                                    } ?>:</td>
                <td><strong><?php $loanPeriod = $loan_detail['installments'] * $loan_detail['repayment_frequency']; ?><?php echo $loanPeriod; ?></strong></td>
                <td>Interest rate:</td>
                <td><strong><?php echo $loan_detail['interest_rate']; ?> %</strong></td>
            </tr>
            <tr>
                <td>Loan instalment:</td>
                <td><strong><?php echo number_format($loan_detail['installments']); ?></strong></td>
                <td>Interest Calculated:</td>
                <td><strong><?php echo $loan_detail['type_name']; ?></strong</td>
            </tr>
            <tr>
                <td>Loan Product:</td>
                <td><strong><?php echo $loan_detail['product_name']; ?></strong></td>
                <td>Application Date:</td>
                <td><strong><?php echo date('d-M-Y', strtotime($loan_detail['application_date'])); ?></strong></td>
            </tr>
            <tr>
                <td>Loan purpose:</td>
                <td colspan="3"><?php echo $loan_detail['loan_purpose']; ?></td>
            </tr>
        </table>
    </div>
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
                        <td colspan="4">No collateral security was provided by the applicant</td>
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
        <h6>Credit Officer: &nbsp <strong><?php echo $loan_detail['credit_officer_name']; ?></strong></h6>
    </div>

    <br>
    <br>
    <div>
        <h4>Declaration</h4>
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
                <td>Applicant's name</td>
                <td>Applicant's signature</td>
                <td>Print Date</td>
            </tr>
        </table>
    </div>



    <?php
    function convert_number_to_words($number)
    {

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