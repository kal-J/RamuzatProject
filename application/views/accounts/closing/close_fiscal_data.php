<style type="text/css">
    section{
    overflow-y : auto;
    }
    /*.inmodal.modal-header{
        padding: 0px 15px;
        text-align: center;
        display: block;
    }

    .modal-body{
        padding: 20px 10px 0px 10px;
    }*/
    @media (min-width: 992px) {
      .modal-lg,
      .modal-xl {
        max-width: 600px;
      }
    }

    @media (min-width: 1200px) {
      .modal-xl {
        max-width: 1140px;
      }
    }
</style>
<div class="modal bd-example-modal-xl inmodal fade" id="close_fiscal-modal" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
              <div class="modal-header" style="padding: 0px 15px; text-align: center; display: block;">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                    Financial Year Ending - <span class="text-success"><?php echo date('F d,Y', strtotime($fiscal_year['end_date'])); ?> </span>
                    </h4>
                    <p>Summary of accounts and reports</p>
                </div>
            
                <div class="modal-body" style="padding: 20px 10px 0px 10px;"> 
                <form method="post" class="formValidate wizard-big" action="<?php echo base_url("Fiscal_year/close_fiscal_year"); ?>" id="formClose_fiscal">
                    <h1>Income and Expense summary</small></h1> <!-- Step one -->
                    <section>
                    <input type="hidden" name="organisation_id" id="organisation_id"  value="<?php echo $_SESSION['organisation_id']; ?>">
                    <input type="hidden" name="fiscal_id" id="fiscal_id"  value="<?php echo $fiscal_year['id']; ?>">
                     <input type="hidden" name="end_date" id="end_date"  value="<?php echo date('d-m-Y', strtotime($fiscal_year['end_date'])); ?>">
                    <div class="row">
                    <div class="col-lg-6">
                        <center><h5>Income / Revenue</h5></center>
                    <div class="table-responsive" style="overflow-y : auto; max-height: 300px;">
                        <table  border="0" class="table-bordered display compact nowrap" style="width:100%">
                            <thead class="thead-light" >
                                <tr>
                                    <th>Account Name</th>
                                    <th>Closing Balance</th>
                                </tr>
                            </thead>
                            <tbody data-bind="foreach:select2accounts([12,13])">
                                 <tr>
                                    <td><span data-bind="text:account_name"></span></td>
                                    <td><span data-bind="text:(parseInt(normal_balance_side)===1)?(curr_format((total_debit?total_debit:0)*1-(total_credit?total_credit:0)*1)):(curr_format((total_credit?total_credit:0)*1-(total_debit?total_debit:0)*1))"></span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div><!-- /.table-responsive-->
                </div>
                <div class="col-lg-6">
                    <center><h5>Expenses</h5></center>
                    <div class="table-responsive"  style="overflow-y : auto; max-height: 300px;">
                        <table   border="0" class="table-bordered display compact nowrap" style="width:100%">
                            <thead class="thead-light" >
                                <tr>
                                    <th>Account Name</th>
                                    <th>Closing Balance</th>
                                </tr>
                            </thead>
                            <tbody data-bind="foreach:select2accounts([14,15,16])">
                                 <tr>
                                    <td><span data-bind="text:account_name"></span></td>
                                    <td><span data-bind="text:(parseInt(normal_balance_side)===1)?(curr_format((total_debit?total_debit:0)*1-(total_credit?total_credit:0)*1)):(curr_format((total_credit?total_credit:0)*1-(total_debit?total_debit:0)*1))"></span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div><!-- /.table-responsive-->
                </div>
            </div>
            <div class="hr-line-dashed"></div>
               <div class="row">
                    <div class="col-lg-3">
                    </div>
                    <div class="col-lg-6">
                    <center><h4>Income Summary Account</h4></center>
                    <div class="table-responsive">
                        <table id="tblAccounts"  border="0" class="table-bordered display compact nowrap" style="width:100%">
                            <thead class="thead-light" >
                                <tr>
                                    <th>Income</th>
                                    <th>Expense</th>
                                </tr>
                            </thead>
                            <tbody >
                                 <tr>
                                    <td><span data-bind="text:curr_format(income_sums())"></span></td>
                                    <td><span data-bind="text:curr_format(expense_sums())"></span></td>
                                </tr>
                                <tr>
                                    <td colspan="2"></td>
                                </tr>
                                 <tr>
                                    <td colspan="2"><center><b>Net Profit: </b><span data-bind="text:curr_format(round(profit_loss(),2))"></span></center></td>
                                </tr>
                            </tbody>
                        </table>
                    </div><!-- /.table-responsive-->
                    </div>
                    <div class="col-lg-3">
                    </div>
                </div>
                <!-- //////////////////////////////////////// -->
                <div class="row">
                    <div class="col-lg-4">
                    <center><h4>Dividends</h4></center>
                    <div class="table-responsive">
                        <table id="tblAccounts"  border="0" class="table-bordered display compact nowrap" style="width:100%">
                            <thead class="thead-light" >
                                <tr>
                                    <th>Type</th>
                                    <th>Closing Balance</th>
                                </tr>
                            </thead>
                            <tbody >
                                 <tr>
                                    <td><span >Dr</span></td>
                                    <td><span data-bind="text:curr_format(0)"></span></td>
                                </tr>
                               
                            </tbody>
                        </table>
                    </div><!-- /.table-responsive-->
                    </div>
                    <div class="col-lg-3">
                    </div>
                    <div class="col-lg-5">
                         <center><h4>Retained Earnings</h4></center>
                    <div class="table-responsive">
                        <table id="tblAccounts"  border="0" class="table-bordered display compact nowrap" style="width:100%">
                            <thead class="thead-light" >
                                <tr>
                                    <th>Account Name</th>
                                    <th>Closing Balance</th>
                                </tr>
                            </thead>
                            <tbody >
                                 <tr>
                                    <td><span >Income summary Account</span></td>
                                    <td><span data-bind="text:curr_format(round(profit_loss(),2))"></span></td>
                                </tr>
                                 <tr>
                                    <td><span >Dividends</span></td>
                                    <td><span data-bind="text:curr_format(0)"></span></td>
                                </tr>
                               
                            </tbody>
                        </table>
                    </div><!-- /.table-responsive-->
                    </div>
                </div>
                    </section>

                    <h1>Trial Balance </h1><!-- Step three -->
                    <section class="section">
                           <center><h5>Trial balance Summary</h5></center>
                    <div class="table-responsive"  >
                        <table  border="0" class="table-bordered display compact nowrap" style="width:100%">
                            <thead class="thead-light" >
                                <tr>
                                    <th>Account Name</th>
                                    <th>Debit Total</th>
                                    <th>Credit Total</th>
                                </tr>
                            </thead>
                            <tbody  data-bind="foreach:select2accounts([1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16])">
                                 <tr>
                                    <td><span data-bind="text:account_name"></span></td>

                                     <td><span data-bind="text:curr_format((total_debit?total_debit:0)*1)"></span></td>

                                    <td><span data-bind="text:curr_format((total_credit?total_credit:0)*1)"></span></td>
                                </tr>
                            </tbody>
                             <tr>
                                <td><b>Totals</b></td>
                                <td><b><span data-bind="text:curr_format(debit_sums())"></span></b></td>
                                <td><b><span data-bind="text:curr_format(credit_sums())"></span></b></td>
                            </tr>
                        </table>
                    </div><!-- /.table-responsive-->
                    </section>

                    <h1>Balance Sheet</h1> <!-- Step two -->
                    <section class="section">
                               <div class="row">
                    <div class="col-lg-6">
                        <center><h5>Assets </h5></center>
                    <div class="table-responsive"  style="overflow-y : auto; max-height: 600px;">
                        <table  border="0" class="table-bordered display compact nowrap" style="width:100%">
                            <thead class="thead-light" >
                                <tr>
                                    <th>Account Name</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody data-bind="foreach:select2accounts([1,2,3,4,5,6,7])">
                                 <tr>
                                    <td><span data-bind="text:account_name"></span></td>
                                    <td><span data-bind="text:(parseInt(normal_balance_side)===1)?(curr_format((total_debit?total_debit:0)*1-(total_credit?total_credit:0)*1)):(curr_format((total_credit?total_credit:0)*1-(total_debit?total_debit:0)*1))"></span></td>
                                </tr>
                            </tbody>
                            <tr>
                                <td><b>Total</b></td>
                                <td><b><span data-bind="text:curr_format(asset_sums())"></span></b></td>
                            </tr>
                        </table>
                    </div><!-- /.table-responsive-->
                </div>
                <div class="col-lg-6">
                    <center><h5>Liability and Equity</h5></center>
                    <div class="table-responsive"  style="overflow-y : auto; max-height: 600px;">
                        <table   border="0" class="table-bordered display compact nowrap" style="width:100%">
                            <thead class="thead-light" >
                                <tr>
                                    <th>Account Name</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody data-bind="foreach:select2accounts([8,9,10,11,19])">
                                 <tr>
                                    <td><span data-bind="text:account_name"></span></td>
                                    <td><span data-bind="text:(parseInt(normal_balance_side)===1)?(curr_format((total_debit?total_debit:0)*1-(total_credit?total_credit:0)*1)):(curr_format((total_credit?total_credit:0)*1-(total_debit?total_debit:0)*1))"></span></td>
                                </tr>
                            </tbody>
                            <tr>
                                <td><b>Total</b></td>
                                <td><b><span data-bind="text:curr_format(parseFloat(liability_sums())+parseFloat(equity_sums())+parseFloat(profit_loss()))"></span></b></td>
                            </tr>
                        </table>
                    </div><!-- /.table-responsive-->
                </div>
            </div>
                    </section>

                    <h1>Close Financial Year</h1><!-- Step three -->
                    <section class="section">
                        <center><h3>Points to note before closing the Financial year ...</h3></center>
                     <p><span>1. </span> A new Financial Year will be automatically created according to your settings. </p>
                     <p><span>2. </span> The System will automatically adjust and zeroout Income / Revenue and Expense accounts of the closed Financial Year</p>
                     <p><span>3. </span> The process will also post accounts opening balances for the new Financial Year.</p>

                     <p><span>4. </span> It also assumes all necessary dividends have been paid and the remaining balance ( <b>Net profit </b> ) will now be posted into Retained earnings </p>
<!-- 
                      <p><span>1. </span> A new Financial Year will be automatically created according to your settings. </p>
                     <p><span>2. </span> The System will automatically adjust and zeroout Income / Revenue and Expense accounts of the close Financial Year</p>
                     <p><span>3. </span> The process will also post accounts opening balances for the new Financial Year.</p>

                     <p><span>4. </span> It also assumes all necessary dividends have been paid and the remaining balance ( <b>Net profit </b> ) will now be posted into Retained earnings </p> -->
                     <br>
                     <br>
                     <br>
                     <p>
                    <input type="checkbox" name="check_me" required="required" >  I am ready to close the Financial Year
                         
                     </p>
                    </section>
                </form>
                </div>
                <!--  <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <?php //if ((in_array('1', $accounts_privilege)) || (in_array('3', $accounts_privilege))) { ?>
                        <button type="submit" class="btn btn-danger btn-flat">Close Fiscal Year</button>
                    <?php // } ?>
                </div> -->
        </div>
    </div>
</div>
