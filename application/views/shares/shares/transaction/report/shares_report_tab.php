 <div class="panel-body">
     <style type="text/css">
         div.panel-heading {
             background: white;
         }

         .borderless td,
         .borderless th {
             border: none;
         }
     </style>
     <div class="row d-flex flex-row-reverse mt-3 mr-4">

         <?php if (isset($client_type) && $client_type == 2) { ?>
         <?php } else { ?>

             <div class="ml-2">
                 <button id="btn_print_shares_report" onclick="handlePrint_shares_report_pdf()" class="btn btn-sm btn-secondary">
                     <i class="fa fa-print fa-2x"></i>
                 </button>
                 <!-- <button id="btn_printing_active_shares" class="btn btn-primary" type="button" disabled>
      <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="false"></span>
        Printing...
    </button>-->
             </div>

             <div class="ml-2">
                 <form action="share_transaction/export_excel_share_report/7/1" method="post">
                     <input type="hidden" type="text" id="transaction_status" name="transaction_status" data-bind="textInput: $root.transaction_status().id">
                     <input type="hidden" type="text" name="start_date" id="start_date" data-bind="textInput: $root.start_date3()">
                     <input type="hidden" type="text" name="end_date" id="end_date" data-bind="textInput: $root.end_date3()">
                     <button type="submit" class="btn btn-sm btn-secondary">
                         <i class="fa fa-file-excel-o fa-2x"></i>
                     </button>
                     </a>
                 </form>

             </div>

         <?php } ?>


     </div>

     <div style="border:0px solid #676A6C;" class="col-lg-12">
         <fieldset style="max-width: 950px;">

             <table style="border-top:none;width: 500px;border:none;">

                 <tr style="border-top:none;" width="auto">

                     <td style="padding-right:20px; ">
                         <label style="display: inline;"><strong>Date From</strong></label></label>
                         <input autocomplete="off" size="10" name="start_date3" id="start_date3" data-bind="datepicker:$root.start_date3, value: $root.start_date" type="text" required>

                     </td>

                     <td style="padding-right:20px;">
                         <label style="display: inline;"><strong>&nbsp;Date To</strong></label></label>
                         <div class="input-group date col-4" data-date-start-date="<?php echo isset($active_month) ? date('d-m-Y', strtotime($active_month['month_start'])) : date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>" data-date-end-date="<?php echo isset($active_month) ? ((strtotime(date('d-m-Y')) < (strtotime($active_month['month_end']))) ? date('d-m-Y') : date('d-m-Y', strtotime($active_month['month_end']))) : ((strtotime(date('d-m-Y')) < (strtotime($fiscal_active['end_date']))) ? date('d-m-Y') : date('d-m-Y', strtotime($fiscal_active['end_date']))); ?>">
                             <input autocomplete="off" placeholder="DD-MM-YYYY" value="<?php echo date('d-m-Y'); ?>" type="text" onkeydown="return false" name="end_date3" id="end_date3" size="10" required data-bind="value: $root.end_date3" />
                             <span class="input-group-addon" style="display: none;"></span>
                         </div>
                     </td>
                     <td style="padding-right:20px; ">
                         <label style="display: inline;"><strong>Gender</strong></label></label>

                         <select name="gender" id="gender" required>
                             <option value="All">Select</option>
                             <option value="1">Male</option>
                             <option value="0">Female</option>
                         </select>
                     </td>

                     <td style="padding-right:20px; ">
                         <label style="display: inline;" class="form-label"><strong>Category</strong></label></label>
                         <select name="issuance_id" id="issuance_id" required>
                             <option value="All">Select</option>
                             <?php foreach ($share_issuances as $cat_name) { ?>
                                 <option value="<?php echo $cat_name['id'] ?>"><?php echo $cat_name['issuance_name'] ?></option><?php
                                                                                                                            } ?>


                         </select>
                     </td>



                     <td style="padding-right: 20px;">
                         <div class="row" style="margin: 6px;">

                             <table with="100%">

                                 <tr>
                                     <label style="display:inline;text-align: center;" class="text-nowrap"><strong>Number of shares</strong></label>
                                     <td>
                                         <select name="less_more_equal" id="less_more_equal">
                                             <option value="2">More Than</option>
                                             <option value="1">Less Than</option>
                                             <option value="3">Equal To</option>
                                         </select>
                                     </td>
                                     <td>
                                         <input type="number" step="0.01" placeholder="No. of shares" min="0" size="10" name="num_limit" id="num_limit" style="width:70px; min-height: 10px;">
                                     </td>
                                 </tr>

                             </table>

                         </div>
                     </td>
                     <td title="Filter out records for those who paid and not ">
                         <label style="display:inline;text-align: center;" class="text-nowrap"><strong>Transaction Status</strong></label>

                         <!-- <select name="transaction_status" id="transaction_status" >
                                    <option value="1">Transacted</option>
                                    <option value="2">Not Transacted</option>
                                    
                                   </select>  -->

                         <select id="transaction_status" name="transaction_status" data-bind="options: [{name: 'Transacted', id: '1'},{name:'Not Transacted', id: '2'}], optionsText: 'name', optionsAfterRender: setOptionValue('id'), value: transaction_status">
                         </select>

                     </td>

                     <td style="padding-left:40px;">
                         <label style="display: inline;"></label></label>
                         <button onclick="set_active_select_value(this)" type="button" class="btn btn-primary btn-sm subbtn" style="box-shadow: 1px 2px;">Filter</button>

                     </td>

                 </tr>


             </table>
             </field>
     </div>


     <table class="table table-borderless" style="padding: 0px;width:65%;-webkit-box-shadow: 0 0 10px #f6f6f6;box-shadow: 0 0 10px #f6f6f6 " data-bind="visible:$root.transaction_status().id==1" align="left">
         <p class="text-left" style="margin: 0  0.9%;"><b>Summary</b></p>
         <thead>
             <tr>
                 <th>Category</th>
                 <th>Total Credit (UGX)</th>
                 <th>Total Debit (UGX)</th>
                 <th>Total Shares (UGX)</th>
             </tr>
         </thead>

         <!-- ko if: $root.summary_data() -->
         <tbody data-bind="foreach:summary_data">
             <tr>

                 <td style="border:none;" data-bind="text:issuance_name" class="card" data-bind="visible:issuance_name !=''"></td>
                 <td style="border:none;"><span data-bind="text:total_share_credit?curr_format(round(total_share_credit,2)*1):0">0</span></td>

                 <td style="border:none;"><span data-bind="text:total_share_debit?curr_format(round(total_share_debit,2)*1):0">0</span></td>
                 <td style="border:none;"><span data-bind="text:overal_total_share?curr_format(round(overal_total_share,2)*1):0">0</span></td>
                 </td>
             </tr>



         </tbody>
         <!-- /ko -->
     </table>




     <br>
     <div class="col-lg-12">
         <div class="table-responsive">
             <table class="table  table-bordered table-hover" id="tblShare_transaction_report" width="100%">
                 <thead>
                     <tr>
                         <th class="text-nowrap" style="width: 8rem;">Account Name</th>
                         <th>Gender</th>
                         <!--<th>Client. No</th>-->
                         <th>Category</th>
                         <th class="text-nowrap">Share A/C NO</th>
                         <th class="text-nowrap">Share.NO</th>
                         <th class="text-nowrap">Price Per Share (UGX)</th>
                         <th>Bought (UGX)</th>
                         <th>Refunded (UGX)</th>
                         <th>Transfered (UGX)</th>
                         <th>Charges (UGX)</th>
                         <th class="text-nowrap">Total Shares (UGX)</th>
                         <th class="text-nowrap" data-bind="visible:$root.transaction_status().id==1">Last Transaction Date</th>


                     </tr>
                 </thead>
                 <tbody>
                 </tbody>
                 <tfoot>
                     <tr>
                         <th colspan="4">Totals</th>
                         <th>&nbsp;</th>
                         <th>&nbsp;</th>
                         <th>&nbsp;</th>
                         <th>&nbsp;</th>
                         <th>&nbsp;</th>
                         <th>&nbsp;</th>
                         <th>&nbsp;</th>
                         <th data-bind="visible:$root.transaction_status().id==1">&nbsp;</th>
                     </tr>
                 </tfoot>

             </table>
         </div>
     </div>
 </div>