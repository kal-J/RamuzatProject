<div class="col-lg-12">
  <div class="row d-flex justify-content-between m-2">
    <a title="Export to excel" target="_blank" data-bind="attr: { href: exportLink}">
      <button class="btn btn-sm btn-primary">
        <i class="fa fa-file-excel-o fa-2x"></i>
      </button>
    </a>
    <div>
      <div class="d-flex flex-column">
        <label> Select Month & Year</label>
        <div>
          <select data-bind="options:months,
        value:currentMonth">
          </select>
          <select data-bind="options:fiscal_years,
        value:currentYear">
          </select>

        </div>
      </div>
    </div>
  </div>



  <div class="table-responsive" data-bind="visible: showAll" style="display: none;">
    <table class="table-bordered display compact nowrap table-hover" width="100%">
      <thead>
        <tr>
          <th style="padding:0.8rem;" colspan="2">ITEM </th>
          <!-- ko foreach: view_all_months -->
          <th style="padding:0.8rem;" data-bind="text: $data"></th>
          <!-- /ko -->
        </tr>
      </thead>
      <tbody>
        <tr>
          <td colspan="14" style="padding: 1.1rem 0.8rem; font-size: 1.01rem; font-weight: 600;">INCOMES</td>
        </tr>
      <tbody data-bind="foreach: revenue_data_income_all">
        <tr>
          <td colspan="2" style="padding:0.8rem; font-weight: 600;" data-bind="text: name"></td>
          <!-- ko foreach: numbers -->
          <td style="padding:0.8rem;" data-bind="text: $data"></td>
          <!-- /ko -->
          <td style="padding:0.8rem;background-color: #eee;font-weight: 800;" data-bind="text: revenueRowTotal"></td>
        </tr>
      </tbody>

      <tr style="background-color: #eee;">
        <td colspan="2" style='padding:0.8rem; font-weight: 800;'>TOTAL</td>
        <!-- ko foreach: all_total_formatted -->
        <td style="padding:0.8rem; font-weight: 800;" data-bind="text: $data"></td>
        <!-- /ko -->
      </tr>

      <!-- EXPENSES -->
      <tr>
        <td colspan="14" style="padding: 1.1rem 0.8rem; font-size: 1.01rem; font-weight: 600;">EXPENSES</td>
      </tr>
      <tbody data-bind="foreach: revenue_data_income_all_expenses">
        <!-- <pre data-bind="text: JSON.stringify(ko.toJS($data), null, 2)"></pre> -->
        <tr>
          <td colspan="2" style="padding:0.8rem; font-weight: 600;" data-bind="text: name"></td>
          <!-- ko foreach: numbers -->
          <td style="padding:0.8rem;" data-bind="text: $data"></td>
          <!-- /ko -->
          <td style="padding:0.8rem;background-color: #eee;font-weight: 800;" data-bind="text: revenueRowTotal"></td>
        </tr>
      </tbody>

      <tr style="background-color: #eee;">
        <td colspan="2" style='padding:0.8rem; font-weight: 800;'>TOTAL</td>
        <!-- ko foreach: all_total_expenses_formatted -->
        <td style="padding:0.8rem;font-weight: 800;" data-bind="text: $data"></td>
        <!-- /ko -->
      </tr>

      <tr style="background-color: #eee;">
        <td colspan="2" style='padding:0.8rem; font-weight: 800;'>Net Profit</td>
        <!-- ko foreach: netProfitAll -->
        <td style="padding:0.8rem;font-weight: 800;" data-bind="text: $data"></td>
        <!-- /ko -->
      </tr>
      <tr style="background-color: #eee;">
        <td colspan="2" style='padding:0.8rem; font-weight: 800;'>Accm Income</td>
        <!-- ko foreach: accmIncome -->
        <td style="padding:0.8rem;font-weight: 800;" data-bind="text: $data"></td>
        <!-- /ko -->
      </tr>

      </tbody>
    </table>
  </div>

  <div class="table-responsive" data-bind="visible: show">
    <table class="table-bordered display compact nowrap table-hover" width="100%">
      <thead>
        <tr>
          <th style="padding:0.8rem;" colspan="2">ITEM </th>
          <th style="padding:0.8rem;" data-bind="text: currentMonth"></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td colspan="14" style="padding: 1.1rem 0.8rem; font-size: 1.01rem; font-weight: 600;">INCOMES</td>
        </tr>

      <tbody data-bind="foreach: revenue_data_income">
        <tr>
          <td colspan="2" style="padding:0.8rem; font-weight: 600;" data-bind="text: name"></td>
          <td style="padding:0.8rem;" data-bind="text: income"></td>
        </tr>
      </tbody>

      <tr style="background-color: #eee;">
        <td colspan="2" style='padding:0.8rem; font-weight: 800;'>TOTAL</td>
        <td colspan="2" style='padding:0.8rem; font-weight: 800;' data-bind="text: total"></td>
      </tr>

      <!-- expenses -->
      <tr>
        <td colspan="14" style="padding: 1.1rem 0.8rem; font-size: 1.01rem; font-weight: 600;">EXPENSES</td>
      </tr>
      <!-- ko foreach: revenue_data_expenses -->
      <tr>
        <!-- ko if: name -->
        <td colspan="2" style="padding:0.8rem; font-weight: 600;" data-bind="text: name"></td>
        <td style="padding:0.8rem;" data-bind="text: income"></td>
        <!-- /ko -->
      </tr>
      <!-- /ko -->

      <tr style="background-color: #eee;">
        <td colspan="2" style='padding:0.8rem; font-weight: 800;'>TOTAL</td>
        <td colspan="2" style='padding:0.8rem; font-weight: 800;' data-bind="text: totalExpenses"></td>
      </tr>

      <tr style="background-color: #eee;">
        <td colspan="2" style='padding:0.8rem; font-weight: 800;'>Net Profit</td>
        <td colspan="2" style='padding:0.8rem; font-weight: 800;' data-bind="text: netProfit"></td>

      </tr>
      </tbody>
    </table>
  </div>
</div>