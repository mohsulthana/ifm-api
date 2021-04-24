<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\Admin_model;

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, OPTIONS, UPDATE, PUT");

class Admin extends ResourceController
{
  protected $format = 'json';
  protected $modelName = 'App\Models\Admin_model';

  public function __construct()
  {
    $this->admin = new Admin_model();
  }

  public function index()
  {
    $admin = $this->model->join('service', 'admin.service_id = service.id', 'inner')->findAll();
    return $this->respond($admin, 200);
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
      'password'  => $password_hash,
      'service_id'  => $json->service_id
    ];

    $register = $this->admin->register($data);

    if ($register == true) {
      $id = $this->admin->insertID();
      $lastAdmin = $this->model->find($id);

      $output = [
        'status'  => 200,
        'message' => 'Admin successfully registered!',
        'data'  => $lastAdmin
      ];
      return $this->respond($output, 200);
    } else {
      $output = [
        'status'  => 400,
        'message' => 'Failed to create admin. Please contact developer.'
      ];
      return $this->respond($output, 400);
    }
  }
}