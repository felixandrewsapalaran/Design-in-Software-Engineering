<?php
/* Add UCID check in SQL later */
require "db.php";
global $con;
header('Content-Type: application/json;charset=utf-8');

$results = $con->query("SELECT * FROM `exam_list` WHERE `exam_id` NOT IN (SELECT `exam_id` FROM `student_ans_exam`)");

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
        'error_message' => 'No Exams are Avaliable'
    );
}
echo(json_encode($data));

?>