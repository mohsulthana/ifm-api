<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SuperAdmin extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'super_id'	=> [
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
			],
			'role'	=> [
				'type'	=> 'VARCHAR',
				'constraint'	=> 100,
				'default'	=> 'super'
			],
		]);
		$this->forge->addKey('super_id', TRUE);
		$this->forge->createTable('super');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		//
	}
}
