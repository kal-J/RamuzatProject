
<div role="tabpanel" id="tab-trial_balance" class="tab-pane active">

     <div class="panel-title" >
        <h3><center>Trial Balance</center></h3>
      </div>
    <div class="panel-body">
         <?php if(in_array('6', $report_privilege)){ ?>
          <div class="d-flex flex-row-reverse mx-4">
              <form target="_blank" action="<?php echo site_url("reports/print_trial_balance_pdf"); ?>" method="post">
                  <input type="hidden" name="print" value="1" />
                  <input type="hidden" name="report_type" value="1"/>
                  <input type="hidden" name="fisc_date_from"
                      data-bind="value:moment(start_date(),'X').format('YYYY-MM-DD')" />
                  <input type="hidden" name="fisc_date_to"
                      data-bind="value:moment(end_date(),'X').format('YYYY-MM-DD')" />
                  <button type="submit" class="btn btn-primary btn-sm"> 
                  <i class="fa fa-print fa-2x"></i> 
                  </button>
              </form>

              <div class="mr-2">
            <a id="print_trial_balance_excel">
                <button class="btn btn-sm btn-primary">
                    <i class="fa fa-file-excel-o fa-2x"></i>
                </button>
            </a>
        </div>

          </div>
          <?php } ?>
      <br>
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table-bordered display compact nowrap table-hover" id="tblTrialbalance" width="100%" >
                    <thead>
                        <tr>
                            <th>Account Name</th>
                            <th>Debit </th>
                            <th>Credit </th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th >Totals</th>
                            <th>0 </th>
                            <th>0 </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $this->view('reports/print_options_trialb'); ?>

<!--  end tab trial balance -->
