               <div class="table-responsive">
                    <table class="table table-bordered table-condensed table-striped" width="100%" >
                        <tbody data-bind="with: account_details">
                            <tr>
                                <th>Account Name.</th>
                                <td data-bind="text: account_name">Cash</td>
                                <th>Number/Code.</th>
                                <td data-bind="text: account_code">##XY</td>
                            </tr>
                            <tr>
                                <th>Category</th>
                                <td data-bind="text: cat_name">category</td>
                                <th>Sub Category</th>
                                <td data-bind="text: sub_cat_name">####</td>
                            </tr>
                            <tr>
                                <th>Description</th>
                                <td data-bind="text: description">description</td>
                                <th>Parent Account</th>
                                <td data-bind="text: p_account_name">parent</td>
                            </tr>
                            <tr>
                                <th>Opening Balance</th>
                                <td data-bind="text: opening_balance?curr_format(opening_balance*1):''">0</td>
                                <th>Opening Balance as at</th>
                                <td data-bind="text: opening_balance_date?moment(opening_balance_date,'YYYY-MM-DD').format('D-MMM-YYYY'):''">2019</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

