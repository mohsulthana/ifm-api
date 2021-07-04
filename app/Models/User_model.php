<?php

namespace App\Models;

use CodeIgniter\Model;

class User_model extends Model {
  protected $table = "users";
  protected $primaryKey = 'user_id';

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