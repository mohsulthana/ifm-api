<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Users extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'user_id'	=> [
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
			'role'	=> [
				'type'	=> 'VARCHAR',
				'constraint'	=> 100,
				'default'	=> 'customer'
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
		$this->forge->addKey('user_id', TRUE);
		$this->forge->createTable('users');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		//
	}
}
