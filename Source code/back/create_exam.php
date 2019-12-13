<?php
require "db.php";
global $con;
header('Content-Type: application/json;charset=utf-8');

$exam_name = $_POST['exam_name'];
$question_list = $_POST['question_list'];
$question_points = $_POST['question_points'];
$max_points = $_POST['max_points'];

$sql = "INSERT INTO exam_list(exam_name,question_list,question_points,max_points) VALUES('$exam_name','$question_list','$question_points','$max_points')";

if(empty($exam_name) || empty($question_list) || empty($question_points) || empty($max_points))
{
    $rc = array(
        'return code' => 0,
        'error_msg' => 'Empty/Missing Variable Passed In',
        'exam_name' => $exam_name,
        'question_list' => $question_list,
        'question_points' => $question_points,
        'max_points' => $max_points
        );
}
else
{
    if ($con->query($sql) === TRUE)
    {
        //Exam created successfully
        $rc = array('return_code' => 1);
    }
    else
    {
        $rc = array(
            'return_code' => 'Insert Failed',
            'error_message' => $mysqli->error,
            'exam_name' => $exam_name,
            'question_list' => $question_list,
            'question_points' => $question_points,
            'max_points' => $max_points
            );
    }
}
  
echo(json_encode($rc));
?>