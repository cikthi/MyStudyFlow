<?php
// notify.php
$data = json_decode(file_get_contents("php://input"), true);
$task = $data['task'] ?? '';
$due  = $data['due'] ?? '';

// Konfigurasi email
$to = "athirahsuhairi1@gmail.com"; // <-- tukar email anda
$subject = "Your Task near due date!";
$message = "Task: $task\nDue date: $due\n\nFinish it now!!.";
$headers = "From: reminder@yourdomain.com";

if(mail($to, $subject, $message, $headers)){
    echo "OK";
} else {
    echo "FAILED";
}
