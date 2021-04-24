<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Service extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id'	=> [
				'type'           => 'INT',
				'constraint'     => 11,
				'auto_increment' => TRUE
			],
			'service'	=> [
				'type'       => 'VARCHAR',
				'constraint' => 100
			],
			'description'	=> [
				'type' => 'TEXT',
				'null' => true
			],
			'created_date datetime default current_timestamp',
			'updated_date datetime default current_timestamp on update current_timestamp',
		]);
		$this->forge->addKey('id', TRUE);
		$this->forge->createTable('service');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		//
	}
}
