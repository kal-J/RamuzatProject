<?php 

  $fiscal_year = explode("-",$fiscal_period[0]['start_date']);
  $end_fiscal_year = explode("-",$fiscal_period[0]['end_date']);
?>
<div>

<div id="div_financial_return_printout" style="display: none;"></div>
<div class="d-flex flex-row-reverse mx-4">
  <button type="submit" class="btn btn-primary btn-sm" onclick="handlePrint_financial_returns()"> 
    <i class="fa fa-print fa-2x"></i> 
  </button>

  <div class="mr-2">
    <a id="print_trial_balance_excel" href="<?php echo base_url(). "FinancialReturns/export_to_excel" ?> ">
        <button class="btn btn-sm btn-primary">
            <i class="fa fa-file-excel-o fa-2x"></i>
        </button>
    </a>
  </div>
</div>


  <table class="table">
    <thead>
      <tr class="text-center">
        <th colspan="4">STATEMENT OF FINANCIAL POSITION</th>
      </tr>
      <tr>
        <th>SACCO NAME</th>
        <th colspan="3"><?php echo $_SESSION['org_name']; ?></th>
      </tr>
      <tr>
        <th>FINANCIAL YEAR</th>
        <th colspan="3"><?php echo $fiscal_year[0] == $end_fiscal_year[0] ? $end_fiscal_year[0] :$fiscal_year[0]." - ".$end_fiscal_year[0]; ?></th>
      </tr>
      <tr>
        <th>START DATE</th>
        <th colspan="3"><?php echo $fiscal_years[0]['start_date'] ?></th>
      </tr>
      <tr>
        <th>END DATE</th>
        <th colspan="3"><?php echo $fiscal_years[0]['end_date'] ?></th>
      </tr>
      <tr>
        <th>ACCOUNT(S)</th>
        <th>CURRENT YEAR QUARTER</th>
        <th>LAST YEAR CORRESPONDING QUARTER</th>
        <th>PREVIOUS QUARTER</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $j = 0;
        $total = 0;
        $total1 = 0;
        $total2 = 0;
        foreach ($data['response'] as $key=>$value) {
          for($i =0; $i<count($value); $i++){
            foreach ($value[$i] as $ke => $val) {
              $single_category_total = 0;
              $single_category_total1 = 0;
              $single_category_total2 = 0;
              ?>
              <tr>
                <th><?php echo strtoupper($ke); ?></th>
              </tr>
              <?php
              foreach($val as $k => $v){
                foreach ($v as $k2 => $v2) {
                  ?>
                    <tr>
                      <td><?php echo $k2?></td>
                      <td><?php echo number_format($v2) ?></td>
                      <?php
                        if(isset($combined[$j][$k2])){
                          $single_category_total1 += $combined[$j][$k2][0];
                          $single_category_total2 += $combined[$j][$k2][1];
                          ?>
                          <td><?php echo number_format($combined[$j][$k2][0]) ?></td>
                          <td><?php echo number_format($combined[$j][$k2][1]) ?></td>
                          <?php
                        }else {
                          $found_key = array_column($combined, $k2);
                          $single_category_total1 += $found_key[0][0];
                          $single_category_total2 += $found_key[0][1];
                          ?>
                          <td><?php echo number_format($found_key[0][0]) ?></td>
                          <td><?php echo number_format($found_key[0][1]) ?></td>
                          <?php
                        }
                      ?>
                     
                    </tr>
                  <?php
                  $single_category_total += $v2;
                  $total += $v2;
                }
                $j += 1;
              }
                ?>
                <tr style="background:#eee;">
                <?php
                  $total1 += $single_category_total1;
                  $total2 += $single_category_total2;
                
                ?>
                  <th></th>
                  <th><?php echo number_format($single_category_total); ?></th>
                  <th><?php echo number_format($single_category_total1); ?></th>
                  <th><?php echo number_format($single_category_total2); ?></th>
                </tr>
                <?php
            }

          }
        }
        ?>
         <tr style="background: #eee;">
         <th></th>
         <th><?php echo number_format($total); ?></th>
         <th><?php echo number_format($total1); ?></th>
         <th><?php echo number_format($total2); ?></th>
        </tr>
        <?php
      
      ?>
    </tbody>
  </table>
</div>

<script>
     function handlePrint_financial_returns(){
    // $('#btn_printing_shares_report').css('display', 'flex');
    //     $('#btn_print_active_report').css('display', 'none');
        $.ajax({
            url: '<?php echo site_url("FinancialReturns/export_to_pdf"); ?>',
            type: 'GET',
            dataType: 'json',
            success: function (response) {

         
                $('#div_financial_return_printout').html(response.the_page_data);
                printJS({printable: 'printable_financial_return_pdf_printout', type: 'html', targetStyles: ['*'], documentTitle: response.sub_title});
            },
            fail: function (jqXHR, textStatus, errorThrown) {
                // $('#btn_printing_active_shares').css('display', 'none');
                // $('#btn_print_active_shares').css('display', 'flex');
            console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            },
            error:  function (err) {
                // $('#btn_printing_active_shares').css('display', 'none');
                // $('#btn_print_active_shares').css('display', 'flex');
            }
        });
    }
</script>