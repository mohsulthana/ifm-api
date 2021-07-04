<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class ProjectRating extends ResourceController
{
  protected $format = 'json';
  protected $modelName = 'App\Models\ProjectRating_model';

  public function index()
  {
    $projectRating = $this->model->join('project', 'project.id = project_rating.project_id', 'inner')->findAll();
    return $this->respond($projectRating, 200);
  }
}
