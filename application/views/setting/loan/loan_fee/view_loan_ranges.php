<div id="view_loan_ranges" class="modal lockmodal fade" tabindex="-1"  role="dialog">
<div class="modal-dialog lockmodal-dialog" style="min-width:600px;">
<div class="modal-content">
        <div class="row col-lg-12">
            <div class="table-responsive">
                <table  class="table ">
                    <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th>Min</th>
                            <th>Max</th>
                            <th>Fee type</th>
                            <th>Rate/Amount</th>
                        </tr>
                    </thead>
                    <tbody data-bind='foreach:$root.loan_range_fees'>
                        <tr>
                            <td>
                               &nbsp;
                            </td>
                            <td>
                                <span data-bind="text:curr_format(min_range)"></span>
                            </td>
                            <td>
                                <span data-bind="text:curr_format(max_range)"></span>
                            </td>
                            <td> 
                                <center><select disabled data-bind='options: $root.amountCalOptions, optionsText: function(item){return item.amountcalculatedas},attr:{name:"rangeFees["+$index()+"][calculatedas_id]"}, optionsAfterRender: setOptionValue("amountcalculatedas_id"), optionsCaption: "-- select --",value:calculatedas_id' class="form-control"  style="width: 170px;"> </select></center>
                            </td>
                            <td>
                               <b><span data-bind="text:curr_format(round(range_amount,0))"></span></b>
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
            
    </div>
    </div>
</div>
