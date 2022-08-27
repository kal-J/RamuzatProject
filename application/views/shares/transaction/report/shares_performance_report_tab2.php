<<<<<<< HEAD
   <style type="text/css">
=======
     <style type="text/css">
>>>>>>> 2f31814f8fbc64f6582d667f5fe23eef8eafa756
  /* ==========TOOL TIP ==================  */  
.tooltip {
  font-size: 14px;
  font-weight: bold;
}

.tooltip-arrow {
  display: none;
  opacity: 0;
}
.borderless td, .borderless th {
    border: none;
}

.tooltip-inner {
  background-color: #FAE6A4;
  border-radius: 4px;
  box-shadow: 0 1px 13px rgba(0, 0, 0, 0.14), 0 0 0 1px rgba(115, 71, 38, 0.23);
  color: #734726;
  min-width: 200px;
  padding: 6px 10px;
  text-align: center;
  text-decoration: none;
}
.tooltip-inner:after {
  content: "";
  display: inline-block;
  left: 100%;
  margin-left: -56%;
  position: absolute;
}
.tooltip-inner:before {
  content: "";
  display: inline-block;
  left: 100%;
  margin-left: -56%;
  position: absolute;
}

.tooltip.top {
  margin-top: -11px;
  padding: 0;
}
.tooltip.top .tooltip-inner:after {
  border-top: 11px solid #FAE6A4;
  border-left: 11px solid transparent;
  border-right: 11px solid transparent;
  bottom: -10px;
}
.tooltip.top .tooltip-inner:before {
  border-top: 11px solid rgba(0, 0, 0, 0.2);
  border-left: 11px solid transparent;
  border-right: 11px solid transparent;
  bottom: -11px;
}

.tooltip.bottom {
  margin-top: 11px;
  padding: 0;
}
.tooltip.bottom .tooltip-inner:after {
  border-bottom: 11px solid #FAE6A4;
  border-left: 11px solid transparent;
  border-right: 11px solid transparent;
  top: -10px;
}
.tooltip.bottom .tooltip-inner:before {
  border-bottom: 11px solid rgba(0, 0, 0, 0.2);
  border-left: 11px solid transparent;
  border-right: 11px solid transparent;
  top: -11px;
}

.tooltip.left {
  margin-left: -11px;
  padding: 0;
}
.tooltip.left .tooltip-inner:after {
  border-left: 11px solid #FAE6A4;
  border-top: 11px solid transparent;
  border-bottom: 11px solid transparent;
  right: -10px;
  left: auto;
  margin-left: 0;
}
.tooltip.left .tooltip-inner:before {
  border-left: 11px solid rgba(0, 0, 0, 0.2);
  border-top: 11px solid transparent;
  border-bottom: 11px solid transparent;
  right: -11px;
  left: auto;
  margin-left: 0;
}

.tooltip.right {
  margin-left: 11px;
  padding: 0;
}
.tooltip.right .tooltip-inner:after {
  border-right: 11px solid #FAE6A4;
  border-top: 11px solid transparent;
  border-bottom: 11px solid transparent;
  left: -10px;
  top: 0;
  margin-left: 0;
}
.tooltip.right .tooltip-inner:before {
  border-right: 11px solid rgba(0, 0, 0, 0.2);
  border-top: 11px solid transparent;
  border-bottom: 11px solid transparent;
  left: -11px;
  top: 0;
  margin-left: 0;
}

</style>
<?php
 $start_date = date('d-m-Y', strtotime($fiscal_year['start_date']));
