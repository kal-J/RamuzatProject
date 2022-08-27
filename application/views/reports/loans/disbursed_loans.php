<div role="tabpanel" id="tab-disbursed_loans" class="tab-pane disbursed_loans">
    <div class="panel-title">
        <br>
        <div style="text-align: center;"><h3 style="font-weight: bold;border-bottom:dashed 1px #ccc;">Disbursed Loan Reports</h3></div>
         
     </div>

    <table style="border: 0px;" class="table-responsive">
        <tr>
           <td style="padding-right: 40px;">

           <div class="row col-12 d-flex justify-content-center m-3">
      <div  id="btn3" class="input-group date col-6">
        <label class="my-auto" for="start_date">From :&nbsp;</label>
        <input class="col-6" autocomplete="off" placeholder="DD-MM-YYYY" value="" type="text" onkeydown="return false" name="start_date_at" id="start_date_at" required />
        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
      </div> 
       
      <div  id="btn3" class="input-group date col-6">
        <label class="my-auto" for="start_date">To :&nbsp;</label>
        <input class="col-6" autocomplete="off" placeholder="DD-MM-YYYY" value="" type="text" onkeydown="return false" name="end_date_at" id="end_date_at" required />
        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
      </div> 
    </div>
      
            </td>

            <td>
            <div class="col-4 mx-4">
      <button id="btn5" onclick="set_disbursed_filter_dates_value()" class="btn btn-sm btn-primary">
      Filter
      </button>
      </div>
      
            </td>
           <!-- <td style="padding-right: 20px;">
                <div class="col">
                    <div class="row" style="min-width: 70px">
                        <label class="col-form-label"><strong>Loan Type</strong></label></div>
                    <div class="row" style="min-width: 70px">
                        <select style="max-height:30px;margin-top:8px;"
                                name="loan_type" id="active_loan_type">
                            <option value="">--Select--</option>
                            <option value="0">Unsecured</option>
                            <option value="1">Secured</option>
                        </select>
                    </div>
                </div>
            </td>
            <td style="padding-right: 20px;">
                <div class="col">
                    <div class="row" style="min-width: 70px">
                        <label class="col-form-label" style="min-width: 70px"><strong>Loan Product</strong></label>
                    </div>
                    <div class="row" style="min-width: 70px">
                        <select style="max-height:30px;margin-top:8px;"
                                data-bind='options:loan_product_data, optionsText: function(item){return item.product_name}, optionsAfterRender: setOptionValue("id"), optionsCaption: "-- select --"'
                                name="product_id" id="active_product_id"> </select>
                    </div>
                </div>
            </td>
            <td style="padding-right: 20px;">
                <div class="col">
                    <div class="row" style="min-width: 70px">
                        <label class="col-form-label"><strong> Due Days</strong></label></div>
                    <div class="row">
                        <div class="col">
                            <select style="max-height:30px;margin-top:8px;"
                                    id="active_condition" name="condition" rel="tooltip"
                                    title="Select Condition"
                                    data-bind='options: period_condition, optionsText: "condition_name", optionsAfterRender: setOptionValue("id"),optionsCaption: "-- select --", value: periods'>
                            </select>

                            <input style="max-height:30px; margin-top:8px; width:80px;"
                                   placeholder="No. of days" name="due_days"
                                   id="active_due_days" min="1" type="number">
                        </div>
                    </div>
                </div>
            </td>
            <td style="padding-right: 20px;">
                <div class="col">
                    <div class="row" style="min-width: 70px">
                        <label class="col-form-label"><strong>Credit officer</strong></label></div>
                    <div class="row" style="min-width: 70px">
                        <select style="max-height:30px;margin-top:8px;"
                                name="credit_officer_id" id="credit_officer_id"
                                data-bind='options: credit_officers, optionsText: function(data){ return data.firstname+" "+ data.lastname+" "+data.othernames}, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id"), value: credit_officer'
                        >
                        </select>
                    </div>
                </div>
            </td>-->
            <!-- <td style="padding-right: 20px;">
                <div class="col">
                    <div class="row" style="min-width: 70px">
                        <label class="col-form-label"><strong> Next Pay Date</strong></label></div>
                    <div class="row">
                        <div class="col">
                            <select style="max-height:30px;margin-top:8px;"
                                    name="due_month" id="next_due_month">
                                <option value="">--Select--</option>
                                <option value="1">January</option>
                                <option value="2">February</option>
                                <option value="3">March</option>
                                <option value="4">April</option>
                                <option value="5">May</option>
                                <option value="6">June</option>
                                <option value="7">July</option>
                                <option value="8">August</option>
                                <option value="9">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>

                            <select style="max-height:30px;margin-top:8px;"
                                    name="due year" id="next_due_year">
                                <option value="">--Select--</option>
                                <?php
                                // for ($i = date("Y") - 3; $i <= date("Y") + 5; $i++) {
                                //     echo '<option value="' . $i . '">' . $i . '</option>' . PHP_EOL;
                                // }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </td> -->
           <!-- <td style="padding-right: 20px;">
                  <div class="row" style="min-width: 70px">
                        <label class="col-form-label"><strong>&nbsp;</strong></label></div>
                <button onclick="set_active_select_value2()" type="button" class=" btn-primary">Filter</button>
            </td>-->
        </tr>
    </table>

    <div  class="d-flex flex-row-reverse mx-2 my-2">
        <a target="_blank" id="print_active_loans_excel">
            <button class="btn btn-primary btn-sm">
               <i class="fa fa-file-excel-o fa-2x"></i>  
            </button> 
        </a>
    </div>

    <br>
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover dataTables-example" id="tblDisbursed_client_loan"
               style="width: 100%">
            <thead>

            <tr>
                <th>Reference #</th>
                <th>Client Name</th>
                <th>Requested Amount</th>
                <th>Disbursed Amount (UGX)</th>
                <th>Expected Interest (UGX)</th>
                <th>Paid Amount (UGX)</th>
                <th>Remaining bal (UGX)</th>
                <th>Status</th>
               
            </tr>

            </thead>
            <tbody>
            </tbody>
         <tfoot>
            <tr>
                <th colspan="2">Totals</th>
                <th></th>
                <th>0</th>
                <th>0</th>
                <th>0</th>
               
                <th colspan="2">&nbsp;</th>
            </tr>
            </tfoot> 
        </table>
    </div>
</div>

