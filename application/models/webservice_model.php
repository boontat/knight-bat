<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class webservice_model extends CI_Model {

    private $db = '';

    public function __construct()
    {
        parent::__construct();
        $this->db = $this->load->database('default', true);
    }

    public function get_service($key)
    {
        $sql = "SELECT * FROM webservice WHERE `key` = '{$key}'";
        $query = $this->db->query($sql);

        return (!empty($query->result())) ? $query->result() : array();
    }
    
    public function get_service_by_id($ids)
    {
        $sql = "SELECT * FROM webservice WHERE id = '{$ids}'";
        $query = $this->db->query($sql);

        return (!empty($query->result())) ? $query->result() : array();
    }

    public function save_service($key, $value)
    {
        $sql = "INSERT INTO webservice (`key`, `value`, created_at, updated_at) VALUES ('{$key}', '{$value}', NOW(), NOW()) ON DUPLICATE KEY UPDATE `value` = '{$value}', updated_at = NOW()";

        $this->db->query($sql);

        return $this->db->insert_id();
    }
}
