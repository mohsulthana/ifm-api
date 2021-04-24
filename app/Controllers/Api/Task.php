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

  public function getTasks($id)
  {
    $task = $this->model->select('task.id, task.task, task.status, task.description, worker.name')->join('project', 'project.id = task.project_id', 'inner')->join('worker', 'worker.worker_id = task.worker_id', 'inner')->where('task.project_id', $id)->findAll();
    return $this->respond($task, 200);
  }

  public function singleTask($id = NULL)
  {
    $data = $this->model->select('task.*, project.qr_code')->join('project', 'project.id = task.project_id', 'inner')->find($id);

    if ($data) {
      $response = [
        'task'  => $data,
        'status'  => 200,
        'error' => true
      ];
      return $this->respond($response, 200);
    }

    print_r($data);
  }

  public function getTaskForWorker($id = NULL)
  {
    $data = $this->model->join('worker', 'worker.worker_id = task.worker_id', 'inner')->where('task.worker_id', $id)->findAll();
    return $this->respond($data, 200);
  }

  public function create()
  {
    $validation = \Config\Services::validation();

    $data = $this->request->getJSON();

    $data = [
      'task'   => $data->task->task,
      'description' => $data->task->description,
      'status' => $data->task->status,
      'worker_id' => $data->task->worker_id,
      'project_id'  => $data->task->project_id
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
      $task = $this->model->select('task.id, task.task, task.status, task.description, worker.name')->join('project', 'project.id = task.project_id', 'inner')->join('worker', 'worker.worker_id = task.worker_id', 'inner')->where('task.project_id', $data['project_id'])->findAll();

      if ($stored) {
        $msg = ['message' => 'Task created'];
        $response = [
          'id'  => $task,
          'status'  => 200,
          'error' => true,
          'data'  => $msg
        ];
        return $this->respond($response, 200);
      }
    }
  }

  // TODO: BEFORE WORK & AFTER WORK UPDATE

  public function updateBeforeWork($id = NULL)
  {
    $json = $this->request->getJSON();

    $data = $this->model->asObject()->find($id);
    if ($data) {
      $photo = [
        'before_work' => $json->before_work,
        'status'      => $json->status
      ];

      $update = $this->model->update($id, $photo);

      if ($update) {
        $msg = ['message' => 'Photo submitted'];
        $response = [
          'status' => 200,
          'task'   => $this->model->find($id),
          'photo' => $this->model->select('before_work')->find($id)
        ];
        return $this->respond($response, 200);
      }
    }
  }

  public function updateAfterWork($id = NULL)
  {
    $json = $this->request->getJSON();

    $data = $this->model->asObject()->find($id);
    if ($data) {
      $photo = [
        'after_work' => $json->after_work,
        'status'      => $json->status
      ];

      $update = $this->model->update($id, $photo);

      if ($update) {
        $msg = ['message' => 'Photo submitted'];
        $response = [
          'status' => 200,
          'task'   => $this->model->find($id),
          'photo' => $this->model->select('after_work')->find($id)
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
        'task'   => $this->model->findAll(),
        'message'    => 'Task deleted successfully'
      ];
      return $this->respond($response, 200);
    } else {
      $response = [
        'status' => 500,
        'error' => true,
        'data'  => 'Task not successfully deleted'
      ];
      return $this->respond($response, 500);
    }
  }

  public function updateStatus($id)
  {
    $json = $this->request->getJSON();

    $data = $this->model->asObject()->find($id);

    if ($data) {
      $task = [
        'status'  => $json->status
      ];
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
