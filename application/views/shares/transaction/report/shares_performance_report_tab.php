<style type="text/css">
  /* ==========TOOL TIP ==================  */
  .tooltip {
    font-size: 14px;
    font-weight: bold;
  }

  .tooltip-arrow {
    display: none;
    opacity: 0;
  }

  .borderless td,
  .borderless th {
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
$end_date   = date('d-m-Y', strtotime($fiscal_year['end_date']));
?>
<div role="tabpanel" id="tab-shares_performance_report" class="tab-pane shares_performance_report" style="background: #F3F3F4;">
  <div class="panel-body">
    <table>
      <tr>
        <td style="padding-right: 20px;margin:0px;">
          <select id="share_summary_report_yr_m_filter">
            <option value="year">Year</option>
            <option value="month">Month</option>

          </select>
        </td>

        <td style="padding-right: 20px;">
          <label><strong>Period</strong></label>
          <select id="period" name="period1" rel="tooltip" title="Select time period" data-bind='options: period_types, optionsText: "period_name", optionsAfterRender: setOptionValue("id"), value: period'>
          </select>

        </td>
 
        <!-- ko with: period -->
        <td style="padding-right: 20px;" data-bind="visible:  parseInt(id) ==parseInt(2)">
          <label style="display: inline;"><strong>From </strong></label>
          <input autocomplete="off" onkeydown="return false" id="start_date1" name="start_date1" data-bind="datepicker: $root.start_dater,textInput:'<?php echo $start_date; ?>'" type="text">
        </td>
        <td style="padding-right: 20px;" data-bind="visible:  parseInt(id) ==parseInt(2)">
          <label style="display: inline;"><strong>To </strong></label>
          <div data-bind="datepicker" style="margin-left:17px;margin-top: -20px;" class="input-group date col-4" data-date-start-date="<?php echo isset($active_month) ? date('d-m-Y', strtotime($active_month['month_start'])) : date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>" data-date-end-date="<?php echo isset($active_month) ? ((strtotime(date('d-m-Y')) < (strtotime($active_month['month_end']))) ? date('d-m-Y') : date('d-m-Y', strtotime($active_month['month_end']))) : ((strtotime(date('d-m-Y')) < (strtotime($fiscal_active['end_date']))) ? date('d-m-Y') : date('d-m-Y', strtotime($fiscal_active['end_date']))); ?>">
            <input autocomplete="off" placeholder="DD-MM-YYYY" value="<?php echo date('d-m-Y'); ?>" type="text" onkeydown="return false" name="end_date1" id="end_date1" required />
            <span class="input-group-addon" style="display: none;"></span>
          </div>
        </td>
        <td style="padding-left: 20px;" data-bind="visible:  parseInt(id) ==parseInt(1)">
          <label style="display: inline;"><strong>Date </strong></label>
          <div style="margin-left:17px;margin-top: -20px;" class="input-group date col-4" data-date-start-date="<?php echo isset($active_month) ? date('d-m-Y', strtotime($active_month['month_start'])) : date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>" data-date-end-date="<?php echo isset($active_month) ? ((strtotime(date('d-m-Y')) < (strtotime($active_month['month_end']))) ? date('d-m-Y') : date('d-m-Y', strtotime($active_month['month_end']))) : ((strtotime(date('d-m-Y')) < (strtotime($fiscal_active['end_date']))) ? date('d-m-Y') : date('d-m-Y', strtotime($fiscal_active['end_date']))); ?>">
            <input autocomplete="off" placeholder="DD-MM-YYYY" value="<?php echo date('d-m-Y'); ?>" type="text" onkeydown="return false" name="date_at1" id="date_at" size="10" required />
            <span class="input-group-addon" style="display: none;"></span>
          </div>

        </td>
        <td style="padding-right: 10px;" data-bind="visible:  parseInt(id) ==parseInt(3)">
          <label><strong>Year #1</strong></label>

          <select id="fiscal_one" name="fiscal_one1" data-bind='options: $root.fiscal_years, optionsText:function(data_item){return moment(data_item.start_date,"YYYY-MM-DD").format("MMM/YYYY") +" - " + moment(data_item.end_date,"YYYY-MM-DD").format("MMM/YYYY");}, optionsAfterRender: setOptionValue("id")'>
          </select>
        </td>
        <td style="padding-right: 10px;" data-bind="visible:  parseInt(id) ==parseInt(3)">
          <label><strong>Year #2</strong></label>

          <select id="fiscal_two" name="fiscal_two1" data-bind='options: $root.fiscal_years, optionsText:function(data_item){return moment(data_item.start_date,"YYYY-MM-DD").format("MMM/YYYY") +" - " + moment(data_item.end_date,"YYYY-MM-DD").format("MMM/YYYY");},optionsCaption: "--select--", optionsAfterRender: setOptionValue("id")'>
          </select>
        </td>
        <td style="padding-right: 20px;" data-bind="visible:  parseInt(id) ==parseInt(3)">
          <label><strong>Year #3</strong></label>

          <select id="fiscal_three" name="fiscal_three1" data-bind='options: $root.fiscal_years, optionsText:function(data_item){return moment(data_item.start_date,"YYYY-MM-DD").format("MMM/YYYY") +" - " + moment(data_item.end_date,"YYYY-MM-DD").format("MMM/YYYY");},optionsCaption: "--select--", optionsAfterRender: setOptionValue("id")'>
          </select>
        </td>
        <!--/ko -->

        <td><button class="btn btn-sucess btn-sm btn-flat" onclick="get_shares_performace_data(this)" style="background:#1ab394;;color:#fff;">Preview</button></td>
      </tr>
    </table>

    <hr />
    <div class="col-lg-12" style="background: #F3F3F4;">
      <section data-bind="visible: !month()">
        <div class="row">
          <div class="col-12 mt-1 mb-1 mt-2">
            <h5 class="text-uppercase">General Share Summary</h5>

          </div>
        </div>
      </section>

      <section data-bind="visible: !month()">
        <div class="row">
          <div class="col-lg-6">
            <div class="card">
              <div class="card-header bg-white">
                <div class="row">
                  <div class="col-xl-3">
                    <h4 class="card-title">Gender</h4>
                  </div>
                  <div class="col-xl-3">
                    <h4 class="card-title">Male: <span class="text-default" data-bind="text:male_members?male_members:0">0</span></span></h4>
                  </div>
                  <div class="col-xl-3">
                    <h4 class="card-title">Female: <span class="text-default" data-bind="text:female_members?female_members:0">0</span></h4>
                  </div>
                </div>
              </div>
              <div class="card-body">

                <table class="table table-bordered" id="tblShare_transaction_report">

                  <thead data-bind="visible:no_of_shareholders">
                    <td style="background:#1c84c6;color: #fff;">Gender</td>
                    <td style="background:#1c84c6;color: #fff;">Category</td>
                    <td style="background:#1c84c6;color: #fff;" class="text-nowrap">Total Share</td>
                    <td style="background:#1c84c6;color: #fff;" class="text-nowrap">Total Amount (UGX.)</td>
                    <td style="background:#1c84c6;color: #fff;">NO. of shares</td>
                    <!--<td>Inactive</td>-->
                  </thead>

                  <tbody data-bind="foreach: gender_summary_data">

                    <tr>
                      <td data-bind="text:gender"></td>

                      <td data-bind="text:issuance_name"></td>

                      <td class="text-default" data-bind="text:price_per_share?parseFloat(amount)/parseFloat(price_per_share):0">0

                      <td class="text-default" data-bind="text:amount?curr_format(round(amount,2)*1):0" style="color: #1c84c6;">0
                      </td>
                      <td data-bind="text: num_account?num_account:0"></td>


                    </tr>

                  </tbody>
                  <tfoot>
                    <tr>
                      <th colspan="2">Total</th>
                      <th></th>
                      <th data-bind="text:overall_total_t1?overall_total_t1:0" style="color: #1c84c6;"></th>
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
                  <div class="col-xl-3">
                    <h3 class="text-nowrap"><b class="badge badge-primary"><span class="text-default" data-bind="text:no_of_shareholders?no_of_shareholders:0"></span></b></span> Share holders</span></h3>

                  </div>


                  <div class="col-xl-3">
                    <h4>Total Credit:
                      <b><span class="text-default" data-bind="text:total_credit?total_credit:0" style="color: #1c84c6;"></span></b>
                    </h4>
                  </div>
                  <div class="col-xl-3">
                    <h4>Total Debit: <b><span class="text-default" data-bind="text: total_debit?total_debit:0" style="color: #1c84c6;"></span></b>
                      <b><span class="text-default"></b>
                    </h4>
                  </div>
                  <div class="col-xl-3">
                    <h4>Total Shares:
                      <b><span class="text-default" data-bind="text:total_shares?total_shares:0" style="color: #1c84c6;"> </span></b>
                    </h4>
                  </div>



                  <div class="card-body" style="padding-top:-10px;">

                    <div class="row">

                      <table class="table table-bordered">
                        <thead data-bind="visible: no_of_shareholders">
                          <tr>
                            <td style="background: #1c84c6;color:#fff;">Category</td>
                            <td style="background: #1c84c6;color:#fff;">Price Per Share</td>
                            <td style="background: #1c84c6;color:#fff;">Total Share</td>
                            <td style="background: #1c84c6;color:#fff;">Total Amount</td>
                          </tr>

                        </thead>

                        <tbody data-bind="foreach: share_report">

                          <tr>
                            <td data-bind="text:issuance_name"></td>

                            <td data-bind="text:price_per_share?curr_format(price_per_share*1):0" style="color: #1c84c6;">0 UGX. each </td>

                            <td class="text-default" data-bind="text:price_per_share?parseFloat(amount)/parseFloat(price_per_share):0">0</td>

                            <td><span class="badge badge-primary">
                                <h4 class="no-margins"><b class="text-default" data-bind="text:amount?curr_format(round(amount,2)*1):0">0</b></h4>
                              </span>
                            </td>

                          </tr>

                        </tbody>
                        <tfoot>
                          <tr>
                            <th colspan="3">Total</th>
                            <!--<th data-bind="text:total_shares_t2?total_shares_t2:0"></th>-->
                            <th data-bind="text:overall_total_t2?overall_total_t2:0" style="color: #1c84c6;"></th>
                          </tr>

                        </tfoot>

                      </table>
                    </div>
                  </div>
      </section>
      <section data-bind="visible: !month()"><br>
        <div class="row">

          <div class="col-xl-3 col-sm-6 col-12 mb-4">
            <div class="card">
              <div class="card-header bg-white">
                <h4>Overall shares transactions (UGX.)</h4>
              </div>
              <div class="card-body">
                <div class="d-flex justify-content-left px-md-1" data-bind="with:total_shares_amount">
                  <div>
                    <b><span class="text-default" data-bind="text:amount_bought?curr_format(round(amount_bought)):0" style="color: #1c84c6;"></span></b>


                  </div>


                </div>

              </div>
            </div>
          </div>
          <div class="col-xl-3 col-sm-6 col-12 mb-4">
            <div class="card">
              <div class="card-header bg-white">
                <h4>Total shares bought (UGX.)</h4>
              </div>
              <div class="card-body">
                <div class="d-flex justify-content-left px-md-1" data-bind="with:no_of_shares_bought">
                  <div>
                    <b><span class="text-default" data-bind="text:amount_bought?curr_format(round(amount_bought)):0" style="color: #1c84c6;"></span></b>


                  </div>

                </div>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-sm-6 col-12 mb-4">
            <div class="card">
              <div class="card-header bg-white">
                <h4> Total shares sold (UGX)</h4>
              </div>
              <div class="card-body">
                <div class="d-flex justify-content-left px-md-1" data-bind="with:no_of_shares_sold">
                  <div>
                    <h4><span data-bind="text:amount_sold?curr_format(round(amount_sold)):0" style="color:#1c84c6">0</span></h4>
                  </div>

                </div>
              </div>
            </div>

          </div>

          <div class="col-xl-3 col-sm-6 col-12 mb-4">
            <div class="card">
              <div class="card-header bg-white">
                <h4> Total shares Transfered (UGX.)</h4>
              </div>

              <div class="card-body">
                <div class="d-flex justify-content-left px-md-1" data-bind="with:no_of_shares_transfered">
                  <div>

                    <h4><span data-bind="text:amount_transfered?curr_format(round(amount_transfered)):0" style="color: #1c84c6;"></span></h4>


                  </div>


                </div>
              </div>
            </div>
          </div>


          <!--end -->


        </div>
      </section>
    </div>

     <section data-bind="visible:month()==1">
      <div class="row">
        <div class="card col-lg-12">
          <div class="card-header" style="background: white">
            <h3>General Monthly Summary </h3>
          </div>

          <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true"><?php echo date('Y'); ?></button>
            </li>

          </ul>

          <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
              <table class="table table-bordered" style="border-color: #e4f0f5;">
                <thead data-bind="visible: month">
                  <th style="background:#1c84c6;color:#fff;border: 1px solid #e4f0f5;">Months</th>
                  <th style="background:#1c84c6;color:#fff;">Category</th>
                </thead>

                <tbody data-bind="foreach: monthly_report">
                  <tr>
                    <td data-bind="text:month_name">
                    <td>
                      <table width="100%" class="borderless" style="border: 2px solid #e4f0f5;font-family:helvetica;">

                        <thead>
                          <th style="background:#e4f0f5;">category</th>
                          <th style="background:#e4f0f5;">Total Shares</th>
                          <th style="background:#e4f0f5;">Total Amount</th>
                          <th style="background:#e4f0f5;">% change (over a month)</th>
                        </thead>
                        <tbody data-bind="foreach:category">
                          <tr>
                            <td data-bind="text:issuance_name"></td>
                            <td data-bind="text:price_per_share?parseFloat(total_amount)/parseFloat(price_per_share):0">0</td>
                            <td><span class="text-default" data-bind="text:total_amount?curr_format(round(total_amount,2)*1):0">0</span></td>
                            <td><i class="fa fa-level-down" data-bind="visible:parseFloat(change) < parseFloat(0)" style="color:red;"></i><i class="fa fa-level-up" data-bind="visible:parseFloat(change) >parseFloat(0)" style="color:#009050;"></i><span class="text-default" data-bind="text:change?round(change,2):0"></span>
                            </td>
                          </tr>
                        </tbody>
                      </table>

                  </tr>

                </tbody>
              </table>

            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</div>

<!--- Yearly -->