<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, OPTIONS, UPDATE, PUT");

class Tag extends ResourceController
{
  protected $format = 'json';
  protected $modelName = 'App\Models\Tag_model';

  public function index()
  {
    return $this->respond($this->model->findAll());
  }

  public function create()
  {
    $data = $this->request->getJSON();

    $tag = [
      'tag_name'  => $data->tag_name,
      'tag_color' => $data->tag_color
    ];

    $stored = $this->model->insert($tag);
    $lastData = $this->model->find($stored);

    if ($stored) {
      $msg = ['message' => 'Tag created'];
      $response = [
        'data'  => $lastData
      ];
      return $this->respondCreated($response, "Tag created");
    }
  }
}