<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
  header('Access-Control-Allow-Origin: *');
  header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
  header("HTTP/1.1 200 OK");
  die();
}

function cleanMe($input, $isRemoveTag = true)
{
  if ($isRemoveTag === true) {
    $input = htmlspecialchars($input, ENT_IGNORE, 'utf-8');
  }
  $input = strip_tags($input);
  $input = stripslashes($input);
  return $input;
}

function response($output, $statusCode)
{
  http_response_code($statusCode);
  echo json_encode($output);
  die();
}

// VALIDATION
print_r($_SERVER["REQUEST_METHOD"]);exit;
if ($_SERVER["REQUEST_METHOD"] === "GET") {
  return response(["status" => false, "response" => "INVALID REQUEST METHOD"], 400);
} else {

  // POST

  // GET
  $post = $_POST;
  if ($post && count($post) > 0) {

    // VALIDATION
    if (!isset($post["host"]) || empty(cleanMe($post["host"]))) {
      return response(["status" => false, "response" => "Host Not Found"], 400);
    } else if (!isset($post["username"]) || empty(cleanMe($post["username"]))) {
      return response(["status" => false, "response" => "Username Not Found"], 400);
    } else if (!isset($post["password"]) || empty(cleanMe($post["password"]))) {
      return response(["status" => false, "response" => "Password Not Found"], 400);
    } else if (!isset($post["port"]) || empty(cleanMe($post["port"]))) {
      return response(["status" => false, "response" => "Port Not Found"], 400);
    } else if (!isset($post["is_smtp"]) || empty(cleanMe($post["is_smtp"]))) {
      return response(["status" => false, "response" => "Is SMTP Not Found"], 400);
    } else if (!isset($post["mail_send_email"]) || empty(cleanMe($post["mail_send_email"]))) {
      return response(["status" => false, "response" => "Sender Id Not Found"], 400);
    } else if (!isset($post["mail_subject"]) || empty(cleanMe($post["mail_subject"]))) {
      return response(["status" => false, "response" => "Mail Subject Not Found"], 400);
    } else if (!isset($post["mail_html"]) || empty(cleanMe($post["mail_html"], false))) {
      return response(["status" => false, "response" => "Mail Body Not Found"], 400);
    } else {

      // OK
      $mail = new PHPMailer(true);

      try {

        // Server settings
        $mail->SMTPDebug  = 0;
        // Is SMTP
        if (cleanMe($post["is_smtp"]) == "true") $mail->isSMTP();
        $mail->Host       = cleanMe($post["host"]);
        $mail->SMTPAuth   = true;
        $mail->Username   = cleanMe($post["username"]);
        $mail->Password   = cleanMe($post["password"]);
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = cleanMe($post["port"]);
        // Recipients
        $mail->setFrom(cleanMe($post["username"]));
        $mail->addAddress(cleanMe($post["mail_send_email"]));
        // Content
        $mail->isHTML(true);
        $mail->Subject = cleanMe($post["mail_send_email"]);
        $mail->Body    = cleanMe($post["mail_html"]);
        // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
        return response(["status" => false, "response" => "Sent"], 200);
      } catch (Exception $e) {
        return response(["status" => false, "response" => $mail->ErrorInfo], 400);
      }
    }
  } else {
    return response(["status" => false, "response" => "INVALID REQUEST DATA"], 400);
  }
}
