<?php

namespace App\Models;

use CodeIgniter\Model;

class Project_model extends Model {
  protected $table = "project";
  protected $allowedFields = ['project', 'description', 'customer_id', 'service_id'];

  public function insertProject($data)
  {
    return $this->db->table($this->table)->insert($data);
  }

  public function updateProject($data, $id)
  {
    return $this->db->table($this->table)->update($data, ['id' => $id]);
  }
}