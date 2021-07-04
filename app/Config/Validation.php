<?php namespace Config;

class Validation
{
	//--------------------------------------------------------------------
	// Setup
	//--------------------------------------------------------------------

	/**
	 * Stores the classes that contain the
	 * rules that are available.
	 *
	 * @var array
	 */
	public $ruleSets = [
		\CodeIgniter\Validation\Rules::class,
		\CodeIgniter\Validation\FormatRules::class,
		\CodeIgniter\Validation\FileRules::class,
		\CodeIgniter\Validation\CreditCardRules::class,
	];

	/**
	 * Specifies the views that are used to display the
	 * errors.
	 *
	 * @var array
	 */
	public $templates = [
		'list'   => 'CodeIgniter\Validation\Views\list',
		'single' => 'CodeIgniter\Validation\Views\single',
	];

	//--------------------------------------------------------------------
	// Rules
	public $task = [
		'task'	=> 'required',
		'description'	=> 'required',
		'status'	=> 'required',
		'project_id'	=> 'required'
	];

	public $task_errors = [
		'task'	=> [
			'required'	=> 'Task is required.'
		],
		'task_description'	=> [
			'required'	=> 'Task description is required.'
		],
		'status'	=> [
			'required'	=> 'Task status is required'
		],
		'project_id'	=> [
			'required'	=> 'Projet ID is required'
		]
	];


	public $project = [
		'project'	=> 'required',
		'description'	=> 'required'
	];

	public $project_errors = [
		'project'	=> [
			'required'	=> 'Project is required.'
		],
		'project_description'	=> [
			'required'	=> 'Project description is required.'
		]
	];

	public $service = [
		'service'	=> 'required',
		'description'	=> 'required'
	];

	public $service_errors = [
		'service'	=> [
			'required'	=> 'Project is required.'
		],
		'service_description'	=> [
			'required'	=> 'Project description is required.'
		]
	];

	public $complain = [
		'complain'	=> 'required',
		'people'	=> 'required',
		'project_id'	=> 'required',
	];

	public $complain_errors = [
		'complain'	=> [
			'required'	=> 'Complain is required.'
		],
		'people'	=> [
			'required'	=> 'Customer is required.'
		],
		'project_id'	=> [
			'required'	=> 'Project is required.'
		],
	];
	//--------------------------------------------------------------------
}
