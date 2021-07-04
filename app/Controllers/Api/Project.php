<?php

namespace App\Controllers\Api;

use App\Models\ProjectRating_model;
use App\Models\WorkerHasProject_model;
use CodeIgniter\RESTful\ResourceController;

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, OPTIONS, UPDATE, PUT");

class Project extends ResourceController
{
  protected $format = 'json';
  protected $modelName = 'App\Models\Project_model';

  public function __construct()
  {
    $this->project_rating = new ProjectRating_model();
  }

  public function index()
  {
    return $this->respond($this->model->select('project.*, tag.tag_name, tag.tag_color')->join('tag', 'tag.id = project.tag', 'left')->findAll(), 200);
  }

  public function projectByCustomer($id = null)
  {
    $projects = $this->model->select('project.id, project.project, project.description, customer.name, project_rating.rate')->join('customer', 'customer.user_id = project.customer_id')->join('project_rating', 'project.id = project_rating.project_id', 'left')->where(['project.customer_id' => $id])->findAll();
    return $this->respond($projects, 200);
  }

  public function projectFolder($id)
  {
    $projects = $this->model->join('worker_has_project', 'worker_has_project.project_id = project.id')->where('worker_has_project.worker_id', $id)->findAll();
    foreach ($projects as $key => $value) {
      $projects[$key]['image'] = base_url() . '/uploads/project/' . $value['image'];
    }
    return $this->respond($projects, 200);
  }

  public function createProjectWorker()
  {
    $data = $this->request->getJSON();
    $projectWorker = new WorkerHasProject_model();

    $data = [
      'project_id' => $data->project->project_id,
      'worker_id' => $data->project->worker_id
    ];

    $stored = $projectWorker->insert($data);
    $response = [
      'id'  => $stored,
      'status'  => 200
    ];
    $this->respondCreated($response, "Your project worker has been created");
  }

  public function convertBase64($image, $id)
  {
    if (!is_dir(ROOTPATH . 'public/uploads/project/' . $id . '/images')) {
      mkdir(ROOTPATH . 'public/uploads/project/' . $id . '/images', 0777, true);
    }
    $image_parts = explode(";base64,", $image);
    $image_type_aux = explode("image/", $image_parts[0]);
    $image_type = $image_type_aux[1];
    $image_base64 = base64_decode($image_parts[1]);
    $image_name = md5(uniqid(rand(), true)) . '.png';
    file_put_contents(ROOTPATH . 'public/uploads/project/' . $id . '/images/' . $image_name, $image_base64);
    return $image_name;
  }

  public function create()
  {
    $validation = \Config\Services::validation();
    $data = $this->request->getJSON();

    $imageFile = $data->project->image;

    $data = [
      'project'   => $data->project->project,
      'description' => $data->project->description,
      'customer_id' => $data->project->customer,
      'tag' => $data->project->tag,
      'start_date'  => $data->project->start_date,
      'end_date'  => $data->project->end_date,
      'service_id'  => $data->project->service
    ];

    if ($validation->run($data, 'project') == FALSE) {
      $response = [
        'status'  => 500,
        'error' => true,
        'data'  => $validation->getErrors(),
      ];
      return $this->respond($response, 500);
    } else {
      $stored = $this->model->insert($data);

      if ($stored) {
        $convert = $this->convertBase64($imageFile, $stored);
        $this->model->update($stored, [
          'image' => $convert
        ]);

        // insert rate project row
        $rate = [
          'project_id'  => $stored,
          'rate'  => null
        ];
        $this->project_rating->insert($rate);

        $project = $this->model->join('tag', 'tag.id = project.tag')->find($stored);
        $response = [
          'id'  => $stored,
          'data'  => $project
        ];
        return $this->respondCreated($response, 'Project created');
      }
    }
  }

  public function update($id = NULL)
  {
    $validation =  \Config\Services::validation();
    $json = $this->request->getJSON();

    $data = $this->model->asObject()->find($id);

    if ($data) {
      $project = [
        'project' => $json->project,
        'description' => $json->description
      ];

      if ($validation->run($project, 'project') == FALSE) {
        $response = [
          'status' => 500,
          'error' => true,
          'data' => $validation->getErrors(),
        ];
        return $this->respond($response, 500);
      } else {
        $update = $this->model->update($id, $project);

        if ($update) {
          $msg = ['message' => 'Project updated'];
          $response = [
            'status' => 200,
            'project'   => $this->model->find($id),
            'data' => $msg,
          ];
          return $this->respond($response, 200);
        }
      }
    }
  }

  public function show($id = NULL)
  {
    $project = $this->model->join('project_rating', 'project.id = project_rating.project_id', 'left')->find($id);
    $task = $this->model->select('*')->join('task', 'task.project_id = project.id', 'inner')->where('project.id', $id)->findAll();

    $project['image'] = base_url() . '/uploads/project/' . $project['image'];
    foreach ($task as $key => $value) {
      $task['qr_code'] = base_url() . '/uploads/task/' . $value['id'] . '/' . $value['qr_code'];
    }


    if ($project) {
      $code = 200;
      $response = [
        'status' => $code,
        'data' => $project,
        'task'  => $task
      ];
    } else {
      $code = 404;
      $msg = ['message' => 'Not Found'];
      $response = [
        'status' => $code,
        'data' => $msg,
      ];
    }
    return $this->respond($response, $code);
  }

  public function delete($id = NULL)
  {
    $delete = $this->model->delete($id);
    if ($delete) {
      $response = [
        'status'  => 200,
        'project'   => $this->model->findAll(),
        'message'    => 'Project deleted successfully'
      ];
      return $this->respond($response, 200);
    } else {
      $response = [
        'status' => 500,
        'error' => true,
        'data'  => 'Project not successfully deleted'
      ];
      return $this->respond($response, 500);
    }
  }

  public function updateRating($id)
  {
    $json = $this->request->getJSON();
    $data = $this->project_rating->asObject()->find($id);

    // ketika sudah ada
    if ($data) {
      $rating = [
        'rate'  => $json->rating
      ];
      $this->project_rating->update($id,$rating);
      $response = [
        'msg' => 'Rating updated',
        'data'  => $this->project_rating->where('project_id', $id)->findAll()
      ];
      return $this->respond($response, 200);
    } else {
      $rating = [
        'project_id'  => $id,
        'rate'  => $json->rating
      ];
      $this->project_rating->insert($rating);
      $response = [
        'msg' => 'Rating submitted',
        'data'  => $this->project_rating->where('project_id', $id)->findAll()
      ];
      return $this->respondCreated($response);
    }
  }

  public function uploadPDF($id)
  {
    $pdf = $this->request->getFile('pdf');
    $fileName = $pdf->getRandomName();

    if ($id) {
      $this->model->update($id, [
        'pdf' => $fileName
      ]);
      $pdf->move('uploads/project/' . $id . '/pdf/', $fileName);
      return $this->respondUpdated($fileName, "PDF Successfully uploaded");
    } else {
      return $this->failNotFound("Project not found");
    }
  }
}
