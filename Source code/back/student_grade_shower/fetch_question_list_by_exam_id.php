<?php
require "db.php";
header('Content-Type: application/json;charset=utf-8');

$POSTunique_id = $_POST['exam_id'];

global $con;
$sql = "SELECT `question_list` FROM `exam_list` WHERE `exam_id` = $POSTunique_id";
$data = [];
$results = $con->query($sql);
if ($results->num_rows > 0)
{
    while ($row = $results->fetch_assoc())
    {
        $data[] = $row;
    }
}

$question_num_list = $data[0]['question_list'];

echo $question_num_list;


?>
