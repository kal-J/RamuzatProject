<div  class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                          <caption class="text-success" style=" font-size: 1.5em; font-weight: bold; text-align: center; caption-side: top;">Client's Savings info</caption>
                        <thead>
                          <tr>
                            <th>Account #</th>
                            <th>Account Balance</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td data-bind="text: account_no"></td>
                            <td data-bind="text: (real_bal)?curr_format(real_bal*1):'Unable to compute'"></td>
                          </tr>
                        </tbody>
                        </table>

                        <table class="table table-striped table-bordered table-hover" >
                            <caption class="text-success" style=" font-size: 1.5em; font-weight: bold; text-align: center; caption-side: top;">Fees payments to be deducted  upon approval</caption>
                              <thead>
                                  <tr>
                                    <th>Fee</th>
                                    <th>Amount(UGX)</th>
                                  </tr>
                              </thead>
                              <!-- ko if: !($root.fees_upon_approval)-->
                                <tbody>
                                  <tr>
                                    <td colspan="2"><span class="text-center">No charge fees for this Product</span></td>
                                  </tr> 
                                </tbody>
                               <!--/ko-->
                              <tbody data-bind="foreach: $root.fees_upon_approval">
                                <tr>
                                  <td><span data-bind="text: feename"></span></td>
                                  <td><span data-bind="text: curr_format(amount*1)"></span></td>
                                </tr>
                              </tbody>
                          </table>
                      </div>