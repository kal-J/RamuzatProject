<style type="text/css">
     tr th{border: none;}
</style>
<!-- bootstrap modal -->
<div class="modal inmodal fade" id="add_member_guarantor-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form action="<?php echo site_url('guarantor/create') ?>" id="formGuarantor" name ="formGuarantor" class="formValidate form-horizontal" method="post" enctype="multipart/form-data">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h3 class="modal-title">Add Guarantor</h3>
                    <small class="font-bold">Please Make sure you enter all the required fields correctly</small>
                </div>
                <div class="modal-body">
                <input type="hidden"  name="client_loan_id" value="<?php echo $loan_detail['id']; ?>">

    <div class="form-group">
    <label class="form-label">Type</label>
    <!--
       <option>Existing <?php //echo $this->lang->line('cont_client_name');?></option>
       <option>Not a <?php //echo $this->lang->line('cont_client_name');?></option>
      -->
    <select   id="guarantor_type" name="guarantor_type" rel="tooltip"  title="Select guarantor" data-bind='options: guarantor_types, optionsText:"guarantor_name", optionsAfterRender: setOptionValue("id"),optionsCaption: "-- select --", value: guarantor_name' class="form-control-sm col-md-3">
          </select>
      </div>
                
      <!-- ko with: guarantor_name  -->
    <table  class="table table-striped table-condensed table-hover" data-bind="visible: parseInt(id) == parseInt(1)">
        <thead>
            <tr>
                <th>Guarantor</th>
                <th>Relationship</th>
                <th><a data-bind="click: $root.addMemberGuarantor" class="btn btn-success btn-xs"><i class="fa fa-plus"></i></a></th>
            </tr>
        </thead>         
        <tbody data-bind='foreach: $root.added_member_guarantor'>
            <tr>
                <td>
                    <select class="form-control-sm" id="member_guarantor_id_3" data-bind='options: $root.filtered_member_names, optionsText: function(data){return data.member_name + " -" + data.client_no;}, optionsCaption: "-- select --", value: selected_member_guarantor, optionsAfterRender: setOptionValue("id"), attr:{name:"member_guarantors["+$index()+"][member_id]"}' style="width: 200px" data-msg-required="A Member account is required"> 
                    </select>
                </td>
                <td data-bind="with: selected_member_guarantor">
                    <select class="form-control form-control-sm" id="relationship_type_id" data-bind='options: $root.relationships, optionsText: "relationship_type", optionsCaption: "Select...", optionsAfterRender: setOptionValue("id"),attr:{name:"member_guarantors["+$index()+"][relationship_type_id]"}' style="width: 100%" data-msg-required="Relationship is required" >
                    </select>
                </td>
                <td>
                    <span title="Remove Guarantor" class="btn text-danger" data-bind='click: $root.removeMemberGuarantor'><i class="fa fa-minus"></i></span>
                </td>
            </tr>
        </tbody>
    </table>
     <table  class="table table-borderless " data-bind="visible: parseInt(id) == parseInt(2)" style="border-collapse: collapse;">
         <!--  <a data-bind="click: $root.addMemberGuarantor2" class="btn btn-success btn-xs"><i class="fa fa-plus"></i></a>-->
            <br/>
           
     
        <thead data-bind='foreach: $root.added_member_guarantor2'>
             
        </thead>         
        <tbody data-bind='foreach: $root.added_member_guarantor2'>
            <tr>
              
                  <td class="col-md-6">
                    <label class="form-label">First Name</label>
                     <input type="text" id="member_guarantor2[]" class="form-control"data-bind='attr:{name:"member_guarantors2["+$index()+"][firstname]"}'>
                  </td>
                 <td class="col-md-6">
                    <label>Last Name</label>
                     <input type="text" id="member_guarantor2[]" class="form-control"data-bind='attr:{name:"member_guarantors2["+$index()+"][lastname]"}'>
                  </td>
                 
           
            </tr>
             <tr>
                 <td class="col-md-6">
                    <label>Gender</label>
                    <select id="member_guarantor2[]" class="form-control"data-bind='attr:{name:"member_guarantors2["+$index()+"][gender]"}'>
                    <option selected disabled>--select--</option>
                    <option value="1">Male</option>
                     <option value="2">Female</option>
                    </select>
                    
                  </td>
              
                  <td class="col-md-6">
                    <label>Phone</label>
                     <input type="text" id="member_guarantor2[]" class="form-control"data-bind='attr:{name:"member_guarantors2["+$index()+"][mobile_number]"}'>
                  </td>
                 
           
            </tr>
            
               <tr>
                  <td class="col-md-6">
                    <label>Email</label>
                     <input type="text" id="member_guarantor2[]" class="form-control"data-bind='attr:{name:"member_guarantors2["+$index()+"][email]"}'>
                  </td>
              
                  <td class="col-md-6">
                    <label>NIN</label>
                     <input type="text" id="member_guarantor2[]" class="form-control"data-bind='attr:{name:"member_guarantors2["+$index()+"][nin]"}'>
                  </td>
                 
              
            </tr>
            <tr>
                  <td class="col-md-6">
                    <label>Scanned Copy of ID</label>
                     
                      <input type='file'data-bind='attr:{name:"file_name[]"}' multiple/> 
                  </td>
                   <td>
                    <label>Comments</label>

                     <textarea style="width: 100%;"  row="3" id="member_guarantor2[]" class="form-control"data-bind='attr:{name:"member_guarantors2["+$index()+"][comment]"}'></textarea> 
                </td>
           
                
            </tr>
             <tr>
                  
                    <td>
                        <label>Relationship</label>
                    <select class="form-control form-control" id="relationship_type_id" data-bind='options: $root.relationships, optionsText: "relationship_type", optionsCaption: "Select...", optionsAfterRender: setOptionValue("id"),attr:{name:"member_guarantors2["+$index()+"][relationship_type_id]"}' style="width: 100%" data-msg-required="Relationship is required">
                    </select>
                </td>
            </tr>
             <!-- <tr>
             <td>
                    <span title="Remove Guarantor" class="btn text-danger" data-bind='click: $root.removeMemberGuarantor2'><i class="fa fa-minus"></i></span>
                </td>
            </tr>-->
      
        </tbody>
    </table>
    <!--/ko-->
                
                

                    
                
                        

                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">
                        Save
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>