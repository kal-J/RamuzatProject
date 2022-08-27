<!-- bootstrap modal -->
<div class="modal inmodal fade" id="add_backup-modal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" class="formValidate" action="<?php echo base_url(); ?>/backup/create" id="formBackup">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h3 class="modal-title">Create DB Backup</h3>
                    <small class="font-bold">Please Make sure you enter all the required fields correctly</small>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    <div class="form-group row">
                            <label class="col-lg-2 col-form-label">File Name<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
                                <input class="form-control" name="file_name" type="text" required />
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
