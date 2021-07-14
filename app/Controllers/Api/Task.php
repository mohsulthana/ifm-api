<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Libraries\Ciqrcode;

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, OPTIONS, UPDATE, PUT");

class Task extends ResourceController
{
  protected $format = 'json';
  protected $modelName = 'App\Models\Task_model';

  public function index()
  {
    $worker_id = $this->request->getJSON();
    return $this->respond($this->model->findAll(), 200);
  }

  public function getAllTasks()
  {
    return $this->respond($this->model->findAll(), 200);
  }

  public function getTasksByProject($id)
  {
    $task = $this->model->select('task.task, task.id, task.qr_code, task.status, task.description, task.token')->join('project', 'project.id = task.project_id', 'inner')->where('task.project_id', $id)->findAll();
    return $this->respond($task, 200);
  }

  public function singleTask($id = NULL)
  {
    $data = $this->model->select('task.*')->join('project', 'project.id = task.project_id', 'inner')->find($id);
    $data['qr_code'] = base_url() . '/uploads/task/' . $id . '/qr_code/' . $data['qr_code'];
    $data['before_work'] = $data['before_work'] != '' ? base_url() . '/uploads/task/' . $id . '/before_work/' . $data['before_work'] : '';
    $data['after_work'] = $data['after_work'] != '' ? base_url() . '/uploads/task/' . $id . '/after_work/' . $data['after_work'] : '';

    if ($data) {
      $response = [
        'task'  => $data,
        'status'  => 200,
        'error' => true
      ];
      return $this->respond($response, 200);
    }
  }

  public function getTaskForWorker($id)
  {
    $data = $this->model->select('task.id, task.task, task.description, task.status')->join('project', 'project.id = task.project_id', 'inner')->where('task.project_id', $id)->findAll();
    return $this->respond($data, 200);
  }

  public function create()
  {
    $validation = \Config\Services::validation();

    $data = $this->request->getJSON();

    $data = [
      'task'   => $data->task->task,
      'description' => $data->task->description,
      'status' => $data->task->status,
      'project_id'  => $data->task->project_id
    ];

    if ($validation->run($data, 'task') == FALSE) {
      $response = [
        'status'  => 500,
        'error' => true,
        'data'  => $validation->getErrors(),
      ];
      return $this->respond($response, 500);
    } else {
      $stored = $this->model->insert($data);

      if ($stored) {
        $token = $this->random_str(10);
        $this->generateQRCode($stored, $token);
        $task = $this->model->select('task.task, task.id, task.qr_code, task.status, task.description, task.token')->join('project', 'project.id = task.project_id', 'inner')->where('task.id', $stored)->findAll();
        $response = [
          'id'  => $stored,
          'data'  => $task
        ];

        // generate QR Code

        return $this->respondCreated($response, 'Task created');
      }
    }
  }

  // Update status of work
  // http://localhost:8080/update-status-work/:id
  public function updateStatusWork($id)
  {
    $row = $this->model->find($id);

    if ($row['status'] == 'Not Completed') {
      $updateStatus = [
        'status' => 'On Progress'
      ];
      $this->model->transStart();
      $this->model->updateTask($updateStatus, $id);
      $this->model->transComplete();
    } else if ($row['status'] == 'On Progress') {
      $updateStatus = [
        'status' => 'Done'
      ];
      $this->model->transStart();
      $this->model->updateTask($updateStatus, $id);
      $this->model->transComplete();
    }
  }

  // Verify QR Code
  public function generateQRCode($id, $token)
  {
    $qrcode = new Ciqrcode();

    // production
    // if (!is_dir(ROOTPATH . '../apiapp/uploads/task/' . $id . '/qr_code')) {
    //   mkdir(ROOTPATH . '../apiapp/uploads/task/' . $id . '/qr_code', 0777, true);
    // }

    // $config['cacheable']  = false; //boolean, the default is true
    // $config['cachedir']    = ROOTPATH . 'cache'; //string, the default is application/cache/
    // $config['errorlog']    = ROOTPATH . 'logs'; //string, the default is application/logs/
    // $config['imagedir']    = ROOTPATH . '../apiapp/uploads/task/' . $id . '/qr_code'; //direktori penyimpanan qr code
    // $config['quality']    = true; //boolean, the default is true
    // $config['size']      = '2048'; //interger, the default is 1024
    // $config['black']    = array(224, 255, 255); // array, default is array(255,255,255)
    // $config['white']    = array(70, 130, 180); // array, default is array(0,0,0)

    if (!is_dir(ROOTPATH . 'public/uploads/task/' . $id . '/qr_code')) {
      mkdir(ROOTPATH . 'public/uploads/task/' . $id . '/qr_code', 0777, true);
    }

    $config['cacheable']  = false; //boolean, the default is true
    $config['cachedir']    = ROOTPATH . 'cache'; //string, the default is application/cache/
    $config['errorlog']    = ROOTPATH . 'logs'; //string, the default is application/logs/
    $config['imagedir']    = ROOTPATH . 'public/uploads/task/' . $id . '/qr_code'; //direktori penyimpanan qr code
    $config['quality']    = true; //boolean, the default is true
    $config['size']      = '2048'; //interger, the default is 1024
    $config['black']    = array(224, 255, 255); // array, default is array(255,255,255)
    $config['white']    = array(70, 130, 180); // array, default is array(0,0,0)

    $qrcode->initialize($config);

    $image_name = md5(uniqid(rand(), true)) . '.png';

    // http://localhost:8080/update-status-work/:id
    $params['data'] = $token; //data yang akan di jadikan QR CODE
    $params['level'] = 'H'; //H=High
    $params['size'] = 10;
    $params['savename'] = $config['imagedir'] . '/' . $image_name; //simpan image QR CODE ke folder assets/images/
    $qrcode->generate($params);

    $updateQR = [
      'qr_code' => $image_name,
      'token' => $token
    ];

    $this->model->transStart();
    $this->model->updateTask($updateQR, $id);
    $this->model->transComplete();

    if ($this->model->transStatus() === FALSE) {
      return false;
    }
    return true;
  }

