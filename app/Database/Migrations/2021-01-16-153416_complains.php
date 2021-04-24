<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Complains extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id'	=> [
				'type'           => 'INT',
				'constraint'     => 11,
				'auto_increment' => TRUE
			],
			'complain'	=> [
				'type' => 'TEXT',
				'null' => true
			],
			'people'	=> [
				'type'       => 'VARCHAR',
				'constraint' => 255
			],
			'project_id'	=> [
				'type' => 'INT',
				'unsigned' => TRUE
			],
			'created_date datetime default current_timestamp',
			'updated_date datetime default current_timestamp on update current_timestamp',
		]);
		// $this->forge->addForeignKey('customer_id', 'users', 'id');
		$this->forge->addKey('id', TRUE);
		$this->forge->createTable('complain');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		//
	}
}
