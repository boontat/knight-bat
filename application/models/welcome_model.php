<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Webservice_model extends CI_Model {

    private $db = '';

    public function __construct()
    {
        parent::__construct();
        $this->db = $this->load->database('default', true);
    }

    public function get_service($key)
    {
        $sql = "SELECT * FROM webservice";
        $query = $this->db->query($sql);

        return (!empty($query->result())) ? $query->result() : array();
    }
}
