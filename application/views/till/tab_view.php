<?php
    $start_date = date('d-m-Y', strtotime(date('Y-m-d')));
    $end_date = date('d-m-Y', strtotime(date('Y-m-d')));
?>
<div role="tabpanel" id="tab-cash_register" class="tab-pane active">
                           
    <div class="row align-items-center mx-4 mt-2">
        <h3 id="tab_title col">
        <center>Cash Register  { <span data-bind="text:moment(start_date(),'DD-MM-YYYY').format('DD-MMMM-YYYY')"></span> - <span data-bind="text:moment(end_date(),'DD-MM-YYYY').format('DD-MMMM-YYYY')"></span> }</center>
        </h3>

        <div class="align-self-end ml-auto">
            <form method="POST" action="<?php echo base_url() ?>Till/export_to_excel">
                <input type="hidden" name="start_date" data-bind="textInput:moment(start_date(),'DD-MM-YYYY').format('DD-MM-YYYY')" />
                <input type="hidden" name="status_id" value="1" />
                <input type="hidden" name="end_date" data-bind="textInput:moment(end_date(),'DD-MM-YYYY').format('DD-MM-YYYY')" />
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fa fa-file-excel-o fa-2x"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="panel-body">
     <div class="col-lg-12">   
    <div class="table-responsive">
   <table class="table display compact nowrap" width="100%">
    <tbody>
       <tr>
         <td style="padding-right: 20px;" >
        <label ><strong>Till / Cash Register</strong></label>
           <select style="height: 27px;"  id="account_id" name="account_id" rel="tooltip"  title="Select till/ Cash Register" data-bind='options: tchannel, optionsText: function(data){ return data.channel_name +" "+data.staff_name}, optionsAfterRender: setOptionValue("linked_account_id"), value: channel'>
          </select>
         <!-- ko with: channel  -->
         <input id="created_by" type="hidden" name="created_by" data-bind="value:user_id" type="text">
          <!-- /ko -->
        </td>
         <td >
        
<div class="form-check form-switch">
  <label class="form-check-label" for="all">All Trans?</label>
  <select id="all" name="all">
      <option value="1" >Yes</option>
      <option value="0" selected>No</option>
  </select>
</div>
      </td>
      </td>
         <td >
        <label style="display: inline;"><strong>From </strong></label>
         <input autocomplete="off"  onkeydown="return false" id="start_date" name="start_date" data-bind="datepicker: $root.start_dater,textInput:'<?php echo $start_date; ?>'" type="text">
      </td>
      <td  >
        <label style="display: inline;"><strong>To </strong></label>
         <input onkeydown="return false" autocomplete="off" id="end_date" name="end_date" data-bind="datepicker: $root.end_dater,textInput:'<?php echo $end_date; ?>'" type="text" >
      </td>
       <td><button class="btn btn-success btn-sm btn-flat" onclick="get_cash_register(this)" >Priview</button></td>
       </tr>
       <tr style="background-color: #1c84c6; color: #fff;">
          <td colspan="4">
             <h4><center>Cash Register  &nbsp; &nbsp; &nbsp;- &nbsp;<span  data-bind="text:moment(end_date(),'DD-MM-YYYY').format('DD-MMMM-YYYY')"></span></center></h4>
          </td>
         </tr>
         </tbody>
   </table>
   </div>
            <div class="table-responsive">
                  
                <table class="table-bordered display compact nowrap table-hover" id="tblJournal_transaction_line"  width="100%" >
 
                    <thead>
                      
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Ref. ID</th>
                            <th>Ref. NO</th>
                            <th>Ref. Name</th>
                            <th>Account</th>
                            <th>Journal Type</th>
                            <th>Debit </th>
                            <th>Credit </th>
                            <th>Narrative</th>
                            <th>Staff Name</th> 
                            <th>#</th> 
                        </tr>
                     <tr style="background-color: #1c84c6; color: #1c84c6;">
                      <td colspan="7">
                         <h4><center>Balance B/F  </center></h4>
                      </td>
                      <td colspan="6"><h4 class="no-margins"><span  style ="font-weight:bold;" data-bind="text:(parseFloat(balance_bf())>=0?' Dr. ':' Cr. ')+ curr_format(round(balance_bf(),2))">0</span></h4></td>
                     </tr>

                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="7">Totals</th>
                            <th>Debit </th>
                            <th>Credit </th>
                            <th>&nbsp;</th> 
                            <th>&nbsp;</th> 
                            <th>&nbsp;</th> 
                        </tr>
                   <tr style="background-color: #fafcdc; color: #1c84c6;" >
                      <td colspan="7">
                         <h4><center>Closing Balance  </center></h4>
                      </td>
                      <td colspan="6"><h4 class="no-margins"><span  style ="font-weight:bold;" data-bind="text:(parseFloat(closing_b())>=0?' Dr. ':' Cr. ')+ curr_format(round(closing_b(),2))">0</span></h4></td>
                     </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    </div>

