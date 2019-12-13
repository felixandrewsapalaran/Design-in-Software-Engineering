<?php
require "db.php";
global $con;
header('Content-Type: application/json;charset=utf-8');

$id = $_POST['unique_id'];
$data2 =[];
$select_completed_exam_query2 = "SELECT `auto_grader_comments` FROM `student_ans_exam` WHERE `uniq_exam_solve_id` = $id";
$results2 = $con->query($select_completed_exam_query2);

if ($results2->num_rows > 0)
{
    while ($row = $results2->fetch_assoc())
    {
        $data2[] = $row;
    }
}

echo json_encode($data2[0]['auto_grader_comments']);


?>