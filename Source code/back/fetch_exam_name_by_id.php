<?php
require "db.php";
global $con;
header('Content-Type: application/json;charset=utf-8');

$POSTExam_id = $_POST['exam_id'];

$results = $con->query("SELECT `exam_name` FROM `exam_list` WHERE `exam_id` = $POSTExam_id ");

if ($results->num_rows > 0)
{
    while ( $row = $results->fetch_assoc())
    {
        $data[] = $row;
    }
}
else
{
    $data = array(
        'return_code' => 0,
        'error_message' => 'Exam ID doesnt exist',
        'exam_id' => $POSTExam_id
    );
}
echo(json_encode($data));


?>