<?php

namespace App\Models;

use CodeIgniter\Model;

class Tag_model extends Model {
  protected $table = "tag";
  protected $allowedFields = ['tag_id', 'tag_name', 'tag_color'];
}