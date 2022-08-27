<div role="tabpanel" id="tab-report" class="tab-pane">
    <div class="panel-body">
        <div style="text-align: center;" id="reports_title"><h3>Savings Accounts - Reports</h3></div>
        <div class="row">
        <div class="float-right col-lg-12">
            <div class="form-row col-md-12">
                <label class="col-form-label">From<span class="text-danger">*</span></label>
                <div class="col-md-2 form-group">
                    <div class="input-group date">
                        <input class="form-control" id="start" autocomplete="off" name="start_date" data-bind="datepicker: $root.start_date,textInput:$root.start_date, event:{ change: $root.updateData}" type="text">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>

                <label class="col-form-label"> To<span class="text-danger">*</span></label>
                <div class="col-md-2 form-group">
                    <div class="input-group date">
                        <input class="form-control" id="end" autocomplete="off" name="end_date" data-bind="datepicker: $root.end_date,textInput:$root.end_date, event:{ change: $root.updateData}" type="text"/><span  class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>

                <label class="col-form-label">Product <span class="text-danger">*</span></label>
                <div class="col-md-2 form-group">
                    <div class="input-group">
<!--                        <select class="form-control select2able" id="product_id" style="width: 100%" name="deposit_Product_id" data-bind="options: ProductOptions, optionsText: 'productname', optionsAfterRender: setOptionValue('id'), optionsCaption: '--select--', value: Product">-->
<!--                        </select>-->
                    </div>
                </div>
                <div class="col-md-1 form-group">
                    <button type="button" class="btn mb-1 btn-primary" onclick="filter_reports_data()"><i class="fa fa-filter"></i> Filter</button>

                </div>
            </div>
        </div>
            <div class="row w-100 d-flex flex-row-reverse mx-4 pr-1 my-2">
                <a target="_blank" id="btn_print_savings_accounts_report">
                    <button class="btn btn-primary btn-sm">
                        <i class="fa fa-file-excel-o fa-2x"></i>
                    </button>
                </a>
            </div>
            <br>
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table class="table table-bordered display compact nowrap table-hover" width="100%"
                           id="tbl_saving_report">
                        <thead>
                        <tr>
                            <th>Account No</th>
                            <th>Name</th>
                            <th>Product</th>
                            <th>Minimum Balance</th>
                            <th>Locked Amount</th>
                            <th>Cash Balance</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>Account No</th>
                            <th>Name</th>
                            <th>Product</th>
                            <th>Minimum Balance</th>
                            <th>Locked Amount</th>
                            <th>Cash Balance</th>
                        </tr>
                        </tfoot>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>