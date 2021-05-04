<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, OPTIONS, UPDATE, PUT");

class Project extends ResourceController
{
  protected $format = 'json';
  protected $modelName = 'App\Models\Project_model';

  public function index()
  {
    return $this->respond($this->model->findAll(), 200);
  }

  public function projectByCustomer($id = null)
  {
    $projects = $this->model->join('customer', 'customer.user_id = project.customer_id')->where(['project.customer_id' => $id])->findAll();
    return $this->respond($projects, 200);
  }

  public function projectFolder($id)
  {
    $projects = $this->model->select('project.project, project.description, project.id, project.image')->join('task', 'task.project_id = project.id', 'inner')->join('worker', 'worker.worker_id = task.worker_id')->where('worker.worker_id', $id)->groupBy('project.id')->findAll();
    return $this->respond($projects, 200);
  }

  public function create()
  {
    $validation = \Config\Services::validation();

    $data = $this->request->getJSON();

    $data = [
      'project'   => $data->project->project,
      'description' => $data->project->description,
      'customer_id' => $data->project->customer,
      'image' => $data->project->image
    ];

    if ($validation->run($data, 'project') == FALSE) {
      $response = [
        'status'  => 500,
        'error' => true,
        'data'  => $validation->getErrors(),
      ];
      return $this->respond($response, 500);
    } else {
      $stored = $this->model->insert($data);

      if ($stored) {
        $msg = ['message' => 'Project created'];
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

    $data = $this->model->asObject()->find($id);

    if ($data) {
      $project = [
        'project' => $json->project,
        'description' => $json->description
      ];

      if ($validation->run($project, 'project') == FALSE) {
        $response = [
          'status' => 500,
          'error' => true,
          'data' => $validation->getErrors(),
        ];
        return $this->respond($response, 500);
      } else {
        $update = $this->model->update($id, $project);

        if ($update) {
          $msg = ['message' => 'Project updated'];
          $response = [
            'status' => 200,
            'project'   => $this->model->find($id),
            'data' => $msg,
          ];
          return $this->respond($response, 200);
        }
      }
    }
  }

  public function show($id = NULL)
  {
    $get = $this->model->find($id);
    $task = $this->model->select('*')->join('task', 'task.project_id = project.id', 'inner')->where('project.id', $id)->findAll();
    // $task = $this->model->join('task', 'task.project_id = project.id', 'inner')->where('project.id', $id)->findAll();

    if ($get) {
      $code = 200;
      $response = [
        'status' => $code,
        'data' => $get,
        'task'  => $task
      ];
    } else {
      $code = 401;
      $msg = ['message' => 'Not Found'];
      $response = [
        'status' => $code,
        'data' => $msg,
      ];
    }
    return $this->respond($response, $code);
  }

  public function delete($id = NULL)
  {
    $delete = $this->model->delete($id);
    if ($delete) {
      $response = [
        'status'  => 200,
        'project'   => $this->model->findAll(),
        'message'    => 'Project deleted successfully'
      ];
      return $this->respond($response, 200);
    } else {
      $response = [
        'status' => 500,
        'error' => true,
        'data'  => 'Project not successfully deleted'
      ];
      return $this->respond($response, 500);
    }
  }
}
