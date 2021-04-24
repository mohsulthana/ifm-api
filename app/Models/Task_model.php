<?php

namespace App\Models;

use CodeIgniter\Model;

class Task_model extends Model {
  protected $table = "task";
  protected $allowedFields = ['task', 'description', 'status'];

  public function insertTask($data)
  {
    return $this->db->table($this->table)->insert($data);
  }

  public function updateTask($data, $id)
  {
    return $this->db->table($this->table)->update($data, ['id' => $id]);
  }
}