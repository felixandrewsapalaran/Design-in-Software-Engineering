<?php
/* Add UCID check in SQL later */
require "db.php";
global $con;
header('Content-Type: application/json;charset=utf-8');

$results = $con->query("SELECT * FROM `student_ans_exam` WHERE `visible_status` = 1");

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
        'error_message' => 'No Exams Have Been Released Yet.'
    );
}
echo(json_encode($data));

?>