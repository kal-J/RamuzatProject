    <div class="panel-body">
     <div class="col-lg-12">   
    <div class="table-responsive" style="border: none;">
   <table class="table display compact nowrap" width="100%">
    <tbody style="border: none;">
       <tr>
         <td>
        <label ><strong>Referrer's Name</strong></label>
         <select name ="introduced_by_id" id="introduced_by_id">
            <option value="All">All</option>
            <?php foreach($member_referral_info as $member){ ?>
            <option value="<?php echo htmlspecialchars($member['introduced_by_id']) ?>"><?php echo htmlspecialchars($member['member_name']);?></option>
              <?php }?>
            
        </select> &nbsp;&nbsp;
        <span><button class="btn btn-primary btn-sm btn-flat" onclick="get_member_referrals(this)" >Preview</button></span>
        <div class="clear"></div>
       
      </td>
       
       </tr>
       <tr style="border-bottom:dashed 1px #eee;padding:5px;">
        
         </tr>
         </tbody>
   </table>
   </div>
            
   <div role="tabpanel" id="tab-member_referral" class="tab-pane active">        
            <table class="table table-sm table-bordered"  width="100%" id="tblMember_referrals">
 
                    <thead>
                      
                        <tr class="td col-sm-2">
                            <th style="width: 100px;">Member Name</th>
                            <th style="width: 100px;">Savings</th>
                            <th style="width: 100px;"># Shares bought</th>
                            <th style="width: 50px;">Membership Status</th>
                           
                           
                        </tr>
            
                    </thead>
                    <tbody>
                    </tbody>
                     
               
  
</table>
            </div>
        </div>
    </div>
    </div>
 