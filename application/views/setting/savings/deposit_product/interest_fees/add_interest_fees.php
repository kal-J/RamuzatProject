<div class="modal inmodal fade" id="add_interest-modal" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<form method="post" class="formValidate" action="<?php echo base_url();?>DepositProduct/Create2" id="formDepositProductInterest">
<div class="modal-header">
 <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
 <h4 class="modal-title">
    <?php
        echo "Term Lenght Interest settings (For Fixed Deposit)";
 ?>
</h4>
 <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
</div>
<div class="modal-body">
 <div class="">
    <input type="hidden" name="id">
   <!--  <div class="form-group row">
        <label class="col-lg-3 col-form-label">Per No of Days<span class="text-danger">*</span></label>
         <div class="col-lg-3">
         <input placeholder="" required class="form-control" name="pernoofdays" type="number" required>
         </div>
         <label class="col-lg-2 col-form-label">When interest is paid<span class="text-danger">*</span></label>
         <div class="col-lg-4">
          <select class="form-control" name="wheninterestispaid" required >
            <option value="" >Select .... </option>
                <?php
                //foreach($wheninterestispaid as $when){
                   // echo "<option value='".$when['id']."'>".$when['name']."</option>";
               // }
                ?>
            </select>
         </div>
    </div> -->
   <fieldset class="col-lg-12">     
            <legend>Interest Rate (Time periods in <b>Months</b>)</legend>
            <div class="table-responsive" >
                    <table  class="table table-striped table-condensed table-hover m-t-md">
                        <thead>
                            <tr>
                                <th>Min-Period</th>
                                <th>Max-Period</th>
                                <th>Rate (%) per annum</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody data-bind='foreach:$root.interest_rate_ranges'>
                            <tr>
                                <td>
                                    <input type="number" data-bind='attr:{name:"rangeFees["+$index()+"][min_range]"},value:min_range' class="form-control"/>
                                    <input type="hidden" data-bind='attr:{name:"rangeFees["+$index()+"][id]"},value:id' class="form-control" min="0.2" />
                                </td>
                                <td>
                                    <input type="number" data-bind='attr:{name:"rangeFees["+$index()+"][max_range]"},value:max_range' class="form-control" min="1" />
                                </td>
                            
                                <td>
                                    <input type="number"  data-bind='attr:{name:"rangeFees["+$index()+"][range_amount]"},value:range_amount'  class="form-control" min="1" />
                                </td>
                                <td>
                                    <span title="Remove item" class="btn text-danger" data-bind='click: $root.removeRangeRate'><i class="fa fa-minus"></i></span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                 <button data-bind='click: $root.addRangeRate' class="btn-white btn-sm pull-right"><i class="fa fa-plus"></i> Add Another Range</button>

                </div>                      
    </fieldset>
    <br>
    
</div>
</div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
    <?php if((in_array('1', $deposit_product_privilege))||(in_array('3', $deposit_product_privilege))){ ?>
        <button type="submit" class="btn btn-primary"><?php
    if (isset($saveButton)) {
        echo $saveButton;
    }else{
        echo "Save";
    }
 ?></button>
   <?php } ?>
    </div>
</form>
</div>
</div>
</div>
