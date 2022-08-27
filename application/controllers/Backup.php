<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Backup extends CI_Controller {
  
    public function __construct() {
     parent::__construct(); 
     $this->load->library("session");
     if(empty($this->session->userdata('id'))){
        redirect('welcome');
    } 
    ini_set('memory_limit', '200M');
    ini_set('upload_max_filesize', '200M');
    ini_set('post_max_size', '200M');
    ini_set('max_input_time', 3600);
    ini_set('max_execution_time', 3600);
    }
    public function jsonList(){
        $dir = "./backup/database_backup/";
        $result = array();
        if (file_exists($dir)) {
          $cdir = scandir($dir);
          foreach ($cdir as $key => $value) {
              if (!in_array($value, array(".", ".."))) {
                  if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                      $result[$value]['file_name'] = dirToArray($dir . DIRECTORY_SEPARATOR . $value);
                  } else {
                      $result[]['file_name'] = $value;
                  }
              }
          }
        }
        $data['data'] = $result;
        echo json_encode($data);
    }

    public function create(){
      $this->load->library('form_validation');
      $feedback['success'] = false;
      $feedback['message'] ='Database backup operation FAILED';
      

      $this->form_validation->set_rules('file_name', 'File Name', array('required'));

        if($this->form_validation->run() === FALSE ){
              $feedback['message'] = validation_errors('<li>','</li>');
            }else{    
                TRUE;           
                $this->load->helper('download');
                $this->load->helper('file');
                $this->load->dbutil();
                $filename = ucfirst($this->input->post('file_name')).'['.date("Y-m-d H-i-s") ."].sql";
                $prefs = array(
                    'ignore' => array(),
                    'format' => 'txt',
                    'filename' => 'mybackup.sql',
                    'add_drop' => TRUE,
                    'add_insert' => TRUE,
                    'newline' => "\n"
                );
                $backup = $this->dbutil->backup($prefs);
                $dir = './backup/database_backup/';

                if (!file_exists($dir)) {
                  mkdir($dir, 0777, true);
                }
                $feedback['success'] = write_file($dir.$filename, $backup);
                if ($feedback['success']) {
                  $feedback['message'] ='Database backed up successfully';
                  //activity log :added the else part. Ambrose

                   $this->helpers->activity_logs($_SESSION['id'],18,"Creating backup ",$feedback['message']." # ". $filename,NULL,"File name ". $filename);
                }
                

            }
        echo json_encode($feedback);
    }

    public function delete_backup(){
      
    }
}
