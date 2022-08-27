<div role="tabpanel" id="tab-loan_history" class="tab-pane loans">
    <br>

    <div class="panel-title">
        <div style="text-align: center;">
            <h3 style="font-weight: bold;">Member Loan History</h3>
        </div>
    </div>
    <br>
    <div class="row d-flex justify-content-center align-items-center">
        <div class="col-3">
            <select class="form-control" id="loan_history_member_select" name="member_id" data-bind='options: members, optionsText: function(item){ return item.member_name+"-"+item.client_no;}, optionsCaption: "---select member---", optionsAfterRender: setOptionValue("id"), value: member' style="width: 100%;">
            </select>
        </div>
        <div>
            <select class="form-control" name="limit" id="loan_limit">
                <option value="5">Last 5 Loans</option>
                <option value="4">Last 4 Loans</option>
                <option value="3">Last 3 Loans</option>
                <option value="2">Last 2 Loans</option>
                <option value="1">Last Loan</option>
            </select>
        </div>

        <!--ko if: member -->
        <div class="d-flex flex-row-reverse mx-2 my-2">
            <button onclick="preview_loan_history()" class="btn btn-primary btn-md">
                Preview
            </button>
        </div>
        <!--/ko -->

    </div>

    <div class="row" id="loan_history"></div>




</div>