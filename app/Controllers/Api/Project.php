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
    // $projects = $this->model->findAll();
    $projects = $this->model->join('users', 'users.user_id = project.customer_id', 'inner')->findAll();
    return $this->respond($projects, 200);
  }

  public function projectByCustomer($id = null)
  {
    $projects = $this->model->join('users', 'users.user_id = project.customer_id', 'inner')->where(['customer_id' => $id])->findAll();
    return $this->respond($projects, 200);
  }

  public function create()
  {
    $validation = \Config\Services::validation();

    $data = $this->request->getJSON();
    print_r($data);
    $data = [
      'project'   => $data->project->project,
      'description' => $data->project->description,
      'customer_id' => $data->project->customer,
      'service_id'  => $data->project->service_id
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