$end_date = date('d-m-Y', strtotime($fiscal_year['end_date']));
?>
 <div role="tabpanel" id="tab-shares_performance_report" class="tab-pane mt-3" style=" background: #F3F3F4;"><br>
   <div class="body-panel">
    <table>
      <tr>
     
     
       
      <td style="padding-right: 20px;20px;0px;0px;">
        <label style="display: inline;"><strong>Months</strong></label> 
        <input type="hidden" name="month" id="month" value="0"><input type="checkbox" onclick="this.previousSibling.value=1-this.previousSibling.value">
      
      </td>
      
       <td style="padding-right: 20px;">
         <label><strong>Period</strong></label> 
           <select   id="period" name="period1" rel="tooltip"  title="Select time period" data-bind='options: period_types, optionsText: "period_name", optionsAfterRender: setOptionValue("id"), value: period'>
          </select>

      </td>
 <!-- ko with: period -->
      <td style="padding-right: 20px;" data-bind="visible:  parseInt(id) ==parseInt(2)">
        <label style="display: inline;"><strong>From </strong></label>
         <input autocomplete="off"  onkeydown="return false" id="start_date1" name="start_date1" data-bind="datepicker: $root.start_dater,textInput:'<?php echo $start_date; ?>'" type="text">
      </td>
      <td style="padding-right: 20px;" data-bind="visible:  parseInt(id) ==parseInt(2)">
        <label style="display: inline;"><strong>To </strong></label>
         <input onkeydown="return false" autocomplete="off" id="end_date1" name="end_date1" data-bind="datepicker: $root.end_dater,textInput:'<?php echo $end_date; ?>'" type="text" >
      </td>
      <td style="padding-right: 20px;" data-bind="visible:  parseInt(id) ==parseInt(1)">
        <label style="display: inline;"><strong>Date </strong></label>
         <input onkeydown="return false" autocomplete="off" id="date_at" name="date_at1" data-bind="datepicker: $root.end_daterd,textInput:'<?php echo $end_date; ?>'" type="text" >
      </td>
      <td style="padding-right: 10px;" data-bind="visible:  parseInt(id) ==parseInt(3)">
        <label ><strong>Year #1</strong></label>

        <select  id="fiscal_one" name="fiscal_one1" data-bind='options: $root.fiscal_years, optionsText:function(data_item){return moment(data_item.start_date,"YYYY-MM-DD").format("MMM/YYYY") +" - " + moment(data_item.end_date,"YYYY-MM-DD").format("MMM/YYYY");}, optionsAfterRender: setOptionValue("id")'>
        </select>
      </td>
      <td style="padding-right: 10px;" data-bind="visible:  parseInt(id) ==parseInt(3)">
        <label ><strong>Year #2</strong></label>

         <select id="fiscal_two" name="fiscal_two1" data-bind='options: $root.fiscal_years, optionsText:function(data_item){return moment(data_item.start_date,"YYYY-MM-DD").format("MMM/YYYY") +" - " + moment(data_item.end_date,"YYYY-MM-DD").format("MMM/YYYY");},optionsCaption: "--select--", optionsAfterRender: setOptionValue("id")'>
        </select>
      </td>
        <td style="padding-right: 20px;" data-bind="visible:  parseInt(id) ==parseInt(3)">
        <label ><strong>Year #3</strong></label>

        <select id="fiscal_three" name="fiscal_three1" data-bind='options: $root.fiscal_years, optionsText:function(data_item){return moment(data_item.start_date,"YYYY-MM-DD").format("MMM/YYYY") +" - " + moment(data_item.end_date,"YYYY-MM-DD").format("MMM/YYYY");},optionsCaption: "--select--", optionsAfterRender: setOptionValue("id")'>
        </select>
      </td>
     <!--/ko -->
      <td><button class="btn btn-success btn-sm btn-flat" onclick="get_shares_performace_data(this)" >Preview</button></td>
     </tr>
    </table>
  
   <hr/>
  <div class="col-lg-12" style=" background: #F3F3F4;" data-bind="visible: parseInt(month())!=parseInt(1)"> 
  <section> 
    <div class="row">
      <div class="col-12 mt-1 mb-1 mt-2">
        <h5 class="text-uppercase">General Share Summary</h5>
       
      </div>
    </div>
    </section>
    <section>
     <div class="row">
      <div class="col-lg-6">
        <div class="card">
          <div class="card-header bg-white">
            <div class="row">
              <div class="col-xl-3">
            <h4 class="card-title">Gender</h4>
          </div>
           <div class="col-xl-3">
            <h4 class="card-title">Male: <span  class="text-default" data-bind="text:male_members?male_members:0">0</span></span></h4>
          </div>
          <div class="col-xl-3">
            <h4 class="card-title">Female: <span  class="text-default" data-bind="text:female_members?female_members:0">0</span></h4>
          </div>
          </div>
         </div>
          <div class="card-body">
           
         <table class="table table-bordered" id="tblShare_transaction_report">
                    <thead data-bind="visible:no_of_shareholders">
                  <td>Gender</td>
                   <td>Category</td>
                  <td class="text-nowrap">Total Share</td>
                   <td class="text-nowrap">Total Amount (UGX.)</td>
                   <td>NO. of Payment</td>
                  <!--<td>Inactive</td>-->
                 </thead>

                 <tbody data-bind="foreach: gender_summary_data"> 
                 
                 <tr>
             <td data-bind="text:gender"></td>
                   
                <td  data-bind="text:issuance_name"></td>
                
                <td  class="text-default" data-bind="text:price_per_share?parseFloat(amount)/parseFloat(price_per_share):0">0  
      
               <td class="text-default" data-bind="text:amount?curr_format(round(amount,2)*1):0">0 
                </td>
                <td data-bind="text: active?active:0"></td>

                
               </tr>
             
              </tbody>
