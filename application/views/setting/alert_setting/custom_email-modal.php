<!-- bootstrap modal -->
<div class="modal inmodal fade" id="custom_email-modal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" class="formValidate" enctype="multipart/form-data" action="<?php echo base_url();?>alert_setting/create2" id="formCustom_email">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">X</span></button>
                <h3 class="modal-title">Compose New Email</h3>
                 <small class="font-bold">Note: Required fields are marked with<span class="text-danger">*</span></small>
            </div>
            <div class="modal-body">
              <!--<div class='form-group row'>
                <label for='to' class='col-2 col-sm-1 col-form-label'>From:</label>
                <div class='col-10 col-sm-11'>
                    <input type='email' name='sender' id='sender' class='form-control' id='to' placeholder='Sender'required>
                </div>
            </div>-->
            <div class='form-group row'>
                <label for='to' class='col-2 col-sm-1 col-form-label'>To:</label>
                <div class='col-10 col-sm-8'>
                 <select class='form-control' name="receiver" id="receiver" required>
                      <option selected disabled>--select--</option>
                     <option value="1">Members</option>
                     <option value="2">Staff</option>
                 </select>
                </div>
            </div>
            <!--<div class='form-group row'>
                <label for='cc' class='col-2 col-sm-1 col-form-label'>CC:</label>
                <div class='col-10 col-sm-11'>
                    <input type='email' name='copy_to' id='copy_to' class='form-control' id='cc' placeholder='Copy to email'>
                </div>
            </div>-->
            <div class='form-group row'>
                <label for='bcc' class='col-2 col-sm-1 col-form-label'>Subject:</label>
                <div class='col-10 col-sm-11'>
                    <input type='text' class='form-control' id='subject' name='subject' placeholder='Email subject' required>
                </div>
            </div>

            <div class='form-group row'>
                  <label for='bcc' class='col-2 col-sm-1 col-form-label'>Message:</label>
                   <div class='col-10 col-sm-11'>
                    <textarea class='form-control' id='message' name='message' rows='6' placeholder='Click here to compose'required></textarea>
                </div>

                    
                </div>
                <div class="modal-footer">
                <?php if(in_array('3', $privileges)){ ?>
                     <button id="btn-submit" type="submit" class="btn btn-success btn-sm save_data">
                        <i class="fa fa-space-ship"></i> Send
                    </button>
                    <?php } ?>
                    <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm">
                        <i class="fa fa-times"></i> Discard</button>
                    </div>
        </form>
        </div>
    </div>
</div>
