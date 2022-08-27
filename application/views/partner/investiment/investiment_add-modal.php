<div class="modal inmodal fade" id="add_investiment-modal" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<form method="post" class="formValidate" action="<?php echo base_url();?>investiment/Create" id="formInvestiment">
<div class="modal-header">
 <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
 <h4 class="modal-title">
    <?php
    if (isset($modalTitle)) {
        echo $modalTitle;
    }else{
        echo "Add New Investiment Target";
    }
 ?></h4>
 <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
</div>

<div class="modal-body">
 
 <div class="">
    <input type="hidden" name="id">
    <div class="form-group row">
       <label class="col-lg-2 col-form-label">Investiment Target Name<span class="text-danger">*</span></label> 
         <div class="col-lg-4">
          <input placeholder="" required class="form-control" name="investiment_name" type="text"> <br>
         </div>
        <label class="col-lg-2 col-form-label">Investiment (Payable) Account<span class="text-danger">*</span></label>
        <div class="col-lg-4">
            <select class="form-control" name="linked_account_id" id="linked_account_id"  data-bind='options: select2accounts(8), optionsText: formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%" required data-msg-required="Select an option">
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-lg-2 col-form-label">Target Amount<span class="text-danger">*</span></label> <br>
        <div class="col-lg-4">
          <input placeholder="" required class="form-control" name="target_amount" type="number"> <br>
        </div>
    <label class="col-lg-2 col-form-label">Collection Frequency<span class="text-danger">*</span></label>

    <div class="col-lg-2">
        <input placeholder="" required class="form-control" name="collection_frequency" type="number">
    </div> 
    <div class="col-lg-2">
        <select class="form-control" name="collection_made_every">
            <option value="">--Select--</option>
            <?php
            foreach ($collection_made_every as $value) {
                echo "<option value='" . $value['id'] . "'>" . $value['made_every_name'] . "</option>";
            }
            ?>
        </select>
    </div>
    </div> 


    <fieldset class="col-lg-12">     
        <legend>Time Range/Frame</legend>
        <div class="form-group row">  
        <label class="col-lg-2 col-form-label">Start Date</label>
        <div class="col-lg-4 form-group">
            <div class="input-group date">
                <input class="form-control"  autocomplete="off" required name="start_date" data-bind="datepicker: $root.start_date" type="text"><span data-bind="datepicker: $root.start_date" class="input-group-addon"><i class="fa fa-calendar"></i></span>
            </div>
        </div>
        <label class="col-lg-2 col-form-label">End Date</label>
        
        <div class="col-lg-4 form-group">
            <div class="input-group date">
                <input class="form-control"  autocomplete="off" required name="end_date" data-bind="datepicker: $root.end_date" type="text"><span data-bind="datepicker: $root.end_date" class="input-group-addon"><i class="fa fa-calendar"></i></span>
            </div>
        </div>
        </div>                            
    </fieldset>
    <hr/> 
    <fieldset class="col-lg-12">  
        <legend>Participating Partners</legend>
        <table  class="table table-striped table-condensed table-hover m-t-md">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Amount</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
                <div class="col-sm-2 pull-right">
                    <a data-bind="click: $root.addPartner" class="btn btn-info btn-sm"><i class="fa fa-plus"></i></a>
                </div>
            <tbody data-bind='foreach: $root.added_partner'>
                <tr>
                    <td>
                       <select data-bind="options: $root.partner_list, optionsText: function(item){
                       return item.firstname +' '+item.lastname;},  
                        optionsCaption: '-- select --',value:selected_partner" class="form-control"  style="width: 250px"> 
                        </select> 
                    </select>
                    </td>
                    <td data-bind="with: selected_partner">
                        <input type="number" data-bind='attr:{name:"partner_list["+$index()+"][required_amount]"}' class="form-control" required/>
                        <input type="hidden" data-bind='attr:{name:"partner_list["+$index()+"][user_id]"}, value: user_id'/>  
                        
                    </td>
                    <td>
                        <span title="Remove income" class="btn text-danger" data-bind='click: $root.removePartner'><i class="fa fa-minus"></i></span>
                    </td>
                </tr>
            </tbody>
        </table>

    </fieldset>
    <hr/> 
    <div class="form-group row">
        <label class="col-lg-2 col-form-label">Description<span class="text-danger">*</span></label>
        <div class="col-lg-10 form-group">
            <textarea class="form-control" rows="3"  required  name="description" ></textarea>
        </div>
    </div><!--/row -->

</div>
</div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
        <?php //if((in_array('1', $deposit_product_privilege))||(in_array('3', $deposit_product_privilege))){ ?>
        <button type="submit" class="btn btn-primary"><?php
            if (isset($saveButton)) {
                echo $saveButton;
            }else{
                echo "Save";
            }
         ?></button>
   <?php //} ?>
    </div>
</form>
</div>
</div>
</div>
