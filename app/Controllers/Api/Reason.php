<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, OPTIONS, UPDATE, PUT");

class Reason extends ResourceController
{
  protected $format = 'json';
  protected $modelName = 'App\Models\Reason_model';

  public function index()
  {
    return $this->respond($this->model->findAll());
  }

  public function create()
  {
    $data = $this->request->getJSON();

    $tag = [
      'reason'  => $data->reason,
      'created_by' => $data->created_by
    ];

    $stored = $this->model->insert($tag);
    $lastData = $this->model->find($stored);

    if ($stored) {
      $response = [
        'data'  => $lastData
      ];
      return $this->respondCreated($response, "Reason created");
    }
  }
}