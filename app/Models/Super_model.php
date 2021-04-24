<?php
namespace App\Models;

use CodeIgniter\Model;

class Super_model extends Model
{
  protected $table = "super";
  protected $primaryKey = "super_id";

  public function register($data)
  {
    $query = $this->db->table($this->table)->insert($data);
    return $query ? true : false;
  }

  public function cek_login($email)
  {
    $query = $this->table($this->table)->where('email')->countAll();

    if ($query > 0) {
      $hasil = $this->table($this->table)->where('email', $email)->limit(1)->get()->getRowArray();
    } else {
      $hasil = array();
    }
    return $hasil;
  }
}