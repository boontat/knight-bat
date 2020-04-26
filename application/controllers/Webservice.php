<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Webservice extends CI_Controller 
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('webservice_model');
    }

    public function index()
    {
        // die('Not available');
    }

    public function object()
    {
        try {
            $request_method = $_SERVER['REQUEST_METHOD'];
            $raw_body = json_decode(file_get_contents('php://input'), true);
            $this->check_request_type($request_method);

            if ($request_method === 'POST') {
                // save/update process
                $this->check_request($raw_body, 'Request body is empty.');
                $saved_ids = $this->save_json($raw_body);

                foreach ($saved_ids as $key => $saved_id) {
                    $result[$saved_id] = $this->webservice_model->get_service_by_id($saved_id);
                }

                foreach ($result as $key => $rows)
                {
                    foreach ($rows as $key2 => $row) {
                        echo json_encode($row);
                    }
                }

            } else if ($request_method === 'GET') {
                // get process
                $return_key = $this->uri->segment(3);
                $this->check_request($return_key,  'Please provide key.');
                $result = $this->webservice_model->get_service($return_key);

                foreach ($result as $row)
                {
                    echo $row->value;
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    private function save_json($data)
    {
        $return_ids = array();
        foreach ($data as $key => $value) {
            $return_ids[] = $this->webservice_model->save_service($key, $value);
        }

        return $return_ids;
    }

    /**
     * check_request_type
     *
     * @param  string $request_method - value from _SERVER's REQUEST_METHOD
     * @return boolean
     */
    private function check_request_type($request_method)
    {
        $supported = array('POST', 'GET');

        if (in_array($request_method, $supported)) {
            return true;
        } else {
            throw new Exception("Request method not supported.");
        }
    }
    
    /**
     * check_request
     *
     * @param  mixed $request
     * @param  string $message - error message
     * @return void
     */
    private function check_request($request, $message)
    {
        if (empty($request)) {
            throw new Exception($message);
        }
    }
}
