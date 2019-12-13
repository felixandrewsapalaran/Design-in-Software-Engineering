<?php
/*
 * Fetched all exams in the exam list
 */

require "db.php";
global $con;
header('Content-Type: application/json;charset=utf-8');

$results = $con->query( "SELECT * FROM `exam_list`" );
$db_data = array();

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
        'error_message' => 'There are no exams in the Exam List'
    );
}
echo(json_encode($data));

?>