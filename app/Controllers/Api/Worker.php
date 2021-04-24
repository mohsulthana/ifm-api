<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\Worker_model;

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, OPTIONS, UPDATE, PUT");

class Worker extends ResourceController
{
  protected $format = 'json';
  protected $modelName = 'App\Models\Worker_model';

  public function __construct()
  {
    $this->worker = new Worker_model();
  }

  public function index()
  {
    $worker = $this->model->findAll();
    return $this->respond($worker, 200);
  }

  public function create()
  {
    $json = $this->request->getJSON();

    $password_hash = password_hash($json->password, PASSWORD_BCRYPT);
    $data = [
      'name'  => $json->name,
      'email' => $json->email,
      'photo' => $json->photo,
      'about' => $json->about,
      'password'  => $password_hash
    ];

    $register = $this->worker->register($data);

    if ($register == true) {
      $id = $this->worker->insertID();
      $lastWorker = $this->model->find($id);

      $output = [
        'status'  => 200,
        'message' => 'Worker successfully registered!',
        'data'  => $lastWorker
      ];
      return $this->respond($output, 200);
    } else {
      $output = [
        'status'  => 400,
        'message' => 'Failed to create worker. Please contact developer.'
      ];
      return $this->respond($output, 400);
    }
  }

  public function delete($id = NULL)
  {
    $deletedRow = $this->model->find($id);

    $delete = $this->model->delete($id);
    if ($delete) {
      $response = [
        'status'  => 200,
        'user'   => $deletedRow,
        'message'    => 'Worker deleted successfully'
      ];
      return $this->respond($response, 200);
    } else {
      $msg = ['message' => 'Worker not successfully deleted'];
      $response = [
        'status' => 500,
        'error' => true,
        'data'  => $msg
      ];
      return $this->respond($response, 500);
    }
  }
}