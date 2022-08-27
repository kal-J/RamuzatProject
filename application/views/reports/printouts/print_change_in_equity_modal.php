<section>
    <div class="modal fade" id="print_change_in_equity_report" tabindex="-1" role="dialog"
        aria-labelledby="printLayoutTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="max-width: 80vw; width: 80vw">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLongTitle">Change In Equity</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div id="change_in_equity_printable">
                        <h3>
                            <center> Statement of changes in equity for the year ended -
                                <?php echo date('d-M-Y',strtotime($end_date));?>
                            </center>
                        </h3>
                        <table class="table table-sm table-bordered" width="100%">
                            <tbody>
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Share Captal</th>
                                        <th>Retained earnings</th>
                                        <th>Revaluation Surplus
                                        </th>
                                        <th>Total equity</th>
                                    </tr>
                                </thead>
                                <tr>
                                    <th>Balance at - <?php echo date('d-M-Y',strtotime($start_date));?></th>
                                    <th>0</th>
                                    <th>0</th>
                                    <th>0</th>
                                    <th>0</th>
                                </tr>
                                <!-- <tr><td>Issue of share capital</td><td>0</td><td>0</td><td>0</td><td>0</td> -->
                                <tr>
                                    <td>Correction of prior period error</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                </tr>
                                <tr>
                                    <th>Restated balance</th>
                                    <th>0</th>
                                    <th>0</th>
                                    <th>0</th>
                                    <th>0</th>
                                <tr>
                                    <td colspan="5"></td>
                                <tr>
                                    <td>Issue of share capital</td>
                                    <td>0</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>0</td>

                                <tr>
                                    <td>Dividends</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>0</td>
                                </tr>
                                <tr>
                                    <td>Income for the year</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>0</td>
                                </tr>
                                <tr>
                                    <td>Revaluation gain </td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                </tr>
                                <tr>
                                    <th>At the end of -<?php echo date('d-M-Y',strtotime($end_date));?></th>
                                    <th>0</th>
                                    <th>0</th>
                                    <th>0</th>
                                    <th>0</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button
                            onclick="printJS({printable: 'change_in_equity_printable', type: 'html', targetStyles: ['*'], documentTitle: 'Change-In-Equity'})"
                            type="button" class="btn btn-primary">Print</button>
                    </div>
                </div>
            </div>
        </div>
</section>
