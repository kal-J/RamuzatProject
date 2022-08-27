<div id="fiscal-modal" class="modal fade" role="dialog" aria-labelledby="modal_fiscal" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
                    <div class="ibox-title">
                        <h2> <center> Fiscal Year    (365 or 366 days) </center> </h2>
                        <div class="ibox-tools">
                            <a data-dismiss="modal" class="close">&times;</a>
                        </div>
                       
                    </div>
                    <div class="modal-body">
                    <hr>
                        <?php echo form_open_multipart("Fiscal_year/create", array('id' => 'formFiscal_year', 'class' => 'formValidate', 'name' => 'formFiscal_year', 'autocomplete'=>'off', 'data-toggle' => 'validator', 'role' => 'form')); ?>
                        <input autocomplete="false" name="hidden" type="text" style="display:none;">
                        <input type="hidden" name="id" id="id" >
                        <input type="hidden" name="organisation_id" id="organisation_id"  value="<?php echo $organisation['id']; ?>">
                        <!--input type="hidden" name="tbl" id="tbl" value="tblBranch" -->
                        Enter the <B> Start Date</B>  of your  Fiscal Year
                        <div class="form-group row">
                            <label class="col-lg-12 col-form-label">Start Date <span class="text-danger">*</span></label>
                            <div class="col-lg-12 form-group">
                                <div class="input-group date">
                                   <input type="text" class="form-control" id="start_date" name="start_date" required><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                       <!--  <div class="form-group row">
                            <label class="col-lg-12 col-form-label">End Date<span class="text-danger">*</span></label>
                            <div class="col-lg-12 form-group">
                                <div class="input-group date">
                                   <input type="text" class="form-control"  id="end_date" name="end_date" required><span class="input-group-addon"><i class="fa fa-calendar"></i> </span>  
                                </div>
                            </div>
                                
                        </div> -->
                        <div class="form-group row">
                            <div class="col-sm-12 ">
                            
                                <button id="btn-submit" name="btn_submit" class="btn btn-primary pull-right btn-sm save_data">
                                    <i class="fa fa-check"></i> Save</button>
                            
                            </div>
                        </div>
                        <span class="text-danger">NOTE: <b>The system will automatically determine your End Date</b> </span>
                        </form>
                    </div><!--/.ibox-content -->
        </div><!--/modal-content -->
    </div><!--/col-lg-6 -->
</div><!--/add_organisation-modal -->
