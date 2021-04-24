<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Admin extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'admin_id'	=> [
				'type'	=> 'INT',
				'constraint'	=> 11,
				'auto_increment'	=> TRUE
			],
			'name'	=> [
				'type'	=> 'VARCHAR',
				'constraint'	=> 100
			],
			'photo'	=> [
				'type'	=> 'VARCHAR',
				'constraint'	=> 255,
				'null'	=> TRUE
			],
			'about'	=> [
				'type'	=> 'VARCHAR',
				'constraint'	=> 255
			],
			'email'	=> [
				'type'	=> 'VARCHAR',
				'constraint'	=> 100
			],
			'password'	=> [
				'type'	=> 'VARCHAR',
				'constraint'	=> 100
			]
		]);
		$this->forge->addKey('admin_id', TRUE);
		$this->forge->createTable('admin');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		//
	}
}
