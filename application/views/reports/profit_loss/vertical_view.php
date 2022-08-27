<!-- <div role="tabpanel" id="tab-pl_vertical" class="tab-pane active"> -->
<div class="panel-body">
    <div class="col-sm-12">
        <?php if(in_array('6', $report_privilege)) { ?>
        <div class="d-flex flex-row-reverse add-record-btn">
            <button
                data-toggle="modal" data-target="#printLayout"
                class="btn btn-primary btn-sm"> <i class="fa fa-print fa-2x"></i> </button>
        </div>
        <?php } ?>

        <div>
            <table class="table table-sm table-bordered" width="100%">
                <tbody>
                    <tr style="background-color: #1c84c6; color: #fff;">
                        <td colspan="2">
                            <h4>
                                <center>Income Statement &nbsp; &nbsp; &nbsp;<span
                                        data-bind="text:moment(end_date(),'X').format('DD-MMMM-YYYY')"></span>
                                </center>
                            </h4>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h4>INCOME</h4>
                        </td>
                    </tr>
                    <!-- ko foreach: income -->
                    <!-- ko if: ((parseFloat(amount)!==parseInt(0)) &&(parseFloat(cat)===parseInt(1))) -->
                    <tr style="background-color: #fafafc;">
                        <td style="padding-left:40px; font-weight:bold;"
                            data-bind="text: '['+account_code+'] '+account_name"></td>
                        <td>
                            <h4 class="no-margins"><span style="font-weight:bold;"
                                    data-bind="text:curr_format(round(amount,2))">0</span></h4>
                        </td>
                    </tr>
                    <!-- /ko -->
                    <!-- ko if: ((parseFloat(amount)!==parseInt(0)) &&(parseFloat(cat)===parseInt(0))) -->
                    <tr>
                        <td style="padding-left:80px;"><a
                                data-bind="attr: {href:'<?php echo site_url("accounts/view/");?>'+id}, text:'['+account_code+'] '+account_name"></a>
                        </td>
                        <td>
                            <h4 class="no-margins"><span class="text-success"
                                    data-bind="text:curr_format(round(amount,2))">0</span></h4>
                        </td>
                    </tr>
                    <!-- /ko -->
                    <!-- /ko -->

                    <tr style="background-color: #fafcdc;">
                        <th>Gross Income </th>
                        <th> <span data-bind="with:profitloss_sums"> <span
                                    data-bind="text:curr_format(round(total_income,2))">0</span> </span> </th>
                    </tr>

                    <tr>
                        <td>
                            <h4>EXPENSE</h4>
                        </td>
                    </tr>
                    <!-- ko foreach: expenses -->
                    <!-- ko if: ((parseFloat(amount)!==parseInt(0)) &&(parseFloat(cat)===parseInt(1))) -->
                    <tr style="background-color: #fafafc;">
                        <td style="padding-left:40px; font-weight:bold;"
                            data-bind="text:'['+account_code+'] '+account_name"></td>
                        <td>
                            <h4 class="no-margins"><span style="font-weight:bold;"
                                    data-bind="text:curr_format(round(amount,2))">0</span></h4>
                        </td>
                    </tr>
                    <!-- /ko -->
                    <!-- ko if: ((parseFloat(amount)!==parseInt(0)) &&(parseFloat(cat)===parseInt(0))) -->
                    <tr>
                        <td style="padding-left:80px;"><a
                                data-bind="attr: {href:'<?php echo site_url("accounts/view/");?>'+id}, text:'['+account_code+'] '+account_name"></a>
                        </td>
                        <td>
                            <h4 class="no-margins"><span class="text-success"
                                    data-bind="text:curr_format(round(amount,2))">0</span></h4>
                        </td>
                    </tr>
                    <!-- /ko -->
                    <!-- /ko -->
                    <!-- ko with: profitloss_sums -->

                    <tr style="background-color: #fafcdc;">
                        <th>Total expense </th>
                        <th> <span> <span data-bind="text:curr_format(parseFloat(total_expense)*1)">0</span> </th>
                    </tr>
                    <tr>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                    <tr class="table-primary" style="padding-left:40px;font-weight: bold;"
                        data-bind="visible: parseFloat(net_profit_loss)>0">
                        <td>Profit/Loss</td>
                        <th>
                            <span> <span data-bind="text:curr_format(round(net_profit_loss,2))">0</span> </span>
                        </th>
                    </tr>

                    <!-- /ko -->

                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- </div> -->

