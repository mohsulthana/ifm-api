<?php

namespace App\Models;

use CodeIgniter\Model;

class WorkerHasProject_model extends Model {
  protected $table = "worker_has_project";
  protected $allowedFields = ['project_id', 'worker_id'];
}