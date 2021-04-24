<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, OPTIONS, UPDATE, PUT");

class Task extends ResourceController
{
  protected $format = 'json';
  protected $modelName = 'App\Models\Task_model';

  public function index()
  {
    return $this->respond($this->model->findAll(), 200);
  }

  public function create()
  {
    $validation = \Config\Services::validation();

    $data = $this->request->getJSON();
    $data = [
      'task'   => $data->task->task,
      'description' => $data->task->description,
      'status' => $data->task->status
    ];

    if ($validation->run($data, 'task') == FALSE) {
      $response = [
        'status'  => 500,
        'error' => true,
        'data'  => $validation->getErrors(),
      ];
      return $this->respond($response, 500);
    } else {
      $stored = $this->model->insert($data);

      if ($stored) {
        $msg = ['message' => 'Task created'];
        $response = [
          'id'  => $stored,
          'status'  => 200,
          'error' => true,
          'data'  => $msg
        ];
        return $this->respond($response, 200);
      }
    }
  }

  public function update($id = NULL)
  {
    $validation =  \Config\Services::validation();
    $json = $this->request->getJSON();

    $id = $json->id;
    $data = $this->model->asObject()->find($id);

    if ($data) {
      $task = [
        'task' => $json->task,
        'description' => $json->description,
        'status'  => $json->status
      ];

      if ($validation->run($task, 'task') == FALSE) {
        $response = [
          'status' => 500,
          'error' => true,
          'data' => $validation->getErrors(),
        ];
        return $this->respond($response, 500);
      } else {
        $update = $this->model->update($id, $task);

        if ($update) {
          $msg = ['message' => 'Task updated'];
          $response = [
            'status' => 200,
            'task'   => $this->model->find($id),
            'data' => $msg,
          ];
          return $this->respond($response, 200);
        }
      }
    }
  }

  public function delete($id = NULL)
  {
    $data = $this->request->getJSON();

    $id = $data->task->id;
    $delete = $this->model->delete($id);
    if ($delete) {
      $msg = ['message' => 'Item deleted successfully'];
      $response = [
        'status'  => 200,
        'task'   => $this->model->findAll(),
        'message'    => $msg
      ];
      return $this->respond($response, 200);
    } else {
      $msg = ['message' => 'Item not successfully deleted'];
      $response = [
        'status' => 500,
        'error' => true,
        'data'  => $msg
      ];
      return $this->respond($response, 500);
    }
  }
}