<div class="modal fade" id="printLayout" tabindex="-1" role="dialog" aria-labelledby="printLayoutTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="max-width: 80vw; width: 80vw">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Income Statement</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="printable">
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
                    <tr style="background-color: #1c84c6; color: #fff;">
                        <td colspan="2">
                            <h4>
                                <center>Income Statement &nbsp; &nbsp; &nbsp;<span
                                        data-bind="text:moment(end_date(),'X').format('DD-MMMM-YYYY')"></span>
                                </center>
                            </h4>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h4>INCOME</h4>
                        </td>
                    </tr>
                    <!-- ko foreach: income -->
                    <!-- ko if: ((parseFloat(amount)!==parseInt(0)) &&(parseFloat(cat)===parseInt(1))) -->
                    <tr style="background-color: #fafafc;">
                        <td style="padding-left:40px; font-weight:bold;"
                            data-bind="text: '['+account_code+'] '+account_name"></td>
                        <td>
                            <h4 class="no-margins"><span style="font-weight:bold;"
                                    data-bind="text:curr_format(round(amount,2))">0</span></h4>
                        </td>
                    </tr>
                    <!-- /ko -->
                    <!-- ko if: ((parseFloat(amount)!==parseInt(0)) &&(parseFloat(cat)===parseInt(0))) -->
                    <tr>
                        <td style="padding-left:80px;"><a
                                data-bind="attr: {href:'<?php echo site_url("accounts/view/");?>'+id}, text:'['+account_code+'] '+account_name"></a>
                        </td>
                        <td>
                            <h4 class="no-margins"><span class="text-success"
                                    data-bind="text:curr_format(round(amount,2))">0</span></h4>
                        </td>
                    </tr>
                    <!-- /ko -->
                    <!-- /ko -->

                    <tr style="background-color: #fafcdc;">
                        <th>Gross Income </th>
                        <th> <span data-bind="with:profitloss_sums"> <span
                                    data-bind="text:curr_format(round(total_income,2))">0</span> </span> </th>
                    </tr>

                    <tr>
                        <td>
                            <h4>EXPENSE</h4>
                        </td>
                    </tr>
                    <!-- ko foreach: expenses -->
                    <!-- ko if: ((parseFloat(amount)!==parseInt(0)) &&(parseFloat(cat)===parseInt(1))) -->
                    <tr style="background-color: #fafafc;">
                        <td style="padding-left:40px; font-weight:bold;"
                            data-bind="text:'['+account_code+'] '+account_name"></td>
                        <td>
                            <h4 class="no-margins"><span style="font-weight:bold;"
                                    data-bind="text:curr_format(round(amount,2))">0</span></h4>
                        </td>
                    </tr>
                    <!-- /ko -->
                    <!-- ko if: ((parseFloat(amount)!==parseInt(0)) &&(parseFloat(cat)===parseInt(0))) -->
                    <tr>
                        <td style="padding-left:80px;"><a
                                data-bind="attr: {href:'<?php echo site_url("accounts/view/");?>'+id}, text:'['+account_code+'] '+account_name"></a>
                        </td>
                        <td>
                            <h4 class="no-margins"><span class="text-success"
                                    data-bind="text:curr_format(round(amount,2))">0</span></h4>
                        </td>
                    </tr>
                    <!-- /ko -->
                    <!-- /ko -->
                    <!-- ko with: $root.profitloss_sums() -->

                    <tr style="background-color: #fafcdc;">
                        <th>Total expense </th>
                        <th> <span> <span data-bind="text:curr_format(parseFloat(total_expense)*1)">0</span> </th>
                    </tr>
                    <tr>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                    <tr class="table-primary" style="padding-left:40px;font-weight: bold;">
                        <td>Profit/Loss</td>
                        <th>
                            <span> <span data-bind="text:curr_format(round(net_profit_loss,2))">0</span> </span>
                        </th>
                    </tr>

                    <!-- /ko -->

                </tbody>
            </table>
        </div>
            
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button onclick="printJS({printable: 'printable', type: 'html', targetStyles: ['*'], documentTitle: 'Income-Statement'})" type="button" class="btn btn-primary">Print</button>
      </div>
    </div>
  </div>
</div>