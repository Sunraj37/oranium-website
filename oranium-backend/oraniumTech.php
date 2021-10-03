
<?php
 
// get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
//header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
 
date_default_timezone_set('Asia/Kolkata');
 
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  // The request is using the POST method
  die();
}
 
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'),true);
if (!$input) $input = array();
// connect to the mysql database
$link = mysqli_connect('localhost', 'root', '', 'oranium_tech');
mysqli_set_charset($link,'utf8');  
// retrieve the table and key from the path
$api = preg_replace('/[^a-z0-9_]+/i','',array_shift($request));
$columns = preg_replace('/[^a-z0-9_]+/i','',array_keys($input));

if($api === 'post') {
  $table = 'form_table';
  postFormData($table,$input,$link);
}
 
function postFormData ($table,$values,$link) {
  $name = $values['name'];
  $phone_number = $values['phone_number'];
  $email_id = $values['email_id'];
  $batch_no = $values['batch_no'];
  $batch_name = $values['batch_name'];
  $class_timings_id = $values['class_timings_id'];
  $class_timings_name = $values['class_timings_name'];
  $date = $values['date'];
  $sql = "insert into ".$table." (name,phone_number,email_id,batch_no,batch_name,class_timings_id,class_timings_name,date) values ('".$name."','".$phone_number."','".$email_id."','".$batch_no."','".$batch_name."','".$class_timings_id."','".$class_timings_name."','".$date."');";
  $result = mysqli_query($link,$sql);
  // die if SQL statement failed
  if (!json_encode($result)) {
    http_response_code(404);
    die(mysqli_error());
  }

  if(json_encode($result)) {
    $myObj          = new \stdClass();
    $myObj->success = TRUE;
    $myObj->message = "Data Successfully Uploaded";
    $myJSON         = json_encode($myObj);
    echo $myJSON;
    //echo json_encode(mysqli_fetch_object($result));
  } else {
    $myObj          = new \stdClass();
    $myObj->success = FALSE;
    $myObj->message = "Data Uploaded Failed";
    $myJSON         = json_encode($myObj);
    echo $myJSON;
  }

  // close mysql connection
  mysqli_close($link);
}

?>