<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('user_model');
    }

    public function loadAdminUnits() {
        //$this->load->model('district_model');
        //$this->load->model('subcounty_model');
        /*$this->load->model('parish_model');
        $this->load->model('village_model');
# Create a DOM parser object
        $dom = new DOMDocument();
        $dom->validateOnParse = true;

        ///$districts = $this->district_model->get_districts(); districts
        //$subcounties = $this->subcounty_model->get_subcounties(); subcounties
        $db_places = $this->parish_model->get_parishes(); //parishes
        $places = [];
        $response = "failure";
        foreach ($db_places as $db_place) {
            $url = "http://www.lcmt.org";
            //$url = "https://www.ura.go.ug/payment.do?dispatch=getVillageList&distName=2&townName=350&subCountyName=11&parishName=782";
            //get the units and insert into the db
            $html = file_get_contents($url . $db_place['url_link']);
            if (!empty($html)) {
                # The @ before the method call suppresses any warnings that
                # loadHTML might throw because of invalid HTML in the page.
                @$dom->loadHTML($html);

                $xpath = new DOMXpath($dom);
                //$tag_subcounty_uls = $xpath->query("//ul[contains(@class, 'district_subcountylist')]"); //searching for subcounties
                //$tag_subcounty_uls = $xpath->query("//ul[contains(@class, 'subcounty_parishlist')]"); searching for parishes
                $tag_place_uls = $xpath->query("//ul[contains(@class, 'parish_villagelist')]"); //searching for villages
                if (!is_null($tag_place_uls)) {
                    foreach ($tag_place_uls as $tag_place_ul) {
                        $tag_places = $tag_place_ul->getElementsByTagName('a');
                        if (!is_null($tag_places)) {
                            foreach ($tag_places as $tag_place) {
                                # Show the <a href>
                                $href = $tag_place->getAttribute('href');
                                //the last anchor to be discarded
                                //$anchor_href = "add/?district_id"; //subcounty add id
                                //$anchor_href = "add/?subcounty_id"; parish add id
                                $anchor_href = "add/?parish_id";////village add id
                                if (strpos($href, $anchor_href) === false) {
                                    //$places[] = ["district_id" => $db_data['id'], "county_id" => 1, "url_link" => $href, "subcounty" => $tag_subcounty->nodeValue]; //subcounties
                                    //$places[] = ["subcounty_id" => $db_data['id'], "url_link" => $href, "parish" => $tag_subcounty->nodeValue]; parishes
                                    $places[] = ["parish_id" => $db_place['id'], "village" => $tag_place->nodeValue];// //villages
                                }
                            }
                        }
                    }
                }
            }
        }
        //if ($this->subcounty_model->set($places)) { //subcounties
        //if ($this->parish_model->set($places)) { //parishes
        if ($this->village_model->set($places)) { //villages
            $response = "Success.<br/> " . count($places) . " records inserted";
        }
        echo $response; 
        print_r($db_places);*/
    }
    public function add_admin_unit() {
        /*$this->load->model('district_model');
        $data['message'] = $this->district_model->set_json();
        if (is_numeric($data['message'])) {
            $data['success'] = TRUE;
        } else {
            $data['success'] = FALSE;
        }
        echo json_encode($data);*/
    }

    /*private function prepare_villages($unprepared_villages_data, $parish_id){
        //get the villages of a given parish and prepare the data accordingly
        $proper_village_data = [];
        foreach ($unprepared_villages_data as $unprepared_village_data){
            $proper_village_data[] = array("village"=>$unprepared_village_data->label,"parish_id"=>$parish_id,"id"=>$unprepared_village_data->value);
        }
        return $proper_village_data;
    } 
    private function get_villages($parish_details){
        $url_parts = explode(",", $parish_details['parishId']);
        $url = "https://www.ura.go.ug/payment.do?dispatch=getVillageList&distName=".$url_parts[0]."&townName=".$url_parts[1]."&subCountyName=".$url_parts[2]."&parishName=".$parish_details['id'];
        $contextOptions = [
            'ssl' => [
                'verify_peer' => false,
                'cafile' => base_url('/uploads/wwwuragoug.crt'),
                'CN_match' => 'www.ura.go.ug',
            ]
        ];
        $context = stream_context_create($contextOptions);
        $html = file_get_contents($url, false, $context);
        return json_decode($html);
    }*/
    /* public function update_admin_unit() {
       $this->load->model('parish_model');
        $this->load->model('village_model');
        $parishes = $this->parish_model->get_parishes();
        foreach ($parishes as $parish) {
        for ($idx=0; $idx<10; $idx++) {
            $parish = $parishes[$idx]; 
            //get the villages from this parish
           $villages_data = $this->prepare_villages($this->get_villages($parish),$parish['id']);
            $data['success'] = $this->village_model->set($villages_data);
        }
    }*/
}
