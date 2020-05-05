<?php namespace App\Models;

use CodeIgniter\Model;

class WebserviceModel extends Model
{
    protected $table = 'webservice2';
    protected $allowedFields = array(
        'key',
        'value',
        'created_at',
        'updated_at',
    );

    public function getWebservice($slug = false, $timestamp = false)
    {
        if ($slug === false || empty($slug)) {
            return $this->findAll();
        }

        $where = ['key' => $slug];

        if (!empty($timestamp)) {
            $where = array_merge($where, ['updated_at' => $timestamp]);
        }

        return $this->asArray()
                    ->where($where)
                    ->first();
    }

    public function saveWebservice($data)
    {
        helper('date');
        $prepared_data = array();
        foreach ($data as $key => $value) {
            $prepared_data = array(
                'key' => $key,
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            );
            $this->insert($prepared_data);
        }
    }

    // public function get_service($option =array())
    // {
    //     $sql = "SELECT `key`, `value`, UNIX_TIMESTAMP(created_at) AS created_at, UNIX_TIMESTAMP(updated_at) AS updated_at FROM webservice WHERE 1=1";

    //     if (!empty($option['id']) && intval($option['id'])) {
    //         $id = intval($option['id']);
    //         $sql .= " AND `id` = '{$id}'";
    //     }

    //     if (!empty($option['key'])) {
    //         $key = urldecode($option['key']); // when key had space in the 
    //         $sql .= " AND `key` = '{$key}'";
    //     }

    //     if (!empty($option['timestamp'])) {
    //         $sql .= "AND UNIX_TIMESTAMP(updated_at) = '{$option['timestamp']}'";
    //     }

    //     $query = $this->db->query($sql);

    //     return (!empty($query->result())) ? $query->result() : array();
    // }

    // public function save_service($key, $value)
    // {
    //     $sql = "INSERT INTO webservice (`key`, `value`, created_at, updated_at) VALUES ('{$key}', '{$value}', NOW(), NOW()) ON DUPLICATE KEY UPDATE `value` = '{$value}', updated_at = NOW()";

    //     $this->db->query($sql);

    //     return $this->db->insert_id();
    // }
}
