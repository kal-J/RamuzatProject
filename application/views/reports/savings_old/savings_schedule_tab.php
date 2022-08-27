<div role="tabpanel" id="tab-savings_schedule" class="tab-pane">
    <div class="panel-body">
        <div class="row">
            <!-- <div class="col-lg-2">
                <h2>Schedule Analysis</h2>
            </div> -->
            <div class="float-right col-lg-12">
                <div class="row">
                    <label class="col-lg-1 col-form-label">Status<span class="text-danger">*</span></label>
                    <div class="col-lg-2 form-group">
                        <select  class="form-control" name="status_id" id="status_id" data-bind='options: statuses, optionsText: function(data){ return data.name}, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id"), value: status, event:{ change: $root.updateData}' style="width: 100%" > 
                        </select>

                    </div>                
                    <label class="col-lg-1 col-form-label">From<span class="text-danger">*</span></label>
                    <div class="col-lg-2 form-group">
                        <div class="input-group date">
                           <input class="form-control" autocomplete="off" name="start_date" data-bind="datepicker: $root.start_date,textInput:$root.start_date, event:{ change: $root.updateData}" type="text"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        </div>
                    </div>

                    <label class="col-lg-1 col-form-label">To<span class="text-danger">*</span></label>
                    <div class="col-lg-2 form-group">
                        <div class="input-group date">
                           <input class="form-control" autocomplete="off" name="end_date" data-bind="datepicker: $root.end_date,textInput:$root.end_date, event:{ change: $root.updateData}" type="text"><span  class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        </div>
                    </div>

                    <label class="col-lg-1 col-form-label">Products<span class="text-danger">*</span></label>
                    <div class="col-lg-2 form-group">
                        <select  class="form-control" name="product_id" id="product_id"
                        data-bind='options: products, optionsText: function(data){ return data.productname}, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id"), value: product, event:{ change: $root.updateData}' style="width: 100%" > 
                        </select>

                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table class="table table-bordered display compact nowrap table-hover"  width="100%" id="tblSavings_schedule">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Account</th>
                                <th>Product</th>
                                <th>Period</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>