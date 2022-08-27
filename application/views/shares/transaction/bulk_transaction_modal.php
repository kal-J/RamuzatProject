<!-- bootstrap modal -->
<div class="modal inmodal fade" id="bulk_transaction-modal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" class="formValidate" enctype="multipart/form-data" action="<?php echo base_url();?>share_transaction/import" id="formBulk_deposit">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">X</span></button>
                <h3 class="modal-title">Bulk Transaction</h3>
                 <small class="font-bold">Note: Required fields are marked with. Only Excel files (.xls or .xlsx)  <span class="text-danger">*</span></small>
            </div>
            <div class="modal-body">
              <div class="form-group row">
                    <label class="col-lg-3 col-form-label"> Date <span class="text-danger">*</span></label>
                    <div class="col-lg-3 form-group">
                    <div class="input-group date"  data-date-start-date="<?php echo isset($active_month)?date('d-m-Y', strtotime($active_month['month_start'])):date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>" data-date-end-date="<?php echo isset($active_month)?((strtotime(date('d-m-Y'))<(strtotime($active_month['month_end'])))?date('d-m-Y'):date('d-m-Y', strtotime($active_month['month_end']))):((strtotime(date('d-m-Y'))<(strtotime($fiscal_active['end_date'])))?date('d-m-Y'):date('d-m-Y', strtotime($fiscal_active['end_date']))); ?>">
                        <input  type="text" class="form-control" onkeydown="return false" name="transaction_date" data-bind="datepicker: $root.transaction_date_bulk" required/>
                        <span class="input-group-addon" data-bind="datepicker: $root.transaction_date_bulk"><i class="fa fa-calendar"></i></span>
                       </div>
                    </div>

                    <label class="col-lg-2 col-form-label">Transaction channel <span class="text-danger">*</span></label>
                    <div class="col-lg-4">
                        <select  class="form-control" id="transaction_channel_id" name="transaction_channel_id" data-bind='options: transaction_channel, optionsText: "channel_name", optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), value:tchannels' data-msg-required="Transaction channel  must be selected" style="width: 100%" required >
                        </select>
                    </div>
                </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Select Excel File <span class="text-danger">*</span></label>
                        <div class="col-lg-9 form-group">
                            <input class="form-control" type="file" name="file" id="file" required accept=".xls, .xlsx" />
                        </div>                                                  
                    </div> 

                    <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Payment Method <span class="text-danger">*</span></label></label>
                        <div class="col-lg-3">
                            <select  data-bind='options: $root.payment_modes_bulk_trans;
                            , optionsText: "payment_mode", optionsCaption: "-- select --" ,optionsAfterRender: setOptionValue("id"),value: $root.payment_mode.id, attr:{name:"payment_id"}' class="form-control"  required > </select>
                        </div>
                     <label class="col-lg-2 col-form-label">Narrative <span class="text-danger">*</span></label>
                        <div class="col-lg-4">
                            <textarea placeholder="" required class="form-control" id="narrative" name="narrative"></textarea>
                        </div>
                    </div> 
                <div data-bind="visible: name_error()!==0">
                    <div style="text-align: center;"><h4 ><span class="alert alert-danger"><i class="fa fa-warning"></i> &nbsp; &nbsp; Excel Error Log</span></h4></div>
                  <table  class="table table-striped table-condensed table-hover m-t-md">
                        <thead>
                            <tr>
                                <th class="text-danger">Row No</th>
                                <th class="text-danger">Account ID</th>
                                <th class="text-danger">Member Name</th>
                                <th class="text-danger">Amount</th>
                                <th class="text-danger">Possible Error</th>
                            </tr>
                        </thead>
                      <tbody data-bind='foreach:name_error'>
                          <tbody>
                            <tr>
                                <th class="text-danger">Row No</th>
                                <th class="text-danger">Account ID</th>
                                <th class="text-danger">Member Name</th>
                                <th class="text-danger">Amount</th>
                                <th class="text-danger">Possible Error</th>
                            </tr>
                            <!-- <tr class="text-danger">
                                <td data-bind="text:row_id"></td>
                                <td data-bind="text:account_no_id"></td>
                                <td data-bind="text:client_name"></td>
                                <td data-bind="text:curr_format(amount,2)"></td>
                                <td data-bind="text:message"></td>
                            </tr>-->
                        </tbody>
                    </table>
                </div>             
                </div>
                <div class="modal-footer">
                <?php if(in_array('3', $share_privilege)){ ?>
                     <button id="btn-submit" type="submit" class="btn btn-success btn-sm save_data">
                        <i class="fa fa-check"></i> Submit Excel
                    </button>
                    <?php } ?>
                    <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm">
                        <i class="fa fa-times"></i> Cancel</button>
                    </div>
        </form>
        </div>
    </div>
</div>
