<div class="modal inmodal fade" id="print-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" target="_blank" class="formValidate" action="<?php echo site_url("dividend_payment/print"); ?>" >
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Printout Options</h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="dividend_declaration_id" value="<?php echo $dividend_declaration['id'];?>" />
                    <input type="hidden" name="fiscal_year_id" value="<?php echo $dividend_declaration['fiscal_year_id'];?>" />
                    <input type="hidden" name="declaration_date" value="<?php echo $dividend_declaration['declaration_date'];?>" />
                    <input type="hidden" name="record_date" value="<?php echo $dividend_declaration['record_date'];?>" />

                  
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
                        	<input type="text" name="filename" value="Dividends Paid" class="form-control" required />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <?php if (in_array('6', $accounts_privilege)) { ?>
                        <button type="submit" target="_blank" class="btn btn-primary"><i class="fa fa-print"></i> Print</button>
                    <?php } ?>
                </div>
            </div>  
        </form>
    </div>
</div>