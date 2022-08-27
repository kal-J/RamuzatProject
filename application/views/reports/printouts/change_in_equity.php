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
<div role="tabpanel" id="tab-change_in_equity" class="tab-pane">
    <div class="panel-body">
    <div class="col-lg-12">
    
     <?php if(in_array('6', $report_privilege)){ ?>
          <div  class="pull-right add-record-btn">
                  <a href="#print_change_in_equity_report" data-toggle="modal" class="btn btn-primary btn-sm"> <i class="fa fa-print fa-2x"></i> </a>
          </div>
      <?php } ?>
      <div>
        <h3>
          <center> Statement of changes in equity for the year ended - <?php echo date('d-M-Y',strtotime($end_date));?>
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
    </div>
    </div>
</div>

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
                    <div class="d-flex flex-column align-items-center">
                <img style="height: 50px;"
                    src="<?php echo base_url("uploads/organisation_".$_SESSION['organisation_id']."/logo/".$org['organisation_logo']);  ?>"
                    alt="logo">

                <div class="mx-auto text-center mb-2">
                    <span>
                        <?php echo $org['name']; ?> ,
                    </span>
                    <span>
                        <?php echo $branch['physical_address']; ?>, <?php echo $branch['branch_name']; ?>
                    </span><br>
                    <span>
                        <?php echo $branch['postal_address']; ?> ,
                    </span>
                    <span>
                        <b>Tel:</b> <?php echo $branch['office_phone']; ?>
                    </span>
                    <br><br>
                </div>
            </div>
                        <h3 class="row w-100 mx-auto d-flex justify-content-center">
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


<script type="text/javascript">
   $(function () {
        $('[rel="tooltip"]').tooltip();
      });
</script>