<div role="tabpanel" id="tab-loan_history" class="tab-pane loans active pb-4">
    <br>

    <div class="panel-title">
        <div style="text-align: center;">
            <h3 style="font-weight: bold;">Member Loan History</h3>
        </div>
    </div>
    <br>
    <div class="row d-flex justify-content-center align-items-center">
        <div class="col-3">
            <select class="form-control" id="loan_history_member_select" name="member_id" style="width: 100%;">
            <option value="">---select member---</option>
            <?php foreach($members as $member) { ?>
                <option value="<?php echo $member['id']; ?>">
                    <?php echo $member['member_name'] . '-' . $member['client_no']; ?>
                </option>
            <?php }; ?>
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

<script>
    $(document).ready(() => {
        $('#loan_history_member_select').select2();
        $('#loan_limit').select2();
    })


    const preview_loan_history = () => {
        $.ajax({
            url: '<?php echo site_url("reports/member_loan_history"); ?>',
            data: {
                member_id: $('#loan_history_member_select').val(),
                limit: $('#loan_limit').val()
            },
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                $('#loan_history').html(response);
                console.log(response);
            },
            fail: function(jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }
</script>