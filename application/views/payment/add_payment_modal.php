<div class="modal fade" id="addPaymentModal">
    <div class="col-lg-8 col-lg-offset-2">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" class="close" data-dismiss="modal" type="button">&times;</button>
                <h4 class="modal-title"><i class="fa fa-save"></i> Payment Details</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="widget-container fluid-height clearfix">
                            <div class="heading">
                                <small><i>All fields marked with asterisks (*) are required</i></small>
                            </div>
                            <div class="widget-content padded">
                                <form  id="formPayment" action="<?php echo site_url("payment/create2"); ?>" method="post" data-toggle="validator" class="form-horizontal">
                                    <div class="col-lg-6">
                                    <div class="form-group">
                                        <div class="col-lg-12">
                                            <input type="hidden" name="tbl" value="tblPayment">
                                            <input type="hidden" id="id"  name="id" >
                                            <input type="hidden" id="sale_id"  name="sale_id" value="<?php echo $this->uri->segment(3); ?>">
                                            <label class="control-label col-md-4"  for="payment_date">Payment date<small>*</small></label>
                                            <div class="form-group col-md-8">
                                                <div class="input-group input-append date" id="payment_date_dtp">
                                                    <input type="text" class="form-control datepicker" id="payment_date" name="payment_date" placeholder="dd-mm-yyyy" data-pattern-error="Invaild date. Required format dd-mm-yyyy" pattern="^(((0[1-9]|[12]\d|3[01])-(0[13578]|1[02])-((19|[2-9]\d)\d{2}))|((0[1-9]|[12]\d|30)-(0[13456789]|1[012])-((19|[2-9]\d)\d{2}))|((0[1-9]|1\d|2[0-8])-02-((19|[2-9]\d)\d{2}))|(29-02-((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$" required />
                                                    <span class="input-group-addon add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                                                </div>
                                                <span class="help-block with-errors"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="form-group">
                                        <div class="col-lg-12">
                                            <label class="control-label col-md-4" for="payment_ref">Reference No <small>*</small></label>
                                            <div class="form-group col-md-8">
                                                <input name="payment_ref" id="payment_ref" type="text" class="form-control" placeholder="Payments reference number" required="required"/>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="form-group">
                                        <div class="col-lg-12">
                                            <label class="control-label col-md-4" for="amount">Amount <small>*</small></label>
                                            <div class="form-group col-md-8">
                                                <input type="number" class="form-control" name="amount" id="amount" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                                <label for="payment_notes">Notes</label>
                                                <div class="form-group col-md-12">
                                                    <textarea name="payment_notes" id="payment_notes" class="form-control" placeholder="Enter N/A if not applicable" rows="4"></textarea>
                                                    <div class="help-block with-errors"></div>
                                                </div>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="form-group">
                                        <div class="col-lg-12">
                                            <label class="control-label col-md-4">&nbsp;</label>
                                            <div class="col-md-7">
                                                <button class="btn btn-primary save" type="submit">Submit</button>
                                                <button class="btn btn-default-outline" type="reset" data-dismiss="modal" >Cancel </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div><!-- /.widget-content-->
                        </div><!-- /.widget-container-->
                    </div><!-- /.col-lg-12-->
                </div><!-- /.row-->
            </div><!-- /.modal-body-->
        </div><!-- /.modal-content-->
    </div><!-- /.col-lg-8-->
</div><!-- /.modal-->