<<<<<<< HEAD
               <tfoot>
=======
               <tfoot data-bind="visible:gender">
>>>>>>> 2f31814f8fbc64f6582d667f5fe23eef8eafa756
                    <tr>
                        <th colspan="2">Totals</th>
                        <th data-bind="text: amount?amount:0"></th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                  
                    </tr>
                </tfoot>
                </table>
      </div>
    </div>
  </div>
      <div class="col-lg-6">
        <div class="card">
          <div class="card-header bg-white" style="display:inline;">
            <div class="row">
            <div class="col-xl-4">
            <h3  class="card-title" data-bind="visible: parseInt(period())!=parseInt(1) || parseInt(period())!=parseInt(2)"><span data-bind="text:no_of_shareholders1?no_of_shareholders1:0" class="badge badge-primary"></span> Share holders</span></h3>
            <!--<h4 ><button class="btn btn-primary btn-sm">Active Shareholders:
            <span  class="text-default" data-bind="text:no_of_shareholders?no_of_shareholders:no_of_shareholders1">0</span></button></h5>-->
            </div>
            <div class="col-xl-4">
            <h4>Total Credit: <b data-bind="visible: parseInt(period())==parseInt(1) || parseInt(period())==parseInt(2)"><span  class="text-default" data-bind="text:no_of_shareholders?no_of_shareholders:0"></span>0</b>
            <b data-bind="visible: parseInt(period())!=parseInt(1) || parseInt(period())!= parseInt(2)"><span  class="text-default"><?php echo number_format($share_report[0]['overal_total_credit'],0); ?> UGX</span></b>
            </h4>
          </div>
           <div class="col-xl-4">
            <h4>Total Debit: <b data-bind="visible: parseInt(period())==parseInt(1) || parseInt(period())==parseInt(2)"><span  class="text-default" data-bind="text:no_of_shareholders?no_of_shareholders:0"></span>0</b>
            <b data-bind="visible: parseInt(period())!=parseInt(1) || parseInt(period())!= parseInt(2)"><span  class="text-default"><?php echo number_format($share_report[0]['total_share_debit'],0); ?> UGX</span></b>
            </h4>
          </div>
          
          <div class="card-body">
             
            <div class="row">

                   
                   <table class="table table-bordered">
                    <thead data-bind="visible: no_of_shareholders">
                   <td>Category</td>
                   <td>Price Per Share</td>
                   <td>Total Share</td>
                   <td>Total Amount</td>

                 </thead>

                 <tbody data-bind="foreach: share_report"> 
                 <h4 data-bind="visible:no_of_shareholders" class="text-default" style="text-align: center;">Category</h4>
                 <tr>
                <td data-bind="text:issuance_name"></td>
                  
                <td data-bind="text:price_per_share?curr_format(price_per_share*1):0" >0 UGX. each </td>
                
                <td class="text-default" data-bind="text:price_per_share?parseFloat(amount)/parseFloat(price_per_share):0">0</td>  
      
               <td><span class="badge badge-primary"><h4 class="no-margins"><b class="text-default" data-bind="text:amount?curr_format(round(amount,2)*1):0">0</b></h4> </span>
                </td>
              
               </tr>
             
              </tbody>
             
                </table>
          </div>
        </div>
      </section>
<<<<<<< HEAD
   <section><br>
=======
    </div>
   <section data-bind="visible:  parseInt(month())!=parseInt(1)"><br>
