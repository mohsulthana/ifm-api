<?php

namespace App\Models;

use CodeIgniter\Model;

class Reason_model extends Model {
  protected $table = "reason";
  protected $allowedFields = ['reason', 'created_by'];
}