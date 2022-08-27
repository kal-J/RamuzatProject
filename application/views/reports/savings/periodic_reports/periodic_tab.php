<div role="tabpanel" id="tab-periodic" class="tab-pane">
    <div class="panel-body">
        <div style="text-align: center;" id="reports_title">
            <h3>Savings Accounts - Periodic Reports</h3>
        </div>
        <div class="row">
            <div class="float-right col-lg-12">
                <div class="form-row col-md-12">
                    <label class="col-form-label">From<span class="text-danger">*</span></label>
                    <div class="col-md-2 form-group">
                        <div class="input-group date">
                            <input class="form-control" id="min" autocomplete="off" name="start_date" data-bind="datepicker: $root.start_date,textInput:$root.start_date, event:{ change: $root.updateData}" type="text"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        </div>
                    </div>

                    <label class="col-form-label"> To<span class="text-danger">*</span></label>
                    <div class="col-md-2 form-group">
                        <div class="input-group date">
                            <input class="form-control" id="max" autocomplete="off" name="end_date" data-bind="datepicker: $root.end_date,textInput:$root.end_date, event:{ change: $root.updateData}" type="text"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        </div>
                    </div>
                    <label class="col-form-label"> Deposit </label>
                    <div class="col-md-2 form-group">
                        <div class="input-group">
                            <select name="deposit" id="deposit" class="form-control">
                                <option value="2">All</option>
                                <option value="1">Deposited</option>
                                <option value="3">No Deposit</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 form-group">
                        <button type="button" class="btn mb-1 btn-primary" onclick="filter_periodic_data()"><i class="fa fa-filter"></i> Filter</button>

                    </div>
                </div>
            </div>
            <div class="row w-100 d-flex flex-row-reverse mx-4 pr-1 my-2">
                <a target="_blank" id="btn_print_savings_periodic_reports">
                    <button class="btn btn-primary btn-sm">
                        <i class="fa fa-file-excel-o fa-2x"></i>
                    </button>
                </a>
            </div>
            <br>
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table class="table table-bordered display compact nowrap table-hover" width="100%" id="tbl_periodic_report">
                        <thead>
                            <tr>
                                <th>Account No</th>
                                <th>Name</th>
                                <th>Deposits</th>
                                <th>Withdraws</th>
                                <th>Transfers</th>
                                <th>Payments</th>
                                <th>Charges</th>
                                <th>Cash Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Account No</th>
                                <th>Name</th>
                                <th>Deposits</th>
                                <th>Withdraws</th>
                                <th>Transfers</th>
                                <th>Payments</th>
                                <th>Charges</th>
                                <th>Cash Balance</th>
                            </tr>
                        </tfoot>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>