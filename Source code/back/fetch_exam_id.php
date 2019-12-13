<?php
/*
 * Fetched a specific exam based on ID.
 */
require "db.php";
global $con;
header('Content-Type: application/json;charset=utf-8');
$exam_id = $_POST['exam_id'];

$results = $con->query( "SELECT * FROM `exam_list` WHERE `exam_id` = $exam_id" );
$row = $results->fetch_assoc();

if ($results->num_rows > 0)
{
        $data = array(
            'return_code' => 1,
            'exam_name' => $row["exam_name"],
            'question_list' => $row["question_list"],
            'question_points' => $row["question_points"],
        );
}
else
{
    $data = array(
        'return_code' => 0,
        'error_message' => 'Exam ID does not exist'
    );
}
echo(json_encode($data));

?>