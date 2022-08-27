<!-- bootstrap modal -->
  
<div class="modal inmodal fade" id="add_contact-modal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" class="formValidate" action="<?php echo base_url(); ?>index.php/contact/create" id="formContact">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h3 class="modal-title">Contact Information</h3>
                    <small class="font-bold">Please Make sure you enter all the required fields correctly</small>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">                
                    <input type="hidden"  name="user_id" id="user_id" value="<?php echo $user['user_id']; ?>">
                    <div class="row">
                        <div class="col">
                            <div class="form-group row m-xxs"><label class="col-xxl-4 col-form-label">Mobile number</label> 
                                <div class="input-group">
                                    <input name="mobile_number" id="mobile_number" type="tel" class="form-control" placeholder="Start with 0 or country code e.g +256" pattern="^(0|\+\d{1,4})([0-9]{8,11})" data-pattern-error="Wrong number format, start with country code e.g +256 or 0" required />
                                </div>
                            </div>
                             
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <p class="modal-row-label-p">
                                <div class="modal-row-input-eidt">
                                    <div class="modal-row-label-eidt"><label for="contact_type_id" class="form-control-label"> Contact type:</label></div>
                                    <select id='contact_type_id' class="form-control required" name="contact_type_id" >
                                        <?php
                                            foreach ($contact_types as $contact_type) {
                                                echo "<option value='" . $contact_type['id'] . "'>" . $contact_type['contact_type'] . "</option>";
                                                
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                        <?php if((in_array('1', $member_staff_privilege))||(in_array('3', $member_staff_privilege))){ ?>
                        <button type="submit" class="btn btn-primary"><?php
                            if (isset($saveButton)) {
                                echo $saveButton;
                            } else {
                                echo "Save";
                            }
                            ?></button>
                        <?php } ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div> 