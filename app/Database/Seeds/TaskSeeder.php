<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TaskSeeder extends Seeder
{
  public function run()
  {
    $data = [
      'task'  => 'Mencuci Baju',
      'description' => 'Lorem ipsum, dolor sit amet consectetur adipisicing elit. Quae, optio. Aliquam saepe fugiat consequatur quod explicabo aut laborum consectetur, temporibus, autem numquam possimus magnam culpa sint. Doloremque, placeat? Placeat, ipsum.',
      'status'  => 'Completed',
    ];
    $this->db->table('task')->insert($data);
  }
}