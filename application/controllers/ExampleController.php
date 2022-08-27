<?php
class ExampleController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
    }
     
    function index(){
        $string =array('Ambrose','Ambrose2021');

        foreach($string as $check){
            if(ctype_alpha($check)){
                echo $check." contains only  character";
                nl2br('\n');
            }
            else{
                echo $check." contains both alphanumerical character";
            }
        }
     
}
}
    
    