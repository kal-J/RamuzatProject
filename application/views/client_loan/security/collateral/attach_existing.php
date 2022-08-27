<!-- bootstrap modal -->
<div class="modal inmodal fade" id="attach_existing_collateral-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form action="<?php echo site_url('loan_collateral/create') ?>" id="formLoan_collateral" class="formValidate form-horizontal" method="post" enctype="multipart/form-data">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h3 class="modal-title">Attach Collateral</h3>
                    <small class="font-bold">Please Make sure you enter all the required fields correctly</small>
                </div>
                <div class="modal-body">
                <input type="hidden"  name="client_loan_id" value="<?php echo $loan_detail['id']; ?>">
                <div class="pull-right">
                    <select style="width: 250px" id="select-existing-collateral" class="form-control-sm" class="form-control">
                    <option value="0">-- Select from Existing --</option>
                    
                    <!-- ko foreach:$root.existing_collateral  -->

                    <option type='button' data-bind="text:collateral_type_name, value: JSON.stringify($data)"></option>
                    <!-- /ko-->
                    </select>
                </div>

                    <table class="table table-striped table-condensed table-hover m-t-md">
                        

                        <thead>
                            <tr>
                                <th>Collateral</th>
                                <th>Description</th>
                                <th>Item value</th>
                                <th>File name</th>
                                <th>
                                    
                                </th>

                            </tr>
                        </thead>
                        <tbody data-bind='foreach: $root.added_existing_collateral'>
                            <tr>
                                <td>
                                    <span data-bind='text:collateral_type_name'></span>
                                    <input type="hidden" data-bind='attr:{name:"collaterals["+$index()+"][member_collateral_id]"}, value: id' />
                                </td>
                                <td>
                                    <span data-bind="text: description"></span>

                                </td>
                                <td>
                                    <span data-bind="text: item_value"></span>
                                    <input type="hidden" data-bind='attr:{name:"collaterals["+$index()+"][item_value]"}, value: item_value' />

                                </td>
                                <td>
                                    <span data-bind="text: file_name"></span>

                                </td>
                                <td>
                                    <span data-bind="click: $root.remove_existing_collateral" title="Remove income" class="btn text-danger"><i class="fa fa-minus"></i></span>
                                </td>
                            </tr>
                        </tbody>

                    </table>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">
                        <?php
                        if (isset($saveButton)) {
                            echo $saveButton;
                        } else {
                            echo "Save";
                        }
                        ?>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>