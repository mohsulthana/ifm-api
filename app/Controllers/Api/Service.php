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
    $data = [
      'totalProject' => $this->model->query("SELECT a.id, a.project, COUNT(*) AS jumlah FROM project a LEFT JOIN service i ON a.id = i.id GROUP BY a.service_id")->getResultArray(),
      'service' => $this->model->join('users', 'users.user_id = service.customer_id', 'inner')->findAll()
    ];
    return $this->respond($data, 200);
  }

  public function create()
  {
    $validation = \Config\Services::validation();

    $data = $this->request->getJSON();
    $data = [
      'service'   => $data->service->service,
      'description' => $data->service->description,
      'customer_id' => $data->service->customer_id
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

      if ($stored) {
        $response = [
          'id'  => $stored,
          'status'  => 200,
          'error' => true,
          'data'  => 'Project created'
        ];
        return $this->respond($response, 200);
      }
    }
  }

  // public function update($id = NULL)
  // {
  //   $validation =  \Config\Services::validation();
  //   $json = $this->request->getJSON();

  //   $id = $json->id;
  //   $data = $this->model->asObject()->find($id);

  //   if ($data) {
  //     $task = [
  //       'task' => $json->task,
  //       'description' => $json->description,
  //       'status'  => $json->status
  //     ];

  //     if ($validation->run($task, 'task') == FALSE) {
  //       $response = [
  //         'status' => 500,
  //         'error' => true,
  //         'data' => $validation->getErrors(),
  //       ];
  //       return $this->respond($response, 500);
  //     } else {
  //       $update = $this->model->update($id, $task);

  //       if ($update) {
  //         $msg = ['message' => 'Task updated'];
  //         $response = [
  //           'status' => 200,
  //           'task'   => $this->model->find($id),
  //           'data' => $msg,
  //         ];
  //         return $this->respond($response, 200);
  //       }
  //     }
  //   }
  // }

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
