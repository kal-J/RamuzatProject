
      <img src="<?php echo base_url(); ?>images/loading.gif" id="gif" style="display: block; margin: 0 auto; width: 30px; visibility: hidden;">

         <?php if(in_array('6', $till_privilege)){ ?>
          <div  class="pull-right">
              <a href="#bal_sheet_modal" data-toggle="modal" class="btn btn-primary btn-sm mb-1"> <i class="fa fa-print fa-2x"></i> </a>
          </div>
         <?php } ?>
      <div>
         <table class="table table-sm table-bordered" id="balancesheet" width="100%">
            <tbody >
                <tr style="background-color: #1c84c6; color: #fff;">
                     <td colspan="7">
                         <h4><center>Cash Register  &nbsp; &nbsp; &nbsp;- &nbsp;<span  data-bind="text:moment(end_date(),'X').format('DD-MMMM-YYYY')"></span></center></h4>
                    </td>
                </tr>
                <tr><td>
                    <h3>Account</h3>
                    </td>
                    <td>
                    <h3>Type</h3>
                    </td>
                    <td>
                    <h3>Reference Name</h3>
                    </td>
                    <td>
                    <h3>Debit</h3>
                    </td>
                    <td>
                    <h3>Credit</h3>
                    </td>
                    <td>
                    <h3>Narrative</h3>
                    </td>
                    <td>
                    <h3>Staff ID</h3>
                    </td>
                </tr>
            <!-- ko foreach: assets -->
                <tr style="background-color: #fafafc;" >
                    <td style ="padding-left:40px; font-weight:bold;" data-bind="text: '['+account_code+'] '+account_name"></td>
                    <td><h4 class="no-margins"><span  style ="font-weight:bold;" data-bind="text:curr_format(round(amount,2))">0</span></h4> <td></td><td></td>
                    <td></td><td></td><td></td>
                </td>
                </tr>
       
           <!-- /ko -->

                <tr class="table-primary">
                <th>Total Assets </th>
                    <th > <span data-bind="with:print_sums"> <span data-bind="text:curr_format(round(total_assets,2))">0</span> </span>  </th> <td></td><td></td>
                    <td></td><td></td><td></td>
                </tr>
           
       

                </tbody>
        </table>
      </div>


<section>
<?php //$this->load->view('reports/balance_sheet/print_out_modal'); ?>
</section>