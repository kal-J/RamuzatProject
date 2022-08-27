<div class="modal inmodal fade" id="add_tax_application-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form method="post" class="formValidate" action="<?php echo base_url(); ?>index.php/tax_application/Create" id="formTax_application">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        <?php
                        if (isset($modalTitle)) {
                            echo $modalTitle;
                        } else {
                            echo "Apply Tax fees";
                        }
                        ?></h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <input class="form-control" name="id" id="id" type="hidden">
                <input class="form-control" name="tax_rate_source_id" value="<?php echo $tax_rate_source['id']; ?>" type="hidden">
                <div class="modal-body">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table  class="table table-striped table-condensed table-hover m-t-md">
                                <thead>
                                    <tr>
                                        <th>Applied to</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>                                    
                                        <td>
                                            <select name="income_source_id" class="form-control" >
                                                <option value="select_one">--select one--</option>
                                                <?php
                                                foreach ($available_income_sources as $income_source) {
                                                    echo "<option value='" . $income_source['id'] . "'>" . $income_source['income_source_name'] . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <?php if ((in_array('1', $privileges)) || (in_array('3', $privileges))) { ?>
                        <button class="btn btn-primary btn-flat" type="submit">Apply Fees</button>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>
</div>
