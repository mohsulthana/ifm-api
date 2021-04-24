<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Project extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id'	=> [
				'type'           => 'INT',
				'constraint'     => 11,
				'auto_increment' => TRUE
			],
			'project'	=> [
				'type'       => 'VARCHAR',
				'constraint' => 100
			],
			'description'	=> [
				'type' => 'TEXT',
				'null' => true
			],
			'customer_id'	=> [
				'type' => 'INT',
				'unsigned' => TRUE
			],
			'created_date datetime default current_timestamp',
			'updated_date datetime default current_timestamp on update current_timestamp',
		]);
		// $this->forge->addForeignKey('customer_id', 'users', 'id');
		$this->forge->addKey('id', TRUE);
		$this->forge->createTable('project');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('project');
	}
}
