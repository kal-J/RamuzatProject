<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Description of Chart of accounts
 *
 * @author reagan
 */
class FinancialReturns extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        $this->load->model('fiscal_model');
        if (empty($this->session->userdata('id'))) {
            redirect('welcome');
        }
        $this->data['privilege_list'] = $this->helpers->user_privileges(8, $this->session->userdata('staff_id'));
        $this->data['fiscal_list'] = $this->helpers->user_privileges(20, $_SESSION['staff_id']);
        $this->data['module_access'] = $this->helpers->org_access_module(8, $_SESSION['organisation_id']);
        if (empty($this->data['privilege_list'])) {
            redirect('my404');
        } else {
            $this->data['accounts_privilege'] = array_column($this->data['privilege_list'], "privilege_code");
            $this->data['fiscal_privilege'] = array_column($this->data['fiscal_list'], "privilege_code");
        }
        $this->load->model('accounts_model');
        $this->load->model('ledger_model');
        $this->load->model('Fiscal_month_model');
        $this->load->model('miscellaneous_model');
        $this->load->model('RolePrivilege_model');
        $this->load->model('FinancialReturns_model');
        $fiscal_year = $this->Dashboard_model->get_current_fiscal_year($_SESSION['organisation_id'], 1);
            if(empty($fiscal_year)){
                redirect('dashboard');
            }else{
            $this->data['fiscal_year'] = array_merge($fiscal_year,['start_date2'=>date("d-m-Y", strtotime($fiscal_year['start_date'])),'end_date2'=>date("d-m-Y", strtotime($fiscal_year['end_date']))]);
            $this->data['lock_month_access'] = $this->helpers->org_access_module($module_id = 23, $_SESSION['organisation_id']);
                if(!empty($this->data['lock_month_access'])){
                    $this->data['active_month'] = $this->Fiscal_month_model->get_active_month();
                    if(empty($this->data['active_month'])){
                       redirect('dashboard');
                    }
                } 
            }
        }

            public function index() {
        // $mydata =$this->accounts_model->get3("category_id IN(1,2,3)");
        // print_r($mydata);die();
        $this->load->model('transactionChannel_model');
        $this->load->model('staff_model');
        $this->load->model('country_model');
        $this->load->model('Share_issuance_model');
        $rand_no = mt_rand(1000, 1200);
        $neededjs = array("plugins/select2/select2.full.min.js", "plugins/selectize/standalone/selectize.min.js","plugins/validate/jquery.validate.min.js", "plugins/daterangepicker/daterangepicker.js","plugins/steps/jquery.steps.min.js?v=$rand_no","plugins/steps/jquery.steps.fix.js","plugins/printjs/print.min.js");

        $neededcss = array("fieldset.css","plugins/selectize/css/selectize.default.css", "plugins/select2/select2.min.css", "plugins/daterangepicker/daterangepicker-bs3.css","plugins/steps/jquery.steps.css");

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
 
        $this->data['fiscal_years'] = $this->fiscal_model->get('close_status=1');
        $this->data['title'] = $this->data['sub_title'] = "Reports";
        // Load a view in the content partial
        $this->template->title = $this->data['title'];
        $this->data['fiscal_period'] = $this->fiscal_model->get('status_id=1');

        $this->data['data'] = $this->FinancialReturns_model->current_year_quarter_data();
        $this->data['combined'] = $this->FinancialReturns_model->combined_data();
        $this->template->content->view('reports/financial_return/index', $this->data);
        // Publish the template
        $this->template->publish();
    }

    public function toJson() {
        $data = $this->FinancialReturns_model->current_year_quarter_data();
        echo json_encode($data);
    }

    public function previousQuarterDatatoJson() {
        $data = $this->FinancialReturns_model->combined_data();
        echo json_encode($data);
        // print_r($data);
    }

    

    public function lastYearCorrespondingQuarterDatatoJson() {
        $data = $this->FinancialReturns_model->last_year_correspondig_quarter_data();
        echo json_encode($data);
    }

    public function export_to_excel() {
        $data = $this->FinancialReturns_model->current_year_quarter_data();
        $combined = $this->FinancialReturns_model->combined_data();
        // create php excel object
        $spreadsheet = new Spreadsheet();
        // set active sheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->mergeCells("A1:D1");
        $sheet->setCellValue('A1', "STATEMENT OF FINANCIAL POSITION");
        $sheet->getStyle("A1:D1")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:D1')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); //Set horizontal center

        // sacco name 
        $sheet->setCellValue('A2', 'SACCO NAME');
        $sheet->mergeCells("B2:D2");
        $sheet->setCellValue('B2', "Sacco name");
        $sheet->getStyle("A2:D2")->getFont()->setBold(true);
        // financial year  
        $sheet->setCellValue('A3', 'FINANCIAL YEAR');
        $sheet->mergeCells("B3:D3");
        $sheet->setCellValue('B3', "2022");
        $sheet->getStyle("A3:D3")->getFont()->setBold(true);
         //start date 
        $sheet->setCellValue('A4', 'START DATE');
        $sheet->mergeCells("B4:D4");
        $sheet->setCellValue('B4', "2020-01-01");
        $sheet->getStyle("A4:D4")->getFont()->setBold(true);
         // end date
        $sheet->setCellValue('A5', 'END DATE');
        $sheet->mergeCells("B5:D5");
        $sheet->setCellValue('B5', "2022-12-31");
        $sheet->getStyle("A5:D5")->getFont()->setBold(true);
          // end date
        $sheet->setCellValue('A6', 'ACCOUNT(S)');
        $sheet->setCellValue('B6', "CURRENT YEAR QUARTER");
        $sheet->setCellValue('C6', "LAST YEAR CORRESPONDING QUARTER");
        $sheet->setCellValue('D6', "PREVIOUS QUARTER");
        $sheet->getStyle("A6:D6")->getFont()->setBold(true);

        // data
        $rowCount = 7;

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
              //name
                $sheet->mergeCells("A".$rowCount.":D".$rowCount);
                $sheet->setCellValue('A'.$rowCount, strtoupper($ke));
                $sheet->getStyle("A".$rowCount.":D".$rowCount)->getFont()->setBold(true);
                $rowCount++; // go to the next line

                foreach($val as $k => $v){
                foreach ($v as $k2 => $v2) {
                    $sheet->setCellValue('A'.$rowCount, $k2);
                    $sheet->setCellValue('B'.$rowCount, number_format($v2));
                        if(isset($combined[$j][$k2])){
                            $single_category_total1 += $combined[$j][$k2][0];
                            $single_category_total2 += $combined[$j][$k2][1];
                            $sheet->setCellValue('C'.$rowCount, number_format($combined[$j][$k2][0]));
                            $sheet->setCellValue('D'.$rowCount,number_format($combined[$j][$k2][1]) );
                        
                        }else {
                            $found_key = array_column($combined, $k2);
                            $single_category_total1 += $found_key[0][0];
                            $single_category_total2 += $found_key[0][1];
                            $sheet->setCellValue('C'.$rowCount, number_format($found_key[0][0]));
                            $sheet->setCellValue('D'.$rowCount,number_format($found_key[0][1]) );
                        }

                        $rowCount++; // go to the next line
                  $single_category_total += $v2;
                  $total += $v2;
                }
                $j += 1;
              }
                $total1 += $single_category_total1;
                  $total2 += $single_category_total2;

                $sheet->setCellValue('A'.$rowCount, '');
                $sheet->setCellValue('B'.$rowCount, number_format($single_category_total));
                $sheet->setCellValue('C'.$rowCount, number_format($single_category_total1));
                $sheet->setCellValue('D'.$rowCount, number_format($single_category_total2));
                $sheet->getStyle("A".$rowCount.":D".$rowCount)->getFont()->setBold(true);
                $rowCount++; // go to the next line
                
               
            }

          }
        }

        $sheet->setCellValue('A'.$rowCount, '');
        $sheet->setCellValue('B'.$rowCount, number_format($total));
        $sheet->setCellValue('C'.$rowCount, number_format($total1));
        $sheet->setCellValue('D'.$rowCount, number_format($total2));
        $sheet->getStyle("A".$rowCount.":D".$rowCount)->getFont()->setBold(true);
        $rowCount++; // go to the next line

        // auto size
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = "financial returns";
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');

    }

    public function export_to_pdf() {
        $this->data['org_name'] = $this->organisation_model->get('id=1');
        $this->data['fiscal_period'] = $this->fiscal_model->get('status_id=1');
        $this->load->helper('pdf_helper');

        $neededjs = array("plugins/validate/jquery.validate.min.js", "plugins/select2/select2.full.min.js", "plugins/daterangepicker/daterangepicker.js", "plugins/validate/jquery.validate.min.js", "plugins/printjs/print.min.js");
        $neededcss = array("plugins/select2/select2.min.css","plugins/daterangepicker/daterangepicker-bs3.css");

        $this->helpers->dynamic_script_tags($neededjs, $neededcss);
        $this->data['title'] = $_SESSION["org_name"];
        $this->data['fiscal_period'] = $this->fiscal_model->get('status_id=1');
        $this->data['sub_title'] = "Portfolio Aging ";
        $this->data['font'] = 'helvetica';
        $this->data['fontSize'] = 7;


        $this->data['fiscal_years'] = $this->fiscal_model->get('close_status=1');
        $this->data['title'] = $this->data['sub_title'] = "Financial returns";
        // Load a view in the content partial
        $this->template->title = $this->data['title'];
        $this->data['data'] = $this->FinancialReturns_model->current_year_quarter_data();
        $this->data['combined'] = $this->FinancialReturns_model->combined_data();

        $this->data['the_page_data'] = $this->load->view('reports/financial_return/financial_return_to_pdf',$this->data, TRUE);
        echo json_encode($this->data);

    }


}
