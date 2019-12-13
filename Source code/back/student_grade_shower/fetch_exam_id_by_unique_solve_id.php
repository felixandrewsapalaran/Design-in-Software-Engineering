<?php
require "../db.php";
$POSTunique_id = $_POST['unique_id'];

global $con;
$sql = "SELECT `exam_id` FROM `student_ans_exam` WHERE `uniq_exam_solve_id` = $POSTunique_id";
$data = [];

$results = $con->query($sql);
if ($results->num_rows > 0)
{
    while ($row = $results->fetch_assoc())
    {
        $data[] = $row;
    }
}
echo $data[0]['exam_id'];
?>