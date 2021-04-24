<?php

namespace App\Models;

use CodeIgniter\Model;

class Service_model extends Model {
  protected $table = "service";
  protected $allowedFields = ['service', 'description', 'customer_id'];

  public function insertService($data)
  {
    return $this->db->table($this->table)->insert($data);
  }

  public function updateTask($data, $id)
  {
    return $this->db->table($this->table)->update($data, ['id' => $id]);
  }
}