<?php namespace App\Controllers;

define('SUCCESS_CODE', 200);
define('ERROR_CODE', 500);

use App\Models\WebserviceModel;
use CodeIgniter\Controller;

use CodeIgniter\API\ResponseTrait;

class Webservice extends Controller
{
    use ResponseTrait;
    private $model;

    public function __construct()
    {
        $this->model = new WebserviceModel();
    }

    public function index()
    {
        return $this->list();
    }

    public function list()
    {
        $data = $this->model->getWebservice();
        return $this->respond($data,200);
    }

    public function view($slug = null, $timestamp = null)
    {
        $data = $this->model->getWebservice($slug, $timestamp);
        return $this->respond($data, 200);
    }

    public function store() {
        try {
            $data = json_decode($this->request->getBody(), true);
            $this->check_request($data, 'Request body is empty.');

            $this->model->saveWebservice($data);
            return $this->respondCreated($data);
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }

    }

    // public function index()
    // {
    //     $this->response(array(
    //         'message' => 'welcome to key/value store webservice'
    //     ), 200);
    // }


    /**
     * object
     *
     * @return void
     */
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
     * get_all
     *
     * @return array
     */
    private function get_all()
    {
        $result = $this->webservice_model->get_service();

        if (empty($result)) {
            throw new Exception("No result found.");
        }

        return $result;
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

        if (empty($saved_ids)) {
            throw new Exception("Create/Update create failed");
        }

        foreach ($saved_ids as $key => $saved_id) {
            $result[] = $this->webservice_model->get_service(array(
                'id' => $saved_id
            ));
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
        $timestamp = $this->input->get('timestamp');
        $return_key = trim($this->uri->segment(3));

        $this->check_request($return_key,  'Please provide key.');
        $result = $this->webservice_model->get_service(array(
            'key' => $return_key,
            'timestamp' =>  $timestamp,
        ));

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
            throw new \Exception($message);
        }
    }
}