  public function verifyToken()
  {
    $json = $this->request->getJSON();

    $id = $json->id;
    $token = $json->token;

    $data = $this->model->find($id);

    // if data ex matched
    if ($data['token'] == $token) {
      $data = [
        'message' => 'Your token is valid.'
      ];

      return $this->respond($data, 200);
    } else {
      return $this->failNotFound('Your token is invalid');
    }
  }

  function random_str(
    int $length = 64,
    string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
  ): string {
    if ($length < 1) {
      throw new \RangeException("Length must be a positive integer");
    }
    $pieces = [];
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
      $pieces[] = $keyspace[random_int(0, $max)];
    }
    return implode('', $pieces);
  }

  // TODO: BEFORE WORK & AFTER WORK UPDATE

  public function updateBeforeWork($id = NULL)
  {
    $data = $this->model->asObject()->find($id);

    $image = $this->request->getFile('before_work');
    $fileName = $image->getRandomName();

    if ($data) {
      $photo = [
        'before_work' => $fileName,
        'status'      => $this->request->getPost('status'),
        'started_time' => date('Y-m-d H:i:s')
      ];
      $image->move('uploads/task/' . $id . '/before_work/', $fileName);

      $update = $this->model->update($id, $photo);

      if ($update) {
        $response = [
          'task'   => $this->model->find($id),
          'photo' => base_url(). '/uploads/task/' . $id . '/before_work/' . $fileName
        ];
        return $this->respond($response, 200);
      }
    }
  }

  public function updateAfterWork($id = NULL)
  {
    $data = $this->model->asObject()->find($id);

    $image = $this->request->getFile('after_work');
    $fileName = $image->getRandomName();
    if ($data) {
      $photo = [
        'after_work' => $fileName,
        'status'      => $this->request->getPost('status'),
        'started_time' => date('Y-m-d H:i:s')
      ];
      $image->move('uploads/task/' . $id . '/after_work/', $fileName);

      $update = $this->model->update($id, $photo);

      if ($update) {
        $response = [
          'task'   => $this->model->find($id),
          'photo' => base_url(). '/uploads/task/' . $id . '/after_work/' . $fileName
        ];
        return $this->respond($response, 200);
      }
    }
  }

  public function cancelTask($id)
  {
    $row = $this->model->find($id);
    $json = $this->request->getJSON();

    if ($row) {
      $cancel = [
        'status'  => 'Cancelled',
        'cancel_reason'  => $json->reason
      ];
      $update = $this->model->update($id, $cancel);
      if ($update) {
        $msg = ['message' => 'Work cancelled'];
        $response = [
          'task'   => $this->model->find($id),
          'status' => $this->model->select('status')->find($id)
        ];
        return $this->respond($response, 200);
      }
    }
  }

  public function delete($id = NULL)
  {
    $delete = $this->model->delete($id);
    if ($delete) {
      $response = [
        'status'  => 200,
        'task'   => $this->model->findAll(),
        'message'    => 'Task deleted successfully'
      ];
      return $this->respond($response, 200);
    } else {
      $response = [
        'status' => 500,
        'error' => true,
        'data'  => 'Task not successfully deleted'
      ];
      return $this->respond($response, 500);
    }
  }

  public function updateStatus($id)
  {
    $json = $this->request->getJSON();

    $data = $this->model->asObject()->find($id);

    if ($data) {
      $task = [
        'status'  => $json->status
      ];
      $update = $this->model->update($id, $task);

      if ($update) {
        $msg = ['message' => 'Task updated'];
        $response = [
          'status' => 200,
          'task'   => $this->model->find($id),
          'data' => $msg,
        ];
        return $this->respond($response, 200);
      }
    }
  }

  public function convertBase64BeforeWork($image, $id)
  {
    if (!is_dir(ROOTPATH . 'public/uploads/task/' . $id . '/before_work')) {
      mkdir(ROOTPATH . 'public/uploads/task/' . $id . '/before_work', 0777, true);
    }
    $image_parts = explode(";base64,", $image);
    $image_type_aux = explode("image/", $image_parts[0]);
    $image_type = $image_type_aux[1];
    $image_base64 = base64_decode($image_parts[1]);
    $image_name = md5(uniqid(rand(), true)) . '.png';
    file_put_contents(ROOTPATH . 'public/uploads/task/' . $id . '/before_work/' . $image_name, $image_base64);
    return $image_name;
  }

  public function convertBase64AfterWork($image, $id)
  {
    if (!is_dir(ROOTPATH . 'public/uploads/task/' . $id . '/after_work')) {
      mkdir(ROOTPATH . 'public/uploads/task/' . $id . '/after_work', 0777, true);
    }
    $image_parts = explode(";base64,", $image);
    $image_type_aux = explode("image/", $image_parts[0]);
    $image_type = $image_type_aux[1];
    $image_base64 = base64_decode($image_parts[1]);
    $image_name = md5(uniqid(rand(), true)) . '.png';
    file_put_contents(ROOTPATH . 'public/uploads/task/' . $id . '/after_work/' . $image_name, $image_base64);
    return $image_name;
  }
}
