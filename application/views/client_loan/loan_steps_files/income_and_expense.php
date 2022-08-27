<?php $this->load->view('client_loan/loan_steps_files/loan_docs_file.php'); ?>
    <fieldset class="col-lg-12">
        <legend>Add Monthly Income</legend>
        <table  class="table table-striped table-condensed table-hover m-t-md">
            <thead>
                <tr>
                    <th>Income</th>
                    <th>Amount</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
                <div class="col-sm-2 pull-right">
                    <a data-bind="click: $root.addIncome_type" class="btn btn-info btn-sm"><i class="fa fa-plus"></i></a>
                </div>
            <tbody data-bind='foreach: $root.added_income_type'>
                <tr>
                    <td>
                       <select data-bind='options: $root.income_items, optionsText: function(item){return item.income_type},  
                        optionsCaption: "-- select --", optionsAfterRender: setOptionValue("id"),value:selected_income' class="form-control"  style="width: 250px"> 
                        </select> 
                    </select>
                    </td>
                    <td data-bind="with: selected_income">
                        <input type="number" data-bind='attr:{name:"incomes["+$index()+"][amount]"}' class="form-control" required/>
                        <input type="hidden" data-bind='attr:{name:"incomes["+$index()+"][income_id]"}, value: id'/>  
                        
                    </td>
                    <td>
                        <span title="Remove income" class="btn text-danger" data-bind='click: $root.removeIncome_type'><i class="fa fa-minus"></i></span>
                    </td>
                </tr>
            </tbody>
        </table>
        </fieldset>
            <fieldset class="col-lg-12">
            <legend>Add Monthly Expenses</legend>
            <table  class="table table-striped table-condensed table-hover m-t-md">
                <thead>
                    <tr>
                        <th>Expense</th>
                        <th>Amount</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                    <div class="col-sm-2 pull-right">
                        <a data-bind="click: $root.addExpense_type" class="btn btn-info btn-sm"><i class="fa fa-plus"></i></a>
                    </div>
                <tbody data-bind='foreach: $root.added_expense_type'>
                    <tr>
                        <td>
                            <select data-bind='options: $root.expense_items, optionsText: function(item){return item.expense_type},  optionsCaption: "-- select --", optionsAfterRender: setOptionValue("id"),value: selected_expense' class="form-control"  style="width: 250px"> 
                        </select>
                        </td>
                        <td data-bind="with: selected_expense">
                            <input type="number" data-bind='attr:{name:"expenses["+$index()+"][amount]"}' class="form-control" required/>
                            <input type="hidden" data-bind='attr:{name:"expenses["+$index()+"][expense_id]"}, value: id'/>  
                            
                        </td>
                        <td>
                            <span title="Remove expense" class="btn text-danger" data-bind='click: $root.removeExpense_type'><i class="fa fa-minus"></i></span>
                        </td>
                    </tr>
                </tbody>
            </table>
            </fieldset>