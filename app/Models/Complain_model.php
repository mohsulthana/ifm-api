<?php

namespace App\Models;

use CodeIgniter\Model;

class Complain_model extends Model {
  protected $table = "complain";
  protected $allowedFields = ['complain', 'people', 'project_id'];
}