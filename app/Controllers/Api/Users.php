<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, OPTIONS, UPDATE, PUT");

class Users extends ResourceController
{
  protected $format = 'json';
  protected $modelName = 'App\Models\Customer_model';

  public function index()
  {
    return $this->respond($this->model->findAll(), 200);
  }

  public function findUser($role = 'admin')
  {
    $result = $this->model->getWhere(['role' => $role])->getResult();
    return $this->respond($result, 200);
  }

  public function delete($id = NULL)
  {
    $deletedRow = $this->model->find($id);

    $delete = $this->model->delete($id);
    if ($delete) {
      $response = [
        'status'  => 200,
        'user'   => $deletedRow,
        'message'    => 'User deleted successfully'
      ];
      return $this->respond($response, 200);
    } else {
      $msg = ['message' => 'User not successfully deleted'];
      $response = [
        'status' => 500,
        'error' => true,
        'data'  => $msg
      ];
      return $this->respond($response, 500);
    }
  }
}