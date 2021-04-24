<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, OPTIONS, UPDATE, PUT");

class Service extends ResourceController
{
  protected $format = 'json';
  protected $modelName = 'App\Models\Service_model';

  public function index($id = null)
  {
    return $this->respond($this->model->findAll(), 200);
  }

  public function show($id = null)
  {
      $data = $this->model->getWhere(['product_id' => $id])->getResult();
      if($data){
          return $this->respond($data);
      }else{
          return $this->failNotFound('No Data Found with id '.$id);
      }
  }

  public function create()
  {
    $validation = \Config\Services::validation();

    $data = $this->request->getJSON();
    $data = [
      'service'   => $data->service->service,
      'description' => $data->service->description
    ];

    if ($validation->run($data, 'service') == FALSE) {
      $response = [
        'status'  => 500,
        'error' => true,
        'data'  => $validation->getErrors(),
      ];
      return $this->respond($response, 500);
    } else {
      $stored = $this->model->insert($data);
      $lastData = $this->model->find($stored);
      if ($stored) {
        $response = [
          'id'  => $stored,
          'status'  => 200,
          'error' => true,
          'data'  => $lastData
        ];
        return $this->respond($response, 200);
      }
    }
  }

  public function update($id = NULL)
  {
    $validation =  \Config\Services::validation();
    $json = $this->request->getJSON();

    $data = $this->model->asObject()->find($id);

    if ($data) {
      $service = [
        'service'   => $json->service->service,
        'description' => $json->service->description
      ];

      if ($validation->run($service, 'service') == FALSE) {
        $response = [
          'status' => 500,
          'error' => true,
          'data' => $validation->getErrors(),
        ];
        return $this->respond($response, 500);
      } else {
        $update = $this->model->update($id, $service);
        if ($update) {
          $msg = ['message' => 'service updated'];
          $response = [
            'status' => 200,
            'service'   => $this->model->find($id),
            'data' => $msg,
          ];
          return $this->respond($response, 200);
        }
      }
    }
  }

  public function delete($id = NULL)
  {
    $delete = $this->model->delete($id);
    if ($delete) {
      $response = [
        'status'  => 200,
        'service'   => $this->model->findAll(),
        'message'    => 'Service deleted successfully'
      ];
      return $this->respond($response, 200);
    } else {
      $response = [
        'status' => 500,
        'error' => true,
        'msg'  => 'Service not successfully deleted'
      ];
      return $this->respond($response, 500);
    }
  }
}
