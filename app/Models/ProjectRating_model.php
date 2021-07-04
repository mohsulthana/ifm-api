<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectRating_model extends Model {
  protected $table = "project_rating";
  protected $allowedFields = ['rate', 'project_id'];
  protected $primaryKey = 'project_id';
}