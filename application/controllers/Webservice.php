<?php
defined('BASEPATH') OR exit('No direct script access allowed');

define('SUCCESS_CODE', 200);
define('ERROR_CODE', 500);

class Webservice extends CI_Controller 
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('webservice_model');
    }

    public function index()
    {
        $this->response(array(
            'message' => 'welcome to key/value store webservice'
        ), 200);
    }

    public function object()
    {
        try {
            $request_method = $_SERVER['REQUEST_METHOD'];
            $this->check_request_type($request_method);

            if ($request_method === 'POST') {
                $result = $this->process_post();
                $this->response($result);
            } else if ($request_method === 'GET') {
                $result = $this->process_get();
                $this->response($result);
            }
        } catch (Exception $e) {
            $this->response(array(
                "message" => $e->getMessage()
            ), ERROR_CODE);
        }
    }
    
    /**
     * process_post
     *
     * @return void
     */
    private function process_post()
    {
        // save/update process
        $raw_body = json_decode(file_get_contents('php://input'), true);
        $this->check_request($raw_body, 'Request body is empty.');
        $saved_ids = $this->save_json($raw_body);

        foreach ($saved_ids as $key => $saved_id) {
            $result[$saved_id] = $this->webservice_model->get_service_by_id($saved_id);
        }

        return $result;
    }
    
    /**
     * process_get
     *
     * @return void
     */
    private function process_get()
    {
        // get process
        $return_key = $this->uri->segment(3);
        $this->check_request($return_key,  'Please provide key.');
        $result = $this->webservice_model->get_service($return_key);

        if (empty($result)) {
            throw new Exception("No result found.");
        }

        return $result;
    }
    
    /**
     * save_json
     *
     * @param  mixed $data
     * @return void
     */
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
    
    /**
     * response
     *
     * @param  mixed $content
     * @return void
     */
    private function response($content, $status_code = '')
    {
        // header('Content-Type: application/json');
        echo json_encode($content);

        if ($status_code) {
            http_response_code($status_code);
        } else {
            http_response_code(SUCCESS_CODE);
        }
    }
}
