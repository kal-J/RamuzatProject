<div role="tabpanel" id="tab-deposit-returns" class="tab-pane deposit_returns">
  <div class="panel-body">
    <div style="text-align: center;" id="reports_title">
      <h3>Deposit Returns - Reports</h3>
    </div>
    <div class="row d-flex justify-content-between m-2">
      
      <div>
        <div class="d-flex flex-column">
        <a title="Export to excel" target="_blank" >
        <button class="btn btn-sm btn-primary">
          <i class="fa fa-file-excel-o fa-2x"></i>
        </button>
      </a>
        </div>
      </div>
    </div>

    <div class="table-responsive" >
      <table class="table-bordered display compact nowrap table-hover" width="100%" id="tbl_deposit_returns">
        <thead>
          <tr style="background-color: #eee; color: #000; font-size: 16px; font-weight: bold;">
            <th style='padding:0.8rem; font-weight: 800;'>Ref#</th>
            <th style='padding:0.8rem; font-weight: 800;'>Range</th>
            <th style='padding:0.8rem; font-weight: 800;'>Type/Deposit</th>
            <th style='padding:0.8rem; font-weight: 800;'>No of A/Cs</th>
            <th style='padding:0.8rem; font-weight: 800;'>Total Amount</th>
          </tr>
        </thead>
        <tbody >

        <!-- <pre data-bind="text: ko.toJSON($data, null, 2)"></pre> -->
        

        <!-- ko foreach: deposit_returns_data -->
    
          
    
    
    
          <tr>

          <td rowspan="3" style='padding:0.8rem; font-weight: 600;' data-bind="text:$index() + 1"></td>
          <td rowspan="3" style='padding:0.8rem; font-weight: 600;' data-bind="text: Object.keys($data)[0]"></td>
          <td style="padding:0.8rem; font-weight: 600;">Non Withdrawable</td>
      
          <td style="padding:0.8rem;" data-bind="text: $data[Object.keys($data)[0]].non_withdrawable.number_of_accounts"></td>
          
            <td style="padding:0.8rem;font-weight: 600;" data-bind="text: curr_format (parseFloat($data[Object.keys($data)[0]].non_withdrawable.total))" ></td>
            

          </tr>











          <tr>
          
         
            
            <td style="padding:0.8rem;" >Savings</td>
          
            <td style="padding:0.8rem;font-weight: 600;" data-bind="text: $data[Object.keys($data)[0]].savings.number_of_accounts"></td>
            <td style="padding:0.8rem;font-weight: 600;" data-bind="text: curr_format
            (parseFloat($data[Object.keys($data)[0]].savings.total))"></td>
            

          </tr>
          <tr>
          
          
            
            <td style="padding:0.8rem;">Terms</td>
          
            <td style="padding:0.8rem;font-weight: 600;" data-bind="text: $data[Object.keys($data)[0]].terms.number_of_accounts"></td>
            <td style="padding:0.8rem;font-weight: 600;" data-bind="text: curr_format
            (parseFloat($data[Object.keys($data)[0]].terms.total))"></td>
            

          </tr>
          <tr style="background-color: #dbcaca;">

          
          <td colspan="3" style="padding:0.8rem;" ></td>
            
            <td style="padding:0.8rem;" data-bind="text: (parseInt($data[Object.keys($data)[0]].non_withdrawable.number_of_accounts)+parseInt($data[Object.keys($data)[0]].savings.number_of_accounts)+parseInt($data[Object.keys($data)[0]].terms.number_of_accounts))"></td>
          
            <td style="padding:0.8rem;font-weight: 800;" data-bind="text: curr_format
            (parseFloat($data[Object.keys($data)[0]].non_withdrawable.total)+parseFloat($data[Object.keys($data)[0]].savings.total)+parseFloat($data[Object.keys($data)[0]].terms.total))"></td>
            

          </tr>
          <!-- /ko -->
          
       

        

        
        <tr style="background-color: #eee;">
          <td rowspan="4" colspan="2" style='padding:0.8rem; font-weight: 800;'></td>
          <td style='padding:0.8rem; font-weight: 800;'>Total Non Withdrawable</td>
          <td style='padding:0.8rem; font-weight: 800;' data-bind="text: non_withdrawable_total"></td>
          <td style='padding:0.8rem; font-weight: 800;'></td>



        </tr>
        <tr style="background-color: #eee;">
         
          <td style='padding:0.8rem; font-weight: 800;' >Total Savings Deposit</td>
          
          <td style='padding:0.8rem; font-weight: 800;'data-bind="text: savings_total" ></td>
          
          <td style='padding:0.8rem; font-weight: 800;'></td>


        </tr>

        <tr style="background-color: #eee;">
          <td style='padding:0.8rem; font-weight: 800;'>Total Term Deposit</td>
          <td style='padding:0.8rem; font-weight: 800;' data-bind="text: terms_total"></td>
          <td style='padding:0.8rem; font-weight: 800;'></td>

        </tr>

        <tr style="background-color: #eee;">
          <td style='padding:0.8rem; font-weight: 800;'>TOTAL</td>
          <td style='padding:0.8rem; font-weight: 800;' data-bind="text:  16"></td>
          <td style='padding:0.8rem; font-weight: 800;'></td>

        </tr>
        
        </tbody>
      </table>
    </div>
  </div>