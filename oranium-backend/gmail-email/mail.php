<?php

// PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Base files 
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
 
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

if($api === 'postEmail') {
  $table = 'form_table';
  postEmail($table,$input,$link);
}

function postEmail ($table,$values,$link) {
    try {
        $name = $values['name'];
        $phone_number = $values['phone_number'];
        $email_id = $values['email_id'];
        $batch_no = $values['batch_no'];
        $batch_name = $values['batch_name'];
        $class_timings_id = $values['class_timings_id'];
        $class_timings_name = $values['class_timings_name'];
        $date = $values['date'];
        
        // create object of PHPMailer class with boolean parameter which sets/unsets exception.
        $mail = new PHPMailer(true);
        $mail->isSMTP(); // using SMTP protocol
        $mail->Host = 'smtp.gmail.com'; // SMTP host as gmail
        $mail->SMTPAuth = true;  // enable smtp authentication
        $mail->Username = 'niralapp@gmail.com';  // sender gmail host
        $mail->Password = 'niralapps'; // sender gmail host password
        $mail->SMTPSecure = 'tls';  // for encrypted connection
        $mail->Port = 587;   // port for SMTP
    
        $mail->setFrom('niralapp@gmail.com', "Niral App"); // sender's email and name
        $mail->addAddress('niralapp@gmail.com', "Niral App");  // receiver's email and name

        $mailBody = "Hi! A new Entry has came. Name: '".$name."', Phone Number: '".$phone_number."', Email id: '".$email_id."', Batch: '".$batch_name."', Class Timings: '".$class_timings_name."', Date: '".$date."'";
    
        $mail->Subject = 'New Entry!';
        $mail->Body    = $mailBody;
    
        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) { // handle error.
        echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
    }
}

?>