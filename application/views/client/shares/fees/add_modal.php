<div class="modal inmodal fade" id="add_share_application-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<form method="post" class="formValidate" action="<?php echo site_url();?>applied_share_fee/create" id="formApplied_share_fee">
<div class="modal-header">
 <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
 <h4 class="modal-title">
    <?php
    if (isset($modalTitle)) {
        echo $modalTitle;
    }else{
        echo "Share fee(s)";
    }
 ?></h4>
 <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
</div>
        <input class="form-control" name="id" id="id" type="hidden">
        <input class="form-control" name="share_account_id" value="<?php echo $share_details['id']; ?>" type="hidden">

                <div class="modal-body">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table  class="table table-striped table-condensed table-hover m-t-md">
                                <thead>
                                    <tr>
                                        <th>Fee</th>
                                        <th>Amount</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody data-bind='foreach: $root.applied_share_fee'>
                                    <tr>
                                        <td>
                                            <select data-bind='attr:{name:"shareFee["+$index()+"][share_fee_id]"}, options: $root.available_share_fees, optionsText: function(data_item){return data_item.feename}, optionsCaption: "-- select --", optionsAfterRender: setOptionValue("id"), value: selected_fee' class="select2able form-control"  style="width: 250px"> </select>
                                        </td>
                                        <td data-bind="with: selected_fee">
                                            <label data-bind="text: curr_format(parseInt(amountcalculatedas_id)==1? round((parseFloat(amount)/100 * <?php echo $share_price_amount; ?> ),1):amount*1)"></label>
                                            <input type="hidden" data-bind='attr:{name:"shareFee["+$index()+"][amount]"}, value: parseInt(amountcalculatedas_id)==1? round((parseFloat(amount)/100 * <?php echo $share_price_amount; ?> ),1) :amount'/>
                                            <!--input type="hidden" data-bind='attr:{name:"shareFee["+$index()+"][amountcalculatedas_id]"}, value: amountcalculatedas_id'/-->
                                        </td>
                                        <td>
                                            <span title="Remove item" class="btn text-danger" data-bind='click: $root.removeShareFee'><i class="fa fa-minus"></i></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-bind='click: $root.addShareFee, enable:$root.available_share_fees().length > 0' class="btn-white btn-sm"><i class="fa fa-plus"></i> Apply another fee</button>
                    <?php if((in_array('1', $share_privilege))||(in_array('3', $share_privilege))){ ?>
                    <button class="btn btn-primary btn-flat" data-bind="enable:$root.applied_share_fee().length > 0" type="submit">Apply Fees</button>
                    <?php } ?>
                </div>

</form>
</div>
</div>
</div>