>>>>>>> 2f31814f8fbc64f6582d667f5fe23eef8eafa756
    <div class="row">
     
      <div class="col-xl-3 col-sm-6 col-12 mb-4">
        <div class="card">
          <div class="card-header bg-white">
            <h4>Total shares bought (UGX.)</h4>
          </div>
          <div class="card-body">
            <div class="d-flex justify-content-left px-md-1" data-bind="with:no_of_shares_bought">
              <div>
              <b><span  class="text-default" data-bind="text:amount_bought?curr_format(round(amount_bought)):0"></span></b>
            
 
              </div>

              
            </div>
           
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-sm-6 col-12 mb-4">
        <div class="card">
           <div class="card-header bg-white">
            <h4>Total shares Sold (UGX.)</h4>
          </div>
          <div class="card-body">
            <div class="d-flex justify-content-left px-md-1" data-bind="with:no_of_shares_bought">
              <div>
                <b><span  class="text-default" data-bind="text:amount_bought?curr_format(round(amount_bought)):0"></span></b>
            
            
              </div>
               
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-sm-6 col-12 mb-4">
        <div class="card">
           <div class="card-header bg-white">
            <h4>  Total shares Transfered (UGX.)</h4>
          </div>
          
          <div class="card-body">
            <div class="d-flex justify-content-left px-md-1" data-bind="with:no_of_shares_transfered">
              <div>
           
                 <h4><span data-bind="text:amount_transfered?curr_format(round(amount_transfered)):0"></span> UGX.</h4>
           
              
              </div>

              
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-sm-6 col-12 mb-4">
        <div class="card">
            <div class="card-header bg-white">
            <h4> No. of reversed transaction</h4>
          </div>
          <div class="card-body">
            <div class="d-flex justify-content-left px-md-1">
              <div>
                <h4><span data-bind="text:no_trans_reversed?no_trans_reversed:0">0</span></h4>
              </div>
               
            </div>
          </div>
        </div>
      
      </div>
   
   
   <!--end -->
    
      </div>
      </section>
<<<<<<< HEAD
   
  </div>
=======
>>>>>>> 2f31814f8fbc64f6582d667f5fe23eef8eafa756
</div>
    <section data-bind="visible:  parseInt(month())==parseInt(1)">
    <div class="row" >
      <div class="card col-lg-12">
        <div class="card-header" style="background: white"> <h3>General Monthly Summary </h3></div>
   
 <ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true"><?php echo date('Y'); ?></button>
  </li>
  
</ul>
 
<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
      <table class="table table-bordered">
                <thead data-bind="visible: month">
<<<<<<< HEAD
<<<<<<< HEAD
                  <th>Months</th>
                   <th>Category</th>
=======
                  <th data-bind="attr{rowspan:2}">Month</th>
                   <th>Category</th>
                   <th>Total Share</th>
                   <th>Total Amount</th>
>>>>>>> 28d3343138e7603431a01c4620f4691bbf2894aa
=======
                  <th>Months</th>
                   <th>Category</th>
>>>>>>> 2f31814f8fbc64f6582d667f5fe23eef8eafa756
                 </thead>

                 <tbody data-bind="foreach: monthly_report"> 
                 <tr>
<<<<<<< HEAD
<<<<<<< HEAD
                 <td data-bind="text:month_name" >
                  <td>
                <table width="100%" class="borderless">
=======
                 <td data-bind="text:month_name" >
                  <td>
                <table width="100%" class="borderless" style="border: 1px solid #ccc;">
>>>>>>> 2f31814f8fbc64f6582d667f5fe23eef8eafa756
                    <thead>
                     <th>category</th>
                     <th>Total Shares</th>
                     <th>Total Amount</th>
                   </thead>
                   <tbody data-bind="foreach:category">
                       <tr>
                           <td data-bind="text:issuance_name"></td>
                           <td data-bind="text:price_per_share?parseFloat(total_amount)/parseFloat(price_per_share):0">0</td>  
                           <td span class="text-default" data-bind="text:total_amount?curr_format(round(total_amount,2)*1):0">0</td>
                      </tr>
                  </tbody>
                </table>

<<<<<<< HEAD
=======
                 <td data-bind="text:month_name ,attr{rowspan:counter}"></td>
                <td data-bind="text:issuance_name" ></td>
                <td data-bind="text:price_per_share?parseFloat(total_amount)/parseFloat(price_per_share):0">0</td>  
               <td span class="text-default" data-bind="text:total_amount?curr_format(round(total_amount,2)*1):0">0                        </td>
                  
>>>>>>> 28d3343138e7603431a01c4620f4691bbf2894aa
=======
>>>>>>> 2f31814f8fbc64f6582d667f5fe23eef8eafa756
               </tr>
             
              </tbody>
                </table>
<<<<<<< HEAD
<<<<<<< HEAD
    </td>
=======
>>>>>>> 28d3343138e7603431a01c4620f4691bbf2894aa
=======
    </td>
>>>>>>> 2f31814f8fbc64f6582d667f5fe23eef8eafa756
  </div>
 
  </div>
  
</div>
<<<<<<< HEAD
    
    </section>
 
=======
</div>
    
    </section>
  </div>
>>>>>>> 2f31814f8fbc64f6582d667f5fe23eef8eafa756
 
  

  

    
