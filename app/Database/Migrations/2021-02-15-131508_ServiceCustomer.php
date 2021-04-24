<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ServiceCustomer extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'customer_id'	=> [
				'type'	=> 'INT',
				'constraint'	=> 11
			],
			'service_id'	=> [
				'type'	=> 'INT',
				'constraint'	=> 11
			],
		]);
		$this->forge->addKey('customer_id', TRUE);
		$this->forge->addKey('service_id', TRUE);
		$this->forge->createTable('service_customer');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		//
	}
}
