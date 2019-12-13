<?php
header('Content-Type: application/json;charset=utf-8');
// A sample PHP Script to POST data using cURL
// Data in JSON format

 
// Prepare new cURL resource
$ch = curl_init('https://web.njit.edu/~oa227/490/final/back/submit_to_auto_grader.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, "ucid=stud&exam_id=60");
 
// Submit the POST request
$result = curl_exec($ch);
echo $result;
 
// Close cURL session handle
curl_close($ch);
 
?>