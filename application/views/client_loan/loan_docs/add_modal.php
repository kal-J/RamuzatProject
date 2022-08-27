<!-- bootstrap modal -->
<div class="modal inmodal fade" id="add_loan_docs-modal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

<form action="<?php echo site_url('client_loan_doc/create')?>" id="formClient_loan_doc" class="formValidate form-horizontal" method="post" enctype="multipart/form-data">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title">Loan document  Information</h3>
                <small class="font-bold">Please Make sure you enter all the required fields correctly</small>
            </div>
            <div class="modal-body">
                <input type="hidden"  name="id">
                <input type="hidden"  name="client_loan_id" value="<?php echo $loan_detail['id']; ?>">
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Document type<span class="text-danger">*</span></label>
                      <div class="col-lg-6 form-group">
                        <select id='loan_doc_type_id' class="form-control required" name="loan_doc_type_id" >
                          <option value="select_one">--Select one--</option>
                            <?php
                            foreach ($loan_doc_types as $loan_doc_type) {
                                echo "<option value='" . $loan_doc_type['id'] . "'>" . $loan_doc_type['loan_doc_type'] . "</option>";
                            }
                            ?>
                        </select>
                      </div>
                 </div>

                 <div class="form-group row">
                     <label class="col-lg-3 col-form-label">Description<span class="text-danger">*</span></label>
                       <div class="col-lg-6 form-group">
                         <textarea type="text" name="description" class="form-control m-b" required="required"></textarea>
                       </div>
                  </div>

                  <div class="form-group row">
                      <label class="col-lg-3 col-form-label">File name<span class="text-danger"></span></label>
                        <div class="col-lg-6 form-group">
                          <?php echo form_input(array('id' => 'file_name', 'name' => 'file_name', 'type' => 'file', 'required' => 'yes' )); ?>
                        </div>
                   </div>

                        <div class="modal-footer">
                                <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                        <?php if((in_array('1', $client_loan_privilege))||(in_array('3', $client_loan_privilege))){ ?>
                                <button type="submit" class="btn btn-primary"><?php
                            if (isset($saveButton)) {
                                echo $saveButton;
                            }else{
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
