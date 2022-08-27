<!-- bootstrap modal -->
<div class="modal inmodal fade" id="undo_close-modal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" class="formValidate" action="<?php echo base_url();?>fiscal_year/undo_close_fy" id="formUndo_close">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">X</span></button>
                <h3 class="modal-title">Undo Close / Rollback</h3>
                <h5><span class="text-danger"><b>NOTE:</b> Please contact IT Support if you have already entered records for the next financial year !</span></h5>
            </div>
            <div class="modal-body">
                    <input type="hidden" name="fisc_date_to" id="fisc_date_to"  value="<?php echo date('Y-m-d', strtotime($fiscal_year['end_date'])); ?>">
                    <input type="hidden" name="new_year_start_date" id="new_year_start_date"  value="<?php echo date('Y-m-d', strtotime($fiscal_year['end_date']. ' +1 day')); ?>">
                    <input type="hidden" name="fiscal_id" id="fiscal_id"  value="<?php echo $fiscal_year['id']; ?>">

                    <?php if (in_array('41', $fiscal_privilege)) { ?>
                     <button id="btn-submit" type="submit" class="btn btn-success btn-sm save_data">
                        <i class="fa fa-check"></i>Rollback
                    </button>
                    <?php } ?>
                    <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm">
                        <i class="fa fa-times"></i> Cancel</button>
                    </div>
        </form>
        </div>
    </div>
</div>
