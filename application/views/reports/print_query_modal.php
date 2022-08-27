<span class="modal inmodal fade" id="print_query-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" target="_blank" action="<?php echo site_url("dividend_payment/print"); ?>" >
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Printout Options</h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">   
                     <input type="hidden" name="membership" data-bind="value:membership" />
                     <input type="hidden" name="savings" data-bind="value:period_savings" />
                     <input type="hidden" name="loans" data-bind="value:loans" />
                     <input type="hidden" name="shares" data-bind="value:shares" />
                     <input type="hidden" name="period" data-bind="value:selected_period" />
                     <input type="hidden" name="start_date" data-bind="value:start_date" />
                     <input type="hidden" name="end_date" data-bind="value:end_date" />
                     <input type="hidden" name="fiscal_1" data-bind="value:fiscal_1" />
                     <input type="hidden" name="fiscal_2" data-bind="value:fiscal_2" />
                     <input type="hidden" name="fiscal_3" data-bind="value:fiscal_3" />
                     <input type="hidden" name="print" value="1" />
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label" for="total_dividends">Paper</label>
                        <div class="form-group col-lg-8">
                         <select class="form-control m-b" name="paper" id="paper" required>
                            <option value="A4">A4</option>
                            <option value="A5">A5</option>
                            <option value="A3">A3</option>
                            <option value="A2">A2</option>
                            <option value="A1">A1</option>
                            <option value="A0">A0</option>
                            <option value="letter">Letter</option>
                        </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label" for="total_dividends">Orientation</label>
                        <div class="form-group col-lg-8">
                         <select class="form-control m-b" name="orientation" id="orientation" required>
                            <option value="portrait">Portrait</option>
                            <option value="landscape">Landscape</option>
                        </select>
                        </div>
                    </div>
                     <div class="form-group row">
                        <label class="col-lg-4 col-form-label" for="total_dividends">Download or Preview</label>
                        <div class="form-group col-lg-8">
                         <select class="form-control m-b" name="stream" id="stream" required>
                            <option value="1">Download</option>
                            <option value="0">Preview</option>
                        </select>
                        </div>
                    </div>
                  
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">File Name</label>
                        <div class="col-lg-8">
                        	<input type="text" name="filename" value="Performance Report" class="form-control" required />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <?php if (in_array('6', $report_privilege)) { ?>
                        <button type="submit" target="_blank" class="btn btn-primary"><i class="fa fa-print"></i> Print</button>
                    <?php } ?>
                </div>
            </div>  
        </form>
    </div>
</span>