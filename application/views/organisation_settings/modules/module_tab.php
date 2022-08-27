<div role="tabpanel" id="tab-modules" class="tab-pane">
<div class="panel-body">
   
<script language="JavaScript">
    function toggle(source) {
        var checkboxes = document.querySelectorAll('input[type="checkbox"]');
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i] != source)
                checkboxes[i].checked = source.checked;
        }
    }
    
</script>
            <div class="ibox-title  back-change">
                <h3 class="text-uppercase text-center"><strong> <?php  echo $organisation['name']; ?> MODULES</strong> </h3>
                <div  class="text-center"><small>Select modules Please</small></div>
            </div>
            <div class="ibox-content">
                <?php echo form_open_multipart("organisation/create_org_modules", array('id' => 'formModules', 'class' => 'formValidate', 'method' => 'post', 'name' => 'formModules', 'data-toggle' => 'validator', 'role' => 'form')); ?>
                <input type="hidden" name="organisation_id" value="<?php echo $organisation['id']; ?>">
                <div class="row">
                    <div class="col-lg-3 " style="font-size:15px;">
                    <input type="checkbox"  onClick="toggle(this)" /> &nbsp; Select All<br />
                    </div>
                </div>
                <div class="row">
                <table  class="table table-striped table-hover" width="100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th class="pull-right">Action</th>
                        </tr>
                    </thead>
                    <tbody data-bind="foreach: org_modules">  
                            <tr>
                            <td> 
                               <b><span data-bind="text:module_name" > </span> </b> </td>
                               <td class="pull-right">
                               <input type="checkbox" data-bind="value:module_id, attr:{hidden:parseInt(module_status)==1,checked:parseInt(yesno)==1, name:'modules_list['+$index()+'_0][module_id]'}" >
                               <input type="hidden" data-bind="value:id, attr:{ name:'modules_list['+$index()+'_0][id]'}"/>
                            </td>
                        </tr>
                   </tbody>
                </table>
                
                </div>
                <div class="pull-right add-record-btn">
                    <button id="btn-submit" type="submit" class="btn btn-primary btn-sm save_data">
                        <i class="fa fa-check"></i> Save </button>
               
                </div>
                </form>
            </div>
</div>
</div>
