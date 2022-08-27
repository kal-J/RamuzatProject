<div id="add_group-modal" class="modal inmodal fade"  tabindex="-1" role="dialog" aria-hidden="true" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">×</span><span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title">New Investiment Group</h4>
                <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
            </div>
            <div class="modal-body">
                <div class="col-lg-12">
                    <form id="formGroup" name="formGroup" action="<?php echo site_url("group/create");?>" class="wizard-big formValidate">
                        <h1>Group Details</h1>
                        <input type="hidden" name="id" id="id" >
                        <div class="form-group">
                            <label>Group Name</label>
                            <label class="req text-danger">*</label>
                            <input  name="group_name" type="text" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <label class="req text-danger">*</label>
                            <textarea  name="description" class="form-control" required></textarea>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-6 col-sm-offset-3">
                            <?php if((in_array('1', $group_privilege))||(in_array('3', $group_privilege))){ ?>
                                <button class="btn btn-primary btn-sm" type="submit">Submit</button>
                            <?php } ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

