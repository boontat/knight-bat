<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Webservice_model extends CI_Model {

    private $db = '';

    public function __construct()
    {
        parent::__construct();
        $this->db = $this->load->database('default', true);
    }

    public function get_service($option =array())
    {
        $sql = "SELECT id, `key`, `value`, UNIX_TIMESTAMP(created_at) AS created_at, UNIX_TIMESTAMP(updated_at) AS updated_at FROM webservice WHERE 1=1";

        if (!empty($option['id']) && intval($option['id'])) {
            $id = intval($option['id']);
            $sql .= " AND `id` = '{$id}'";
        }

        if (!empty($option['key'])) {
            $key = urldecode($option['key']); // when key had space in the 
            $sql .= " AND `key` = '{$key}'";
        }

        if (!empty($option['timestamp'])) {
            $sql .= "AND UNIX_TIMESTAMP(updated_at) = '{$option['timestamp']}'";
        }

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
