<div class="d-none hidden">


    <div id="printable_loan_amortization">
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

        <div class="table">
            <table class="table table-striped table-bordered table-hover">
                <caption class="text-success" style=" font-size: 1.5em; font-weight: bold; text-align: center; caption-side: top;">Loan Amortization Schedule</caption>
                <thead>
                    <tr>
                        <th>## </th>
                        <th>Date of Payment</th>
                        <th>Interest Amount(UGX)</th>
                        <th>Principal Amount(UGX)</th>
                        <th>Total Installment(UGX)</th>
                    </tr>
                </thead>
                <tbody data-bind="foreach: payment_schedule">
                    <tr>
                        <td><span data-bind="text: (installment_number)?installment_number:''"></span></td>
                        <td><span data-bind="text: (payment_date)?moment(payment_date,'X').format('D-MMM-YYYY'):'None';"></span></td>
                        <td><span data-bind="text: curr_format(round(interest_amount,2))"></span></td>
                        <td> <span data-bind="text: curr_format(round(principal_amount,2))"></span></td>
                        <td data-bind="text: curr_format(round(paid_principal,2))"></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr data-bind="with: payment_summation">
                        <th></th>
                        <th data-bind="text: 'Period '+ payment_date"></th>
                        <th data-bind="text: 'Total '+ curr_format(round(interest_amount,2))"></th>
                        <th data-bind="text: 'Total '+ curr_format(round(principal_amount,0))"></th>
                        <th data-bind="text: 'Total '+ curr_format(round(paid_principal,2))"></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- ko if: $root.filtered_loan_fees() && $root.product_name() -->
        <div class="row col-lg-12">
            <fieldset class="col-lg-12">
                <legend class="text-success" style="text-align: center; font-size: 1.4em;">Required Fees</legend>
                <table class='table table-hover'>
                    <thead>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Amount</th>
                    </thead>
                    <tbody>
                        <!-- ko foreach: $root.filtered_loan_fees -->
                        <tr>
                            <td data-bind="text: feename"></td>
                            <td data-bind="text: feetype"></td>
                            <td data-bind="text: parseInt(amountcalculatedas_id) === 2 ? curr_format(round(amount , 2)) : (
                                            parseInt(amountcalculatedas_id) === 1 ? curr_format(round(parseFloat($root.amount() ? $root.amount() : $root.product_name().min_amount)*( parseFloat(amount) /100) , 2)) : (
                                                parseInt(amountcalculatedas_id) === 3 ? curr_format(round($root.compute_fee_amount(loanfee_id, $root.amount() ? $root.amount() : $root.product_name().min_amount) )) : 0
                                            )
                                        )"></td>

                        </tr>

                        <!-- /ko -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>
                                <strong>Total</strong>

                            </td>
                            <td></td>
                            <td style="font-weight: bold;" data-bind="text: $root.filtered_loan_fees_total()"></td>
                        </tr>
                    </tfoot>
                </table>
            </fieldset>
        </div>
        <div class="col-lg-12 my-1"></div>
        <!--/ko -->


    </div>
</div>