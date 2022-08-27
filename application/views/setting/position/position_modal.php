<!-- bootstrap modal -->
<div class="modal inmodal fade" id="add_position-modal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form class="formValidate" id="formPosition" method="post" action="<?php echo site_url()?>position/create">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title">Position Creation</h3>
                <small class="font-bold">Please Make sure you enter all the required fields correctly</small>
            </div>
            <div class="modal-body">
                        <input type="hidden" name="id" id="id">
                        <div class="form-group row">
                            <label class="col-lg-1 col-form-label">Position</label>
                            <div class="col-lg-4 form-group">
                                <input type="text" required name="position" class="form-control">
                            </div>
                            <label class="col-lg-2 col-form-label">Description</label>
                            <div class="col-lg-5 form-group">
                                <textarea class="form-control" rows="3" name="description" id="description"></textarea>
                            </div>
                        </div>
                                                   
            </div>
            <div class="modal-footer"><!-- start of the modal footer -->
            <?php if((in_array('1', $privileges))||(in_array('3', $privileges))){ ?>
                <button id="btn-submit" type="submit" class="btn btn-success btn-sm save_data">
                    <i class="fa fa-check"></i> 
                    <?php
                        if (isset($saveButton)) {
                            echo $saveButton;
                        }else{
                            echo "Save";
                        }
                     ?>
                </button>
                <?php } ?>
                <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm">
                    <i class="fa fa-times"></i> Cancel</button>
            </div><!-- End of the modal footer -->            
        </form>
        </div>
    </div>
</div>
<!-- bootstrap modal ends -->